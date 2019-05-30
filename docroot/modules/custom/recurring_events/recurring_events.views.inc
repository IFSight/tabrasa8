<?php

/**
 * @file
 * Views functionality for the recurring_events module.
 */

/**
 * Implements hook_views_data_alter().
 */
function recurring_events_views_data_alter(array &$data) {
  // Create a field to show the number of instances of an eventseries.
  $data['eventseries_field_data']['eventseries_instance_count'] = [
    'title' => t('Event series instance count'),
    'field' => [
      'title' => t('Event series instance count'),
      'help' => t('The number of event instances in a series.'),
      'id' => 'eventseries_instance_count',
    ],
  ];

  // Create a field to show the start date of an event series.
  $data['eventseries_field_data']['eventseries_start_date'] = [
    'title' => t('Event series start date'),
    'field' => [
      'title' => t('Event series start date'),
      'help' => t('The date on which an event first occurs.'),
      'id' => 'eventseries_start_date',
    ],
  ];

  // Create a field to show the inherited title of an event instance.
  $data['eventinstance_field_data']['eventinstance_title'] = [
    'title' => t('Event instance title'),
    'field' => [
      'title' => t('Event instance title'),
      'help' => t('The inherited title of an event instance.'),
      'id' => 'eventinstance_title',
      'field_name' => 'title',
    ],
  ];

  // Create a field to show the inherited description of an event instance.
  $data['eventinstance_field_data']['eventinstance_description'] = [
    'title' => t('Event instance description'),
    'field' => [
      'title' => t('Event instance description'),
      'help' => t('The inherited description of an event instance.'),
      'id' => 'eventinstance_description',
      'field_name' => 'description',
    ],
  ];
}
