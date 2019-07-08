<?php

namespace Drupal\recurring_events\Plugin\FieldInheritance;

use Drupal\recurring_events\FieldInheritancePluginInterface;

/**
 * Entity Reference Inheritance plugin.
 *
 * @FieldInheritance(
 *   id = "entity_reference_inheritance",
 *   name = @Translation("Entity Reference Field Inheritance"),
 *   types = {
 *     "entity_reference",
 *     "image",
 *     "file",
 *     "webform"
 *   }
 * )
 */
class EntityReferenceFieldInheritancePlugin extends FieldInheritancePluginBase implements FieldInheritancePluginInterface {
}
