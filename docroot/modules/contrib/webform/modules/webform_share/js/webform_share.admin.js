/**
 * @file
 * JavaScript behaviors for webform share admin.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Webform share admin copy.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformShareAdminCopy = {
    attach: function (context) {
      $(context).find('.js-webform-share-admin-copy').once('webform-share-admin-copy').each(function () {
        var $container = $(this);
        var $textarea = $container.find('textarea');
        var $button = $container.find(':submit, :button');
        var $message = $container.find('.webform-share-admin-copy-message');
        // Copy code from textarea to the clipboard.
        // @see https://stackoverflow.com/questions/37658524/copying-text-of-textarea-in-clipboard-when-button-is-clicked
        $button.click(function () {
          $textarea.select();
          document.execCommand('copy');
          $message.show().delay(1500).fadeOut('slow');
          $button.focus();
          Drupal.announce(Drupal.t('Code copied to clipboard…'));
          return false;
        });
      });
    }
  };

})(jQuery, Drupal);
