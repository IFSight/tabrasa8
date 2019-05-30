<?php

namespace Drupal\recurring_events\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Field inheritance entities.
 */
interface FieldInheritanceInterface extends ConfigEntityInterface {

  /**
   * Get the inheritance type.
   *
   * @return string
   *   The inheritance type.
   */
  public function type();

  /**
   * Get the inheritance source field.
   *
   * @return string
   *   The inheritance source field.
   */
  public function sourceField();

  /**
   * Get the inheritance entity field.
   *
   * @return string
   *   The inheritance entity field.
   */
  public function entityField();

  /**
   * Get the inheritance plugin.
   *
   * @return string
   *   The inheritance plugin.
   */
  public function plugin();

  /**
   * Set the inheritance type.
   *
   * @var string $type
   *   The inheritance type.
   *
   * @return $this
   */
  public function setType($type);

  /**
   * Set the inheritance source field.
   *
   * @var string $source_field
   *   The inheritance source field.
   *
   * @return $this
   */
  public function setSourceField($source_field);

  /**
   * Set the inheritance entity field.
   *
   * @var string $entity_field
   *   The inheritance entity field.
   *
   * @return $this
   */
  public function setEntityField($entity_field);

  /**
   * Set the inheritance plugin.
   *
   * @var string $plugin
   *   The inheritance plugin.
   *
   * @return $this
   */
  public function setPlugin($plugin);

}
