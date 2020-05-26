/**
 * @file
 * Provides Slick Browser utilitiy functions.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Adds relevant context when slick browser is active.
   *
   * @param {jQuery.Event} event
   *   The event triggered.
   *
   * @todo: Remove this when SB can stay at parent window.
   */
  function sbModal(event) {
    var $content = $(event.target);
    var id = $content.attr('id');
    var $iframe = $content.find('iframe[name*="slick_browser"]');

    if (id.indexOf('slick-browser') !== -1) {
      $('html')[event.type === 'dialogopen' ? 'addClass' : 'removeClass']('sb-html-dialog');
      $('.ui-dialog')[event.type === 'dialogopen' ? 'addClass' : 'removeClass']('ui-dialog--sb');

      // Remove padding for spacious window with tabs, navs and video previews.
      $content.addClass('media--loading').css('padding', 0);

      window.setTimeout(function () {
        if ($iframe.length) {
          $iframe.on('load', function () {
            $content.removeClass('media--loading');
          });
        }
      }, 600);
    }

    // Anything else, after AJAX-related events such as Edit/ Remove buttons.
    if (event.type === 'dialogclose') {
      $('.media--loading').removeClass('media--loading');
    }
  }

  /**
   * Attaches Slick Browser modal behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.slickBrowserModal = {
    attach: function (context) {
      $(document)
        .on('dialogopen', '.ui-dialog', sbModal)
        .on('dialogclose', '.ui-dialog', sbModal);
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $('html').removeClass('sb-html-dialog');
      }
    }
  };

})(jQuery, Drupal);
