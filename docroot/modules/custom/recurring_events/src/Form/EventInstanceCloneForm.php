<?php

namespace Drupal\recurring_events\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the eventinstance entity clone form.
 *
 * @ingroup recurring_events
 */
class EventInstanceCloneForm extends EventInstanceForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->entity = $this->entity->createDuplicate();
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $event_instance = $this->getEntity();
    // Add this eventinstance to the eventseries.
    $event_series = $event_instance->getEventSeries();
    $event_series->event_instances[] = ['target_id' => $event_instance->id()];
    $event_series->save();
  }

}
