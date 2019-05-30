<?php

namespace Drupal\recurring_events\Plugin\FieldInheritance;

use Drupal\recurring_events\FieldInheritancePluginInterface;

/**
 * Email Inheritance plugin.
 *
 * @FieldInheritance(
 *   id = "email_inheritance",
 *   name = @Translation("Email Field Inheritance"),
 *   types = {
 *     "email"
 *   }
 * )
 */
class EmailFieldInheritancePlugin extends FieldInheritancePluginBase implements FieldInheritancePluginInterface {
}
