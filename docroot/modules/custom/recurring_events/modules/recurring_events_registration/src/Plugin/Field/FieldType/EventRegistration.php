<?php

namespace Drupal\recurring_events_registration\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;

/**
 * Plugin implementation of the 'event_registration' field type.
 *
 * @FieldType (
 *   id = "event_registration",
 *   label = @Translation("Event Registration"),
 *   description = @Translation("Stores an event registration configuration"),
 *   default_widget = "event_registration",
 *   default_formatter = "event_registration"
 * )
 */
class EventRegistration extends DateRangeItem {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['registration'] = [
      'type' => 'int',
      'default' => 0,
      'unsigned' => TRUE,
    ];

    $schema['columns']['registration_type'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    $schema['columns']['registration_dates'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    $schema['columns']['capacity'] = [
      'type' => 'int',
      'unsigned' => TRUE,
    ];

    $schema['columns']['waitlist'] = [
      'type' => 'int',
      'default' => 0,
      'unsigned' => TRUE,
    ];

    $schema['columns']['time_amount'] = [
      'type' => 'int',
      'unsigned' => TRUE,
    ];

    $schema['columns']['time_type'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $registration = $this->get('registration')->getValue();
    $registration_type = $this->get('registration_type')->getValue();
    $registration_dates = $this->get('registration_dates')->getValue();
    $capacity = $this->get('capacity')->getValue();
    $waitlist = $this->get('waitlist')->getValue();
    $time_amount = $this->get('time_amount')->getValue();
    $time_type = $this->get('time_type')->getValue();
    return parent::isEmpty() && empty($registration) && empty($registration_type)
      && empty($registration_dates) && empty($capacity) && empty($waitlist)
      && empty($time_amount) && empty($time_type);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['registration'] = DataDefinition::create('boolean')
      ->setLabel(t('Enable Registration'))
      ->setDescription(t('Select whether to enable registration for this series.'));

    $properties['registration_type'] = DataDefinition::create('string')
      ->setLabel(t('Registration Type'))
      ->setDescription(t('Select which type of registration applies to this event.'));

    $properties['registration_dates'] = DataDefinition::create('string')
      ->setLabel(t('Registration Dates'))
      ->setDescription(t('Select whether to enable open or scheduled registration.'));

    $properties['capacity'] = DataDefinition::create('integer')
      ->setLabel(t('Capacity'))
      ->setDescription(t('Enter the number of registrants that can attend the event.'));

    $properties['waitlist'] = DataDefinition::create('boolean')
      ->setLabel(t('Waitlist'))
      ->setDescription(t('Select whether to enable a waitlist.'));

    $properties['time_amount'] = DataDefinition::create('integer')
      ->setLabel(t('Registration Time Amount'))
      ->setDescription(t('Select how many days or hours before the event registration opens.'));

    $properties['time_type'] = DataDefinition::create('string')
      ->setLabel(t('Registration Time Type'))
      ->setDescription(t('Select either days or hours.'));

    return $properties;
  }

}
