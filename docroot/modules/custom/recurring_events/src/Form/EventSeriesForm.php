<?php

namespace Drupal\recurring_events\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\recurring_events\EventCreationService;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Datetime\DateFormatter;

/**
 * Form controller for the eventseries entity create form.
 *
 * @ingroup recurring_events
 */
class EventSeriesForm extends ContentEntityForm {

  /**
   * The current step of the form.
   *
   * @var int
   */
  protected $step = 0;

  /**
   * The event creation service.
   *
   * @var \Drupal\recurring_events\EventCreationService
   */
  protected $creationService;

  /**
   * The entity storage interface.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('recurring_events.event_creation_service'),
      $container->get('entity_type.manager')->getStorage('eventseries'),
      $container->get('entity.manager'),
      $container->get('messenger'),
      $container->get('date.formatter')
    );
  }

  /**
   * Construct an EventSeriesForm.
   *
   * @param \Drupal\recurring_events\EventCreationService $creation_service
   *   The event creation service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The storage interface.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(EventCreationService $creation_service, EntityStorageInterface $storage, EntityManagerInterface $entity_manager, Messenger $messenger, DateFormatter $date_formatter) {
    $this->creationService = $creation_service;
    $this->storage = $storage;
    $this->messenger = $messenger;
    $this->dateFormatter = $date_formatter;
    parent::__construct($entity_manager);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $editing = ($form_state->getBuildInfo()['form_id'] == 'eventseries_edit_form');

    /* @var $entity \Drupal\recurring_events\Entity\EventSeries */
    $entity = $this->entity;

    $form['custom_date']['#states'] = [
      'visible' => [
        ':input[name="recur_type"]' => ['value' => 'custom'],
      ],
    ];

    if ($editing) {
      $original = $this->storage->loadUnchanged($entity->id());
      if ($this->step === 1) {
        $diff_array = $this->creationService->buildDiffArray($original, $form_state);

        if (!empty($diff_array)) {
          $this->step = 0;
          $form['diff'] = [
            '#type' => 'container',
            '#weight' => -10,
          ];

          $form['diff']['diff_title'] = [
            '#type' => '#markup',
            '#prefix' => '<h2>',
            '#markup' => $this->t('Confirm Date Changes'),
            '#suffix' => '</h2>',
          ];

          $form['diff']['diff_message'] = [
            '#type' => '#markup',
            '#prefix' => '<p>',
            '#markup' => $this->t('Recurrence configuration has been changed, as a result all instances will be removed and recreated. This action cannot be undone.'),
            '#suffix' => '</p>',
          ];

          $form['diff']['table'] = [
            '#type' => 'table',
            '#header' => [
              $this->t('Data'),
              $this->t('Stored'),
              $this->t('Overridden'),
            ],
            '#rows' => $diff_array,
          ];

          $form['diff']['confirm'] = [
            '#type' => 'submit',
            '#value' => $this->t('Confirm Date Changes'),
            '#submit' => [
              '::submitForm',
              '::save',
            ],
          ];
        }
      }
    }

    $form['advanced']['#attributes']['class'][] = 'entity-meta';

    $form['meta'] = [
      '#type' => 'details',
      '#group' => 'advanced',
      '#weight' => -10,
      '#title' => $this->t('Status'),
      '#attributes' => ['class' => ['entity-meta__header']],
      '#tree' => TRUE,
      '#access' => \Drupal::currentUser()->hasPermission('administer eventseries'),
    ];
    $form['meta']['published'] = [
      '#type' => 'item',
      '#markup' => $entity->isPublished() ? $this->t('Published') : $this->t('Not published'),
      '#access' => !$entity->isNew(),
      '#wrapper_attributes' => ['class' => ['entity-meta__title']],
    ];
    $form['meta']['changed'] = [
      '#type' => 'item',
      '#title' => $this->t('Last saved'),
      '#markup' => !$entity->isNew() ? $this->dateFormatter->format($entity->getChangedTime(), 'short') : $this->t('Not saved yet'),
      '#wrapper_attributes' => ['class' => ['entity-meta__last-saved']],
    ];
    $form['meta']['author'] = [
      '#type' => 'item',
      '#title' => $this->t('Author'),
      '#markup' => $entity->getOwner()->getUsername(),
      '#wrapper_attributes' => ['class' => ['entity-meta__author']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    /* @var $entity \Drupal\recurring_events\Entity\EventSeries */
    $entity = $this->entity;

    $editing = ($form_state->getBuildInfo()['form_id'] == 'eventseries_edit_form');
    $trigger = $form_state->getTriggeringElement();

    if ($trigger['#id'] !== 'edit-confirm' && $editing) {
      $original = $this->storage->loadUnchanged($entity->id());
      if ($this->creationService->checkForFormRecurConfigChanges($original, $form_state)) {
        $this->step = 1;
        $form_state->setRebuild(TRUE);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $original = NULL;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('revision') && $form_state->getValue('revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionUserId(\Drupal::currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    if (!$entity->isNew()) {
      $original = $this->storage->loadUnchanged($entity->id());
    }

    if ($entity->isDefaultTranslation()) {
      $this->messenger->addStatus($this->t('Successfully saved the %name event series', [
        '%name' => $entity->title->value,
      ]));
    }
    else {
      $this->messenger->addStatus($this->t('@language translation of the @type %label has been saved.', [
        '@language' => $entity->language()->getName(),
        '@type' => 'Event ',
        '%label' => $entity->getUntranslated()->title->value,
      ]));
    }

    parent::save($form, $form_state);
    $form_state->setRedirect('entity.eventseries.canonical', ['eventseries' => $entity->id()]);
  }

}
