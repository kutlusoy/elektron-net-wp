(function() {
  'use strict';

  var root = document.documentElement;

  function store(key, value) {
    try {
      window.localStorage.setItem(key, value);
    } catch (e) {}
  }

  function resolveScheme() {
    var current = root.getAttribute('data-scheme');
    if (current === 'light' || current === 'dark') {
      return current;
    }
    var fallback = root.getAttribute('data-default-scheme');
    if (fallback === 'light') {
      return 'light';
    }
    if (fallback === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
      return 'light';
    }
    return 'dark';
  }

  function initScheme() {
    var toggle = document.querySelector('.scheme-toggle');
    if (!toggle) {
      return;
    }
    var sync = function() {
      toggle.setAttribute('aria-pressed', resolveScheme() === 'light' ? 'true' : 'false');
    };
    toggle.addEventListener('click', function() {
      var next = resolveScheme() === 'light' ? 'dark' : 'light';
      root.setAttribute('data-scheme', next);
      store('elektron-scheme', next);
      sync();
      document.dispatchEvent(new CustomEvent('elektron:palette'));
    });
    sync();
  }

  function initStyleMenu() {
    var menu = document.querySelector('.style-menu');
    if (!menu) {
      return;
    }
    var options = menu.querySelectorAll('.palette-option');
    var fxButton = menu.querySelector('.fx-toggle');
    var order = ['off', 'subtle', 'full'];
    var labels = {};
    var template = '%s';
    try {
      labels = JSON.parse(fxButton.getAttribute('data-labels'));
      template = fxButton.getAttribute('data-label') || template;
    } catch (e) {}

    var sync = function() {
      var current = root.getAttribute('data-palette');
      options.forEach(function(option) {
        option.setAttribute('aria-pressed', option.getAttribute('data-palette') === current ? 'true' : 'false');
      });
      var fx = root.getAttribute('data-fx') || 'subtle';
      fxButton.textContent = template.replace('%s', labels[fx] || fx);
    };

    options.forEach(function(option) {
      option.addEventListener('click', function() {
        var value = option.getAttribute('data-palette');
        root.setAttribute('data-palette', value);
        store('elektron-palette', value);
        sync();
        document.dispatchEvent(new CustomEvent('elektron:palette'));
      });
    });

    fxButton.addEventListener('click', function() {
      var next = order[(order.indexOf(root.getAttribute('data-fx') || 'subtle') + 1) % order.length];
      root.setAttribute('data-fx', next);
      store('elektron-fx', next);
      sync();
    });

    document.addEventListener('click', function(event) {
      if (menu.open && !menu.contains(event.target)) {
        menu.open = false;
      }
    });
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && menu.open) {
        menu.open = false;
        menu.querySelector('summary').focus();
      }
    });
    sync();
  }

  function initNav() {
    var nav = document.getElementById('site-nav');
    var toggle = document.querySelector('.menu-toggle');
    if (!nav || !toggle) {
      return;
    }
    var set = function(open) {
      nav.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    toggle.addEventListener('click', function() {
      set(toggle.getAttribute('aria-expanded') !== 'true');
    });
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && nav.classList.contains('is-open')) {
        set(false);
        toggle.focus();
      }
    });
  }

  function initHeaderState() {
    var header = document.querySelector('.site-header');
    if (!header) {
      return;
    }
    var update = function() {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  initScheme();
  initStyleMenu();
  initNav();
  initHeaderState();
}());

jQuery(function($) {
  var $body = $('body');

  function openLayer($el) {
    $el.addClass('is-open');
    $body.addClass('layer-open');
  }

  function closeLayers() {
    $('.fixed-layout.is-open').removeClass('is-open');
    $body.removeClass('layer-open');
  }

  $body.on('click', 'a.comment-reply', function(e) {
    e.preventDefault();
    var replyId = parseInt($(this).attr('data-id'), 10);
    var replyText = $(this).attr('data-text');
    var replyRating = parseInt($(this).attr('data-rating'), 10);

    if (replyId > 0) {
      $('form[name="comment_form"] input[name="replyId"]').val(replyId);
      $('form[name="comment_form"] .reply-text').text(replyText).show(0);

      if (replyRating === 0) {
        $('form[name="comment_form"] .control-group.rating').hide(0);
        $('form[name="comment_form"] input[name="rating"]').val('');
      }
    }

    $(window).scrollTop($('form[name="comment_form"]').offset().top - 100);
  });

  $body.on('click', 'form[name="comment_form"] .reply-text', function(e) {
    e.preventDefault();
    $('form[name="comment_form"] input[name="replyId"]').val('');
    $('form[name="comment_form"] .reply-text').text('').hide(0);
    $('form[name="comment_form"] .control-group.rating').show(0);
  });

  $body.on('click', '.show-filters-btn', function(e) {
    e.preventDefault();
    openLayer($('body.search #sidebar'));
  });

  $body.on('click', '.show-contact-btn', function(e) {
    e.preventDefault();
    openLayer($('#contact-in'));
  });

  $body.on('click', '.show-menu-btn', function(e) {
    e.preventDefault();
    openLayer($('#sidebar'));
  });

  $body.on('click', '.fixed-close', function(e) {
    e.preventDefault();
    closeLayers();
  });

  $(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLayers();
    }
  });

  $('.flashmessage .ico-close').on('click', function() {
    $(this).parents('.flashmessage').remove();
  });

  $('.see_by > label').on('click', function() {
    $(this).parent().toggleClass('hover');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('.see_by').length) {
      $('.see_by').removeClass('hover');
    }
  });
});

if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.autocomplete !== 'undefined') {
  jQuery.ui.autocomplete.prototype._resizeMenu = function() {
    var ul = this.menu.element;
    ul.outerWidth(this.element.outerWidth());
  };
}
