(function ($) {
  "use strict";

  document.querySelectorAll("[data-after-bg]").forEach((el) => {
    el.style.setProperty("--after-bg", `url('${el.dataset.afterBg}')`);
  });

  // services 02 hover
  $(".services-two__wrapper .service-card-two").on("mousemove", function () {
    $(".services-two__wrapper .service-card-two").removeClass("active");
    $(this).addClass("active");
  });

  // work process animation
  document.addEventListener("DOMContentLoaded", () => {
    const processItems = document.querySelectorAll(".work-process-two__item");

    const observerOptions = {
      root: null,
      rootMargin: "-10% 0px -20% 0px",
      threshold: 0.2
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("active");
        } else {
          entry.target.classList.remove("active");
        }
      });
    }, observerOptions);

    processItems.forEach((item) => observer.observe(item));
  });

  // webgl images hover animation
  function initWebglHover() {
    if ($(".bw-hover-item").length && typeof hoverEffect === "function") {
      let hoverAnimation__do = function (t, n) {
        try {
          let imgEl = n.eq(0)[0];
          let ratio = (imgEl && imgEl.naturalWidth && imgEl.naturalHeight)
            ? (imgEl.naturalHeight / imgEl.naturalWidth)
            : (imgEl && imgEl.offsetHeight && imgEl.offsetWidth ? imgEl.offsetHeight / imgEl.offsetWidth : 9 / 16);

          if (!ratio || isNaN(ratio) || !isFinite(ratio) || ratio <= 0) {
            ratio = 9 / 16;
          }

          let a = new hoverEffect({
            parent: t.get(0),
            intensity: t.data("intensity") || void 0,
            speedIn: t.data("speedin") || void 0,
            speedOut: t.data("speedout") || void 0,
            easing: t.data("easing") || void 0,
            hover: t.data("hover") || void 0,
            image1: n.eq(0).attr("src"),
            image2: n.eq(0).attr("src"),
            displacementImage: t.data("displacement"),
            imagesRatio: ratio,
            hover: !1
          });
          t.closest(".bw-hover-item")
            .on("mouseenter", function () {
              a.next();
            })
            .on("mouseleave", function () {
              a.previous();
            });
        } catch (e) {
          // Fallback safely: original <img> remains visible
        }
      };
      let hoverAnimation = function () {
        $(".bw-hover-img").each(function () {
          let n = $(this);
          let e = n.find("img");
          let i = e.eq(0);
          if (i.length && i[0].complete) {
            hoverAnimation__do(n, e);
          } else if (i.length) {
            i.on("load", function () {
              hoverAnimation__do(n, e);
            });
          }
        });
      };
      hoverAnimation();
    }
  }

  // growth progress bars
  $(function () {
    const cardEl = document.getElementById("growthCard");
    if (!cardEl) return;

    function barWidth(n) {
      if (n <= 3) return 68;
      if (n <= 5) return 56;
      if (n <= 8) return 44;
      if (n <= 12) return 34;
      return 26;
    }
    function cardHeight(n) {
      if (n <= 3) return 188;
      if (n <= 6) return 155;
      return 130;
    }
    function fontSize(w) {
      if (w >= 54) return "1.2rem";
      if (w >= 40) return "1rem";
      return ".82rem";
    }
    function stagger(n) {
      return n <= 6 ? 0.18 : n <= 12 ? 0.12 : 0.07;
    }

    const $items = $("#barsContainer [data-bar]");
    const n = $items.length;
    const maxH = cardHeight(n);
    const bw = barWidth(n);
    const fs = fontSize(bw);
    const gap = stagger(n);

    let css = `.bars { height: ${maxH}px; }\n.bar { width: ${bw}px; }\n.bar__label { font-size: ${fs}; }\n`;
    $items.each(function (i) {
      const pct = $(this).data("pct");
      const delay = (i * gap).toFixed(2);
      css += `.bar[data-target="${pct}"][data-index="${i}"].animated { height: calc(${maxH}px * ${(pct / 100).toFixed(4)}); }\n`;
      css += `.bar[data-target="${pct}"][data-index="${i}"] { transition-delay: ${delay}s; }\n`;
    });
    $("<style>").text(css).appendTo("head");

    $items.each(function (i) {
      const pct = $(this).data("pct");
      $(this).replaceWith(
        $('<div class="bar-wrap">').append(
          $('<div class="bar">')
            .attr({ "data-target": pct, "data-index": i, "data-pct": pct })
            .append($('<span class="bar__label">').text("0%"))
        )
      );
    });

    function countUp($label, target, delayMs) {
      setTimeout(function () {
        let cur = 0;
        const step = Math.max(1, Math.ceil(target / 60));
        const t = setInterval(function () {
          cur = Math.min(cur + step, target);
          $label.text(cur + "%");
          if (cur >= target) clearInterval(t);
        }, 18);
      }, delayMs);
    }

    function animateBars() {
      $("#barsContainer .bar").each(function (i) {
        const pct = parseInt($(this).data("pct"));
        const delayMs = Math.round(i * gap * 1000);
        const $bar = $(this);
        const $label = $bar.find(".bar__label");
        setTimeout(function () {
          $bar.addClass("animated");
          countUp($label, pct, 850);
        }, delayMs);
      });
    }

    let done = false;

    if ("IntersectionObserver" in window) {
      const io = new IntersectionObserver(
        function (entries) {
          if (entries[0].isIntersecting && !done) {
            done = true;
            animateBars();
            io.disconnect();
          }
        },
        { threshold: 0.3 }
      );
      io.observe(cardEl);
    } else {
      const $cardEl = $(cardEl);
      $(window).on("scroll", function () {
        if (done) return;
        if (
          $(window).scrollTop() + $(window).height() >
          $cardEl.offset().top + 80
        ) {
          done = true;
          animateBars();
        }
      });
    }
  });

  // hover image
  const link = document.querySelectorAll(".hover-item");
  const linkHoverReveal = document.querySelectorAll(".hover-item__box");
  const linkImages = document.querySelectorAll(".hover-item__box__img");
  for (let i = 0; i < link.length; i++) {
    if (!linkHoverReveal[i] || !linkImages[i]) continue;
    link[i].addEventListener("mousemove", (e) => {
      linkHoverReveal[i].style.opacity = 1;
      linkHoverReveal[i].style.transform =
        `translate(-100%, -50% ) rotate(-14deg)`;
      linkImages[i].style.transform = "scale(1, 1)";
      linkHoverReveal[i].style.left = e.clientX + "px";
    });
    link[i].addEventListener("mouseleave", (e) => {
      linkHoverReveal[i].style.opacity = 0;
      linkHoverReveal[i].style.transform =
        `translate(-50%, -50%) rotate(14deg)`;
      linkImages[i].style.transform = "scale(0.8, 0.8)";
    });
  }

  /*-- Checkout Accoradin --*/
  if ($(".checkout-page__payment__title").length) {
    $(".checkout-page__payment__item")
      .find(".checkout-page__payment__content")
      .hide();
    $(".checkout-page__payment__item--active")
      .find(".checkout-page__payment__content")
      .show();
    $(".checkout-page__payment__title").on("click", function (e) {
      e.preventDefault();
      $(this)
        .parents(".checkout-page__payment")
        .find(".checkout-page__payment__item")
        .removeClass("checkout-page__payment__item--active");
      $(this)
        .parents(".checkout-page__payment")
        .find(".checkout-page__payment__content")
        .slideUp();
      $(this).parent().addClass("checkout-page__payment__item--active");
      $(this).parent().find(".checkout-page__payment__content").slideDown();
    });
  }

  let dynamicyearElm = $(".dynamic-year");
  if (dynamicyearElm.length) {
    let currentYear = new Date().getFullYear();
    dynamicyearElm.html(currentYear);
  }

  // Date Picker
  if ($(".misiom-datepicker").length) {
    $(".misiom-datepicker").each(function () {
      $(this).datepicker();
    });
  }

  // Popular Causes Progress Bar
  if ($(".count-bar").length) {
    $(".count-bar").appear(
      function () {
        var el = $(this);
        var percent = el.data("percent");
        $(el).css("width", percent).addClass("counted");
      },
      {
        accY: -50
      }
    );
  }

  //Fact Counter + Text Count
  if ($(".count-box").length) {
    $(".count-box").appear(
      function () {
        var $t = $(this),
          n = $t.find(".count-text").attr("data-stop"),
          r = parseInt($t.find(".count-text").attr("data-speed"), 10);

        if (!$t.hasClass("counted")) {
          $t.addClass("counted");
          $({
            countNum: $t.find(".count-text").text()
          }).animate(
            {
              countNum: n
            },
            {
              duration: r,
              easing: "linear",
              step: function () {
                $t.find(".count-text").text(Math.floor(this.countNum));
              },
              complete: function () {
                $t.find(".count-text").text(this.countNum);
              }
            }
          );
        }
      },
      {
        accY: 0
      }
    );
  }

  // custom coursor
  if ($(".custom-cursor").length && window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    var cursor = document.querySelector(".custom-cursor__cursor");
    var cursorinner = document.querySelector(".custom-cursor__cursor-two");
    var a = document.querySelectorAll("a");

    document.addEventListener("mousemove", function (e) {
      var x = e.clientX;
      var y = e.clientY;
      cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`;
    });

    document.addEventListener("mousemove", function (e) {
      var x = e.clientX;
      var y = e.clientY;
      cursorinner.style.left = x + "px";
      cursorinner.style.top = y + "px";
    });

    document.addEventListener("mousedown", function () {
      cursor.classList.add("click");
      cursorinner.classList.add("custom-cursor__innerhover");
    });

    document.addEventListener("mouseup", function () {
      cursor.classList.remove("click");
      cursorinner.classList.remove("custom-cursor__innerhover");
    });

    a.forEach((item) => {
      item.addEventListener("mouseover", () => {
        cursor.classList.add("custom-cursor__hover");
      });
      item.addEventListener("mouseleave", () => {
        cursor.classList.remove("custom-cursor__hover");
      });
    });
  }

  if ($(".contact-form-validated").length) {
    $(".contact-form-validated").validate({
      // initialize the plugin
      rules: {
        name: {
          required: true
        },
        email: {
          required: true,
          email: true
        },
        message: {
          required: true
        },
        subject: {
          required: true
        }
      },
      submitHandler: function (form) {
        // sending value with ajax request
        $.post(
          $(form).attr("action"),
          $(form).serialize(),
          function (response) {
            $(form).parent().find(".result").append(response);
            $(form).find('input[type="text"]').val("");
            $(form).find('input[type="email"]').val("");
            $(form).find("textarea").val("");
          }
        );
        return false;
      }
    });
  }

  // mailchimp form
  if ($(".mc-form").length) {
    $(".mc-form").each(function () {
      var Self = $(this);
      var mcURL = Self.data("url");
      var mcResp = Self.parent().find(".mc-form__response");

      Self.ajaxChimp({
        url: mcURL,
        callback: function (resp) {
          // appending response
          mcResp.append(function () {
            return '<p class="mc-message">' + resp.msg + "</p>";
          });
          // making things based on response
          if (resp.result === "success") {
            // Do stuff
            Self.removeClass("errored").addClass("successed");
            mcResp.removeClass("errored").addClass("successed");
            Self.find("input").val("");

            mcResp.find("p").fadeOut(10000);
          }
          if (resp.result === "error") {
            Self.removeClass("successed").addClass("errored");
            mcResp.removeClass("successed").addClass("errored");
            Self.find("input").val("");

            mcResp.find("p").fadeOut(10000);
          }
        }
      });
    });
  }

  // video play & pause
  $(function () {
    const playIcon = `<i class="fas fa-play"></i>`;
    const pauseIcon = `<i class="fas fa-pause"></i>`;

    $(document).on("click", ".play-btn", function () {
      const $btn = $(this);
      const video = $btn.closest(".video-section").find(".bg-video")[0];

      if (!video) return;
      if (video.paused) {
        video.play();
        $btn.find(".play-btn-icon").html(pauseIcon);
      } else {
        video.pause();
        $btn.find(".play-btn-icon").html(playIcon);
      }
    });
  });

  function dynamicCurrentMenuClass(selector) {
    let FileName = window.location.href.split("/").reverse()[0];

    selector.find("li").each(function () {
      let anchor = $(this).find("a");
      if ($(anchor).attr("href") == FileName) {
        $(this).addClass("current");
      }
    });
    // if any li has .current elmnt add class
    selector.children("li").each(function () {
      if ($(this).find(".current").length) {
        $(this).addClass("current");
      }
    });
    // if no file name return
    if ("" == FileName) {
      selector.find("li").eq(0).addClass("current");
    }
  }

  if ($(".main-menu__list").length) {
    // dynamic current class
    let mainNavUL = $(".main-menu__list");
    dynamicCurrentMenuClass(mainNavUL);
  }

  if ($(".service-sidebar__nav").length) {
    // dynamic current class
    let mainNavUL = $(".service-sidebar__nav");
    dynamicCurrentMenuClass(mainNavUL);
  }

  if ($(".main-menu").length && $(".mobile-nav__container").length) {
    let navContent = document.querySelector(".main-menu").innerHTML;
    let mobileNavContainer = document.querySelector(".mobile-nav__container");
    mobileNavContainer.innerHTML = navContent;
  }

  if ($(".sticky-header").length) {
    $(".sticky-header")
      .clone()
      .insertAfter(".sticky-header")
      .addClass("sticky-header--cloned");
  }

  $(".mobile-nav__container .dropdown > a").each(function (index) {
    const anchor = $(this);
    const submenu = anchor.siblings("ul").attr("id", "mobile-submenu-" + index);
    $("<button>", { type: "button", class: "mobile-dropdown-toggle",
      "aria-label": "Toggle " + anchor.text().trim(), "aria-expanded": "false",
      "aria-controls": submenu.attr("id"), html: "<i class='fa fa-angle-down' aria-hidden='true'></i>"
    }).insertAfter(anchor).on("click", function () {
      const expanded = this.getAttribute("aria-expanded") !== "true";
      this.setAttribute("aria-expanded", String(expanded));
      submenu.stop(true, true).slideToggle(150);
    });
  });

  let panelOpener = null;
  function togglePanel(selector, activeClass, trigger, forceClose) {
    const panel = $(selector);
    const open = !forceClose && !panel.hasClass(activeClass);
    panel.toggleClass(activeClass, open).attr("aria-hidden", String(!open));
    $(trigger).filter("button").attr("aria-expanded", String(open));
    $("body").toggleClass("locked", open);
    if (open) {
      panelOpener = document.activeElement;
      panel.find("button, a[href], input").filter(":visible").first().trigger("focus");
    } else if (panelOpener) {
      panelOpener.focus();
    }
  }
  $(".mobile-nav__toggler").on("click", function (event) {
    event.preventDefault();
    togglePanel(".mobile-nav__wrapper", "expanded", ".mobile-nav__btn");
  });
  $(".sidebar-btn__toggler").on("click", function (event) {
    event.preventDefault();
    togglePanel(".sidebar-two", "active", ".main-header .sidebar-btn__toggler");
  });
  $(document).on("keydown", function (event) {
    const mobile = $(".mobile-nav__wrapper").hasClass("expanded");
    const sidebar = $(".sidebar-two").hasClass("active");
    if (!mobile && !sidebar) return;
    if (event.key === "Escape") {
      togglePanel(mobile ? ".mobile-nav__wrapper" : ".sidebar-two", mobile ? "expanded" : "active",
        mobile ? ".mobile-nav__btn" : ".main-header .sidebar-btn__toggler", true);
    }
    if (event.key === "Tab") {
      const items = $(mobile ? ".mobile-nav__content" : ".sidebar-two__content")
        .find("a[href], button, input, select, textarea, [tabindex='0']").filter(":visible").filter(":not(:disabled)");
      const first = items[0], last = items[items.length - 1];
      if (event.shiftKey && (document.activeElement === first || !items.is(document.activeElement))) {
        event.preventDefault(); last?.focus();
      } else if (!event.shiftKey && (document.activeElement === last || !items.is(document.activeElement))) {
        event.preventDefault(); first?.focus();
      }
    }
  });

  $(".misiom-accordion .accordion-title").attr({ role: "button", tabindex: "0" })
    .on("keydown", function (event) {
      if (event.key === "Enter" || event.key === " ") { event.preventDefault(); $(this).trigger("click"); }
    });
  $("form[method='POST']").on("submit", function (event) {
    if (this.dataset.submitting === "true") { event.preventDefault(); return; }
    if (!this.checkValidity() || event.isDefaultPrevented()) return;
    this.dataset.submitting = "true";
    $(this).attr("aria-busy", "true").find("button[type='submit']").prop("disabled", true);
  });
  $(window).on("pageshow", function () {
    $("form[data-submitting]").removeAttr("data-submitting aria-busy").find("button[type='submit']").prop("disabled", false);
  });

  if ($(".odometer").length) {
    $(".odometer").appear(function (e) {
      var odo = $(".odometer");
      odo.each(function () {
        var countNumber = $(this).attr("data-count");
        $(this).html(countNumber);
      });
    });
  }

  //accordion
  if ($(".misiom-accordion").length) {
    var accordionGrp = $(".misiom-accordion");
    accordionGrp.each(function () {
      var accordionName = $(this).data("grp-name");
      var Self = $(this);
      var accordion = Self.find(".accordion");
      Self.addClass(accordionName);
      Self.find(".accordion .accordion-content").hide();
      Self.find(".accordion.active").find(".accordion-content").show();
      accordion.each(function () {
        $(this)
          .find(".accordion-title")
          .on("click", function () {
            if ($(this).parent().hasClass("active") === false) {
              $(".misiom-accordion." + accordionName)
                .find(".accordion")
                .removeClass("active");
              $(".misiom-accordion." + accordionName)
                .find(".accordion")
                .find(".accordion-content")
                .slideUp();
              $(this).parent().addClass("active");
              $(this).parent().find(".accordion-content").slideDown();
            }
          });
      });
    });
  }

  $(".add").on("click", function () {
    if ($(this).prev().val() < 999) {
      $(this)
        .prev()
        .val(+$(this).prev().val() + 1);
    }
  });

  $(".sub").on("click", function () {
    if ($(this).next().val() > 0) {
      if ($(this).next().val() > 0)
        $(this)
          .next()
          .val(+$(this).next().val() - 1);
    }
  });

  if ($(".tabs-box").length) {
    $(".tabs-box .tab-buttons .tab-btn").on("click", function (e) {
      e.preventDefault();
      var target = $($(this).attr("data-tab"));

      if ($(target).is(":visible")) {
        return false;
      } else {
        target
          .parents(".tabs-box")
          .find(".tab-buttons")
          .find(".tab-btn")
          .removeClass("active-btn");
        $(this).addClass("active-btn");
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .fadeOut(0);
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .removeClass("active-tab");
        $(target).fadeIn(300);
        $(target).addClass("active-tab");
      }
    });
  }

  function thmOwlInit() {
    // owl slider
    let misiomowlCarousel = $(".misiom-owl__carousel");
    if (misiomowlCarousel.length) {
      misiomowlCarousel.each(function () {
        let elm = $(this);
        let options = elm.data("owl-options");
        let thmOwlCarousel = elm.owlCarousel(
          "object" === typeof options ? options : JSON.parse(options)
        );
        elm.find("button").each(function () {
          $(this).attr("aria-label", "carousel button");
        });
      });
    }
    let misiomowlCarouselNav = $(".misiom-owl__carousel--custom-nav");
    if (misiomowlCarouselNav.length) {
      misiomowlCarouselNav.each(function () {
        let elm = $(this);
        let owlNavPrev = elm.data("owl-nav-prev");
        let owlNavNext = elm.data("owl-nav-next");
        $(owlNavPrev).on("click", function (e) {
          elm.trigger("prev.owl.carousel");
          e.preventDefault();
        });

        $(owlNavNext).on("click", function (e) {
          elm.trigger("next.owl.carousel");
          e.preventDefault();
        });
      });
    }
  }

  /*-- Handle Scrollbar --*/
  function handleScrollbar() {
    const bodyHeight = $("body").height();
    const scrollPos = $(window).innerHeight() + $(window).scrollTop();
    let percentage = (scrollPos / bodyHeight) * 100;
    if (percentage > 100) {
      percentage = 100;
    }
    $(".scroll-to-top .scroll-to-top__inner").css("width", percentage + "%");
  }

  /*-- One Page Menu --*/
  function SmoothMenuScroll() {
    var anchor = $(".scrollToLink");
    if (anchor.length) {
      anchor.children("a").bind("click", function (event) {
        if ($(window).scrollTop() > 10) {
          var headerH = "0";
        } else {
          var headerH = "0";
        }
        var target = $(this);
        $("html, body")
          .stop()
          .animate(
            {
              scrollTop: $(target.attr("href")).offset().top - headerH + "px"
            },
            900,
            "easeInOutExpo"
          );
        anchor.removeClass("current");
        anchor.removeClass("current-menu-ancestor");
        anchor.removeClass("current_page_item");
        anchor.removeClass("current-menu-parent");
        target.parent().addClass("current");
        event.preventDefault();
      });
    }
  }
  SmoothMenuScroll();

  function OnePageMenuScroll() {
    var windscroll = $(window).scrollTop();
    if (windscroll >= 117) {
      var menuAnchor = $(".one-page-scroll-menu .scrollToLink").children("a");
      menuAnchor.each(function () {
        var sections = $(this).attr("href");
        $(sections).each(function () {
          if ($(this).offset().top <= windscroll + 100) {
            var Sectionid = $(sections).attr("id");
            $(".one-page-scroll-menu").find("li").removeClass("current");
            $(".one-page-scroll-menu")
              .find("li")
              .removeClass("current-menu-ancestor");
            $(".one-page-scroll-menu")
              .find("li")
              .removeClass("current_page_item");
            $(".one-page-scroll-menu")
              .find("li")
              .removeClass("current-menu-parent");
            $(".one-page-scroll-menu")
              .find("a[href*=\\#" + Sectionid + "]")
              .parent()
              .addClass("current");
          }
        });
      });
    } else {
      $(".one-page-scroll-menu li.current").removeClass("current");
      $(".one-page-scroll-menu li:first").addClass("current");
    }
  }

  // window scroll event
  function stickyMenuUpScroll($targetMenu, $toggleClass) {
    var lastScrollTop = 0;
    window.addEventListener(
      "scroll",
      function () {
        var st = window.pageYOffset || document.documentElement.scrollTop;
        if (st > 500) {
          if (st > lastScrollTop) {
            // downscroll code
            $targetMenu.removeClass($toggleClass);
          } else {
            // upscroll code
            $targetMenu.addClass($toggleClass);
          }
        } else {
          $targetMenu.removeClass($toggleClass);
        }
        lastScrollTop = st;
      },
      false
    );
  }
  stickyMenuUpScroll($(".sticky-header--normal"), "active");

  //Strech Column
  function misiom_stretch() {
    var i = $(window).width();
    $(".row .misiom-stretch-element-inside-column").each(function () {
      var $this = $(this),
        row = $this.closest(".row"),
        cols = $this.closest('[class^="col-"]'),
        colsheight = $this.closest('[class^="col-"]').height(),
        rect = this.getBoundingClientRect(),
        l = row[0].getBoundingClientRect(),
        s = cols[0].getBoundingClientRect(),
        r = rect.left,
        d = i - rect.right,
        c = l.left + (parseFloat(row.css("padding-left")) || 0),
        u = i - l.right + (parseFloat(row.css("padding-right")) || 0),
        p = s.left,
        f = i - s.right,
        styles = {
          "margin-left": 0,
          "margin-right": 0
        };
      if (Math.round(c) === Math.round(p)) {
        var h = parseFloat($this.css("margin-left") || 0);
        styles["margin-left"] = h - r;
      }
      if (Math.round(u) === Math.round(f)) {
        var w = parseFloat($this.css("margin-right") || 0);
        styles["margin-right"] = w - d;
      }
      $this.css(styles);
    });
  }
  misiom_stretch();

  /*-- Price Range --*/
  function priceFilter() {
    if ($(".price-ranger").length) {
      $(".price-ranger #slider-range").slider({
        range: true,
        min: 50,
        max: 1000,
        values: [11, 500],
        slide: function (event, ui) {
          $(".price-ranger .ranger-min-max-block .min").val("$" + ui.values[0]);
          $(".price-ranger .ranger-min-max-block .max").val("$" + ui.values[1]);
        }
      });
      $(".price-ranger .ranger-min-max-block .min").val(
        "$" + $(".price-ranger #slider-range").slider("values", 0)
      );
      $(".price-ranger .ranger-min-max-block .max").val(
        "$" + $(".price-ranger #slider-range").slider("values", 1)
      );
    }
  }

  // window load event
  $(window).on("load", function () {
    if ($(".preloader").length) {
      $(".preloader").fadeOut();
    }
    thmOwlInit();
    priceFilter();
    initWebglHover();
    AOS.init();
  });

  $(window).on("scroll", function () {
    OnePageMenuScroll();
    handleScrollbar();
    if ($(".sticky-header--one-page").length) {
      var headerScrollPos = 130;
      var stricky = $(".sticky-header--one-page");
      if ($(window).scrollTop() > headerScrollPos) {
        stricky.addClass("active");
      } else if ($(this).scrollTop() <= headerScrollPos) {
        stricky.removeClass("active");
      }
    }

    var scrollToTopBtn = ".scroll-to-top";
    if (scrollToTopBtn.length) {
      if ($(window).scrollTop() > 500) {
        $(scrollToTopBtn).addClass("show");
      } else {
        $(scrollToTopBtn).removeClass("show");
      }
    }
  });

  $(window).on("resize", function () {
    misiom_stretch();
  });
})(jQuery);
