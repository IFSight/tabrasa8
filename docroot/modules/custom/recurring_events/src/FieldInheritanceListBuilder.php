<?php

namespace Drupal\recurring_events;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Field inheritance entities.
 */
class FieldInheritanceListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Field inheritance');
    $header['id'] = $this->t('Machine name');
    $header['type'] = $this->t('Type');
    $header['source_field'] = $this->t('Source Field');
    $header['entity_field'] = $this->t('Entity Field');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['type'] = $entity->type();
    $row['source_field'] = $entity->sourceField();
    $row['entity_field'] = $entity->entityField() ?: $this->t('N/A');
    return $row + parent::buildRow($entity);
  }

}
