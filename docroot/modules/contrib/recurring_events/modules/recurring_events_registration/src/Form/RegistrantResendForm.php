<?php

namespace Drupal\recurring_events_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\recurring_events_registration\RegistrationCreationService;
use Drupal\recurring_events_registration\NotificationService;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Mail\MailManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\recurring_events\Entity\EventInstance;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a form for resending Registrant registration emails.
 *
 * @ingroup recurring_events_registration
 */
class RegistrantResendForm extends FormBase {

  /**
   * The request stack object.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * The registration creation service.
   *
   * @var \Drupal\recurring_events_registration\RegistrationCreationService
   */
  protected $creationService;

  /**
   * The registration notification service.
   *
   * @var \Drupal\recurring_events_registration\NotificationService
   */
  protected $notificationService;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $mail;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * The event instance object.
   *
   * @var \Drupal\recurring_events\Entity\EventInstance
   */
  protected $eventInstance;

  /**
   * The registrant object.
   *
   * @var \Drupal\recurring_events_registration\Entity\Registrant
   */
  protected $registrant;

  /**
   * Constructs a ContactForm object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   The request object.
   * @param \Drupal\recurring_events_registration\RegistrationCreationService $creation_service
   *   The registration creation service.
   * @param \Drupal\recurring_events_registration\NotificationService $notification_service
   *   The registration notification service.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   * @param \Drupal\Core\Mail\MailManager $mail
   *   The mail manager service.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer service.
   */
  public function __construct(RequestStack $request, RegistrationCreationService $creation_service, NotificationService $notification_service, Messenger $messenger, MailManager $mail, Renderer $renderer) {
    $this->request = $request;
    $this->creationService = $creation_service;
    $this->notificationService = $notification_service;
    $this->messenger = $messenger;
    $this->mail = $mail;
    $this->renderer = $renderer;

    $request = $this->request->getCurrentRequest();
    $params = $request->attributes->all();
    if (!empty($params['eventinstance']) && !empty($params['registrant'])) {
      $this->eventInstance = $params['eventinstance'];
      $this->creationService->setEventInstance($this->eventInstance);

      $this->registrant = $params['registrant'];
      $key = 'registration_notification';
      if ($this->registrant->getWaitlist() == '1') {
        $key = 'waitlist_notification';
      }
      $this->notificationService->setEntity($this->registrant)->setKey($key);
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('recurring_events_registration.creation_service'),
      $container->get('recurring_events_registration.notification_service'),
      $container->get('messenger'),
      $container->get('plugin.manager.mail'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'recurring_events_registration_resend_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['resend'] = [
      '#type' => 'container',
      '#weight' => -99,
      'title' => [
        '#type' => 'markup',
        '#prefix' => '<h2 class="registration-register-title">',
        '#markup' => $this->t('Resend Registration Email'),
        '#suffix' => '</h2>',
      ],
      'intro' => [
        '#type' => 'markup',
        '#prefix' => '<p class=registration-register-intro">',
        '#markup' => $this->t('You are resending the registration email for %email for %event.', [
          '%email' => $this->registrant->email->value,
          '%event' => $this->eventInstance->title->value,
        ]),
        '#suffix' => '</p>',
      ],
    ];

    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Subject'),
      '#description' => $this->t('The subject of the email to send to the registrant'),
      '#default_value' => $this->notificationService->getSubject(FALSE),
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Email Message'),
      '#description' => $this->t('The message for the email to send to the registrant'),
      '#default_value' => $this->notificationService->getMessage(FALSE),
      '#required' => TRUE,
    ];

    $form['tokens'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['form-item'],
      ],
      'tokens' => $this->notificationService->getAvailableTokens(),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Resend Email'),
    ];

    $link = Link::fromTextAndUrl($this->t('Go Back to Registration List'), new Url('entity.registrant.instance_listing', [
      'eventinstance' => $this->eventInstance->id(),
    ]));

    $form['back_link'] = [
      '#type' => 'markup',
      '#prefix' => '<span class="register-back-link">',
      '#markup' => $link->toString(),
      '#suffix' => '</span>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getUserInput();

    $params = [
      'subject' => $values['subject'],
      'body' => $values['message'],
      'registrant' => $this->registrant,
    ];

    $to = $this->registrant->mail->value;
    $this->mail->mail('recurring_events_registration', 'custom', $to, \Drupal::languageManager()->getDefaultLanguage()->getId(), $params);
    $this->messenger->addMessage($this->t('Registrant email successfully resent.'));
  }

}
