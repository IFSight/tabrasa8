<?php

namespace Drupal\webform;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Utility\Token;
use Drupal\webform\Utility\WebformFormHelper;

/**
 * Defines a class to manage token replacement.
 */
class WebformTokenManager implements WebformTokenManagerInterface {

  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * Constructs a WebformTokenManager object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration object factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   */
  public function __construct(AccountInterface $current_user, LanguageManagerInterface $language_manager, ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, Token $token) {
    $this->currentUser = $current_user;
    $this->languageManager = $language_manager;
    $this->configFactory = $config_factory;
    $this->moduleHandler = $module_handler;
    $this->token = $token;

    $this->config = $this->configFactory->get('webform.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function replace($text, EntityInterface $entity = NULL, array $data = [], array $options = [], BubbleableMetadata $bubbleable_metadata = NULL) {
    // Replace tokens within an array.
    if (is_array($text)) {
      foreach ($text as $key => $token_value) {
        $text[$key] = $this->replace($token_value, $entity, $data, $options);
      }
      return $text;
    }

    // Most strings won't contain tokens so let's check and return ASAP.
    if (!is_string($text) || strpos($text, '[') === FALSE) {
      return $text;
    }

    if ($entity) {
      // Replace @deprecated [webform-submission] with [webform_submission].
      $text = str_replace('[webform-submission:', '[webform_submission:', $text);

      // Set token data based on entity type.
      $this->setTokenData($data, $entity);

      // Set token options based on entity.
      $this->setTokenOptions($options, $entity);
    }

    // For anonymous users remove all [current-user] tokens to prevent
    // anonymous user properties from being displayed.
    // For example, the [current-user:display-name] token will return
    // 'Anonymous', which is not an expected behavior.
    if ($this->currentUser->isAnonymous() && strpos($text, '[current-user:') !== FALSE) {
      $text = preg_replace('/\[current-user:[^]]+\]/', '', $text);
    }

    // Initializ token suffixes by wrapping them in temp
    // {webform-token-suffixes} tags.
    //
    // [webform:token:clear:urlencode] becomes
    // {webform-token-suffixes:clear:urlencode}[webform:token]{/webform-token-suffixes}.
    if (preg_match_all('/\[(.+?)((?::clear|:htmldecode|:urlencode|:striptags)+)\]/', $text, $matches)) {
      foreach ($matches[0] as $index => $match) {
        $token_value = $matches[1][$index];
        $token_suffixes = $matches[2][$index];
        $token_wrapper = '{webform-token-suffixes' . $token_suffixes . '}[' . $token_value . ']{/webform-token-suffixes}';
        $text = str_replace($match, $token_wrapper, $text);
      }
    }

    // Replace the webform related tokens.
    $text = $this->token->replace($text, $data, $options, $bubbleable_metadata);

    // Process token suffixes.
    if (preg_match_all('/{webform-token-suffixes:([^}]+)}(.*?){\/webform-token-suffixes}/ms', $text, $matches)) {
      foreach ($matches[0] as $index => $match) {
        $token_search = $matches[0][$index];
        $token_replace = $matches[2][$index];

        $token_value = $matches[2][$index];
        $token_suffixes = explode(':', $matches[1][$index]);
        $token_suffixes = array_combine($token_suffixes, $token_suffixes);

        // If token is not replaced then only the :clear suffix is applicable.
        if (preg_match('/^\[[^}]+\]$/', $token_value)) {
          // Clear token text or restore the original token.
          $token_original = str_replace(']', ':' . $matches[1][$index] . ']', $token_value);
          $token_replace = (isset($token_suffixes['clear'])) ? '' : $token_original;
        }
        else {
          // Decode and XSS filter value first.
          if (isset($token_suffixes['htmldecode'])) {
            $token_replace = html_entity_decode($token_replace);
            $token_replace = (isset($token_suffixes['striptags'])) ? strip_tags($token_replace) : html_entity_decode(Xss::filterAdmin($token_replace));
          }
          // Encode value second.
          if (isset($token_suffixes['urlencode'])) {
            $token_replace = urlencode($token_replace);
          }
        }

        $text = str_replace($token_search, $token_replace, $text);
      }
    }

    // Clear current user tokens for undefined values.
    if (strpos($text, '[current-user:') !== FALSE) {
      $text = preg_replace('/\[current-user:[^\]]+\]/', '', $text);
    }

    return $text;
  }

  /**
   * {@inheritdoc}
   */
  public function replaceNoRenderContext($text, EntityInterface $entity = NULL, array $data = [], array $options = []) {
    // Create BubbleableMetadata object which will be ignored.
    $bubbleable_metadata = new BubbleableMetadata();
    return $this->replace($text, $entity, $data, $options, $bubbleable_metadata);
  }

  /**
   * {@inheritdoc}
   */
  public function buildTreeLink(array $token_types = ['webform', 'webform_submission', 'webform_handler']) {
    if (!$this->moduleHandler->moduleExists('token')) {
      return [
        '#type' => 'link',
        '#title' => $this->t('You may use tokens.'),
        '#url' => Url::fromUri('https://www.drupal.org/project/token'),
      ];
    }
    else {
      return [
        '#theme' => 'token_tree_link',
        '#text' => $this->t('You may use tokens.'),
        '#token_types' => $token_types,
        '#click_insert' => TRUE,
        '#dialog' => TRUE,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildTreeElement(array $token_types = ['webform', 'webform_submission', 'webform_handler'], $description = NULL) {
    if (!$this->moduleHandler->moduleExists('token')) {
      return [];
    }

    $build = [
      '#theme' => 'token_tree_link',
      '#token_types' => $token_types,
      '#click_insert' => TRUE,
      '#dialog' => TRUE,
    ];

    if ($description) {
      if ($this->config->get('ui.description_help')) {
        return [
          '#type' => 'container',
          'token_tree_link' => $build,
          'help' => [
            '#type' => 'webform_help',
            '#help' => $description,
          ],
        ];
      }
      else {
        return [
          '#type' => 'container',
          'token_tree_link' => $build,
          'description' => [
            '#prefix' => ' ',
            '#markup' => $description,
          ],
        ];
      }
    }
    else {
      return [
        '#type' => 'container',
        'token_tree_link' => $build,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function elementValidate(array &$form, array $token_types = ['webform', 'webform_submission', 'webform_handler']) {
    if (!function_exists('token_element_validate')) {
      return;
    }

    // Always add system tokens.
    // @see system_token_info()
    $token_types = array_merge($token_types, ['site', 'date']);

    $text_element_types = [
      'email' => 'email',
      'textfield' => 'textfield',
      'textarea' => 'textarea',
      'url' => 'url',
      'webform_codemirror' => 'webform_codemirror',
      'webform_email_multiple' => 'webform_email_multiple',
      'webform_html_editor' => 'webform_html_editor',
      'webform_checkboxes_other' => 'webform_checkboxes_other',
      'webform_select_other' => 'webform_select_other',
      'webform_radios_other' => 'webform_radios_other',
    ];
    $elements =& WebformFormHelper::flattenElements($form);
    foreach ($elements as &$element) {
      if (!isset($element['#type']) || !isset($text_element_types[$element['#type']])) {
        continue;
      }

      $element['#element_validate'][] = [get_called_class(), 'validateElement'];
      $element['#token_types'] = $token_types;
    }
  }

  /**
   * Get token data based on an entity's type.
   *
   * @param array $data
   *   An array of token data.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   A Webform or Webform submission entity.
   */
  protected function setTokenData(array &$data, EntityInterface $entity) {
    if ($entity instanceof WebformSubmissionInterface) {
      $data['webform_submission'] = $entity;
      $data['webform'] = $entity->getWebform();
    }
    elseif ($entity instanceof WebformInterface) {
      $data['webform'] = $entity;
    }
  }

  /**
   * Set token option based on the entity.
   *
   * @param array $options
   *   An array of token data.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   A Webform or Webform submission entity.
   */
  protected function setTokenOptions(array &$options, EntityInterface $entity) {
    $token_options = [];
    if ($entity instanceof WebformSubmissionInterface) {
      $token_options['langcode'] = $entity->language()->getId();
    }
    elseif ($entity instanceof WebformInterface) {
      $token_options['langcode'] = $this->languageManager->getCurrentLanguage()->getId();
    }
    $options += $token_options;
  }

  /**
   * Validates an element's tokens.
   *
   * Note:
   * Element is not being based by reference since the #value is being altered.
   */
  public static function validateElement($element, FormStateInterface $form_state, &$complete_form) {
    $value = isset($element['#value']) ? $element['#value'] : $element['#default_value'];

    if (!mb_strlen($value)) {
      return $element;
    }

    // Remove suffixes which are not valid.
    $element['#value'] = preg_replace('/\[(webform[^]]+)((?::clear|:htmldecode|:urlencode|:striptags)+)\]/', '[\1]', $value);

    token_element_validate($element, $form_state);
  }

}
