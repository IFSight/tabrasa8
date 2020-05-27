/**
 * @file
 * Provides Slick Browser media switch utilitiy functions.
 *
 * This can be used for both Slick browsers and widgets.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Slick Browser media utility functions.
   *
   * @param {int} i
   *   The index of the current element.
   * @param {HTMLElement} media
   *   The media player HTML element.
   */
  function sbMedia(i, media) {
    var $media = $(media);
    var $sb = $media.closest('.sb--wrapper') || $media.closest('.sb');
    var $zoom = $('.sb__zoom', $sb);
    var $body = $sb.closest('body');
    var $wParent = window.parent.document;
    var $formModal = $media.closest('.form--sb');
    var id = 'sb-target';
    var wpad = Math.round((($(window).height() / $(window).width()) * 100), 2) + '%';

    /**
     * Play the media.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     */
    function play(event) {
      var $btn = $(event.currentTarget);
      var $current = $btn.closest('.media');

      $body.addClass('is-zoom');
      $sb.addClass('is-sb-zoomed');

      // @todo $iframe.on('load', function () {
      // @todo });
      var $clone = $current.clone(true, true);

      if (!$('.media--player', $zoom).length) {
        $clone.appendTo($zoom);
        $clone.find('img').remove();
        $clone.css('padding-bottom', wpad);
        $current.find('iframe').remove();
      }

      if ($formModal.length) {
        $('.ui-dialog:visible', $wParent).addClass('ui-dialog--zoom');
      }
      else {
        Drupal.slickBrowser.jump(id);
      }
    }

    /**
     * Close the media.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     */
    function stop(event) {
      $zoom.empty();
      $body.removeClass('is-zoom');
      $sb.removeClass('is-sb-zoomed');
      $('.is-playing').removeClass('is-playing');

      if ($formModal.length) {
        $('.ui-dialog:visible', $wParent).removeClass('ui-dialog--zoom');
      }
    }

    $media.on('click.sbMediaPlay', '.media__icon--play', play);
    $media.on('click.sbMediaClose', '.media__icon--close', stop);
  }

  /**
   * Attaches Slick Browser media behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.slickBrowserMedia = {
    attach: function (context) {
      $('.media--player', context).once('sbMedia').each(sbMedia);
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $('.media--player', context).removeOnce('sbMedia').off('.sbMediaPlay .sbMediaClose');
      }
    }
  };

})(jQuery, Drupal);
