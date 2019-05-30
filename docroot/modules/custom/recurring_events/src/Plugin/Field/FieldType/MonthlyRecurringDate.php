<?php

namespace Drupal\recurring_events\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'monthly_recurring_date' field type.
 *
 * @FieldType (
 *   id = "monthly_recurring_date",
 *   label = @Translation("Monthly Recurring Date"),
 *   description = @Translation("Stores a monthly recurring date configuration"),
 *   default_widget = "monthly_recurring_date",
 *   default_formatter = "monthly_recurring_date"
 * )
 */
class MonthlyRecurringDate extends WeeklyRecurringDate {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['type'] = [
      'type' => 'varchar',
      'length' => 20,
      'not null' => TRUE,
    ];

    $schema['columns']['day_occurrence'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    $schema['columns']['days']['not null'] = FALSE;

    $schema['columns']['day_of_month'] = [
      'type' => 'varchar',
      'length' => 255,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $type = $this->get('type')->getValue();
    $occurrence = $this->get('day_occurrence')->getValue();
    $day_of_month = $this->get('day_of_month')->getValue();
    return parent::isEmpty() && empty($type) && empty($occurrence) && empty($day_of_month);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    // Add our properties.
    $properties['type'] = DataDefinition::create('string')
      ->setLabel(t('Event Recurrence Scheduling'))
      ->setDescription(t('Whether this event recurs based on weekdays, or days of the month'));

    $properties['day_occurrence'] = DataDefinition::create('string')
      ->setLabel(t('Day Occurrence'))
      ->setDescription(t('Which occurence of the day(s) of the week should event take place'));

    $properties['day_of_month'] = DataDefinition::create('string')
      ->setLabel(t('Day of Month'))
      ->setDescription(t('The days of the month on which the event takes place'));

    return $properties;
  }

}
