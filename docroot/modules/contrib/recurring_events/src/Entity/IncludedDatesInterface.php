<?php

namespace Drupal\recurring_events\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Included dates entities.
 */
interface IncludedDatesInterface extends ConfigEntityInterface {

  /**
   * Get the start date.
   *
   * @return string
   *   The start date.
   */
  public function start();

  /**
   * Get the end date.
   *
   * @return string
   *   The start date.
   */
  public function end();

  /**
   * Set the start date.
   *
   * @var string $start
   *   The start date.
   *
   * @return $this
   */
  public function setStart($start);

  /**
   * Set the end date.
   *
   * @var string $end
   *   The end date.
   *
   * @return $this
   */
  public function setEnd($end);

}
