<?php

namespace Drupal\fulcrum_whitelist;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Fulcrum Whitelist Entity entities.
 *
 * @ingroup fulcrum_whitelist
 */
class FulcrumWhitelistEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Fulcrum Whitelist Entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\fulcrum_whitelist\Entity\FulcrumWhitelistEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.fulcrum_whitelist_entity.edit_form',
      ['fulcrum_whitelist_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
