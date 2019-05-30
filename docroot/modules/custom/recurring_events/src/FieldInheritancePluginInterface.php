<?php

namespace Drupal\recurring_events;

/**
 * FieldInheritancePluginInterface interface definition.
 */
interface FieldInheritancePluginInterface {

  /**
   * Compute the value of the field.
   */
  public function computeValue();

}
