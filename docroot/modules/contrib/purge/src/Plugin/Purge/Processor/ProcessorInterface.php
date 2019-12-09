<?php

namespace Drupal\purge\Plugin\Purge\Processor;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Describes a plugin that processes invalidations.
 */
interface ProcessorInterface extends PluginInspectionInterface {

  /**
   * Retrieve the title of this processor.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The translated label.
   */
  public function getLabel();

  /**
   * Retrieve the description of this processor.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The translated description.
   */
  public function getDescription();

}
