<?php

namespace Drupal\recurring_events_registration;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\recurring_events\Entity\EventInstance;
use Drupal\recurring_events\Entity\EventSeries;
use Drupal\Core\Messenger\Messenger;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * RegistrationCreationService class.
 */
class RegistrationCreationService {

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
   * The entity storage for registrants.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Event instance entity.
   *
   * @var \Drupal\recurring_events\Entity\EventInstance
   */
  protected $eventInstance;

  /**
   * Event series entity.
   *
   * @var \Drupal\recurring_events\Entity\EventSeries
   */
  protected $eventSeries;

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
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(TranslationInterface $translation, Connection $database, LoggerChannelFactoryInterface $logger, Messenger $messenger, EntityTypeManager $entity_type_manager) {
    $this->translation = $translation;
    $this->database = $database;
    $this->loggerFactory = $logger->get('recurring_events_registration');
    $this->messenger = $messenger;
    $this->storage = $entity_type_manager->getStorage('registrant');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('string_translation'),
      $container->get('database'),
      $container->get('logger.factory'),
      $container->get('messenger'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Set the event entities.
   *
   * @param Drupal\recurring_events\Entity\EventInstance $event_instance
   *   The event instance.
   */
  public function setEventInstance(EventInstance $event_instance) {
    $this->eventInstance = $event_instance;
    $this->eventSeries = $event_instance->getEventSeries();
  }

  /**
   * Set the event series, helpful to get a fresh copy of the entity.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event_series
   *   The event series.
   */
  public function setEventSeries(EventSeries $event_series) {
    $this->eventSeries = $event_series;
  }

  /**
   * Retreive all registered parties.
   *
   * @param bool $include_nonwaitlisted
   *   Whether or not to include non-waitlisted registrants.
   * @param bool $include_waitlisted
   *   Whether or not to include waitlisted registrants.
   * @param int $uid
   *   The user ID for whom to retrieve registrants.
   *
   * @return array
   *   An array of registrants.
   */
  public function retrieveRegisteredParties($include_nonwaitlisted = TRUE, $include_waitlisted = TRUE, $uid = FALSE) {
    $parties = [];
    $properties = [];

    if ($include_nonwaitlisted && !$include_waitlisted) {
      $properties['waitlist'] = 0;
    }
    elseif (!$include_nonwaitlisted && $include_waitlisted) {
      $properties['waitlist'] = 1;
    }

    if (!$include_waitlisted) {
      $properties['waitlist'] = 0;
    }

    if ($uid) {
      $properties['user_id'] = $uid;
    }

    switch ($this->getRegistrationType()) {
      case 'series':
        $properties['eventseries_id'] = $this->eventSeries->id();
        break;

      case 'instance':
        $properties['eventinstance_id'] = $this->eventInstance->id();
        break;
    }
    $results = $this->storage->loadByProperties($properties);

    if (!empty($results)) {
      $parties = $results;
    }
    return $parties;
  }

  /**
   * Retreive all registered parties for a series.
   *
   * @return array
   *   An array of registrants.
   */
  public function retrieveAllSeriesRegisteredParties() {
    $parties = [];
    $properties = [
      'eventseries_id' => $this->eventSeries->id(),
    ];

    $results = $this->storage->loadByProperties($properties);

    if (!empty($results)) {
      $parties = $results;
    }
    return $parties;
  }

  /**
   * Get registration availability.
   *
   * @return int
   *   The number of spaces available for registration.
   */
  public function retrieveAvailability() {
    $availability = 0;
    $parties = $this->retrieveRegisteredParties(TRUE, FALSE);

    $capacity = $this->eventSeries->event_registration->capacity;
    if (empty($capacity)) {
      $capacity = 0;
    }
    $availability = $capacity - count($parties);
    if ($availability < 0) {
      $availability = 0;
    }
    return $availability;
  }

  /**
   * Get whether this event has a waitlist.
   *
   * @return bool
   *   Whether or not there is a waitlist for this event.
   */
  public function hasWaitlist() {
    $waitlist = FALSE;
    if (!empty($this->eventSeries->event_registration->waitlist)) {
      $waitlist = (bool) $this->eventSeries->event_registration->waitlist;
    }
    return $waitlist;
  }

  /**
   * Get whether this event has registration.
   *
   * @return bool
   *   Whether or not registration is open for this event.
   */
  public function hasRegistration() {
    $registration = FALSE;
    if (!empty($this->eventSeries->event_registration->registration)) {
      $registration = (bool) $this->eventSeries->event_registration->registration;
    }
    return $registration;
  }

  /**
   * Get registration date range.
   *
   * @return array
   *   The registration date range array.
   */
  public function getRegistrationDateRange() {
    $date_range = [];

    $value = $this->eventSeries->event_registration->getValue();
    if (!empty($value)) {
      $date_range['value'] = $value['value'];
      $date_range['end_value'] = $value['end_value'];
    }

    return $date_range;
  }

  /**
   * Has the user registered for this event before.
   *
   * @param int $uid
   *   The ID of the user.
   *
   * @return bool
   *   Whether this user has already registered for this event.
   */
  public function hasUserRegisteredById($uid) {
    $properties = [];

    $registrants = $this->retrieveRegisteredParties(TRUE, TRUE, $uid);
    return !empty($registrants);
  }

  /**
   * Retreive all waitlisted users.
   *
   * @return array
   *   An array of Drupal\recurring_events_registration\Entity\Registrant users.
   */
  public function retrieveWaitlistedParties() {
    $parties = [];
    $registrants = $this->retrieveRegisteredParties(FALSE, TRUE);
    if (!empty($registrants)) {
      $parties = $registrants;
    }
    return $parties;
  }

  /**
   * Retreive first user on the waitlist.
   *
   * @return Drupal\recurring_events_registration\Entity\Registrant
   *   A fully loaded registrant entity.
   */
  public function retrieveFirstWaitlistParty() {
    $waitlisted_users = $this->retrieveWaitlistedParties();
    if (!empty($waitlisted_users)) {
      $first = reset($waitlisted_users);
      \Drupal::moduleHandler()->alter('recurring_events_registration_first_waitlist', $first);
      return $first;
    }
    return NULL;
  }

  /**
   * Get registration type.
   *
   * @return string
   *   The type of registration: series, or instance.
   */
  public function getRegistrationType() {
    $type = FALSE;

    if (!empty($this->eventSeries->event_registration->registration_type)) {
      $type = $this->eventSeries->event_registration->registration_type;
    }

    return $type;
  }

  /**
   * Get registration dates type.
   *
   * @return string
   *   The type of registration dates: open, or scheduled.
   */
  public function getRegistrationDatesType() {
    $type = FALSE;

    if (!empty($this->eventSeries->event_registration->registration_dates)) {
      $type = $this->eventSeries->event_registration->registration_dates;
    }

    return $type;
  }

  /**
   * Get registration time.
   *
   * @return string
   *   The time before each event that registration opens.
   */
  public function getRegistrationTime() {
    $time = FALSE;

    if (!empty($this->eventSeries->event_registration->time_amount) && !empty($this->getRegistrationTimeUnit())) {
      $time = $this->eventSeries->event_registration->time_amount . ' ' . $this->getRegistrationTimeUnit();
    }

    return $time;
  }

  /**
   * Get registration time unit.
   *
   * @return string
   *   The unit used to define the registration time, days or hours.
   */
  public function getRegistrationTimeUnit() {
    $unit = FALSE;

    if (!empty($this->eventSeries->event_registration->time_type)) {
      $unit = $this->eventSeries->event_registration->time_type;
    }

    return $unit;
  }

  /**
   * Is registration open for this event?
   *
   * @return bool
   *   Whether or not registration is open for this event.
   */
  public function registrationIsOpen() {
    $registration = FALSE;
    if ($this->hasRegistration()) {
      $now = new DrupalDateTime();

      $reg_open_close_dates = $this->registrationOpeningClosingTime();

      if (!empty($reg_open_close_dates)) {
        $registration = (
          $now->getTimestamp() >= $reg_open_close_dates['reg_open']->getTimestamp()
          && $now->getTimestamp() < $reg_open_close_dates['reg_close']->getTimestamp()
        );
      }
    }
    return $registration;
  }

  /**
   * Get registration opening date and time.
   *
   * @return array
   *   An array of drupal date time objects for when registration opens/closes.
   */
  public function registrationOpeningClosingTime() {
    $reg_dates = FALSE;

    // Does this event even have registration?
    if ($this->hasRegistration()) {
      // Grab the type of registration and the type of dates.
      $reg_type = $this->getRegistrationType();
      $reg_dates_type = $this->getRegistrationDatesType();

      $timezone = new \DateTimeZone(drupal_get_user_timezone());
      $utc_timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);

      $now = new DrupalDateTime();

      switch ($reg_dates_type) {
        case 'open':
          // For series, the event registration should close when the first
          // event in that series begins. For instance registration the event
          // registration should close when that instance begins.
          switch ($reg_type) {
            case 'series':
              $event_date = $this->eventSeries->getSeriesStart();
              break;

            case 'instance':
              $event_date = $this->eventInstance->date->start_date;
              break;
          }

          $event_date->setTimezone($timezone);

          $reg_dates = [
            'reg_open' => $now,
            'reg_close' => $event_date,
          ];
          break;

        case 'scheduled':
          // The two registration types are 'series' or 'instance'.
          switch ($reg_type) {
            case 'series':
              $reg_date_range = $this->getRegistrationDateRange();

              if (!empty($reg_date_range)) {
                $reg_start = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $reg_date_range['value'], $utc_timezone);
                $reg_end = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $reg_date_range['end_value'], $utc_timezone);
                $reg_start->setTimezone($timezone);
                $reg_end->setTimezone($timezone);
              }
              break;

            case 'instance':
              $reg_time_string = $this->getRegistrationTime();

              if (!empty($reg_time_string)) {
                $event_date = $this->eventInstance->date->start_date;

                $reg_end = clone $event_date;
                $reg_start = clone $event_date;

                // Subtract the number of days/hours from the event start date.
                $reg_start->modify('-' . $reg_time_string);
              }
              break;
          }

          $reg_dates = [
            'reg_open' => $reg_start,
            'reg_close' => $reg_end,
          ];
          break;
      }
    }
    return $reg_dates;
  }

  /**
   * Promote a registrant from the waitlist.
   */
  public function promoteFromWaitlist() {
    if (!$this->hasWaitlist()) {
      return;
    }

    if ($this->retrieveAvailability() > 0) {
      $first_waitlist = $this->retrieveFirstWaitlistParty();
      if (!empty($first_waitlist)) {
        $first_waitlist->setWaitlist('0');
        $first_waitlist->save();

        $key = 'promotion_notification';
        recurring_events_registration_send_notification($key, $first_waitlist);
      }
    }
  }

}
