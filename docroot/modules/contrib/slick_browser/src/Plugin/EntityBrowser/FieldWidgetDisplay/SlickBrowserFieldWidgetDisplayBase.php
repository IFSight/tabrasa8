<?php

namespace Drupal\slick_browser\Plugin\EntityBrowser\FieldWidgetDisplay;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\entity_browser\FieldWidgetDisplayBase;
use Drupal\slick_browser\SlickBrowserDefault;
use Drupal\slick_browser\SlickBrowserUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Slick Browser entity display and or entity selection.
 */
abstract class SlickBrowserFieldWidgetDisplayBase extends FieldWidgetDisplayBase implements ContainerFactoryPluginInterface {

  /**
   * The blazy entity service.
   *
   * @var \Drupal\blazy\BlazyEntityInterface
   */
  protected $blazyEntity;

  /**
   * The blazy manager service.
   *
   * @var \Drupal\blazy\BlazyManagerInterface
   */
  protected $blazyManager;

  /**
   * The slick browser service.
   *
   * @var \Drupal\slick_browser\SlickBrowserInterface
   */
  protected $slickBrowser;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Entity type to be displayed.
   *
   * @var string
   */
  protected $targetEntityType;

  /**
   * Bundle to be displayed.
   *
   * @var string
   */
  protected $bundle;

  /**
   * A list of field definitions eligible for configuration in this display.
   *
   * @var \Drupal\Core\Field\FieldDefinitionInterface[]
   */
  protected $fieldDefinitions;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    return self::injectServices($instance, $container);
  }

  /**
   * Injects DI services.
   */
  protected static function injectServices($instance, ContainerInterface $container) {
    $instance->slickBrowser = $container->get('slick_browser');
    $instance->blazyEntity = isset($instance->blazyEntity) ? $instance->blazyEntity : $instance->slickBrowser->blazyEntity();
    $instance->blazyManager = isset($instance->blazyManager) ? $instance->blazyManager : $instance->slickBrowser->blazyManager();
    $instance->entityFieldManager = isset($instance->entityFieldManager) ? $instance->entityFieldManager : $container->get('entity_field.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $is_widget = isset($form['#fields']);

    // This form can be either on Entity browser display, or widget plugin.
    $element['_context'] = [
      '#type' => 'hidden',
      '#default_value' => $is_widget ? 'widget' : 'selection',
    ];

    // Do not use hook_field_widget_third_party_settings_form(), as this form is
    // also duplicated at "Manage form display" page.
    // Cases:
    // - Media any cardinality (1, > 1, -1). Unless using Media library widget.
    // - File cardinality -1. Not 1 nor > 1. The reason: EB has no plugin
    // selector except for -1 to load this form.
    if ($is_widget) {
      $definition = $this->getScopedFormElements();
      $this->bundle = $definition['bundle'] = $form['#bundle'];
      $this->targetEntityType = $form['#entity_type'];

      $this->checkFieldDefinitions($definition, $form_state);

      // Build this plugin specific form.
      $this->slickBrowser->buildSettingsForm($element, $definition);
    }
    else {
      // This is the selection previews normally tiny thumbnails or labels.
      $element['selection_position'] = [
        '#type'    => 'select',
        '#title'   => $this->t('Selection position'),
        '#options' => [
          'left'   => $this->t('Left'),
          'right'  => $this->t('Right'),
          'bottom' => $this->t('Bottom'),
        ],
        '#default_value' => isset($this->configuration['selection_position']) ? $this->configuration['selection_position'] : 'bottom',
        '#description'   => $this->t('Left and Right positions are more suitable for large displays such as Modal. They are affected by the Slick Browser: Tabs positioning. If Tabs is placed at Top or Bottom, Selection position can be placed on the Right or Left, otherwise resulting in too narrow form. Basically if Tabs Left, Selection should be Left. If Tabs Right, Selection should be Right. Adjust it accordingly.'),
      ];
    }

    return $element;
  }

  /**
   * Gets EB widget settings.
   */
  public function buildSettings($defaults = []) {
    $defaults = $defaults ?: SlickBrowserDefault::widgetSettings();
    $settings = array_merge($defaults, $this->configuration);

    // Only load the Blazy library if using SB browsers, but not SB widgets.
    $settings['_detached'] = !empty($settings['style']);
    $settings['entity_type'] = isset($this->configuration['entity_type']) ? $this->configuration['entity_type'] : '';
    $settings['media_switch'] = empty($settings['media_switch']) ? 'media' : $settings['media_switch'];
    $settings['thumbnail_style'] = empty($settings['thumbnail_style']) ? 'slick_browser_thumbnail' : $settings['thumbnail_style'];
    $settings['plugin_id_entity_display'] = $this->getPluginId();

    // Enforces thumbnail without video iframes for tiny selection thumbnail.
    $selection = $settings['_context'] == 'selection';
    if ($selection) {
      $settings['lazy'] = $settings['ratio'] = '';
      $settings['_noiframe'] = TRUE;
    }

    return array_merge(SlickBrowserDefault::entitySettings(), $settings);
  }

  /**
   * Checks for the current definitions with various AJAX contents.
   */
  protected function checkFieldDefinitions(array &$definition, FormStateInterface $form_state) {
    $definition['plugin_id_entity_display'] = $this->getPluginId();
    // This one is changing when EB starts picking up plugin selectors.
    if ($values = $form_state->getValues()) {
      foreach ($values['fields'] as $field_name => $data) {
        $definition['field_name'] = $field_name;
        // The entity_browser_entity_reference, entity_browser_file, etc.
        $definition['plugin_id_widget'] = $data['type'];
        break;
      }
    }

    // We want a consistent field_name regardless the current AJAX states.
    $triggers = $form_state->getTriggeringElement();
    if (isset($triggers['#field_name'])) {
      $definition['field_name'] = $triggers['#field_name'];
      $definition['plugin_id_widget'] = $data['type'];
    }
    elseif (isset($triggers['#parents']) && mb_substr($triggers['#parents'][1], 0, 6) === 'field_') {
      $definition['field_name'] = $triggers['#parents'][1];
    }

    // We must travel around just to get cardinality here. This is needed so
    // to serve the correct Display style: Grid/Column, Slick or Single.
    if (!empty($definition['field_name'])) {
      $field_definition = $this->getFieldDefinition($definition['field_name']);
      $definition['cardinality'] = $field_definition->getFieldStorageDefinition()->getCardinality();
    }
  }

  /**
   * Gets the field definition of a field.
   */
  protected function getFieldDefinition($field_name) {
    $definitions = $this->getFieldDefinitions();
    return isset($definitions[$field_name]) ? $definitions[$field_name] : NULL;
  }

  /**
   * Gets the definitions of the fields that are candidate for display.
   */
  protected function getFieldDefinitions() {
    if (!isset($this->fieldDefinitions)) {
      $this->fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($this->targetEntityType, $this->bundle);
    }
    return $this->fieldDefinitions;
  }

  /**
   * Defines the scope for the form elements.
   */
  public function getScopedFormElements() {
    return [
      'settings' => $this->buildSettings(),
      'target_type' => $this->configuration['entity_type'],
    ] + SlickBrowserUtil::scopedFormElements();
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    foreach (['image_style', 'thumbnail_style'] as $style) {
      if (isset($this->configuration[$style]) && $image_style = $this->blazyManager->getEntityTypeManager()->getStorage($style)->load($this->configuration[$style])) {
        $dependencies[$image_style->getConfigDependencyKey()][] = $image_style->getConfigDependencyName();
      }
    }
    return $dependencies;
  }

}
