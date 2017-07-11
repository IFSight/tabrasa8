<?php

namespace Drupal\viewsreference\Plugin\Field\FieldType;

use Drupal\views\Views;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\PreconfiguredFieldUiOptionsInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'viewsreference' entity field type.
 *
 * Supported settings (below the definition's 'settings' key) are:
 * - target_type: The entity type to reference. Required.
 *
 * @FieldType(
 *   id = "viewsreference",
 *   label = @Translation("Views reference"),
 *   description = @Translation("A field reference to a view."),
 *   category = @Translation("Reference"),
 *   default_widget = "viewsreference_autocomplete",
 *   default_formatter = "viewsreference_formatter",
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 * )
 */
class ViewsReferenceItem extends EntityReferenceItem implements
    OptionsProviderInterface,
    PreconfiguredFieldUiOptionsInterface {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return array(
      'target_type' => 'view',
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return array(
      'plugin_types' => array('block' => 'block'),
      'preselect_views' => array(),
    ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['display_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Display Id'))
      ->setDescription(new TranslatableMarkup('The referenced display Id'));

    $properties['argument'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Argument'))
      ->setDescription(new TranslatableMarkup('An optional argument or contextual filter to apply to the View'));

    $properties['title'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Title'))
      ->setDescription(new TranslatableMarkup('Whether or not to include the View or Block title'));

    $properties['data'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Data'))
      ->setDescription(new TranslatableMarkup('Settings data for advanced use'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return parent::mainPropertyName();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);
    $target_type = $field_definition->getSetting('target_type');
    $target_type_info = \Drupal::entityTypeManager()->getDefinition($target_type);
    $properties = static::propertyDefinitions($field_definition)['target_id'];
    $schema['columns']['display_id'] = array(
      'description' => 'The ID of the display.',
      'type' => 'varchar_ascii',
      // If the target entities act as bundles for another entity type,
      // their IDs should not exceed the maximum length for bundles.
      'length' => $target_type_info->getBundleOf() ? EntityTypeInterface::BUNDLE_MAX_LENGTH : 255,
    );

    $schema['columns']['argument'] = array(
      'description' => 'An optional argument.',
      'type' => 'varchar_ascii',
      'length' => 255,
    );

    $schema['columns']['title'] = array(
      'description' => 'Include title.',
      'type' => 'int',
      'length' => 11,
    );

    $schema['columns']['data'] = array(
      'description' => 'Serialized data.',
      'type' => 'text',
      'size' => 'big'
    );

    $schema['indexes']['display_id'] = array('display_id');

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    return parent::getConstraints();
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    // Select widget has extra layer of items.
    if (isset($values['target_id']) && is_array($values['target_id'])) {
      $values['target_id'] = isset($values['target_id'][0]['target_id']) ? $values['target_id'][0]['target_id'] : NULL;
    }
    // Empty string argument only possible if no argument supplied.
    if (isset($values['argument']) && $values['argument'] === '') {
      $values['argument'] = NULL;
    }
    parent::setValue($values, FALSE);

  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return parent::getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name, $notify = TRUE) {
    return parent::onChange($property_name, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    return parent::storageSettingsForm($form, $form_state, $has_data);
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);
    $settings = $this->getSettings();
    $preselect_views = $settings['preselect_views'];
    $default_plugins = $settings['plugin_types'];
    $display_options = $this->getAllPluginList();
    $view_list = $this->getAllViewsNames();
    $form['plugin_types'] = [
      '#type' => 'checkboxes',
      '#options' => $display_options,
      '#title' => $this->t('View display plugins to allow'),
      '#default_value' => $default_plugins,
      '#weight' => 1,
    ];

    $form['preselect_views'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Preselect View Options'),
      '#options' => $view_list,
      '#default_value' => $preselect_views,
      '#weight' => 2,
    );

    return $form;
  }

  /**
   * Determines whether the item holds an unsaved entity.
   *
   * This is notably used for "autocreate" widgets, and more generally to
   * support referencing freshly created entities (they will get saved
   * automatically as the hosting entity gets saved).
   *
   * @return bool
   *   TRUE if the item holds an unsaved entity.
   */
  public function hasNewEntity() {
    return !$this->isEmpty() && $this->target_id === NULL && $this->entity->isNew();
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    return array();

  }

  /**
   * Helper function to get all display ids.
   */
  private function getAllPluginList() {
    $types = Views::pluginList();
    $options = array();
    foreach ($types as $key => $type) {
      if ($type['type'] == 'display') {
        $options[str_replace('display:', '', $key)] = $type['title']->render();
      }
    }
    return $options;
  }

  /**
   * Helper function to get all View Names.
   */
  private function getAllViewsNames() {
    $views = Views::getEnabledViews();
    $options = array();
    foreach ($views as $view) {
      $options[$view->get('id')] = $view->get('label');
    }
    return $options;
  }

}
