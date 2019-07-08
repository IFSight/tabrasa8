<?php

namespace Drupal\recurring_events;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class EventInstanceStorage extends SqlContentEntityStorage implements EventInstanceStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EventInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {eventinstance_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {eventinstance_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EventInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {eventinstance_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('eventinstance_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
