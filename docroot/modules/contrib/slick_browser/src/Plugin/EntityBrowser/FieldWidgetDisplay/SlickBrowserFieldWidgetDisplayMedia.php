<?php

namespace Drupal\slick_browser\Plugin\EntityBrowser\FieldWidgetDisplay;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Displays Slick Browser Media thumbnail.
 *
 * @EntityBrowserFieldWidgetDisplay(
 *   id = "slick_browser_media",
 *   label = @Translation("Slick Browser: Media"),
 *   description = @Translation("Displays a preview of a Media using Blazy, if applicable.")
 * )
 */
class SlickBrowserFieldWidgetDisplayMedia extends SlickBrowserFieldWidgetDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity) {
    $data['settings'] = $this->buildSettings();

    $content = $this->blazyEntity->build($data, $entity, $entity->label());
    $content['#entity'] = $entity;
    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntityTypeInterface $entity_type) {
    return $entity_type->getClass() == 'Drupal\media\Entity\Media' || $entity_type->entityClassImplements('MediaInterface');
  }

}
