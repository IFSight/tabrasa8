<?php

namespace Drupal\recurring_events\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class IncludedDatesForm.
 */
class IncludedDatesForm extends EntityForm {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * Construct an FieldInheritanceForm.
   *
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   */
  public function __construct(Messenger $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['#attached']['library'][] = 'recurring_events/recurring_events.date_form';

    $included_dates = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $included_dates->label(),
      '#description' => $this->t("Label for the Included dates."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $included_dates->id(),
      '#machine_name' => [
        'exists' => '\Drupal\recurring_events\Entity\IncludedDates::load',
      ],
      '#disabled' => !$included_dates->isNew(),
    ];

    $form['start'] = [
      '#type' => 'date',
      '#title' => $this->t('Start date'),
      '#description' => $this->t('Enter the start date of this inclusion range'),
      '#default_value' => $included_dates->start(),
      '#required' => TRUE,
    ];

    $form['end'] = [
      '#type' => 'date',
      '#title' => $this->t('End date'),
      '#description' => $this->t('Enter the end date of this inclusion range'),
      '#default_value' => $included_dates->end(),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $included_dates = $this->entity;
    $status = $included_dates->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger->addMessage($this->t('Created the %label Included dates.', [
          '%label' => $included_dates->label(),
        ]));
        break;

      default:
        $this->messenger->addMessage($this->t('Saved the %label Included dates.', [
          '%label' => $included_dates->label(),
        ]));
    }
    $form_state->setRedirectUrl($included_dates->toUrl('collection'));
  }

}
