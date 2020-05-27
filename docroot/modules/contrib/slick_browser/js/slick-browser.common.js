/**
 * @file
 * Provides Slick Browser utilitiy functions.
 */

(function ($, Drupal) {

  'use strict';

  var _mouseTimer;

  /**
   * Slick Browser utility functions.
   *
   * @namespace
   */
  Drupal.slickBrowser = Drupal.slickBrowser || {

    /**
     * Provides common Slick Browser utilities.
     *
     * @name sb
     *
     * @param {int} i
     *   The index of the current element.
     * @param {HTMLElement} elm
     *   Any slick browser HTML element.
     */
    sb: function (i, elm) {
      var $elm = $(elm);
      var $slick = $('.slick__slider', elm);
      var cardinality = parseInt($elm.data('sbCardinality'));

      $('.slick__arrow button', elm).addClass('button');
      $('.slick__arrow', elm).addClass('button-group button-group--icon');

      $elm.on('click', '.button--wrap__mask', function () {
        $(this).parent().addClass('is-open');
        return false;
      });

      $('.button--remove', elm).on('click mousedown', function () {
        $(this).closest('.grid, .slide, .item-container').addClass('is-deleted');
      });

      // , .button-wrap--confirm input
      // $('.button--wrap__confirm', elm).on('mouseleave touchend', function () {
      $elm.on('click', function () {
        // Fix for tests not recognizing hover.
        window.clearTimeout(_mouseTimer);
        _mouseTimer = window.setTimeout(function () {
          $('.is-open', elm).removeClass('is-open');
        }, 1000);
      });

      var updateCount = function () {
        if ($elm.hasClass('sb--launcher')) {
          var count = $elm.find('input[data-entity-id]').length;
          $elm.attr('data-sb-count', count);

          if (cardinality > 0 && cardinality <= count) {
            $elm.addClass('is-sb-ajax');
          }
        }
      };

      updateCount();

      // @todo support slick for quick deletion.
      $('.button--wrap__confirm', elm).on('click', function () {
        var $btn = $(this);
        var eid = $btn.next('input').data('entityId');
        var $static = $btn.closest('.sb--wrapper');

        if (eid && $static.length && !$elm.hasClass('is-sb-ajax')) {
          var $storage = $static.find('> .details-wrapper > input:first');
          if ($storage.length) {
            var value = $storage.val();
            value = value.replace(eid, '').trim();
            $storage.val(value);

            if ($slick.length) {
              var index = $btn.closest('.slide').data('slickIndex');
              $slick.slick('slickRemove', index);
              $static.find('.sb__sortitem[data-row-id="' + index + '"]').remove();
            }

            $btn.closest('.grid, .slide').remove();
          }
        }

        updateCount();
      });
    },

    /**
     * Fixes for hidden slick within details as otherwise broken.
     *
     * @name sbDetails
     *
     * @param {int} i
     *   The index of the current element.
     * @param {HTMLElement} elm
     *   Any details HTML element.
     */
    sbDetails: function (i, elm) {
      var $details = $(elm);
      if ($details.hasClass('sb--safe')) {
        return;
      }

      if ($('.sb__display', elm).length) {
        $details.find('.details-wrapper').addClass('visually-hidden');
      }

      $('summary', elm).on('click', function () {
        if ($details.attr('open')) {
          $details.find('.details-wrapper').removeClass('visually-hidden');
          $details.addClass('sb--safe');
          return false;
        }
      });
    },

    /**
     * Fixes for empty preview with rich media.
     *
     * @name sbMediaRendered
     *
     * @param {int} i
     *   The index of the current element.
     * @param {HTMLElement} elm
     *   Any .media--rendered HTML element.
     */
    sbMediaRendered: function (i, elm) {
      var $el = $(elm);
      if ($el.data('thumb') && !$el.hasClass('b-bg')) {
        $el.css('backgroundImage', 'url(' + $el.data('thumb') + ')');
      }
    },

    /**
     * Reacts on item container button actions.
     *
     * @name itemContainer
     *
     * @param {int} i
     *   The index of the current element.
     * @param {HTMLElement} elm
     *   The item container HTML element.
     */
    itemContainer: function (i, elm) {
      $('.button', elm).on('mousedown.sbAction', function () {
        $(this).closest('.item-container').addClass('media--loading');
      });
    },

    /**
     * Jump to the top.
     *
     * @name jump
     *
     * @param {HTMLElement} id
     *   The slick widget HTML element ID.
     */
    jump: function (id) {

      /* @todo
      if ($('#' + id).length) {
        $('html, body').stop().animate({
          scrollTop: $('#' + id).offset().top - 140
        }, 800);
      }
      */
    },

    /**
     * Add loading indicator in replacement for the stone-aged thobber.
     *
     * @param {jQuery.Event} event
     *   The event triggered by an AJAX `mousedown` event.
     */
    loading: function (event) {
      if (!$(event.currentTarget).hasClass('is-active')) {
        $(event.currentTarget).closest('.js-form-managed-file, .form--sb').addClass('media--loading');
      }
    },

    /**
     * Removed loading indicator.
     *
     * @param {bool} all
     *   If true, remove all loading classes.
     */
    loaded: function (all) {
      $('.js-form-managed-file').removeClass('media--loading');
      if (all) {
        $('.media--loading').removeClass('media--loading');
      }
    }
  };

  /**
   * Attaches Slick Browser common behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.slickBrowser = {
    attach: function (context) {
      var me = Drupal.slickBrowser;

      $('.sb', context).once('sb').each(me.sb);
      $('.sb .item-container', context).once('sbItem').each(me.itemContainer);
      $('.sb--wrapper-hidden', context).once('sbDetails').each(me.sbDetails);
      $('.media--rendered', context).once('sbMediaRendered').each(me.sbMediaRendered);
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $('.sb .item-container', context).find('.button').off('.sbAction');
      }
    }
  };

})(jQuery, Drupal);
