<?php

namespace Drupal\recurring_events;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Included dates entities.
 */
class IncludedDatesListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Included dates');
    $header['id'] = $this->t('Machine name');
    $header['start'] = $this->t('Start');
    $header['end'] = $this->t('End');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['start'] = $entity->start();
    $row['end'] = $entity->end();
    return $row + parent::buildRow($entity);
  }

}
