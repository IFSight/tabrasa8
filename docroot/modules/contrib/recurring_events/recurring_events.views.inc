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

  // Create a views field for each of the inherited fields.
  $inherited_fields = \Drupal::entityTypeManager()->getStorage('field_inheritance')->loadMultiple();
  $set = FALSE;
  if (!empty($inherited_fields)) {
    foreach ($inherited_fields as $field) {
      if ($field->id() == 'title') {
        $data['eventinstance_field_data']['table']['base']['defaults']['field'] = 'title';
        $set = TRUE;
      }
      $data['eventinstance_field_data'][$field->id()] = [
        'title' => $field->label(),
        'field' => [
          'title' => $field->label(),
          'help' => t('The inherited @field field', [
            '@field' => $field->label(),
          ]),
          'id' => 'inherited_field',
          'field_name' => $field->id(),
        ],
      ];
    }
  }

  if (!$set) {
    $data['eventinstance_field_data']['table']['base']['defaults']['field'] = 'id';
  }
}
