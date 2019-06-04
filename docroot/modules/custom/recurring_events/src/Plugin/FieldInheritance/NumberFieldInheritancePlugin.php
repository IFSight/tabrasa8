<?php

namespace Drupal\recurring_events\Plugin\FieldInheritance;

use Drupal\recurring_events\FieldInheritancePluginInterface;

/**
 * Number Inheritance plugin.
 *
 * @FieldInheritance(
 *   id = "number_inheritance",
 *   name = @Translation("Number Field Inheritance"),
 *   types = {
 *     "decimal",
 *     "float",
 *     "integer",
 *     "list_float",
 *     "list_integer"
 *   }
 * )
 */
class NumberFieldInheritancePlugin extends FieldInheritancePluginBase implements FieldInheritancePluginInterface {
}
