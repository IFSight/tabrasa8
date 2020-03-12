<?php

namespace Drupal\fulcrum_whitelist;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Fulcrum Whitelist Entity entity.
 *
 * @see \Drupal\fulcrum_whitelist\Entity\FulcrumWhitelistEntity.
 */
class FulcrumWhitelistEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\fulcrum_whitelist\Entity\FulcrumWhitelistEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished fulcrum whitelist entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published fulcrum whitelist entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit fulcrum whitelist entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete fulcrum whitelist entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add fulcrum whitelist entity entities');
  }

}
