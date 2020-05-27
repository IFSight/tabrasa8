/**
 * @file
 * Provides Slick Browser view utilitiy functions.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Slick Browser utility functions.
   *
   * @param {int} i
   *   The index of the current element.
   * @param {HTMLElement} switcher
   *   The switcher HTML element.
   */
  function sbViewSwitch(i, switcher) {
    var $switcher = $(switcher);
    var $form = $switcher.closest('.form--sb');
    var $container = $form.length ? $form : $switcher.closest('.view');
    var $head = $('.sb__header');
    // @todo var $slick = $container.find('.slick:first');
    // @todo var slicked = $slick.length && $('.slick__slider', $slick).hasClass('slick-initialized');
    var $firstGrid = $container.find('.sb__grid:first');
    var classes = $firstGrid.attr('class');

    /**
     * Build the fake table header like.
     */
    function buildTableHeader() {
      var $content = $('.view-content', $container);

      // Faking table header for the list view.
      if ($container.find('.grid').length && !$('.view-list--header', $container).length) {
        var $grid = $firstGrid.find('.grid:first .grid__content');
        var $cloned = $grid.clone();

        $cloned.detach().insertBefore($content);
        $cloned.wrapAll('<div class="view-list view-list--header grid" />').once();

        // Extracts the views-label to be the fake table header.
        $cloned.find('.views-field').each(function () {
          var $item = $(this);
          var txt = $item.find('.views-label').text();

          $item.empty().text(txt);
        });

        $cloned.find('.grid__info, .button-group').remove();
      }
    }

    /**
     * Switch the view display.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     */
    function switchView(event) {
      event.preventDefault();

      var $btn = $(event.currentTarget);
      var target = $btn.data('target');
      var $view = $('.view--sb');

      $btn.closest('.button-group').find('.button').removeClass('is-sb-active');
      $btn.addClass('is-sb-active');

      if (target && $view.length) {
        $('.is-info-active').removeClass('is-info-active');

        if (target === 'help') {
          $container.removeClass('is-sb-collapsed');
          $container.toggleClass('view--sb-help');
          $btn.text($container.hasClass('view--sb-help') ? 'x' : '?');
        }
        else {
          $('.button--help', $container).text('?');
          $container.removeClass('view--sb-help');
          $view.removeClass('view--sb-grid view--sb-list view--sb-help');
          $view.find('.sb__grid').attr('class', target === 'list' ? 'sb__grid' : $switcher.data('classes'));
          $view.addClass('view--sb-' + target);

          // Revalidate potential slick clones.
          if (Drupal.blazy && Drupal.blazy.init !== null) {
            Drupal.blazy.init.revalidate(true);
          }

          // Manually refresh positioning of slick as the layout changes.
          // @todoif (slicked) {
          // @todo  $('.slick__slider', $container)[0].slick.refresh();
          // @todo}
        }
      }
    }

    /**
     * Trigger AJAX when reacing the end.
     *
     * @param {HTMLElement} elm
     *   The form or view container HTML element.
     */
    function triggerAjax(elm) {
      $('.slick__arrow', elm).addClass('button-group button-group--icon');
      $('.slick__slider', elm).on('beforeChange', function (event, slick, currentSlide) {
        var totalSlide = slick.$slides.length;
        var curr = currentSlide + 1;
        var $next = $('.pager__items a[rel="next"]', elm);
        var $prev = $('.pager__items a[rel="prev"]', elm);
        // Claro, doh.
        var $nextClaro = $('.pager__items .pager__item--next a', elm);
        var $prevClaro = $('.pager__items .pager__item--previous a', elm);

        if (curr === totalSlide) {
          if ($next.length) {
            $next.click();
          }
          else if ($prev.length) {
            $prev.click();
          }
          if ($nextClaro.length) {
            $nextClaro.click();
          }
          else if ($prevClaro.length) {
            $prevClaro.click();
          }
        }
      });
    }

    // Store original classes for the switcher.
    $switcher.data('classes', classes);

    // Build the fake table header.
    buildTableHeader();

    // If the switcher is embedded inside EB, append it to the form header.
    if ($head.length) {
      $head.find('.sb__viewswitch').remove();

      $switcher.addClass('sb__viewswitch--header').appendTo($head);
    }

    // The switcher can live within, or outside view, when EB kicks in.
    $('.button', switcher).on('click.sbSwitch', switchView);

    // Makes the active button active.
    $('#sb-viewswitch', $container).find('.button--view.is-sb-active').click();
    triggerAjax($container);
  }

  /**
   * Attaches Slick Browser view behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.slickBrowserViewSwitch = {
    attach: function (context) {
      $(context).find('.sb__viewswitch').once('sbViewSwitch').each(sbViewSwitch);
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.sb__viewswitch').removeOnce('sbViewSwitch');
      }
    }
  };

})(jQuery, Drupal);
