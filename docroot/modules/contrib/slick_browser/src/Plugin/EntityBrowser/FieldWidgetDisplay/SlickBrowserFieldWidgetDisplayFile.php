<?php

namespace Drupal\slick_browser\Plugin\EntityBrowser\FieldWidgetDisplay;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Displays Slick Browser File thumbnail if applicable.
 *
 * The main difference from core EB is it strives to display a thumbnail image
 * before giving up to view mode because mostly dealing with small preview.
 *
 * @EntityBrowserFieldWidgetDisplay(
 *   id = "slick_browser_file",
 *   label = @Translation("Slick Browser: File"),
 *   description = @Translation("Displays a preview of a file or entity using Blazy, if applicable.")
 * )
 */
class SlickBrowserFieldWidgetDisplayFile extends SlickBrowserFieldWidgetDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity) {
    /** @var \Drupal\file\Entity\File $entity */
    $data = $this->blazyEntity->oembed()->getImageItem($entity);
    $data['settings'] = isset($data['settings']) ? array_merge($this->buildSettings(), $data['settings']) : $this->buildSettings();

    $content = $this->blazyEntity->build($data, $entity, $entity->getFilename());
    $content['#entity'] = $entity;
    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntityTypeInterface $entity_type) {
    return $entity_type->getClass() == 'Drupal\file\Entity\File' || $entity_type->entityClassImplements('FileInterface');
  }

}
