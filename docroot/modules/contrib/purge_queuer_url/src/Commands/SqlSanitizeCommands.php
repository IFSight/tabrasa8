<?php

namespace Drupal\purge_queuer_url\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\purge_queuer_url\TrafficRegistryInterface;
use Drush\Commands\DrushCommands;
use Drush\Drupal\Commands\sql\SanitizePluginInterface;
use Drush\Drush;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Sanitize plugin for Drush sql:sanitize which clears the URL registry.
 */
class SqlSanitizeCommands extends DrushCommands implements SanitizePluginInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The traffic registry with the stored URLs and tags.
   *
   * @var \Drupal\purge_queuer_url\TrafficRegistryInterface
   */
  protected $registry;

  /**
   * Constructs a SqlSanitizeCommands object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\purge_queuer_url\TrafficRegistryInterface $registry
   *   The traffic registry with the stored URLs and tags.
   */
  public function __construct(ModuleHandlerInterface $module_handler, TrafficRegistryInterface $registry) {
    $this->moduleHandler = $module_handler;
    $this->registry = $registry;
  }

  /**
   * Clear Purge URLs queuer traffic history.
   *
   * @hook post-command sql-sanitize
   *
   * @inheritdoc
   */
  public function sanitize($result, CommandData $commandData) {
    if ($this->applies()) {
      $this->registry->clear();
    }
  }

  /**
   * Return the messages.
   *
   * @hook on-event sql-sanitize-confirms
   *
   * @inheritdoc
   */
  public function messages(&$messages, InputInterface $input) {
    if ($this->applies()) {
      $messages[] = dt('Clear Purge URLs queuer traffic history.');
    }
    return [];
  }

  /**
   * Verify if the module is enabled.
   */
  protected function applies() {
    Drush::bootstrapManager()->doBootstrap(DRUSH_BOOTSTRAP_DRUPAL_FULL);
    return $this->moduleHandler->moduleExists('purge_queuer_url');
  }

}
