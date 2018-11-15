<?php

namespace Drupal\webform_access;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\webform\Utility\WebformDialogHelper;
use Drupal\webform_access\Entity\WebformAccessGroup;

/**
 * Defines a class to build a listing of webform access type entities.
 *
 * @see \Drupal\webform\Entity\WebformOption
 */
class WebformAccessTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $limit = FALSE;

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = [];

    // Display info.
    $build['info'] = $this->buildInfo();

    // Table.
    $build += parent::render();
    $build['table']['#sticky'] = TRUE;

    // Attachments.
    $build['#attached']['library'][] = 'webform/webform.admin.dialog';

    return $build;
  }

  /**
   * Build information summary.
   *
   * @return array
   *   A render array representing the information summary.
   */
  protected function buildInfo() {
    $total = $this->getStorage()->getQuery()->count()->execute();
    if (!$total) {
      return [];
    }

    $build['info'] = [
      '#markup' => $this->formatPlural($total, '@total access type', '@total access types', ['@total' => $total]),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['groups'] = [
      'data' => $this->t('Groups'),
      'class' => [RESPONSIVE_PRIORITY_LOW],
    ];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\webform_access\WebformAccessTypeInterface $entity */

    // Label.
    $row['label'] = $entity->toLink($entity->label(), 'edit-form');

    // Groups.
    $entity_ids = \Drupal::entityQuery('webform_access_group')
      ->condition('type', $entity->id())
      ->execute();
    $items = [];
    if ($entity_ids) {
      $webform_access_groups = WebformAccessGroup::loadMultiple($entity_ids);
      foreach ($webform_access_groups as $webform_access_group) {
        $items[] = $webform_access_group->label();
      }
    }
    $row['groups'] = ['data' => ['#theme' => 'item_list', '#items' => $items]];
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity, $type = 'edit') {
    $operations = parent::getDefaultOperations($entity);
    if (isset($operations['delete'])) {
      $operations['delete']['attributes'] = WebformDialogHelper::getModalDialogAttributes(WebformDialogHelper::DIALOG_NARROW);
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOperations(EntityInterface $entity) {
    return parent::buildOperations($entity) + [
      '#prefix' => '<div class="webform-dropbutton">',
      '#suffix' => '</div>',
    ];
  }

}
