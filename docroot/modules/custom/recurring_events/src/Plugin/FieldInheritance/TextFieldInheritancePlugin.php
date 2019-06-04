<?php

namespace Drupal\recurring_events\Plugin\FieldInheritance;

use Drupal\recurring_events\FieldInheritancePluginInterface;

/**
 * Text Inheritance plugin.
 *
 * @FieldInheritance(
 *   id = "text_inheritance",
 *   name = @Translation("Text Field Inheritance"),
 *   types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *     "string",
 *     "string_long",
 *     "list_string"
 *   }
 * )
 */
class TextFieldInheritancePlugin extends FieldInheritancePluginBase implements FieldInheritancePluginInterface {
}
