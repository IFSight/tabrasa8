<?php

namespace Drupal\recurring_events;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Defines the storage handler class for eventseries entities.
 *
 * This extends the base storage class, adding required special handling for
 * eventseries entities.
 *
 * @ingroup recurring_events
 */
interface EventSeriesStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of eventseries revision IDs for a specific eventseries.
   *
   * @param \Drupal\recurring_events\EventInterface $entity
   *   The eventseries entity.
   *
   * @return int[]
   *   Eventseries revision IDs (in ascending order).
   */
  public function revisionIds(EventInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as eventseries author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Eventseries revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\recurring_events\EventInterface $entity
   *   The eventseries entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EventInterface $entity);

  /**
   * Unsets the language for all eventseries with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
