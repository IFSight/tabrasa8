<?php

/**
 * @file
 * Tokens for the recurring_events module.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function recurring_events_token_info() {
  $eventinstance = [];
  $eventinstance_type = [
    'name' => t('Event Instance'),
    'description' => t('Tokens for the eventinstance entity type.'),
    'needs-data' => 'eventinstance',
  ];

  $eventinstance['title'] = [
    'name' => t('Event Instance Inherited Title'),
    'description' => t('The title of the event instance.'),
  ];

  $eventinstance['description'] = [
    'name' => t('Event Instance Inherited Description'),
    'description' => t('The description of the event instance.'),
  ];

  $eventinstance['date'] = [
    'name' => t('Event Instance Start Date'),
    'description' => t('The start date of the event instance.'),
  ];

  $eventinstance['end_date'] = [
    'name' => t('Event Instance End Date'),
    'description' => t('The end date of the event instance.'),
  ];

  $eventinstance['url'] = [
    'name' => t('Event Instance URL'),
    'description' => t('The URL of the event instance.'),
  ];

  $eventseries = [];
  $eventseries_type = [
    'name' => t('Event Series'),
    'description' => t('Tokens for the eventseries entity type.'),
    'needs-data' => 'eventseries',
  ];

  $eventseries['title'] = [
    'name' => t('Event Series Title'),
    'description' => t('The title of the event series.'),
  ];

  $eventseries['body'] = [
    'name' => t('Event Series Body'),
    'description' => t('The description of the event series.'),
  ];

  return [
    'types' => [
      'eventinstance' => $eventinstance_type,
      'eventseries' => $eventseries_type,
    ],
    'tokens' => [
      'eventinstance' => $eventinstance,
      'eventseries' => $eventseries,
    ],
  ];

}

/**
 * Implements hook_tokens().
 */
function recurring_events_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  if ($type == 'eventinstance' && !empty($data['eventinstance'])) {
    $event_instance = $data['eventinstance'];
    $date_format = \Drupal::config('recurring_events.eventinstance.config')->get('date_format');
    $timezone = new \DateTimeZone(drupal_get_user_timezone());
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'title':
          $replacements[$original] = $event_instance->title->value;
          break;

        case 'description':
          $replacements[$original] = $event_instance->description->value;
          break;

        case 'date':
          $replacements[$original] = $event_instance->date->start_date->setTimezone($timezone)->format($date_format);
          break;

        case 'end_date':
          $replacements[$original] = $event_instance->date->end_date->setTimezone($timezone)->format($date_format);
          break;

        case 'url':
          $replacements[$original] = $event_instance->toUrl('canonical')->setAbsolute(TRUE)->toString();
          break;
      }
    }
  }
  if ($type == 'eventseries' && !empty($data['eventseries'])) {
    $event_series = $data['eventseries'];
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'title':
          $replacements[$original] = $event_series->title->value;
          break;

        case 'body':
          $replacements[$original] = $event_series->body->value;
          break;

      }
    }
  }
  return $replacements;
}
