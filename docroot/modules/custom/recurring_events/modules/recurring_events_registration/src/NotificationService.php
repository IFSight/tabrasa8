<?php

namespace Drupal\recurring_events_registration;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Utility\Token;
use Drupal\recurring_events_registration\Entity\RegistrantInterface;
use Drupal\Core\Extension\ModuleHandler;

/**
 * NotificationService class.
 */
class NotificationService {

  /**
   * The translation interface.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  private $translation;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

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
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * The registrant entity.
   *
   * @var \Drupal\recurring_events_registration\Entity\RegistrantInterface
   */
  protected $entity;

  /**
   * The email key.
   *
   * @var string
   */
  protected $key;

  /**
   * The email subject.
   *
   * @var string
   */
  protected $subject;

  /**
   * The email message.
   *
   * @var string
   */
  protected $message;

  /**
   * The from address.
   *
   * @var string
   */
  protected $from;

  /**
   * The config name.
   *
   * @var string
   */
  protected $configName;

  /**
   * Whether this is a custom or configure email.
   *
   * @var bool
   */
  protected $custom = FALSE;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   *   The translation interface.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger factory.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The module handler service.
   */
  public function __construct(TranslationInterface $translation, ConfigFactory $config_factory, LoggerChannelFactoryInterface $logger, Messenger $messenger, Token $token, ModuleHandler $module_handler) {
    $this->translation = $translation;
    $this->configFactory = $config_factory;
    $this->loggerFactory = $logger->get('recurring_events_registration');
    $this->messenger = $messenger;
    $this->token = $token;
    $this->moduleHandler = $module_handler;
    $this->configName = 'recurring_events_registration.registrant.config';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('string_translation'),
      $container->get('config.factory'),
      $container->get('logger.factory'),
      $container->get('messenger'),
      $container->get('token'),
      $container->get('module_handler'),
      $container->get('string_translation')
    );
  }

  /**
   * Set the registrant entity.
   *
   * @param \Drupal\recurring_events_registration\Entity\RegistrantInterface $registrant
   *   The registrant entity.
   *
   * @return $this
   *   The NotificationService object.
   */
  public function setEntity(RegistrantInterface $registrant) {
    $this->entity = $registrant;
    return $this;
  }

  /**
   * Set the email key.
   *
   * @param string $key
   *   The email key to use.
   *
   * @return $this
   */
  public function setKey($key) {
    $this->key = $key;
    if ($this->key === 'custom') {
      $this->custom = TRUE;
    }
    return $this;
  }

  /**
   * Set the email subject.
   *
   * @param string $subject
   *   The email subject line.
   *
   * @return $this
   */
  public function setSubject($subject) {
    $this->subject = $subject;
    return $this;
  }

  /**
   * Set the email message.
   *
   * @param string $message
   *   The email message.
   *
   * @return $this
   */
  public function setMessage($message) {
    $this->message = $message;
    return $this;
  }

  /**
   * Set the email from address.
   *
   * @param string $from
   *   The from email address.
   *
   * @return $this
   */
  public function setFrom($from) {
    $this->from = $from;
    return $this;
  }

  /**
   * Set the config name.
   *
   * @param string $name
   *   The name of the config value to use.
   *
   * @return $this
   */
  public function setConfigName($name) {
    $this->configName = $name;
    return $this;
  }

  /**
   * Get the key.
   *
   * @return string|bool
   *   The key, or FALSE if not set.
   */
  protected function getKey() {
    if (empty($this->key)) {
      $this->messenger->addError($this->translation->translate('No key defined for @module notifications.', [
        '@module' => 'recurring_events_registration',
      ]));
      $this->loggerFactory->error('No key defined @module notifications. Call @function before proceding.', [
        '@module' => 'recurring_events_registration',
        '@function' => 'NotificationService::setKey()',
      ]);
      return FALSE;
    }
    return $this->key;
  }

  /**
   * Get the config name.
   *
   * @return string
   *   The name of the config element.
   */
  protected function getConfigName() {
    if (empty($this->configName)) {
      $this->messenger->addError($this->translation->translate('No config name defined for @module notifications.', [
        '@module' => 'recurring_events_registration',
      ]));
      $this->loggerFactory->error('No config name defined for @module notifications. Call @function before proceding.', [
        '@module' => 'recurring_events_registration',
        '@function' => 'NotificationService::setConfigName()',
      ]);
      return FALSE;
    }
    return $this->configName;
  }

  /**
   * Retrieve config value.
   *
   * @var string $name
   *   The name of the config value to retrieve
   *
   * @return string|bool
   *   Return the config value, or FALSE if not set.
   */
  protected function getConfigValue($name) {
    $value = FALSE;
    if (!is_null($this->configFactory->get($this->getConfigName())->get($name))) {
      $value = $this->configFactory->get($this->getConfigName())->get($name);
    }

    return $value;
  }

  /**
   * Get the from address.
   *
   * @return string
   *   The from address.
   */
  public function getFrom() {
    $key = $this->getKey();
    if ($key) {
      $from = $this->from;
      if (empty($from)) {
        $from = $this->configFactory->get('system.site')->get('mail');
        $this->setFrom($from);
      }

      if (empty($from)) {
        $this->messenger->addError($this->translation->translate('No default from address configured. Please check the system.site mail config.'));
        return '';
      }
      return $from;
    }
    return '';
  }

  /**
   * Check notification is enabled.
   *
   * @return bool
   *   Returns TRUE if enabled, FALSE otherwise.
   */
  public function isEnabled() {
    $key = $this->getKey();
    if ($this->custom) {
      return TRUE;
    }
    if ($key) {
      $value = $key . '_enabled';
      return (bool) $this->getConfigValue($value);
    }
    return FALSE;
  }

  /**
   * Get the email subject.
   *
   * @param bool $parse_tokens
   *   Whether or not to parse out the tokens.
   *
   * @return string
   *   The email subject line.
   */
  public function getSubject($parse_tokens = TRUE) {
    $key = $this->getKey();
    if ($key) {
      $subject = $this->subject;
      if (empty($subject)) {
        $value = $key . '_subject';
        $subject = $this->getConfigValue($value);
        $this->setSubject($subject);
      }

      if (empty($subject)) {
        $this->messenger->addError($this->translation->translate('No default subject configured for @key emails in @config_name.', [
          '@key' => $key,
          '@config_name' => $this->getConfigName(),
        ]));
        return '';
      }

      if ($parse_tokens) {
        return $this->parseTokenizedString($subject);
      }
      return $subject;
    }
    return '';
  }

  /**
   * Get the email message.
   *
   * @param bool $parse_tokens
   *   Whether or not to parse out the tokens.
   *
   * @return string
   *   The email message.
   */
  public function getMessage($parse_tokens = TRUE) {
    $key = $this->getKey();
    if ($key) {
      $message = $this->message;
      if (empty($message)) {
        $value = $key . '_body';
        $message = $this->getConfigValue($value);
        $this->setMessage($message);
      }

      if (empty($message)) {
        $this->messenger->addError($this->translation->translate('No default body configured for @key emails in @config_name.', [
          '@key' => $key,
          '@config_name' => $this->getConfigName(),
        ]));
        return '';
      }

      if ($parse_tokens) {
        return $this->parseTokenizedString($message);
      }
      return $message;
    }
    return '';
  }

  /**
   * Parse a tokenized string.
   *
   * @var string $string
   *   The string to parse.
   *
   * @return string
   *   The parsed string.
   */
  public function parseTokenizedString($string) {
    $data = [
      'registrant' => $this->entity,
      'eventinstance' => $this->entity->getEventInstance(),
      'eventseries' => $this->entity->getEventSeries(),
    ];
    return $this->token->replace($string, $data);
  }

  /**
   * Get available tokens form element.
   *
   * @return array
   *   A render array to render on the site.
   */
  public function getAvailableTokens() {
    $relevant_tokens = [
      'eventseries',
      'eventinstance',
      'registrant',
    ];

    if ($this->moduleHandler->moduleExists('token')) {
      $token_help = [
        '#theme' => 'token_tree_link',
        '#token_types' => $relevant_tokens,
      ];
    }
    else {
      $all_tokens = $this->token->getInfo();
      $tokens = [];
      foreach ($relevant_tokens as $token_prefix) {
        if (!empty($all_tokens['tokens'][$token_prefix])) {
          foreach ($all_tokens['tokens'][$token_prefix] as $token_key => $value) {
            $tokens[] = '[' . $token_prefix . ':' . $token_key . ']';
          }
        }
      }

      $token_text = $this->translation->translate('Available tokens are: @tokens', [
        '@tokens' => implode(', ', $tokens),
      ]);

      $token_help = [
        '#type' => 'markup',
        '#markup' => $token_text->render(),
      ];
    }

    return $token_help;
  }

}
