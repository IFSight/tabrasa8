/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function (Drupal, $) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.if_admin_utils = {
    attach: function (context, settings) {
      var $bgcheckbox = $('.field--name-field-background-image').find('.form-checkbox');

      $bgcheckbox.each(function(){
        if ($(this).is(':checked')) {
          $(this).closest('.field--name-field-background-image').next('.field--name-field-overlay-color').addClass('visible');
        }
      })

      $bgcheckbox.change(function(){
        if ($(this).is(':checked')) {
            $(this).closest('.field--name-field-background-image').next('.field--name-field-overlay-color').addClass('visible');
        } else {
            $(this).closest('.field--name-field-background-image').next('.field--name-field-overlay-color').removeClass('visible');
        }
      });
    }
  };

  // We pass the parameters of this anonymous function are the global variables
  // that this script depend on. For example, if the above script requires
  // jQuery, you should change (Drupal) to (Drupal, jQuery) in the line below
  // and, in this file's first line of JS, change function (Drupal) to
  // (Drupal, $)
})(Drupal, jQuery);
