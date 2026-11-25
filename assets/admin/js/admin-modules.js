/* Digi Admin — behaviour for the modules added on top of the original panel.
   admin.js is untouched; this file only adds new interactions. */
(function () {
  'use strict';

  /* ---------- Collapsible sidebar groups ---------- */

  var STORAGE_KEY = 'digiAdminOpenGroups';

  function readOpenGroups() {
    try {
      return JSON.parse(window.localStorage.getItem(STORAGE_KEY)) || [];
    } catch (e) {
      return [];
    }
  }

  function writeOpenGroups(list) {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    } catch (e) {
      /* storage unavailable — groups simply reset on reload */
    }
  }

  var openGroups = readOpenGroups();

  document.querySelectorAll('.admin-nav-group').forEach(function (group) {
    var name = group.getAttribute('data-group');
    var toggle = group.querySelector('.admin-nav-group__toggle');

    if (group.querySelector('.admin-nav-link.is-active') || openGroups.indexOf(name) !== -1) {
      group.classList.add('is-open');
    }

    if (!toggle) return;

    toggle.addEventListener('click', function () {
      group.classList.toggle('is-open');

      var index = openGroups.indexOf(name);

      if (group.classList.contains('is-open') && index === -1) {
        openGroups.push(name);
      } else if (!group.classList.contains('is-open') && index !== -1) {
        openGroups.splice(index, 1);
      }

      writeOpenGroups(openGroups);
    });
  });

  /* ---------- Notification dropdown ---------- */

  var notifBtn = document.querySelector('[data-notif-toggle]');
  var notifPanel = document.querySelector('[data-notif-panel]');

  if (notifBtn && notifPanel) {
    notifBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      notifPanel.classList.toggle('is-open');
    });

    document.addEventListener('click', function (e) {
      if (!notifPanel.contains(e.target)) {
        notifPanel.classList.remove('is-open');
      }
    });
  }

  /* ---------- Repeatable line items (quotations, invoices, checklists) ---------- */

  document.querySelectorAll('[data-repeater]').forEach(function (repeater) {
    var template = repeater.querySelector('[data-repeater-template]');
    var rows = repeater.querySelector('[data-repeater-rows]');
    var addBtn = repeater.querySelector('[data-repeater-add]');

    if (!template || !rows) return;

    // Continue numbering after any rows already rendered for an existing record.
    var index = rows.querySelectorAll('[data-repeater-row]').length;

    function bindRemove(row) {
      var remove = row.querySelector('[data-repeater-remove]');
      if (!remove) return;

      remove.addEventListener('click', function () {
        row.remove();
        recalculate(repeater);
      });
    }

    rows.querySelectorAll('[data-repeater-row]').forEach(bindRemove);

    if (addBtn) {
      addBtn.addEventListener('click', function () {
        var markup = template.innerHTML.replace(/__INDEX__/g, index);
        var wrapper = document.createElement('div');
        wrapper.innerHTML = markup;

        var row = wrapper.firstElementChild;
        rows.appendChild(row);
        bindRemove(row);
        index++;
        recalculate(repeater);
      });
    }

    repeater.addEventListener('input', function () {
      recalculate(repeater);
    });

    repeater.addEventListener('change', function () {
      recalculate(repeater);
    });

    recalculate(repeater);
  });

  /* Live totals preview. The server always recalculates on save — this is
     only so the user sees the maths while typing. */
  function recalculate(repeater) {
    var form = repeater.closest('form');
    if (!form) return;

    var subtotal = 0;
    var freeValue = 0;
    var optionalValue = 0;

    repeater.querySelectorAll('[data-repeater-row]').forEach(function (row) {
      var qty = parseFloat((row.querySelector('[data-line-qty]') || {}).value) || 0;
      var price = parseFloat((row.querySelector('[data-line-price]') || {}).value) || 0;
      var isFree = !!(row.querySelector('[data-line-free]') || {}).checked;
      var isOptional = !!(row.querySelector('[data-line-optional]') || {}).checked;
      var gross = qty * price;

      var lineOut = row.querySelector('[data-line-total]');
      if (lineOut) lineOut.textContent = formatMoney(isFree ? 0 : gross);

      if (isFree) {
        freeValue += gross;
      } else if (isOptional) {
        optionalValue += gross;
      } else {
        subtotal += gross;
      }
    });

    var discountType = (form.querySelector('[name="discount_type"]') || {}).value || 'percentage';
    var discountValue = parseFloat((form.querySelector('[name="discount_value"]') || {}).value) || 0;
    var taxPercent = parseFloat((form.querySelector('[name="tax_percentage"]') || {}).value) || 0;

    var discount = discountType === 'percentage'
      ? subtotal * (Math.min(Math.max(discountValue, 0), 100) / 100)
      : Math.max(discountValue, 0);

    discount = Math.min(discount, subtotal);

    var tax = (subtotal - discount) * (Math.max(taxPercent, 0) / 100);
    var grand = subtotal - discount + tax;

    setText(form, '[data-total-subtotal]', subtotal);
    setText(form, '[data-total-discount]', discount);
    setText(form, '[data-total-tax]', tax);
    setText(form, '[data-total-grand]', grand);
    setText(form, '[data-total-free]', freeValue);
    setText(form, '[data-total-optional]', optionalValue);
  }

  function setText(scope, selector, value) {
    var el = scope.querySelector(selector);
    if (el) el.textContent = formatMoney(value);
  }

  function formatMoney(value) {
    return (Math.round(value * 100) / 100).toFixed(2);
  }

  /* ---------- Pricing item picker: fill a quotation line from the price book ---------- */

  document.addEventListener('change', function (e) {
    var picker = e.target.closest('[data-pricing-picker]');
    if (!picker) return;

    var option = picker.options[picker.selectedIndex];
    if (!option || !option.value) return;

    var row = picker.closest('[data-repeater-row]');
    if (!row) return;

    // The picked price-book id must reach the server, so it is always written.
    var idField = row.querySelector('[data-line-pricing-id]');
    if (idField) idField.value = picker.value;

    // Price, duration and cost come straight from the price book. Name and the
    // free-text fields only fill when empty so typed edits are never clobbered.
    overwrite(row, '[data-line-price]', option.getAttribute('data-price'));
    overwrite(row, '[data-line-days]', option.getAttribute('data-days'));
    overwrite(row, '[data-line-cost]', option.getAttribute('data-cost'));
    fillIfEmpty(row, '[data-line-name]', option.getAttribute('data-name'));
    fillIfEmpty(row, '[data-line-description]', option.getAttribute('data-description'));
    fillIfEmpty(row, '[data-line-features]', option.getAttribute('data-features'));

    var repeater = row.closest('[data-repeater]');
    if (repeater) recalculate(repeater);
  });

  function fillIfEmpty(row, selector, value) {
    var field = row.querySelector(selector);
    if (field && value && !field.value) field.value = value;
  }

  function overwrite(row, selector, value) {
    var field = row.querySelector(selector);
    if (field && value !== null && value !== '') field.value = value;
  }

  /* ---------- Checklist quick toggle ---------- */

  document.querySelectorAll('[data-checklist-toggle]').forEach(function (input) {
    input.addEventListener('change', function () {
      var form = input.closest('form');
      if (form) form.submit();
    });
  });

  /* ---------- Confirm on any form marked for it ---------- */

  document.querySelectorAll('.js-confirm-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm') || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });
})();
