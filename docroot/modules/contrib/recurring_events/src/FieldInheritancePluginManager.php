<?php

namespace Drupal\recurring_events;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a FieldInheritance plugin manager.
 *
 * @see \Drupal\recurring_events\Annotation\FieldInheritance
 * @see \Drupal\recurring_events\FieldInheritancePluginInterface
 * @see plugin_api
 */
class FieldInheritancePluginManager extends DefaultPluginManager {

  /**
   * Constructs a FieldInheritancePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/FieldInheritance',
      $namespaces,
      $module_handler,
      'Drupal\recurring_events\FieldInheritancePluginInterface',
      'Drupal\recurring_events\Annotation\FieldInheritance'
    );
    $this->alterInfo('field_inheritance_info');
    $this->setCacheBackend($cache_backend, 'field_inheritance_info_plugins');
  }

}
