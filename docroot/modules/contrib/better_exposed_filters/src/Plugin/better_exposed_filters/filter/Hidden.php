<?php

namespace Drupal\better_exposed_filters\Plugin\better_exposed_filters\filter;

use Drupal\Core\Form\FormStateInterface;

/**
 * Default widget implementation.
 *
 * @BetterExposedFiltersFilterWidget(
 *   id = "bef_hidden",
 *   label = @Translation("Hidden"),
 * )
 */
class Hidden extends FilterWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function exposedFormAlter(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\views\Plugin\views\filter\FilterPluginBase $filter */
    $filter = $this->handler;
    // Form element is designated by the element ID which is user-
    // configurable.
    $field_id = $filter->options['expose']['identifier'];

    parent::exposedFormAlter($form, $form_state);

    if (empty($form[$field_id]['#multiple'])) {
      // Single entry filters can simply be changed to a different element
      // type.
      $form[$field_id]['#type'] = 'hidden';
    }
    else {
      // Hide the label.
      $form['#info']["filter-$field_id"]['label'] = '';
      $form[$field_id]['#title'] = '';

      // Use BEF's preprocess and template to output the hidden elements.
      $form[$field_id]['#theme'] = 'bef_hidden';
    }
  }

}
