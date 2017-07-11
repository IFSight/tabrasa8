<?php

namespace Drupal\date_popup;

/**
 * Shared code between the Date and Datetime plugins.
 */
trait DatePopupTrait {

  /**
   * Apply the HTML5 date popup to the views filter form.
   *
   * @param array $form
   *   The form to apply it to.
   */
  protected function applyDatePopupToForm(array &$form) {
    if (!empty($this->options['expose']['identifier'])) {
      // Detect filters that are using min/max.
      if (isset($form[$this->options['expose']['identifier']]['min'])) {
        $form[$this->options['expose']['identifier']]['min']['#type'] = 'date';
        $form[$this->options['expose']['identifier']]['max']['#type'] = 'date';
      }
      else {
        $form[$this->options['expose']['identifier']]['#type'] = 'date';
      }
    }
  }

}
