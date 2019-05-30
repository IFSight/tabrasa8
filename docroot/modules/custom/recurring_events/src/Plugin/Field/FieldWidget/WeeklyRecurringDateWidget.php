<?php

namespace Drupal\recurring_events\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\datetime_range\Plugin\Field\FieldWidget\DateRangeDefaultWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Plugin implementation of the 'weekly recurring date' widget.
 *
 * @FieldWidget (
 *   id = "weekly_recurring_date",
 *   label = @Translation("Weekly recurring date widget"),
 *   field_types = {
 *     "weekly_recurring_date"
 *   }
 * )
 */
class WeeklyRecurringDateWidget extends DateRangeDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#type'] = 'container';
    $element['#states'] = [
      'visible' => [
        ':input[name="recur_type"]' => ['value' => 'weekly'],
      ],
    ];

    $element['value']['#title'] = t('Create Events Between');
    $element['value']['#weight'] = 1;
    $element['value']['#date_date_format'] = DateTimeItemInterface::DATE_STORAGE_FORMAT;
    $element['value']['#date_date_element'] = 'date';
    $element['value']['#date_time_format'] = '';
    $element['value']['#date_time_element'] = 'none';

    $element['end_value']['#title'] = t('And');
    $element['end_value']['#weight'] = 2;
    $element['end_value']['#date_date_format'] = DateTimeItemInterface::DATE_STORAGE_FORMAT;
    $element['end_value']['#date_date_element'] = 'date';
    $element['end_value']['#date_time_format'] = '';
    $element['end_value']['#date_time_element'] = 'none';

    $times = $this->getTimeOptions();
    $element['time'] = [
      '#type' => 'select',
      '#title' => t('Event Start Time'),
      '#options' => $times,
      '#default_value' => $items[$delta]->time ?: '',
      '#weight' => 3,
    ];

    $durations = $this->getDurationOptions();
    $element['duration'] = [
      '#type' => 'select',
      '#title' => t('Event Duration'),
      '#options' => $durations,
      '#default_value' => $items[$delta]->duration ?: '',
      '#weight' => 4,
    ];

    $days = $this->getDayOptions();
    $element['days'] = [
      '#type' => 'checkboxes',
      '#title' => t('Days of the Week'),
      '#options' => $days,
      '#default_value' => $items[$delta]->days ? explode(',', $items[$delta]->days) : [],
      '#weight' => 5,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$item) {
      if (empty($item['value'])) {
        $item['value'] = '';
      }
      elseif (!$item['value'] instanceof DrupalDateTime) {
        $item['value'] = substr($item['value'], 0, 10);
      }
      else {
        $item['value'];
      }
      if (empty($item['end_value'])) {
        $item['end_value'] = '';
      }
      elseif (!$item['end_value'] instanceof DrupalDateTime) {
        $item['end_value'] = substr($item['end_value'], 0, 10);
      }
      else {
        $item['end_value'];
      }

      $item['days'] = array_filter($item['days']);
      if (!empty($item['days'])) {
        $item['days'] = implode(',', $item['days']);
      }
      else {
        $item['days'] = '';
      }

    }
    $values = parent::massageFormValues($values, $form, $form_state);
    return $values;
  }

  /**
   * Generate times based on specific intervals and min/max times.
   *
   * @return array
   *   An array of times suitable for a select list.
   */
  protected function getTimeOptions() {
    $times = [];

    $config = \Drupal::config('recurring_events.eventseries.config');
    // Take interval in minutes, and multiply it by 60 to convert to seconds.
    $interval = $config->get('interval') * 60;
    $min_time = $config->get('min_time');
    $max_time = $config->get('max_time');
    $format = $config->get('time_format');

    $min_time = DrupalDateTime::createFromFormat('h:ia', $min_time);
    $max_time = DrupalDateTime::createFromFormat('h:ia', $max_time);

    // Convert the mininum time to a number of seconds after midnight.
    $lower_hour = $min_time->format('H') * 60 * 60;
    $lower_minute = $min_time->format('i') * 60;
    $lower = $lower_hour + $lower_minute;

    // Convert the maximum time to a number of seconds after midnight.
    $upper_hour = $max_time->format('H') * 60 * 60;
    $upper_minute = $max_time->format('i') * 60;
    $upper = $upper_hour + $upper_minute;

    $range = range($lower, $upper, $interval);
    $utc_timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);

    foreach ($range as $time) {
      $time_option = DrupalDateTime::createFromTimestamp($time, $utc_timezone);
      $times[$time_option->format('h:i a')] = $time_option->format($format);
    }

    \Drupal::moduleHandler()->alter('recurring_events_times', $times);

    return $times;
  }

  /**
   * Return durations for events.
   *
   * @return array
   *   An array of durations suitable for a select list.
   */
  protected function getDurationOptions() {
    $durations = [
      '900' => t('15 minutes'),
      '1800' => t('30 minutes'),
      '2700' => t('45 minutes'),
      '3600' => t('1 hour'),
      '5400' => t('1.5 hours'),
      '7200' => t('2 hours'),
      '9000' => t('2.5 hours'),
      '10800' => t('3 hours'),
      '12600' => t('3.5 hours'),
      '14400' => t('4 hours'),
      '16200' => t('4.5 hours'),
      '18000' => t('5 hours'),
      '19800' => t('5.5 hours'),
      '21600' => t('6 hours'),
      '25200' => t('7 hours'),
      '28800' => t('8 hours'),
      '32400' => t('9 hours'),
      '36000' => t('10 hours'),
      '39600' => t('11 hours'),
      '43200' => t('12 hours'),
      '46800' => t('13 hours'),
      '50400' => t('14 hours'),
      '54000' => t('15 hours'),
      '57600' => t('16 hours'),
      '61200' => t('17 hours'),
      '64800' => t('18 hours'),
      '68400' => t('19 hours'),
      '72000' => t('20 hours'),
      '75600' => t('21 hours'),
      '79200' => t('22 hours'),
      '82800' => t('23 hours'),
      '86400' => t('24 hours'),
    ];

    \Drupal::moduleHandler()->alter('recurring_events_durations', $durations);

    return $durations;
  }

  /**
   * Return day options for events.
   *
   * @return array
   *   An array of days suitable for a checkbox field.
   */
  protected function getDayOptions() {
    $config = \Drupal::config('recurring_events.eventseries.config');
    $days = explode(',', $config->get('days'));
    // All labels should have a capital first letter as they are proper nouns.
    $day_labels = array_map('ucwords', $days);
    $days = array_combine($days, $day_labels);

    \Drupal::moduleHandler()->alter('recurring_events_days', $days);

    return $days;
  }

}
