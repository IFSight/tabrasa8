<?php

namespace Drupal\recurring_events\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;

/**
 * Plugin implementation of the 'weekly_recurring_date' field type.
 *
 * @FieldType (
 *   id = "weekly_recurring_date",
 *   label = @Translation("Weekly Recurring Date"),
 *   description = @Translation("Stores a weekly recurring date configuration"),
 *   default_widget = "weekly_recurring_date",
 *   default_formatter = "weekly_recurring_date"
 * )
 */
class WeeklyRecurringDate extends DateRangeItem {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['time'] = [
      'type' => 'varchar',
      'length' => 20,
    ];

    $schema['columns']['duration'] = [
      'type' => 'int',
      'unsigned' => TRUE,
    ];

    $schema['columns']['days'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $time = $this->get('time')->getValue();
    $duration = $this->get('duration')->getValue();
    $days = $this->get('days')->getValue();
    return parent::isEmpty() && empty($time) && empty($duration) && empty($days);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    // Add our properties.
    $properties['time'] = DataDefinition::create('string')
      ->setLabel(t('Time'))
      ->setDescription(t('The time the event begins'));

    $properties['duration'] = DataDefinition::create('integer')
      ->setLabel(t('Duration'))
      ->setDescription(t('The duration of the event in minutes'));

    $properties['days'] = DataDefinition::create('string')
      ->setLabel(t('Days'))
      ->setDescription(t('The days of the week on which this event occurs'));

    return $properties;
  }

}
