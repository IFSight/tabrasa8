<?php

namespace Drupal\linkit\Plugin\Linkit\Substitution;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\GeneratedUrl;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\linkit\SubstitutionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A substitution plugin for the URL to a file.
 *
 * @Substitution(
 *   id = "media",
 *   label = @Translation("Direct URL to media file entity"),
 * )
 */
class Media extends PluginBase implements SubstitutionInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * Get the URL associated with a given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to get a URL for.
   *
   * @return \Drupal\Core\GeneratedUrl
   *   A url to replace.
   */
  public function getUrl(EntityInterface $entity) {
    $url = new GeneratedUrl();

    /** @var \Drupal\media\MediaTypeInterface $media_type */
    $media_type = $entity->get('bundle')->entity;
    // Default to the canonical URL if the bundle doesn't have a source field.
    if (empty($media_type->getSource()->getConfiguration()['source_field'])) {
      return $entity->toUrl('canonical')->toString(TRUE);
    }

    $source_field = $media_type->getSource()->getConfiguration()['source_field'];
    /** @var \Drupal\file\FileInterface $file */
    $file = $entity->{$source_field}->entity;
    $url->setGeneratedUrl(file_create_url($file->getFileUri()));
    $url->addCacheableDependency($entity);
    return $url;
  }

  /**
   * Checks if this substitution plugin is applicable for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type object.
   *
   * @return bool
   *   If the plugin is applicable.
   */
  public static function isApplicable(EntityTypeInterface $entity_type) {
    return $entity_type->entityClassImplements('Drupal\media\MediaInterface');
  }

}
