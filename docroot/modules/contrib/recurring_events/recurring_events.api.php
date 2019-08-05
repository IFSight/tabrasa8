<?php

/**
 * @file
 * Custom hooks exposed by the recurring_events module.
 */

/**
 * Alter the time options available when creating an event series entity.
 *
 * @param array $times
 *   An array of times in the format h:i a.
 */
function hook_recurring_events_times_alter(array &$times = []) {
  // Events cannot occur at midnight.
  unset($times['00:00 am']);
}

/**
 * Alter the duration options available when creating an event series entity.
 *
 * @param array $durations
 *   An array of durations in seconds.
 */
function hook_recurring_events_durations_alter(array &$durations = []) {
  // Events can last for 2 days.
  $durations[172800] = t('2 days');
}

/**
 * Alter the days options available when creating an event series entity.
 *
 * @param array $days
 *   An array of available days.
 */
function hook_recurring_events_days_alter(array &$days = []) {
  // No events can take place on sundays.
  unset($days['sunday']);
}

/**
 * Alter the month days options available when creating an event series entity.
 *
 * @param array $month_days
 *   An array of available days of the month.
 */
function hook_recurring_events_month_days_alter(array &$month_days = []) {
  // No events can take place on the 17th of a month.
  unset($month_days[17]);
}

/**
 * Alter the event instance entity prior to saving it when creating a series.
 *
 * @param array $event_instance
 *   An array of data to be stored against a event instance.
 */
function hook_recurring_events_event_instance_alter(array &$event_instance = []) {
  // Change the series ID.
  $event_instance['event_series_id'] = 12;
}

/**
 * Alter the form config array after it has been generated.
 *
 * @param array $form_config
 *   An array of data representing the date recurring configuration.
 */
function hook_recurring_events_form_config_array_alter(array &$form_config = []) {
  // Remove the first custom date.
  unset($form_config['custom_dates'][0]);
}

/**
 * Alter the entity config array after it has been generated.
 *
 * @param array $entity_config
 *   An array of data representing the date recurring configuration.
 */
function hook_recurring_events_entity_config_array_alter(array &$entity_config = []) {
  // Remove the first custom date.
  unset($form_config['custom_dates'][0]);
}

/**
 * Alter the diff array after it has been generated.
 *
 * @param array $diff
 *   An array of differences between the stored and updated event date config.
 */
function hook_recurring_events_diff_array_alter(array &$diff = []) {
  // Do not show differences in custom dates.
  unset($form_config['custom_dates']);
}

/**
 * Alter the inheritance class used to build the inherited basefield.
 *
 * @var string $class
 *   The class to alter.
 * @var Drupal\Core\Field\FieldDefinitionInterface $field
 *   The field context.
 */
function hook_recurring_events_inheritance_class_alter(&$class, $field) {
  if ($field->plugin() === 'entity_reference_inheritance') {
    $class = '\Drupal\my_module\EntityReferenceFieldInheritanceFactory';
  }
}

/**
 * Alter the array of event instances before they get created.
 *
 * @var array $event_instances
 *   The array of event instances to be created.
 */
function hook_recurring_events_event_instances_pre_create_alter(&$event_instances) {
  $blacklist_dates = [
    '2019-07-01',
    '2019-07-02',
  ];

  // If this date is blacklisted, then do not create an event.
  foreach ($event_instances as $key => $instance) {
    if (array_search($instance['start_date']->format('Y-m-d'), $blacklist_dates) !== FALSE) {
      unset($event_instances[$key]);
    }
  }
}

/**
 * Execute custom code before event instances are deleted.
 *
 * When an eventseries is updated and has date recurring configuration changes
 * the EventCreationService will delete all the instances that existed before
 * and recreate them. This hook allows you to execute code prior to the deletion
 * of those instances.
 *
 * @param Drupal\recurring_events\Entity\EventSeries $event_series
 *   The eventseries being altered.
 * @param Drupal\recurring_events\Entity\EventSeries $original
 *   The original, unaltered eventseries.
 */
function hook_recurring_events_save_pre_instances_deletion(EventSeries $event_series, EventSeries $original) {
}

/**
 * Execute custom code after event instances are deleted.
 *
 * When an eventseries is updated and has date recurring configuration changes
 * the EventCreationService will delete all the instances that existed before
 * and recreate them. This hook allows you to execute code after the deletion
 * of those instances.
 *
 * @param Drupal\recurring_events\Entity\EventSeries $event_series
 *   The eventseries being altered.
 * @param Drupal\recurring_events\Entity\EventSeries $original
 *   The original, unaltered eventseries.
 */
function hook_recurring_events_save_post_instances_deletion(EventSeries $event_series, EventSeries $original) {
}

/**
 * Execute custom code before a specific event instance is deleted.
 *
 * When an eventseries is updated and has date recurring configuration changes
 * the EventCreationService will delete all the instances that existed before
 * and recreate them. This hook allows you to execute code prior to the deletion
 * of each instance.
 *
 * @param Drupal\recurring_events\Entity\EventSeries $event_series
 *   The eventseries being altered.
 * @param Drupal\recurring_events\Entity\EventInstance $event_instance
 *   The event instance being deleted.
 */
function hook_recurring_events_save_pre_instance_deletion(EventSeries $event_series, EventInstance $event_instance) {
}

/**
 * Execute custom code after a specific event instance is deleted.
 *
 * When an eventseries is updated and has date recurring configuration changes
 * the EventCreationService will delete all the instances that existed before
 * and recreate them. This hook allows you to execute code after the deletion
 * of each instance.
 *
 * @param Drupal\recurring_events\Entity\EventSeries $event_series
 *   The eventseries being altered.
 * @param Drupal\recurring_events\Entity\EventInstance $event_instance
 *   The event instance being deleted.
 */
function hook_recurring_events_save_post_instance_deletion(EventSeries $event_series, EventInstance $event_instance) {
}

/**
 * Execute custom code before all instances are deleted.
 *
 * This hook differs to @see hook_recurring_events_save_pre_instances_deletion
 * in that this hook fires before deleting instances by deleting the series
 * rather than as a result of changing series date configuration.
 */
function hook_recurring_events_pre_delete_instances(EventSeries $event_series) {
}

/**
 * Execute custom code after all instances are deleted.
 *
 * This hook differs to @see hook_recurring_events_save_post_instances_deletion
 * in that this hook fires after deleting instances by deleting the series
 * rather than as a result of changing series date configuration.
 */
function hook_recurring_events_post_delete_instances(EventSeries $event_series) {
}

/**
 * Execute custom code before an instance is deleted.
 *
 * This hook differs to @see hook_recurring_events_save_pre_instance_deletion
 * in that this hook fires before deleting an instance directly from the
 * instance rather than as a result of changing series date configuration.
 */
function hook_recurring_events_pre_delete_instance(EventInstance $event_instance) {
}

/**
 * Execute custom code after an instance is deleted.
 *
 * This hook differs to @see hook_recurring_events_save_post_instance_deletion
 * in that this hook fires after deleting an instance directly from the
 * instance rather than as a result of changing series date configuration.
 */
function hook_recurring_events_post_delete_instance(EventInstance $event_instance) {
}
