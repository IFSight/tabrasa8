<?php

/**
 * @file
 * Custom hooks exposed by the recurring_events_registration module.
 */

use Drupal\recurring_events_registration\Entity\Registrant;

/**
 * Alter the registrant to be promoted from the waitlist.
 *
 * If you need to apply custom logic to determining which user should be
 * promoted from the waitlist when a registration spot opens up you can
 * implement this hook and write your custom logic here. The hook must return an
 * instance of Drupal\recurring_events_registration\Entity\Registrant for the
 * specified event, which can be retrieved from the registrant entity.
 *
 * @param Drupal\recurring_events_registration\Entity\Registrant $registrant
 *   The default selected registrant.
 *
 * @return Drupal\recurring_events_registration\Entity\Registrant
 *   A valid registrant entity.
 */
function hook_recurring_events_registration_first_waitlist_alter(Registrant $registrant) {
  // Find the ID of the registrant you wish to promote, then load the entity.
  $new_registrant = \Drupal::entityTypeManager()->getStorage('registrant')->load($id);
  return $new_registrant;
}

/**
 * Alter the types of notification available in the registrant settings.
 *
 * The notification types array allows a developer to override which types are
 * configurable in the registrant settings. The array should be formatted as
 * such:
 *  Key - the machine name of the notification type. This must be unique.
 *  Value - an array containing two keys:
 *    name - the translated name of the notification.
 *    description - the translated description of the notification.
 *
 * @param array $notification_types
 *   The notification types array.
 */
function hook_recurring_events_registration_notification_types_alter(array &$notification_types) {
  $notification_types['rename'] = [
    'name' => t('Event Rename Notification'),
    'description' => t('Send an email to registrants when the event name changes?'),
  ];
}
