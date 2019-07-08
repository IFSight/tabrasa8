<?php

namespace Drupal\recurring_events\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a field inheritance plugin annotation object.
 *
 * @Annotation
 */
class FieldInheritance extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The name of the form plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $name;

  /**
   * An array of field types the inheritance plugin supports.
   *
   * @var array
   */
  public $types = [];

}
