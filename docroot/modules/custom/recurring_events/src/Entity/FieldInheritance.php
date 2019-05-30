<?php

namespace Drupal\recurring_events\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Field inheritance entity.
 *
 * @ConfigEntityType(
 *   id = "field_inheritance",
 *   label = @Translation("Field inheritance"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\recurring_events\FieldInheritanceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\recurring_events\Form\FieldInheritanceForm",
 *       "edit" = "Drupal\recurring_events\Form\FieldInheritanceForm",
 *       "delete" = "Drupal\recurring_events\Form\FieldInheritanceDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\recurring_events\FieldInheritanceHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "field_inheritance",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/events/instance/settings/field_inheritance/{field_inheritance}",
 *     "add-form" = "/admin/structure/events/instance/settings/field_inheritance/add",
 *     "edit-form" = "/admin/structure/events/instance/settings/field_inheritance/{field_inheritance}/edit",
 *     "delete-form" = "/admin/structure/events/instance/settings/field_inheritance/{field_inheritance}/delete",
 *     "collection" = "/admin/structure/events/instance/settings/field_inheritance"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "type",
 *     "sourceField",
 *     "entityField",
 *     "plugin"
 *   }
 * )
 */
class FieldInheritance extends ConfigEntityBase implements FieldInheritanceInterface {

  /**
   * The field inheritance ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The field inheritance label.
   *
   * @var string
   */
  protected $label;

  /**
   * The field inheritance type.
   *
   * @var string
   */
  protected $type;

  /**
   * The field inheritance source field.
   *
   * @var string
   */
  protected $sourceField;

  /**
   * The field inheritance entity field.
   *
   * @var string
   */
  protected $entityField;

  /**
   * The field inheritance plugin.
   *
   * @var string
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public function type() {
    return isset($this->type) ? $this->type : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function sourceField() {
    return isset($this->sourceField) ? $this->sourceField : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function entityField() {
    return isset($this->entityField) ? $this->entityField : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function plugin() {
    return isset($this->plugin) ? $this->plugin : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setSourceField($source_field) {
    $this->sourceField = $source_field;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setEntityField($entity_field) {
    $this->entityField = $entity_field;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setPlugin($plugin) {
    $this->plugin = $plugin;
    return $this;
  }

}
