<?php

namespace Drupal\recurring_events\Plugin\FieldInheritance;

use Drupal\recurring_events\FieldInheritancePluginInterface;

/**
 * Date Inheritance plugin.
 *
 * @FieldInheritance(
 *   id = "date_inheritance",
 *   name = @Translation("Date Field Inheritance"),
 *   types = {
 *     "datetime",
 *     "daterange",
 *   }
 * )
 */
class DateFieldInheritancePlugin extends FieldInheritancePluginBase implements FieldInheritancePluginInterface {
}
