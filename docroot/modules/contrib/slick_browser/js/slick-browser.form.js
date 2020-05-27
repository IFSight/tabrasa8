/**
 * @file
 * Provides Slick Browser utilitiy functions.
 */

(function ($, Drupal) {

  'use strict';

  var _messageTimer;

  Drupal.slickBrowser = Drupal.slickBrowser || {};

  /**
   * Slick Browser utility functions.
   *
   * @namespace
   */
  Drupal.slickBrowser.form = Drupal.slickBrowser.form || {

    $form: null,

    /**
     * Checks if selection is empty.
     *
     * @return {bool}
     *   True if a selection is not available, else false.
     */
    isEmpty: function () {
      return !$('#edit-selected', this.$form).children().length;
    },

    /**
     * Do something if selection is empty.
     */
    doEmpty: function () {
      // @todo $('.button-wrap--show-selection', $footer)[$form.hasClass('form--tabs-v') ? 'show' : 'hide']();
      this.$form.addClass('is-sb-empty is-sb-collapsed');
    },

    /**
     * Remove empty marker whenever an item is selected, or uploaded.
     */
    noLongerEmpty: function () {
      this.$form.removeClass('is-sb-empty');
    },

    /**
     * Remove empty marker whenever an item is selected, or uploaded.
     */
    onUpload: function () {
      this.noLongerEmpty();
    },

    /**
     * Toggle selection counter.
     */
    toggleCounter: function () {
      var me = this;
      var cardinality = me.$form.data('cardinality') || -1;
      var $editSelected = $('#edit-selected', me.$form);
      var $counter = $('#edit-counter', me.$form);

      // Only multistep display has selection, so do nothing.
      if ($editSelected.length) {
        var _selectedCount = $editSelected.children().length;
        var total = cardinality === -1 ? 'unlimited' : cardinality;
        var text = Drupal.formatPlural(_selectedCount, '1', '@count');

        text += ' ' + Drupal.formatPlural(total, 'of 1 item selected', 'of @count items selected');

        if (me.$form.hasClass('form--overlimit')) {
          text += ' ' + Drupal.t('(Remove one to select another)');
          $counter.text(_selectedCount > 0 ? text : '');
        }
        else {
          $counter.text(_selectedCount > 0 ? text : '');
        }

        if (me.isEmpty()) {
          me.doEmpty();
        }
      }
    },

    /**
     * Marks selected item enabled or disabled.
     *
     * @param {string} value
     *   The checkbox value.
     * @param {boolean} enabled
     *   Whether to enable or disable.
     */
    toggleSelected: function (value, enabled) {
      var $input = $('input[name="entity_browser_select[' + value + ']"]');
      var txt = enabled ? '' : Drupal.t('Was selected');
      var $grid;

      if ($input.length) {
        $grid = $input.closest('.grid');
        if (enabled) {
          $input.prop('checked', false).removeAttr('disabled');
          $grid.removeClass('is-marked is-checked was-checked');
        }
        else {
          $input.prop('checked', true).attr('disabled', 'disabled');
          $grid.addClass('is-marked was-checked');
        }

        if ($grid.find('img').length) {
          $grid.find('img').attr('title', txt);
        }
        else {
          $grid.attr('title', txt);
        }
      }
    }

  };

  /**
   * Slick Browser utility functions.
   *
   * @param {int} j
   *   The index of the current element.
   * @param {HTMLElement} form
   *   The Entity Browser form HTML element.
   */
  function sbForm(j, form) {
    var me = Drupal.slickBrowser.form;
    var $form = $(form);
    var $body = $form.closest('body');
    var $wParent = $(window.parent.document);
    var $dialog = $('.ui-dialog:visible', $wParent);
    // @todo var $footer = $('#edit-footer', form);
    var $checkBox = $('input[name*="entity_browser_select"]', form);
    var $btnUse = $('.button[name="use_selected"]', form);
    var txtUse = $btnUse.length ? $btnUse.val() : Drupal.t('Add to Page');
    var clonedUse = '#edit-use-selected-clone';
    var $editSelected = $('#edit-selected', form).removeClass('hidden');
    var cardinality = $form.data('cardinality') || -1;

    me.$form = $form;

    if ($('.sb__radios', form).length) {
      $form.addClass('form--media-bundle-selection');
    }

    /**
     * Selects item within Entity Browser iframes.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     *
     * @return {bool}|{mixed}
     *   Return false if no context available.
     */
    function onAddItem(event) {
      event.preventDefault();

      var grid = event.currentTarget;

      checkItem(grid);

      // Only multistep display has selection, so do nothing.
      if (!$editSelected.length) {
        $form.addClass('is-sb-collapsed');
        return false;
      }
      else {
        $editSelected.trigger('change.sbCounter');
      }

      // Show the selection button.
      $('.button-wrap--show-selection', form).show();

      // Refresh selection sortable.
      // @todo figure out replacement for deprecated $editSelected.sortable('refresh');
      $form.removeClass('is-sb-collapsed');
    }

    /**
     * Clones item into selection display.
     *
     * @param {HTMLElement} grid
     *   The grid HTML element.
     */
    function cloneItem(grid) {
      var $grid = $(grid);
      var $input = $('input[name^="entity_browser_select"]', grid);
      var entity = $input.val();
      var split = entity.split(':');
      var id = split[1];
      var $img = $('img', grid);
      var thumb = $('.media', grid).data('thumb');
      // @todo proper preview selection.
      var $txt = $('.views-field--selection', grid).length ? $('.views-field--selection', grid) : $('.views-field:nth-child(2)', grid);
      var $clone = null;

      me.noLongerEmpty();

      $grid.attr('data-entity-id', id).attr('data-entity', entity);

      // If it has thumbnails.
      if (thumb) {
        $clone = $('<img src="' + thumb + '" alt="' + Drupal.t('Thumbnail') + '">');
      }
      // If it has images.
      else if ($img.length) {
        $clone = $img;
      }
      // If it has no images, and has a special class .views-field--selection.
      // @todo fault proof.
      else if ($txt.length) {
        $clone = $txt;
      }

      if ($clone === null) {
        return;
      }

      // Only multistep display has selection, so do nothing.
      if (!$editSelected.length) {
        return;
      }

      $clone.clone()
        .addClass('item-selected')
        .detach()
        .appendTo($editSelected)
        .wrapAll('<div class="item-container" data-entity-id="' + id + '" data-entity="' + entity + '" />').once();

      // Adds dummy elements for quick interaction.
      var $weight = '<input class="weight" value="" type="hidden" />';
      var $remove = '<span class="button-wrap button-wrap--remove"><input value="Remove" class="button button--remove button--remove-js" type="button"></span>';
      $('.item-container', $editSelected).each(function (i) {
        var t = $(this);

        if (!$('.weight', t).length) {
          t.append($remove);
          t.append($weight);

          $('.button--remove', t).attr('data-entity-id', id).attr('data-entity', entity).attr('data-remove-entity', 'items_' + entity).attr('name', 'remove_' + id + '_' + i);
          // <input class="weight" data-drupal-selector="edit-selected-items-220-0-weight" type="hidden" name="selected[items_220_0][weight]" value="0">
          $('.weight', t).val(i).attr('name', 'selected[items_' + id + '_' + i + '][weight]').attr('data-drupal-selector', 'edit-selected-items-' + id + '-' + i + '-weight');
        }
      });

      // Remove the clone when the input is unchecked.
      if (!$input.prop('checked')) {
        $editSelected.find('.item-container[data-entity-id="' + id + '"]').remove();
      }
    }

    /**
     * Check the EB input when the outer element is clicked.
     *
     * @param {HTMLElement} grid
     *   The grid HTML element.
     *
     * @return {bool}
     *   Return false if not applicable.
     */
    function checkItem(grid) {
      var $grid = $(grid);
      var input = 'input[name^="entity_browser_select"]';
      var $input = $(input, grid);
      var entity = $input.val();
      var split = entity.split(':');
      var id = split[1];
      var $view = $grid.closest('.view--sb');

      var checkOne = function () {
        $input.prop('checked', !$input.prop('checked')).attr('data-entity-id', id).attr('data-entity', entity);
        $grid[$input.prop('checked') ? 'addClass' : 'removeClass']('is-marked is-checked');

        $('.button--select', grid).html($input.prop('checked') ? '&#10003;' : '&#43;');
      };

      var uncheckOne = function () {
        $input.prop('checked', false);
        $grid.removeClass('is-marked is-checked');
        $('.button--select', grid).html('&#43;');

        if ($editSelected.length) {
          $editSelected.find('.item-container[data-entity-id="' + id + '"]').remove();
        }
      };

      var resetAll = function () {
        $(input).not($grid.find('input')).prop('checked', false);
        $view.find('.grid').not(this).removeClass('is-marked is-checked');
      };

      var checkAndClone = function () {
        checkOne();
        cloneItem(grid);
      };

      switch (cardinality) {
        case 1:
          // Do not proceed if one is already stored, until removed.
          if ($view.find('.was-checked').length) {
            $form.addClass('form--overlimit');
            return false;
          }

          $form.removeClass('form--overlimit');
          resetAll();
          checkAndClone();

          // Remove anything else but the new one selected.
          if ($editSelected.length) {
            $editSelected.find('.item-container:not([data-entity-id="' + id + '"])').remove();
          }
          break;

        case -1:
          checkAndClone();
          break;

        default:
          var total = $view.find('.is-marked').length;
          // Only multistep display has selection, so still check it.
          if ($editSelected.length && $editSelected.children().length) {
            total = $editSelected.children().length;
          }

          $form[total === cardinality ? 'addClass' : 'removeClass']('form--overlimit');
          if (total >= cardinality) {
            // @todo resetOne, checkOne? Or let the user remove one instead?
            if ($grid.hasClass('is-checked')) {
              uncheckOne();
              $form.removeClass('form--overlimit');
            }
            else {
              $form.addClass('form--overlimit');
            }
            return false;
          }
          else {
            checkAndClone();
          }
          break;
      }
    }

    /**
     * Removes item within Entity Browser selection display.
     *
     * @param {jQuery.Event} event
     *   The event triggered by a `click` event.
     */
    function onRemoveItem(event) {
      event.preventDefault();

      var $btn = $(event.currentTarget);
      var $item = $btn.closest('.item-container');
      var entity = $item.data('entity');
      var $input = $form.find('input[name="entity_browser_select[' + entity + ']"]');
      var $marked = $form.find('.is-marked[data-entity="' + entity + '"]');

      // Remove markers from input container.
      $marked.removeClass('is-marked is-checked');

      $input.prop('checked', false).closest('.is-checked').removeClass('is-marked is-checked');

      // Remove selection item as well.
      $item.remove();
      $form.removeClass('form--overlimit');
      $('.button--select', $marked).html('&#43;');

      if ($editSelected.length) {
        $editSelected.trigger('change.sbCounter');
      }

      if (me.isEmpty()) {
        me.doEmpty();
      }
    }

    /**
     * Toggles the selection displays.
     */
    function onToggleSelection() {
      $form.toggleClass('is-sb-collapsed');
    }

    /**
     * Dialog actions.
     */
    function doDialog() {
      var $fake = $('<button id="edit-use-selected-clone" class="button button--primary button--sb button--use-selected-clone">' + txtUse + '</button>');
      var $close = $dialog.eq(0).find('.ui-dialog-titlebar-close');

      if ($btnUse.length && !$dialog.find(clonedUse).length) {
        $fake.insertBefore($close);
      }

      $dialog.on('click.sbDialogInsert', clonedUse, function (e) {
        $(e.delegateTarget).addClass('media--loading');
        $btnUse.click();
      });
    }

    /**
     * Remove annoying messages on small window by clicking it.
     *
     * @param {Event} e
     *   The click event.
     */
    function onCloseMessages(e) {
      $(e.target).remove();
    }

    /**
     * Finalizes the form actions.
     */
    function finalize() {
      // Remove giant dup messages since we are on small windows, need room.
      window.clearTimeout(_messageTimer);
      _messageTimer = window.setTimeout(function () {
        $('.messages', $body).remove();
      }, 6000);

      $body.addClass('sb-body');
      $('> div:not(.sb__aside)', form).addClass('sb__main');

      // Only proceed if we have selections.
      if (me.isEmpty()) {
        me.doEmpty();
        if ($dialog.length) {
          $dialog.find(clonedUse).remove();
        }
        return;
      }

      // Do dialog stuffs.
      if ($dialog.length) {
        doDialog();
      }

      $(clonedUse).text(txtUse).removeClass('visually-hidden');

      // This selection can be loaded anywhere out of Views, form upload, etc.
      if (!$checkBox.length) {
        return;
      }

      me.noLongerEmpty();
    }

    // Events.
    $form.on('click.sbGrid', '.grid:not(.view-list--header, .was-checked)', onAddItem);
    $form.on('click.sbRemove', '.button--remove-js', onRemoveItem);
    $form.on('click.sbShow', '.button--show-selection', onToggleSelection);
    $form.on('click.sbUpload', '.js-form-file, .dz-clickable', me.onUpload.bind(me));
    $form.on('click.sbInsert', '#edit-use-selected', Drupal.slickBrowser.loading);
    $form.on('click.sbSubmit', '#edit-submit', Drupal.slickBrowser.loading);
    $body.on('click.sbMessage', '.messages', onCloseMessages);

    finalize();
  }

  /**
   * Slick Browser utility functions.
   *
   * @param {int} j
   *   The index of the current element.
   * @param {HTMLElement} elm
   *   The #edit-selected HTML element.
   */
  function sbEntitiesList(j, elm) {
    var me = Drupal.slickBrowser.form;
    var $elm = $(elm);
    var $form = $elm.closest('form');
    var targetType = $form.data('targetType');

    me.$form = $form;

    $elm.children().each(function (i, item) {
      var $item = $(item);
      var id = $item.data('entityId');
      var $input = $('input', item);
      var value = targetType + ':' + id;

      // @todo $input.attr('data-entity', value);
      me.toggleSelected(value, false);
      $input.on('mousedown', function () {
        me.toggleSelected(value, true);

        // Should listen to ajaxing, maybe later.
        window.setTimeout(function () {
          $elm.trigger('change.sbCounter');
        }, 300);
      });
    });

    var checkCounter = function () {
      me.toggleCounter();
    };

    checkCounter();
    $elm.on('change.sbCounter', checkCounter);
  }

  /**
   * Attaches Slick Browser form behavior to HTML element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.slickBrowserForm = {
    attach: function (context) {
      $('.form--sb', context).once('sbForm').each(sbForm);
      $('.entities-list', context).once('sbEntitiesList').each(sbEntitiesList);
    },
    detach: function (context, setting, trigger) {
      if (trigger === 'unload') {
        $('.form--sb', context).removeOnce('sbForm').off('.sbGrid .sbInsert .sbRemove .sbShow .sbUpload');
      }
    }
  };

})(jQuery, Drupal);
