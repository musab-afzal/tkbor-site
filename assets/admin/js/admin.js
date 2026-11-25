(function () {
  'use strict';

  // Sidebar toggle (mobile)
  var toggleBtn = document.querySelector('[data-sidebar-toggle]');
  var sidebar = document.querySelector('.admin-sidebar');
  var overlay = document.querySelector('.admin-sidebar__overlay');

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('is-open');
    if (overlay) overlay.classList.remove('is-visible');
  }

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('is-open');
      if (overlay) overlay.classList.toggle('is-visible');
    });
  }

  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Password show/hide toggle
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-toggle-password'));
      if (!input) return;
      var showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      btn.innerHTML = showing
        ? '<i class="fas fa-eye"></i>'
        : '<i class="fas fa-eye-slash"></i>';
    });
  });

  // Delete confirmation
  document.querySelectorAll('.js-delete-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var message = form.getAttribute('data-confirm') || 'Are you sure you want to delete this record?';
      if (!window.confirm(message)) {
        e.preventDefault();
      }
    });
  });

  // Login form loading state
  var authForm = document.querySelector('.js-auth-form');
  if (authForm) {
    authForm.addEventListener('submit', function () {
      authForm.classList.add('is-loading');
      var btn = authForm.querySelector('.auth-submit, .login-submit');
      if (btn) btn.disabled = true;
    });
  }

  // Auto-dismiss flash toasts
  document.querySelectorAll('.admin-alert[data-autodismiss]').forEach(function (alertEl) {
    setTimeout(function () {
      alertEl.style.transition = 'opacity .3s ease';
      alertEl.style.opacity = '0';
      setTimeout(function () { alertEl.remove(); }, 300);
    }, 4500);
  });

  document.querySelectorAll('.admin-alert-close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var alertEl = btn.closest('.admin-alert');
      if (alertEl) alertEl.remove();
    });
  });
})();
