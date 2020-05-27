/**
 * @file
 * Provides Slick Browser view exposed form utilitiy functions.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Slick Browser utility functions.
   *
   * @param {int} i
   *   The index of the current element.
   * @param {HTMLElement} el
   *   The form-radio HTML element.
   */
  function sbAutoSubmit(i, el) {
    var $el = $(el);
    var $view = $el.closest('.view--sb');
    var val = $el.find('.form-radio:checked').val().replace('_', '-');

    $view.addClass('view--sb-filter-' + val);
    if ($view.find('.view-empty').length) {
      $view.addClass('view--sb-filter-empty');
    }

    $el.find('.form-radio').each(function () {
      var $radio = $(this);
      val = $radio.val().replace('_', '-');

      $radio.on('change', function () {
        $view.removeClass('view--sb-filter-' + val);
        $view.addClass('view--sb-filter-' + val);

        $('.is-filter-checked', $el).removeClass('is-filter-checked');
        $radio.parent().addClass('is-filter-checked');

        $el.find('.form-submit').trigger('click');
      });
    });
  }

  /**
   * Autu select bundle if it is a Media browser to save another click.
   *
   * @param {int} i
   *   The index of the current element.
   * @param {HTMLElement} widget
   *   The .sb--autoselect HTML element.
   */
  function sbAutoSelect(i, widget) {
    var $widget = $(widget);
    var $iframe = $('iframe[name*="slick_browser"]', widget);
    var $doc;
    var entityTypeId = $widget.data('sbEntityTypeId');
    var bundle = $widget.data('sbBundle');
    var $autoSubmit;
    var $radio;

    var autoSelect = function () {
      $doc = $iframe.contents();
      $autoSubmit = $('.sb__autosubmit', $doc);
      $radio = $('.form-radio[value="' + bundle + '"]', $autoSubmit);

      // We are outside node form, at Media browser containing media bundle.
      if ($radio.length) {
        $radio.trigger('click');
      }
    };

    if (entityTypeId === 'media') {
      $iframe.on('load', autoSelect);
    }
  }

  /**
   * Attaches Slick Browser view exposed form behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.slickBrowserAutoSubmit = {
    attach: function (context) {
      $('.sb__autosubmit', context).once('sbAutoSubmit').each(sbAutoSubmit);
      // The [data-sb-target-type="media"] is from node, else from media.
      // [data-sb-target-type="media"], [data-sb-entity-type-id="media"]
      $('.sb--autoselect', context).once('sbAutoSelect').each(sbAutoSelect);
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $(context).find('.sb__autosubmit').removeOnce('sbAutoSubmit');
      }
    }
  };

})(jQuery, Drupal);
