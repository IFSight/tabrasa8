<?php

namespace Drupal\recurring_events\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the eventseries entity clone form.
 *
 * @ingroup recurring_events
 */
class EventSeriesCloneForm extends EventSeriesForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->entity = $this->entity->createDuplicate();
    // Clear all references to event instances.
    unset($this->entity->event_instances);
    return parent::buildForm($form, $form_state);
  }

}
