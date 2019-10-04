<?php

namespace Drupal\recurring_events;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\recurring_events\Entity\EventSeries;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * EventCreationService class.
 */
class EventCreationService {

  /**
   * The translation interface.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  private $translation;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   *   The translation interface.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger factory.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   */
  public function __construct(TranslationInterface $translation, Connection $database, LoggerChannelFactoryInterface $logger, Messenger $messenger) {
    $this->translation = $translation;
    $this->database = $database;
    $this->loggerFactory = $logger->get('recurring_events');
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('string_translation'),
      $container->get('database'),
      $container->get('logger.factory'),
      $container->get('messenger')
    );
  }

  /**
   * Check whether there have been form recurring configuration changes.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of an updated event series entity.
   *
   * @return bool
   *   TRUE if recurring config changes, FALSE otherwise.
   */
  public function checkForFormRecurConfigChanges(EventSeries $event, FormStateInterface $form_state) {
    $entity_config = $this->convertEntityConfigToArray($event);
    $form_config = $this->convertFormConfigToArray($form_state);
    return !(serialize($entity_config) === serialize($form_config));
  }

  /**
   * Check whether there have been original recurring configuration changes.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   * @param Drupal\recurring_events\Entity\EventSeries $original
   *   The original stored event series entity.
   *
   * @return bool
   *   TRUE if recurring config changes, FALSE otherwise.
   */
  public function checkForOriginalRecurConfigChanges(EventSeries $event, EventSeries $original) {
    $entity_config = $this->convertEntityConfigToArray($event);
    $original_config = $this->convertEntityConfigToArray($original);
    return !(serialize($entity_config) === serialize($original_config));
  }

  /**
   * Converts an EventSeries entity's recurring configuration to an array.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   *
   * @return array
   *   The recurring configuration as an array.
   */
  public function convertEntityConfigToArray(EventSeries $event) {
    $config = [];
    $config['type'] = $event->getRecurType();
    $config['excluded_dates'] = $event->getExcludedDates();
    $config['included_dates'] = $event->getIncludedDates();

    switch ($event->getRecurType()) {
      case 'weekly':
        $config['start_date'] = $event->getWeeklyStartDate();
        $config['end_date'] = $event->getWeeklyEndDate();
        $config['time'] = $event->getWeeklyStartTime();
        $config['duration'] = $event->getWeeklyDuration();
        $config['days'] = $event->getWeeklyDays();
        break;

      case 'monthly':
        $config['start_date'] = $event->getMonthlyStartDate();
        $config['end_date'] = $event->getMonthlyEndDate();
        $config['time'] = $event->getMonthlyStartTime();
        $config['duration'] = $event->getMonthlyDuration();
        $config['monthly_type'] = $event->getMonthlyType();

        switch ($event->getMonthlyType()) {
          case 'weekday':
            $config['day_occurrence'] = $event->getMonthlyDayOccurrences();
            $config['days'] = $event->getMonthlyDays();
            break;

          case 'monthday':
            $config['day_of_month'] = $event->getMonthlyDayOfMonth();
            break;
        }
        break;

      case 'custom':
        $config['custom_dates'] = $event->getCustomDates();
        break;
    }

    \Drupal::moduleHandler()->alter('recurring_events_entity_config_array', $config);

    return $config;
  }

  /**
   * Converts a form state object's recurring configuration to an array.
   *
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state of an updated event series entity.
   *
   * @return array
   *   The recurring configuration as an array.
   */
  public function convertFormConfigToArray(FormStateInterface $form_state) {
    $config = [];

    $user_timezone = new \DateTimeZone(drupal_get_user_timezone());
    $utc_timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
    $user_input = $form_state->getUserInput();

    $config['type'] = $user_input['recur_type'];

    $config['excluded_dates'] = $this->getDatesFromForm($user_input['excluded_dates']);
    $config['included_dates'] = $this->getDatesFromForm($user_input['included_dates']);

    switch ($config['type']) {
      case 'weekly':
        $time = $user_input['weekly_recurring_date'][0]['time'];
        $time_parts = $this->convertTimeTo24hourFormat($time);
        $timestamp = implode(':', $time_parts) . ':00';

        $start_timestamp = $user_input['weekly_recurring_date'][0]['value']['date'] . 'T' . $timestamp;
        $start_date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $start_timestamp, $user_timezone);
        $start_date->setTime(0, 0, 0);

        $end_timestamp = $user_input['weekly_recurring_date'][0]['end_value']['date'] . 'T' . $timestamp;
        $end_date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $end_timestamp, $user_timezone);
        $end_date->setTime(0, 0, 0);

        $config['start_date'] = $start_date;
        $config['end_date'] = $end_date;

        $config['time'] = $time;
        $config['duration'] = $user_input['weekly_recurring_date'][0]['duration'];
        $config['days'] = array_filter(array_values($user_input['weekly_recurring_date'][0]['days']));
        break;

      case 'monthly':
        $time = $user_input['monthly_recurring_date'][0]['time'];
        $time_parts = $this->convertTimeTo24hourFormat($time);
        $timestamp = implode(':', $time_parts) . ':00';

        $start_timestamp = $user_input['monthly_recurring_date'][0]['value']['date'] . 'T' . $timestamp;
        $start_date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $start_timestamp, $user_timezone);
        $start_date->setTime(0, 0, 0);

        $end_timestamp = $user_input['monthly_recurring_date'][0]['end_value']['date'] . 'T' . $timestamp;
        $end_date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $end_timestamp, $user_timezone);
        $end_date->setTime(0, 0, 0);

        $config['start_date'] = $start_date;
        $config['end_date'] = $end_date;

        $config['time'] = $time;
        $config['duration'] = $user_input['monthly_recurring_date'][0]['duration'];
        $config['monthly_type'] = $user_input['monthly_recurring_date'][0]['type'];

        switch ($config['monthly_type']) {
          case 'weekday':
            $config['day_occurrence'] = array_filter(array_values($user_input['monthly_recurring_date'][0]['day_occurrence']));
            $config['days'] = array_filter(array_values($user_input['monthly_recurring_date'][0]['days']));
            break;

          case 'monthday':
            $config['day_of_month'] = array_filter(array_values($user_input['monthly_recurring_date'][0]['day_of_month']));
            break;
        }
        break;

      case 'custom':
        foreach ($user_input['custom_date'] as $custom_date) {
          $start_date = $end_date = NULL;

          if (!empty($custom_date['value']['date'])
            && !empty($custom_date['value']['time'])
            && !empty($custom_date['end_value']['date'])
            && !empty($custom_date['end_value']['time'])) {

            // For some reason, sometimes we do not receive seconds from the
            // date range picker.
            if (strlen($custom_date['value']['time']) == 5) {
              $custom_date['value']['time'] .= ':00';
            }
            if (strlen($custom_date['end_value']['time']) == 5) {
              $custom_date['end_value']['time'] .= ':00';
            }

            $start_timestamp = implode('T', $custom_date['value']);
            $start_date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $start_timestamp, $user_timezone);
            // Convert the DateTime object back to UTC timezone.
            $start_date->setTimezone($utc_timezone);

            $end_timestamp = implode('T', $custom_date['end_value']);
            $end_date = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $end_timestamp, $user_timezone);
            // Convert the DateTime object back to UTC timezone.
            $end_date->setTimezone($utc_timezone);

            $config['custom_dates'][] = [
              'start_date' => $start_date,
              'end_date' => $end_date,
            ];
          }
        }
        break;
    }

    \Drupal::moduleHandler()->alter('recurring_events_form_config_array', $config);

    return $config;
  }

  /**
   * Build diff array between stored entity and form state.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   (Optional) The form state of an updated event series entity.
   * @param Drupal\recurring_events\Entity\EventSeries $edited
   *   (Optional) The edited event series entity.
   *
   * @return array
   *   An array of differences.
   */
  public function buildDiffArray(EventSeries $event, FormStateInterface $form_state = NULL, EventSeries $edited = NULL) {
    $diff = [];

    $entity_config = $this->convertEntityConfigToArray($event);
    $form_config = [];

    if (!is_null($form_state)) {
      $form_config = $this->convertFormConfigToArray($form_state);
    }
    if (!is_null($edited)) {
      $form_config = $this->convertEntityConfigToArray($edited);
    }

    if (empty($form_config)) {
      return $diff;
    }

    if ($entity_config['type'] !== $form_config['type']) {
      $diff['type'] = [
        'label' => $this->translation->translate('Recur Type'),
        'stored' => $entity_config['type'],
        'override' => $form_config['type'],
      ];
    }
    else {
      if ($entity_config['excluded_dates'] !== $form_config['excluded_dates']) {
        $entity_dates = $this->buildDateString($entity_config['excluded_dates']);
        $config_dates = $this->buildDateString($form_config['excluded_dates']);
        $diff['excluded_dates'] = [
          'label' => $this->translation->translate('Excluded Dates'),
          'stored' => $entity_dates,
          'override' => $config_dates,
        ];
      }
      if ($entity_config['included_dates'] !== $form_config['included_dates']) {
        $entity_dates = $this->buildDateString($entity_config['included_dates']);
        $config_dates = $this->buildDateString($form_config['included_dates']);
        $diff['included_dates'] = [
          'label' => $this->translation->translate('Included Dates'),
          'stored' => $entity_dates,
          'override' => $config_dates,
        ];
      }
      switch ($entity_config['type']) {
        case 'weekly':
        case 'monthly':
          if ($entity_config['start_date']->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT) !== $form_config['start_date']->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT)) {
            $diff['start_date'] = [
              'label' => $this->translation->translate('Start Date'),
              'stored' => $entity_config['start_date']->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
              'override' => $form_config['start_date']->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
            ];
          }
          if ($entity_config['end_date']->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT) !== $form_config['end_date']->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT)) {
            $diff['end_date'] = [
              'label' => $this->translation->translate('End Date'),
              'stored' => $entity_config['end_date']->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
              'override' => $form_config['end_date']->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
            ];
          }
          if ($entity_config['time'] !== $form_config['time']) {
            $diff['time'] = [
              'label' => $this->translation->translate('Time'),
              'stored' => $entity_config['time'],
              'override' => $form_config['time'],
            ];
          }
          if ($entity_config['duration'] !== $form_config['duration']) {
            $diff['duration'] = [
              'label' => $this->translation->translate('Duration'),
              'stored' => $entity_config['duration'],
              'override' => $form_config['duration'],
            ];
          }

          if ($entity_config['type'] === 'weekly') {
            if ($entity_config['days'] !== $form_config['days']) {
              $diff['days'] = [
                'label' => $this->translation->translate('Days'),
                'stored' => implode(',', $entity_config['days']),
                'override' => implode(',', $form_config['days']),
              ];
            }
          }

          if ($entity_config['type'] === 'monthly') {
            if ($entity_config['monthly_type'] !== $form_config['monthly_type']) {
              $diff['monthly_type'] = [
                'label' => $this->translation->translate('Monthly Type'),
                'stored' => $entity_config['monthly_type'],
                'override' => $form_config['monthly_type'],
              ];
            }
            if ($entity_config['monthly_type'] === 'weekday') {
              if ($entity_config['day_occurrence'] !== $form_config['day_occurrence']) {
                $diff['day_occurrence'] = [
                  'label' => $this->translation->translate('Day Occurrence'),
                  'stored' => implode(',', $entity_config['day_occurrence']),
                  'override' => implode(',', $form_config['day_occurrence']),
                ];
              }
              if ($entity_config['days'] !== $form_config['days']) {
                $diff['days'] = [
                  'label' => $this->translation->translate('Days'),
                  'stored' => implode(',', $entity_config['days']),
                  'override' => implode(',', $form_config['days']),
                ];
              }
            }
            else {
              if ($entity_config['day_of_month'] !== $form_config['day_of_month']) {
                $diff['day_of_month'] = [
                  'label' => $this->translation->translate('Day of the Month'),
                  'stored' => implode(',', $entity_config['day_of_month']),
                  'override' => implode(',', $form_config['day_of_month']),
                ];
              }
            }
          }

          break;

        case 'custom':
          if ($entity_config['custom_dates'] !== $form_config['custom_dates']) {
            $stored_start_ends = $overridden_start_ends = [];

            foreach ($entity_config['custom_dates'] as $date) {
              if (!empty($date['start_date']) && !empty($date['end_date'])) {
                $stored_start_ends[] = $date['start_date']->format('Y-m-d h:ia') . ' - ' . $date['end_date']->format('Y-m-d h:ia');
              }
            }

            foreach ($form_config['custom_dates'] as $dates) {
              if (!empty($date['start_date']) && !empty($date['end_date'])) {
                $overridden_start_ends[] = $date['start_date']->format('Y-m-d h:ia') . ' - ' . $date['end_date']->format('Y-m-d h:ia');
              }
            }

            $diff['custom_dates'] = [
              'label' => $this->translation->translate('Custom Dates'),
              'stored' => implode(', ', $stored_start_ends),
              'override' => implode(', ', $overridden_start_ends),
            ];
          }
          break;
      }
    }

    \Drupal::moduleHandler()->alter('recurring_events_diff_array', $diff);

    return $diff;
  }

  /**
   * Create an event based on the form submitted values.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   * @param Drupal\recurring_events\Entity\EventSeries $original
   *   The original, unsaved event series entity.
   */
  public function saveEvent(EventSeries $event, EventSeries $original = NULL) {
    // We want to always create instances if this is a brand new series.
    if ($event->isNew()) {
      $create_instances = TRUE;
    }
    else {
      // If there are date differences, we need to clear out the instances.
      $create_instances = $this->checkForOriginalRecurConfigChanges($event, $original);
      if ($create_instances) {
        // Allow other modules to react prior to the deletion of all instances.
        \Drupal::moduleHandler()->invokeAll('recurring_events_save_pre_instances_deletion', [$event, $original]);

        // Find all the instances and delete them.
        $instances = $event->event_instances->referencedEntities();
        if (!empty($instances)) {
          foreach ($instances as $index => $instance) {
            // Allow other modules to react prior to deleting a specific
            // instance after a date configuration change.
            \Drupal::moduleHandler()->invokeAll('recurring_events_save_pre_instance_deletion', [$event, $instance]);

            $instance->delete();

            // Allow other modules to react after deleting a specific instance
            // after a date configuration change.
            \Drupal::moduleHandler()->invokeAll('recurring_events_save_post_instance_deletion', [$event, $instance]);
          }
          $this->messenger->addStatus($this->translation->translate('A total of %count existing event instances were removed', [
            '%count' => count($instances),
          ]));
        }

        // Allow other modules to react after the deletion of all instances.
        \Drupal::moduleHandler()->invokeAll('recurring_events_save_post_instances_deletion', [$event, $original]);
      }
    }

    // Only create instances if date changes have been made or the event is new.
    if ($create_instances) {
      $this->createInstances($event);
    }
  }

  /**
   * Create the event instances from the form state.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   */
  public function createInstances(EventSeries $event) {
    $form_data = $this->convertEntityConfigToArray($event);
    $event_instances = [];

    $timezone = new \DateTimeZone(drupal_get_user_timezone());
    $utc_timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);

    if (!empty($form_data['type'])) {
      switch ($form_data['type']) {
        case 'custom':
          if (!empty($form_data['custom_dates'])) {
            $events_to_create = [];
            foreach ($form_data['custom_dates'] as $date_range) {
              // Set this event to be created.
              $events_to_create[$date_range['start_date']->format('r')] = [
                'start_date' => $date_range['start_date'],
                'end_date' => $date_range['end_date'],
              ];
            }

            // Allow modules to alter the array of event instances before they
            // get created.
            \Drupal::moduleHandler()->alter('recurring_events_event_instances_pre_create', $events_to_create, $event);

            if (!empty($events_to_create)) {
              foreach ($events_to_create as $custom_event) {
                $event_instances[] = $this->createEventInstance($event, $custom_event['start_date'], $custom_event['end_date']);
              }
            }
          }
          break;

        case 'weekly':
          if (!empty($form_data['days'])) {
            $dates = [];

            // Loop through each weekday and find occurrences of it in the
            // date range provided.
            foreach ($form_data['days'] as $weekday) {
              $weekday_dates = $this->findWeekdaysBetweenDates($weekday, $form_data['start_date'], $form_data['end_date']);
              $dates = array_merge($dates, $weekday_dates);
            }
            $time_parts = $this->convertTimeTo24hourFormat($form_data['time']);

            if (!empty($dates)) {
              $events_to_create = [];
              foreach ($dates as $weekly_date) {
                // Set the time of the start date to be the hours and minutes.
                $weekly_date->setTime($time_parts[0], $time_parts[1]);
                // Configure the right timezone.
                $weekly_date->setTimezone($utc_timezone);
                // Create a clone of this date.
                $weekly_date_end = clone $weekly_date;
                // Add the number of seconds specified in the duration field.
                $weekly_date_end->modify('+' . $form_data['duration'] . ' seconds');
                // Set this event to be created.
                $events_to_create[$weekly_date->format('r')] = [
                  'start_date' => $weekly_date,
                  'end_date' => $weekly_date_end,
                ];
              }

              // Allow modules to alter the array of event instances before they
              // get created.
              \Drupal::moduleHandler()->alter('recurring_events_event_instances_pre_create', $events_to_create, $event);

              if (!empty($events_to_create)) {
                foreach ($events_to_create as $weekly_event) {
                  $event_instances[] = $this->createEventInstance($event, $weekly_event['start_date'], $weekly_event['end_date']);
                }
              }
            }
          }
          break;

        case 'monthly':
          $dates = [];
          $time_parts = $this->convertTimeTo24hourFormat($form_data['time']);

          if (!empty($form_data['monthly_type'])) {
            $dates = [];
            switch ($form_data['monthly_type']) {
              case 'weekday':
                // Loop through each weekday occurrence and weekday.
                if (!empty($form_data['day_occurrence']) && !empty($form_data['days'])) {
                  foreach ($form_data['day_occurrence'] as $occurrence) {
                    foreach ($form_data['days'] as $weekday) {
                      // Find the occurrence of the specific weekdays within
                      // each month.
                      $day_occurrences = $this->findWeekdayOccurrencesBetweenDates($occurrence, $weekday, $form_data['start_date'], $form_data['end_date']);
                      $dates = array_merge($dates, $day_occurrences);
                    }
                  }
                }
                break;

              case 'monthday':
                foreach ($form_data['day_of_month'] as $day_of_month) {
                  $days_of_month = $this->findMonthDaysBetweenDates($day_of_month, $form_data['start_date'], $form_data['end_date']);
                  $dates = array_merge($dates, $days_of_month);
                }
                break;

            }

            // If valid recurring dates were found.
            if (!empty($dates)) {
              $events_to_create = [];
              foreach ($dates as $monthly_date) {
                // Set the time of the start date to be the hours and
                // minutes.
                $monthly_date->setTime($time_parts[0], $time_parts[1]);
                // Configure the timezone.
                $monthly_date->setTimezone($utc_timezone);
                // Create a clone of this date.
                $monthly_date_end = clone $monthly_date;
                // Add the number of seconds specified in the duration
                // field.
                $monthly_date_end->modify('+' . $form_data['duration'] . ' seconds');
                // Set this event to be created.
                $events_to_create[$monthly_date->format('r')] = [
                  'start_date' => $monthly_date,
                  'end_date' => $monthly_date_end,
                ];
              }

              // Allow modules to alter the array of event instances before they
              // get created.
              \Drupal::moduleHandler()->alter('recurring_events_event_instances_pre_create', $events_to_create, $event);

              if (!empty($events_to_create)) {
                foreach ($events_to_create as $monthly_event) {
                  $event_instances[] = $this->createEventInstance($event, $monthly_event['start_date'], $monthly_event['end_date']);
                }
              }
            }
          }
          break;

      }
    }

    // Create a message to indicate how many instances were changed.
    $this->messenger->addMessage($this->translation->translate('A total of %items event instances were created as part of this event series.', [
      '%items' => count($event_instances),
    ]));
    $event->set('event_instances', $event_instances);
  }

  /**
   * Convert a time from 12 hour format to 24 hour format.
   *
   * @var string $time
   *   The time to convert to 24 hour format.
   *
   * @return array
   *   An array of time parts.
   */
  public function convertTimeTo24hourFormat($time) {
    $time_parts = [];

    // Split the start time up to separate out hours and minutes.
    $time_parts = explode(':', $time);
    // If this is PM then add 12 hours to the hours, unless the time was
    // set as noon.
    if (strpos($time_parts[1], 'pm') !== FALSE && $time_parts[0] != '12') {
      $time_parts[0] += 12;
    }
    // If this is AM and the time was midnight, set hours to 00.
    elseif (strpos($time_parts[1], 'am') !== FALSE && $time_parts[0] == '12') {
      $time_parts[0] = '00';
    }
    // Strip out AM or PM from the time.
    $time_parts[1] = substr($time_parts[1], 0, -3);

    return $time_parts;
  }

  /**
   * Find all the weekday occurrences between two dates.
   *
   * @param string $weekday
   *   The name of the day of the week.
   * @param Drupal\Core\Datetime\DrupalDateTime $start_date
   *   The start date.
   * @param Drupal\Core\Datetime\DrupalDateTime $end_date
   *   The end date.
   *
   * @return array
   *   An array of matching dates.
   */
  public static function findWeekdaysBetweenDates($weekday, DrupalDateTime $start_date, DrupalDateTime $end_date) {
    $dates = [];

    // Clone the date as we do not want to make changes to the original object.
    $start = clone $start_date;
    $end = clone $end_date;

    // We want to create events up to and including the last day, so modify the
    // end date to be midnight of the next day.
    $end->modify('midnight next day');

    // If the start date is after the end date then we have an invalid range so
    // just return nothing.
    if ($start->getTimestamp() > $end->getTimestamp()) {
      return $dates;
    }

    // If the start date is not the weekday we are seeking, jump to the next
    // instance of that weekday.
    if ($start->format('l') != ucwords($weekday)) {
      $start->modify('next ' . $weekday);
    }

    // Loop through a week at a time, storing the date in the array to return
    // until the end date is surpassed.
    while ($start->getTimestamp() <= $end->getTimestamp()) {
      // If we do not clone here we end up modifying the value of start in
      // the array and get some funky dates returned.
      $dates[] = clone $start;
      $start->modify('+1 week');
    }

    return $dates;
  }

  /**
   * Find all the day-of-month occurrences between two dates.
   *
   * @param int $day_of_month
   *   The day of the month.
   * @param Drupal\Core\Datetime\DrupalDateTime $start_date
   *   The start date.
   * @param Drupal\Core\Datetime\DrupalDateTime $end_date
   *   The end date.
   *
   * @return array
   *   An array of matching dates.
   */
  public function findMonthDaysBetweenDates($day_of_month, DrupalDateTime $start_date, DrupalDateTime $end_date) {
    $dates = [];

    // Clone the date as we do not want to make changes to the original object.
    $start = clone $start_date;
    $end = clone $end_date;

    // We want to create events up to and including the last day, so modify the
    // end date to be midnight of the next day.
    $end->modify('midnight next day');

    // If the start date is after the end date then we have an invalid range so
    // just return nothing.
    if ($start->getTimestamp() > $end->getTimestamp()) {
      return $dates;
    }

    $day_to_check = $day_of_month;

    // If day of month is set to -1 that is the last day of the month, we need
    // to calculate how many days a month has.
    if ($day_of_month === '-1') {
      $day_to_check = $start->format('t');
    }

    // If the day of the month is after the start date.
    if ($start->format('d') < $day_to_check) {
      $new_date = clone $start;
      $curr_month = $new_date->format('m');
      $curr_year = $new_date->format('Y');

      // Check to see if that date is a valid date.
      if (!checkdate($curr_month, $day_to_check, $curr_year)) {
        // If not, go find the next valid date.
        $start = $this->findNextMonthDay($day_of_month, $start);
      }
      else {
        // This is a valid date, so let us start there.
        $start->setDate($curr_year, $curr_month, $day_to_check);
      }
    }
    // If the day of the month is in the past.
    elseif ($start->format('d') > $day_to_check) {
      // Find the next valid start date.
      $start = $this->findNextMonthDay($day_of_month, $start);
    }

    // Loop through each month checking to see if the day of the month is a
    // valid day, until the end date has been surpassed.
    while ($start->getTimestamp() <= $end->getTimestamp()) {
      // If we do not clone here we end up modifying the value of start in
      // the array and get some funky dates returned.
      $dates[] = clone $start;
      // Find the next valid event date.
      $start = $this->findNextMonthDay($day_of_month, $start);
    }

    return $dates;
  }

  /**
   * Find the next day-of-month occurrence.
   *
   * @param int $day_of_month
   *   The day of the month.
   * @param Drupal\Core\Datetime\DrupalDateTime $date
   *   The start date.
   *
   * @return Drupal\Core\Datetime\DrupalDateTime
   *   The next occurrence of a specific day of the month.
   */
  public function findNextMonthDay($day_of_month, DrupalDateTime $date) {
    $new_date = clone $date;

    $curr_month = $new_date->format('m');
    $curr_year = $new_date->format('Y');
    $next_month = $curr_month;
    $next_year = $curr_year;

    do {
      $next_month = ($next_month + 1) % 12 ?: 12;
      $next_year = $next_month == 1 ? $next_year + 1 : $next_year;

      // If the desired day of the month is the last day, calculate what that
      // day is.
      if ($day_of_month === '-1') {
        $new_date->setDate($next_year, $next_month, '1');
        $day_of_month = $new_date->format('t');
      }
    } while (checkdate($next_month, $day_of_month, $next_year) === FALSE);

    $new_date->setDate($next_year, $next_month, $day_of_month);
    return $new_date;
  }

  /**
   * Find all the monthly occurrences of a specific weekday between two dates.
   *
   * @param string $occurrence
   *   Which occurrence of the weekday to find.
   * @param string $weekday
   *   The name of the day of the week.
   * @param Drupal\Core\Datetime\DrupalDateTime $start_date
   *   The start date.
   * @param Drupal\Core\Datetime\DrupalDateTime $end_date
   *   The end date.
   *
   * @return array
   *   An array of matching dates.
   */
  public function findWeekdayOccurrencesBetweenDates($occurrence, $weekday, DrupalDateTime $start_date, DrupalDateTime $end_date) {
    $dates = [];

    // Clone the date as we do not want to make changes to the original object.
    $start = clone $start_date;

    // If the start date is after the end date then we have an invalid range so
    // just return nothing.
    if ($start->getTimestamp() > $end_date->getTimestamp()) {
      return $dates;
    }

    // Grab the occurrence of the weekday we want for this current month.
    $start->modify($occurrence . ' ' . $weekday . ' of this month');

    // Make sure we didn't just go back in time.
    if ($start < $start_date) {
      // Go straight to next month.
      $start->modify($occurrence . ' ' . $weekday . ' of next month');
    }

    // Loop through a week at a time, storing the date in the array to return
    // until the end date is surpassed.
    while ($start->getTimestamp() <= $end_date->getTimestamp()) {
      // If we do not clone here we end up modifying the value of start in
      // the array and get some funky dates returned.
      $dates[] = clone $start;
      $start->modify($occurrence . ' ' . $weekday . ' of next month');
    }

    return $dates;
  }

  /**
   * Create an event instance from an event series.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The stored event series entity.
   * @param Drupal\Core\Datetime\DrupalDateTime $start_date
   *   The start date and time of the event.
   * @param Drupal\Core\Datetime\DrupalDateTime $end_date
   *   The end date and time of the event.
   *
   * @return static
   *   The created event instance entity object.
   */
  public function createEventInstance(EventSeries $event, DrupalDateTime $start_date, DrupalDateTime $end_date) {
    $data = [
      'eventseries_id' => $event->id(),
      'date' => [
        'value' => $start_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
        'end_value' => $end_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
      ],
    ];

    \Drupal::moduleHandler()->alter('recurring_events_event_instance', $data);

    $entity = \Drupal::entityTypeManager()
      ->getStorage('eventinstance')
      ->create($data);

    $entity->save();

    return $entity;
  }

  /**
   * Get exclude/include dates from form.
   *
   * @param array $field
   *   The field from which to retrieve the dates.
   *
   * @return array
   *   An array of dates.
   */
  private function getDatesFromForm(array $field) {
    $dates = [];

    if (!empty($field)) {
      foreach ($field as $date) {
        if (!empty($date['value']['date']) && !empty($date['end_value']['date'])) {
          $dates[] = [
            'value' => $date['value']['date'],
            'end_value' => $date['end_value']['date'],
          ];
        }
      }
    }
    return $dates;
  }

  /**
   * Build a string from excluded or included date ranges.
   *
   * @var array $config
   *   The configuration from which to build a string.
   *
   * @return string
   *   The formatted date string.
   */
  private function buildDateString(array $config) {
    $string = '';

    $string_parts = [];
    if (!empty($config)) {
      foreach ($config as $date) {
        $range = $this->translation->translate('@start_date to @end_date', [
          '@start_date' => $date['value'],
          '@end_date' => $date['end_value'],
        ]);
        $string_parts[] = '(' . $range . ')';
      }

      $string = implode(', ', $string_parts);
    }
    return $string;
  }

}
