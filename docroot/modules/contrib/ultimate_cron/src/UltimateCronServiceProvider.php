<?php

namespace Drupal\ultimate_cron;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Service Provider for File entity.
 */
class UltimateCronServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('cron');
    $definition->setClass('Drupal\ultimate_cron\UltimateCron');
    $definition->addArgument(new Reference('config.factory'));
  }
}
