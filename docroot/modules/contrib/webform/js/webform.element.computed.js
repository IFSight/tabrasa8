/**
 * @file
 * JavaScript behaviors for computed elements.
 */

(function ($, Drupal, debounce) {

  'use strict';

  Drupal.webform = Drupal.webform || {};
  Drupal.webform.computed = Drupal.webform.computed || {};
  Drupal.webform.computed.delay = Drupal.webform.computed.delay || 500;

  /**
   * Initialize computed elements.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformComputed = {
    attach: function (context) {
      $(context).find('.js-webform-computed').once('webform-computed').each(function () {
        // Get computed element and parent form.
        var $element = $(this);
        var $form = $element.closest('form');

        // Get elements that are used by the computed element.
        var elementKeys = $(this).data('webform-element-keys').split(',');
        if (!elementKeys) {
          return;
        }

        // Add event handler to elements that are used by the computed element.
        $.each(elementKeys, function (i, key) {
          $form.find(':input[name^="' + key + '"]')
            .on('keyup change', debounce(triggerUpdate, Drupal.webform.computed.delay));
        });

        // Initialize computed element update which refreshes the displayed
        // value and accounts for any changes to the #default_value for a
        // computed element.
        triggerUpdate();

        function triggerUpdate() {
          // Prevent duplicate computations.
          // @see Drupal.behaviors.formSingleSubmit
          var formValues = $form.find('input[name!=form_build_id]').serialize();
          var previousValues = $element.attr('data-webform-computed-last');
          if (previousValues === formValues) {
            return;
          }
          $element.attr('data-webform-computed-last', formValues);

          // Add loading class to computed wrapper.
          $element.find('.js-webform-computed-wrapper')
            .addClass('webform-computed-loading');

          // Trigger computation.
          $element.find('.js-form-submit').mousedown();
        }
      });
    }
  };

})(jQuery, Drupal, Drupal.debounce);
