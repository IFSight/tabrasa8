<?php

namespace Drupal\recurring_events\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\Messenger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\recurring_events\FieldInheritancePluginManager;

/**
 * Class FieldInheritanceForm.
 */
class FieldInheritanceForm extends EntityForm {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * The field inheritance plugin manager.
   *
   * @var \Drupal\recurring_events\FieldInheritancePluginManager
   */
  protected $fieldInheritance;

  /**
   * Construct an FieldInheritanceForm.
   *
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   * @param \Drupal\Core\Entity\EntityFieldManager $entity_field_manager
   *   The entity field manager service.
   * @param \Drupal\recurring_events\FieldInheritancePluginManager $field_inheritance
   *   The field inheritance plugin manager.
   */
  public function __construct(Messenger $messenger, EntityFieldManager $entity_field_manager, FieldInheritancePluginManager $field_inheritance) {
    $this->messenger = $messenger;
    $this->entityFieldManager = $entity_field_manager;
    $this->fieldInheritance = $field_inheritance;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('entity_field.manager'),
      $container->get('plugin.manager.field_inheritance')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $field_inheritance = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $field_inheritance->label(),
      '#description' => $this->t("Label for the Field inheritance."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $field_inheritance->id(),
      '#machine_name' => [
        'exists' => '\Drupal\recurring_events\Entity\FieldInheritance::load',
      ],
      '#disabled' => !$field_inheritance->isNew(),
    ];

    $help = [
      $this->t('<b>Inherit</b> - Pull field data directly from the series.'),
      $this->t('<b>Prepend</b> - Place instance data above series data.'),
      $this->t('<b>Append</b> - Place instance data below series data.'),
      $this->t('<b>Fallback</b> - Show instance data, if set, otherwise show series data.'),
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Inheritance Strategy'),
      '#description' => $this->t('Select the method/strategy used to inherit data.'),
      '#options' => [
        'inherit' => $this->t('Inherit'),
        'prepend' => $this->t('Prepend'),
        'append' => $this->t('Append'),
        'fallback' => $this->t('Fallback'),
      ],
      '#required' => TRUE,
      '#default_value' => $field_inheritance->type() ?: 'inherit',
    ];
    $form['information'] = [
      '#type' => 'markup',
      '#prefix' => '<p>',
      '#markup' => implode('</p><p>', $help),
      '#suffix' => '</p>',
    ];

    $series_fields = array_keys($this->entityFieldManager->getFieldDefinitions('eventseries', 'eventseries'));
    $series_fields = array_combine($series_fields, $series_fields);

    $form['sourceField'] = [
      '#type' => 'select',
      '#title' => $this->t('Source/Series Field'),
      '#description' => $this->t('Select the field on the series from which to inherit data.'),
      '#options' => $series_fields,
      '#required' => TRUE,
      '#default_value' => $field_inheritance->sourceField(),
    ];

    $instance_fields = array_keys($this->entityFieldManager->getFieldDefinitions('eventinstance', 'eventinstance'));
    $instance_fields = array_combine($instance_fields, $instance_fields);

    $form['entityField'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity/Instance Field'),
      '#description' => $this->t('Select the field on the instance to use during inheritance.'),
      '#options' => $instance_fields,
      '#states' => [
        'visible' => [
          'select[name="type"]' => ['!value' => 'inherit'],
        ],
        'required' => [
          'select[name="type"]' => ['!value' => 'inherit'],
        ],
      ],
      '#default_value' => $field_inheritance->entityField(),
    ];

    $plugins = array_keys($this->fieldInheritance->getDefinitions());
    $plugins = array_combine($plugins, $plugins);

    $form['plugin'] = [
      '#type' => 'select',
      '#title' => $this->t('Inheritance Plugin'),
      '#description' => $this->t('Select the plugin used to perform the inheritance.'),
      '#options' => $plugins,
      '#required' => TRUE,
      '#default_value' => $field_inheritance->plugin(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $values = $form_state->getValues();

    if (!empty($values['sourceField']) && !empty($values['entityField'])) {
      $series_definitions = $this->entityFieldManager->getFieldDefinitions('eventseries', 'eventseries');
      $instance_definitions = $this->entityFieldManager->getFieldDefinitions('eventinstance', 'eventinstance');

      if ($series_definitions[$values['sourceField']]->getType() !== $instance_definitions[$values['entityField']]->getType()) {
        $message = $this->t('Source and entity field definition types must be the same to inherit data. Source - @source_name type: @source_type. Entity - @entity_name type: @entity_type', [
          '@source_name' => $values['sourceField'],
          '@source_type' => $series_definitions[$values['sourceField']]->getType(),
          '@entity_name' => $values['entityField'],
          '@entity_type' => $instance_definitions[$values['entityField']]->getType(),
        ]);
        $form_state->setErrorByName('sourceField', $message);
        $form_state->setErrorByName('entityField', $message);
      }

      $plugin_definition = $this->fieldInheritance->getDefinition($values['plugin']);
      $field_types = $plugin_definition['types'];

      if (!in_array($series_definitions[$values['sourceField']]->getType(), $field_types)) {
        $message = $this->t('The selected plugin @plugin does not support @source_type fields. The supported field types are: @field_types', [
          '@plugin' => $values['plugin'],
          '@source_type' => $series_definitions[$values['sourceField']]->getType(),
          '@field_types' => implode(',', $field_types),
        ]);
        $form_state->setErrorByName('sourceField', $message);
        $form_state->setErrorByName('plugin', $message);
      }

    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $field_inheritance = $this->entity;
    $status = $field_inheritance->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger->addMessage($this->t('Created the %label Field inheritance.', [
          '%label' => $field_inheritance->label(),
        ]));
        break;

      default:
        $this->messenger->addMessage($this->t('Saved the %label Field inheritance.', [
          '%label' => $field_inheritance->label(),
        ]));
    }
    $this->entityFieldManager->clearCachedFieldDefinitions();
    $form_state->setRedirectUrl($field_inheritance->toUrl('collection'));
  }

}
