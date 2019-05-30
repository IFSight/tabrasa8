<?php

namespace Drupal\recurring_events\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Plugin implementation of the 'monthly recurring date' widget.
 *
 * @FieldWidget (
 *   id = "monthly_recurring_date",
 *   label = @Translation("Monthly recurring date widget"),
 *   field_types = {
 *     "monthly_recurring_date"
 *   }
 * )
 */
class MonthlyRecurringDateWidget extends WeeklyRecurringDateWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['#type'] = 'container';
    $element['#states'] = [
      'visible' => [
        ':input[name="recur_type"]' => ['value' => 'monthly'],
      ],
    ];

    $element['type'] = [
      '#type' => 'radios',
      '#title' => t('Event Recurrence Schedule'),
      '#options' => [
        'weekday' => t('Recur on Day of Week'),
        'monthday' => t('Recur on Day of Month'),
      ],
      '#default_value' => $items[$delta]->type ?: '',
      '#weight' => 5,
    ];

    $element['day_occurrence'] = [
      '#type' => 'checkboxes',
      '#title' => t('Day Occurrence'),
      '#options' => [
        'first' => t('First'),
        'second' => t('Second'),
        'third' => t('Third'),
        'fourth' => t('Fourth'),
        'last' => t('Last'),
      ],
      '#default_value' => $items[$delta]->day_occurrence ? explode(',', $items[$delta]->day_occurrence) : [],
      '#states' => [
        'visible' => [
          ':input[name="monthly_recurring_date[0][type]"]' => ['value' => 'weekday'],
        ],
      ],
      '#weight' => 6,
    ];

    $days = $this->getDayOptions();
    $element['days'] = [
      '#type' => 'checkboxes',
      '#title' => t('Days of the Week'),
      '#options' => $days,
      '#default_value' => $items[$delta]->days ? explode(',', $items[$delta]->days) : [],
      '#states' => [
        'visible' => [
          ':input[name="monthly_recurring_date[0][type]"]' => ['value' => 'weekday'],
        ],
      ],
      '#weight' => 7,
    ];

    $month_days = $this->getMonthDayOptions();
    $element['day_of_month'] = [
      '#type' => 'checkboxes',
      '#title' => t('Days of the Month'),
      '#options' => $month_days,
      '#default_value' => $items[$delta]->day_of_month ? explode(',', $items[$delta]->day_of_month) : [],
      '#states' => [
        'visible' => [
          ':input[name="monthly_recurring_date[0][type]"]' => ['value' => 'monthday'],
        ],
      ],
      '#weight' => 8,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $values = parent::massageFormValues($values, $form, $form_state);

    foreach ($values as &$item) {

      $item['day_occurrence'] = array_filter($item['day_occurrence']);
      if (!empty($item['day_occurrence'])) {
        $item['day_occurrence'] = implode(',', $item['day_occurrence']);
      }
      else {
        $item['day_occurrence'] = '';
      }

      $item['day_of_month'] = array_filter($item['day_of_month']);
      if (!empty($item['day_of_month'])) {
        $item['day_of_month'] = implode(',', $item['day_of_month']);
      }
      else {
        $item['day_of_month'] = '';
      }
    }
    return $values;
  }

  /**
   * Return day of month options for events.
   *
   * @return array
   *   An array of days of month suitable for a checkbox field.
   */
  protected function getMonthDayOptions() {
    $days = [];
    $start = date('Y') . '-01-01';
    $date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $start);

    for ($x = 1; $x <= 31; $x++) {
      $days[$x] = $date->format('jS');
      $date->modify('+1 day');
    }

    $days[-1] = t('Last');

    \Drupal::moduleHandler()->alter('recurring_events_month_days', $days);

    return $days;
  }

}
