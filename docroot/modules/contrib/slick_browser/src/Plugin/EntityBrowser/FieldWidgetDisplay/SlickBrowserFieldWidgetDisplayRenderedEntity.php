<?php

namespace Drupal\slick_browser\Plugin\EntityBrowser\FieldWidgetDisplay;

use Drupal\Core\Entity\EntityInterface;

/**
 * Displays Slick Browser as a rendered entity.
 *
 * @EntityBrowserFieldWidgetDisplay(
 *   id = "slick_browser_rendered_entity",
 *   label = @Translation("Slick Browser: Rendered entity"),
 *   description = @Translation("Displays a rendered entity.")
 * )
 */
class SlickBrowserFieldWidgetDisplayRenderedEntity extends SlickBrowserFieldWidgetDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity) {
    $settings = $this->buildSettings();
    $settings['view_mode'] = isset($this->configuration['view_mode']) ? $this->configuration['view_mode'] : 'slick_browser';

    $content = $this->blazyEntity->getEntityView($entity, $settings, $entity->label());
    $content['#entity'] = $entity;
    return $content;
  }

}
