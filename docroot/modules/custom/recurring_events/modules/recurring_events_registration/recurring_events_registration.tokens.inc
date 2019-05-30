<?php

/**
 * @file
 * Tokens for the recurring_events_registration module.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function recurring_events_registration_token_info() {
  $eventinstance = [];
  $eventinstance_type = [
    'name' => t('Event Instance'),
    'description' => t('Event Instance tokens from the Recurring Events Registration module.'),
    'needs-data' => 'eventinstance',
  ];

  $eventinstance['reg_url'] = [
    'name' => t('Register URL'),
    'description' => t('The URL for registering for an event instance.'),
  ];

  $eventinstance['reg_type'] = [
    'name' => t('Event Registration Type'),
    'description' => t('The registration type of the event instance.'),
  ];

  $registrant = [];
  $registrant_type = [
    'name' => t('Registrant'),
    'description' => t('Registrant tokens from the Recurring Events Registration module.'),
    'needs-data' => 'registrant',
  ];

  $registrant['edit_url'] = [
    'name' => t('Edit Registrant URL'),
    'description' => t('The URL to edit a registrant.'),
  ];

  $registrant['delete_url'] = [
    'name' => t('Delete Registrant URL'),
    'description' => t('The URL to delete a registrant.'),
  ];

  return [
    'types' => [
      'eventinstance' => $eventinstance_type,
      'registrant' => $registrant_type,
    ],
    'tokens' => [
      'eventinstance' => $eventinstance,
      'registrant' => $registrant,
    ],
  ];

}

/**
 * Implements hook_tokens().
 */
function recurring_events_registration_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  if ($type == 'eventinstance' && !empty($data['eventinstance'])) {
    $event_instance = $data['eventinstance'];
    $event_series = $event_instance->getEventSeries();
    $creation_service = \Drupal::service('recurring_events_registration.creation_service');
    $creation_service->setEventInstance($event_instance);
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'reg_url':
          $replacements[$original] = $event_instance->toUrl('canonical')->setAbsolute(TRUE)->toString() . '/registration/add';
          break;

        case 'reg_type':
          $replacements[$original] = $creation_service->getRegistrationType() == 'series' ? t('series') : t('event');
          break;
      }
    }
  }

  if ($type == 'registrant' && !empty($data['registrant'])) {
    $registrant = $data['registrant'];
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'edit_url':
          $url = $registrant->toUrl('edit-form')->setAbsolute(TRUE)->toString();
          if ($registrant->user_id->target_id === '0') {
            $url = $registrant->toUrl('anon-edit-form')->setAbsolute(TRUE)->toString();
          }
          $replacements[$original] = $url;
          break;

        case 'delete_url':
          $url = $registrant->toUrl('delete-form')->setAbsolute(TRUE)->toString();
          if ($registrant->user_id->target_id === '0') {
            $url = $registrant->toUrl('anon-delete-form')->setAbsolute(TRUE)->toString();
          }
          $replacements[$original] = $url;
          break;
      }
    }
  }
  return $replacements;
}
