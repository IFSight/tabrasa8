<?php

namespace Drupal\recurring_events;

use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining an EventSeries and EventInstance entities.
 */
interface EventInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, RevisionLogInterface, EntityPublishedInterface {
  /**
   * Denotes that the event/eventinstance is not published.
   */
  const NOT_PUBLISHED = 0;

  /**
   * Denotes that the event/eventinstance is published.
   */
  const PUBLISHED = 1;

  /**
   * Gets the event revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the event revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\recurring_events\EventInterface
   *   The called event entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the event revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   *
   * @deprecated in Drupal 8.2.0, will be removed before Drupal 9.0.0. Use
   *   \Drupal\Core\Entity\RevisionLogInterface::getRevisionUser() instead.
   */
  public function getRevisionAuthor();

  /**
   * Sets the event revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\recurring_events\EventInterface
   *   The called event entity.
   *
   * @deprecated in Drupal 8.2.0, will be removed before Drupal 9.0.0. Use
   *   \Drupal\Core\Entity\RevisionLogInterface::setRevisionUserId() instead.
   */
  public function setRevisionAuthorId($uid);

}
