<?php

namespace Drupal\recurring_events;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class EventSeriesStorage extends SqlContentEntityStorage implements EventSeriesStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EventInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {eventseries_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {eventseries_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EventInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {eventseries_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('eventseries_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
