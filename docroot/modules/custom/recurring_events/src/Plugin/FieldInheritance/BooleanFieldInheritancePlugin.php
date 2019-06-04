<?php

namespace Drupal\recurring_events\Plugin\FieldInheritance;

use Drupal\recurring_events\FieldInheritancePluginInterface;

/**
 * Boolean Inheritance plugin.
 *
 * @FieldInheritance(
 *   id = "boolean_inheritance",
 *   name = @Translation("Boolean Field Inheritance"),
 *   types = {
 *     "boolean"
 *   }
 * )
 */
class BooleanFieldInheritancePlugin extends FieldInheritancePluginBase implements FieldInheritancePluginInterface {
}
