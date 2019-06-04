<?php

namespace Drupal\recurring_events;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Defines the storage handler class for eventinstance entities.
 *
 * This extends the base storage class, adding required special handling for
 * eventinstance entities.
 *
 * @ingroup recurring_events
 */
interface EventInstanceStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of eventinstance revision IDs for a specific eventinstance.
   *
   * @param \Drupal\recurring_events\EventInterface $entity
   *   The eventinstance entity.
   *
   * @return int[]
   *   Eventinstance revision IDs (in ascending order).
   */
  public function revisionIds(EventInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as eventinstance author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Eventinstance revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\recurring_events\EventInterface $entity
   *   The eventinstance entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EventInterface $entity);

  /**
   * Unsets the language for all eventinstance with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
