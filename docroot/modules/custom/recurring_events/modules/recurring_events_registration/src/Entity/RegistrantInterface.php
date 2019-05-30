<?php

namespace Drupal\recurring_events_registration\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Registrant entities.
 *
 * @ingroup recurring_events_registration
 */
interface RegistrantInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Registrant creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Registrant.
   */
  public function getCreatedTime();

  /**
   * Sets the Registrant creation timestamp.
   *
   * @param int $timestamp
   *   The Registrant creation timestamp.
   *
   * @return \Drupal\recurring_events_registration\Entity\RegistrantInterface
   *   The called Registrant entity.
   */
  public function setCreatedTime($timestamp);

}
