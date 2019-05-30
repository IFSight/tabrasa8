<?php

namespace Drupal\recurring_events_registration\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\recurring_events\Entity\EventInstance;
use Drupal\recurring_events\Entity\EventSeries;

/**
 * Defines the Registrant entity.
 *
 * @ingroup recurring_events_registration
 *
 * @ContentEntityType(
 *   id = "registrant",
 *   label = @Translation("Registrant"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\recurring_events_registration\RegistrantListBuilder",
 *     "views_data" = "Drupal\recurring_events_registration\Entity\RegistrantViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\recurring_events_registration\Form\RegistrantForm",
 *       "add" = "Drupal\recurring_events_registration\Form\RegistrantForm",
 *       "edit" = "Drupal\recurring_events_registration\Form\RegistrantForm",
 *       "delete" = "Drupal\recurring_events_registration\Form\RegistrantDeleteForm",
 *       "anon-edit" = "Drupal\recurring_events_registration\Form\RegistrantForm",
 *       "anon-delete" = "Drupal\recurring_events_registration\Form\RegistrantDeleteForm"
 *     },
 *     "access" = "Drupal\recurring_events_registration\RegistrantAccessControlHandler",
 *   },
 *   base_table = "registrant",
 *   translatable = FALSE,
 *   fieldable = TRUE,
 *   admin_permission = "administer registrant entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *   },
 *   links = {
 *     "canonical" = "/events/{eventinstance}/registrant/{registrant}",
 *     "edit-form" = "/events/{eventinstance}/registrant/{registrant}/edit",
 *     "delete-form" = "/events/{eventinstance}/registrant/{registrant}/delete",
 *     "anon-edit-form" = "/events/{eventinstance}/registrant/{registrant}/{uuid}/edit",
 *     "anon-delete-form" = "/events/{eventinstance}/registrant/{registrant}/{uuid}/delete"
 *   },
 *   field_ui_base_route = "registrant.settings"
 * )
 */
class Registrant extends ContentEntityBase implements RegistrantInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    if (!$update) {
      $key = 'registration_notification';
      if ($this->getWaitlist()) {
        $key = 'waitlist_notification';
      }
      recurring_events_registration_send_notification($key, $this);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Registrant entity.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email Address'))
      ->setDescription(t('The email address of the registrant'))
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => -6,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the registrant entity.'))
      ->setReadOnly(TRUE);

    $fields['eventseries_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Event Series ID'))
      ->setDescription(t('The ID of the eventseries entity.'))
      ->setSetting('target_type', 'eventseries');

    $fields['eventinstance_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Event Instance ID'))
      ->setDescription(t('The ID of the eventinstance entity.'))
      ->setSetting('target_type', 'eventinstance');

    $fields['waitlist'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Waitlist'))
      ->setDescription(t('Whether this registrant is waitlisted.'));

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type'))
      ->setDescription(t('The type of registration this is: series or instance'))
      ->setSettings([
        'default_value' => 'series',
        'max_length' => 255,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * Get the event series.
   *
   * @return Drupal\recurring_events\Entity\EventSeries
   *   The event series entity.
   */
  public function getEventSeries() {
    return $this->get('eventseries_id')->entity;
  }

  /**
   * Set the event series ID.
   *
   * @param Drupal\recurring_events\Entity\EventSeries $event
   *   The event series entity.
   *
   * @return Drupal\recurring_events_registration\Entity\RegistrantInterface
   *   The registrant entity.
   */
  public function setEventSeries(EventSeries $event) {
    $this->set('eventseries_id', $event->id());
    return $this;
  }

  /**
   * Get the event.
   *
   * @return Drupal\recurring_events\Entity\EventInstance
   *   The eventinstance entity.
   */
  public function getEventInstance() {
    return $this->get('eventinstance_id')->entity;
  }

  /**
   * Set the event ID.
   *
   * @param Drupal\recurring_events\Entity\EventInstance $event
   *   The eventinstance entity.
   *
   * @return Drupal\recurring_events_registration\Entity\RegistrantInterface
   *   The registrant entity.
   */
  public function setEventInstance(EventInstance $event) {
    $this->set('eventinstance_id', $event->id());
    return $this;
  }

  /**
   * Get registration type.
   *
   * @return string
   *   The type of registration, series or instance.
   */
  public function getRegistrationType() {
    return $this->get('type')->value;
  }

  /**
   * Set the registration type.
   *
   * @param string $type
   *   The type of registration, series or instance.
   *
   * @return Drupal\recurring_events_registration\Entity\RegistrantInterface
   *   The registrant entity.
   */
  public function setRegistrationType($type) {
    $this->set('type', $type);
    return $this;
  }

  /**
   * Get the event.
   *
   * @return int
   *   Whether the registrant is on the waitlist.
   */
  public function getWaitlist() {
    return $this->get('waitlist')->value;
  }

  /**
   * Set the waitlist.
   *
   * @param int $waitlist
   *   Whether the registrant is on the waitlist.
   *
   * @return Drupal\recurring_events_registration\Entity\RegistrantInterface
   *   The registrant entity.
   */
  public function setWaitlist($waitlist) {
    $this->set('waitlist', $waitlist);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    $uri_route_parameters['eventinstance'] = $this->getEventInstance()->id();
    $uri_route_parameters['registrant'] = $this->id();
    if ($rel == 'anon-edit-form' || $rel == 'anon-delete-form') {
      $uri_route_parameters['uuid'] = $this->uuid->value;
    }
    return $uri_route_parameters;
  }

}
