<?php

namespace Drupal\fulcrum_whitelist\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Fulcrum Whitelist Entity entities.
 *
 * @ingroup fulcrum_whitelist
 */
interface FulcrumWhitelistEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Fulcrum Whitelist Entity name.
   *
   * @return string
   *   Name of the Fulcrum Whitelist Entity.
   */
  public function getName();

  /**
   * Sets the Fulcrum Whitelist Entity name.
   *
   * @param string $name
   *   The Fulcrum Whitelist Entity name.
   *
   * @return \Drupal\fulcrum_whitelist\Entity\FulcrumWhitelistEntityInterface
   *   The called Fulcrum Whitelist Entity entity.
   */
  public function setName($name);

  /**
   * Gets the Fulcrum Whitelist Entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Fulcrum Whitelist Entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Fulcrum Whitelist Entity creation timestamp.
   *
   * @param int $timestamp
   *   The Fulcrum Whitelist Entity creation timestamp.
   *
   * @return \Drupal\fulcrum_whitelist\Entity\FulcrumWhitelistEntityInterface
   *   The called Fulcrum Whitelist Entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Fulcrum Whitelist Entity published status indicator.
   *
   * Unpublished Fulcrum Whitelist Entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Fulcrum Whitelist Entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Fulcrum Whitelist Entity.
   *
   * @param bool $published
   *   TRUE to set this Fulcrum Whitelist Entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\fulcrum_whitelist\Entity\FulcrumWhitelistEntityInterface
   *   The called Fulcrum Whitelist Entity entity.
   */
  public function setPublished($published);

}
