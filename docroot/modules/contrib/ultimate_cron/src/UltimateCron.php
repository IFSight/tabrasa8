<?php

namespace Drupal\ultimate_cron;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cron;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\Core\State\StateInterface;
use Drupal\ultimate_cron\Entity\CronJob;
use Psr\Log\LoggerInterface;

/**
 * The Ultimate Cron service.
 */
class UltimateCron extends Cron {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a cron object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock service.
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Session\AccountSwitcherInterface $account_switcher
   *    The account switching service.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Queue\QueueWorkerManagerInterface
   *   The queue plugin manager.
   */
  public function __construct(ModuleHandlerInterface $module_handler, LockBackendInterface $lock, QueueFactory $queue_factory, StateInterface $state, AccountSwitcherInterface $account_switcher, LoggerInterface $logger, QueueWorkerManagerInterface $queue_manager, ConfigFactoryInterface $config_factory) {
    parent::__construct($module_handler, $lock, $queue_factory, $state, $account_switcher, $logger, $queue_manager);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function run() {

    // Load the cron jobs in the right order.
    $job_ids = \Drupal::entityQuery('ultimate_cron_job')
      ->condition('status', TRUE)
      ->sort('weight', 'ASC')

      ->execute();

    $launcher_jobs = array();
    foreach (CronJob::loadMultiple($job_ids) as $job) {
      /* @var \Drupal\Core\Plugin\DefaultPluginManager $manager */
      $manager = \Drupal::service('plugin.manager.ultimate_cron.' . 'launcher');
      $launcher = $manager->createInstance($job->getLauncherId());
      $launcher_definition = $launcher->getPluginDefinition();

      if (!isset($launchers) || in_array($launcher->getPluginId(), $launchers)) {
        $launcher_jobs[$launcher_definition['id']]['launcher'] = $launcher;
        $launcher_jobs[$launcher_definition['id']]['sort'] = array($launcher_definition['weight']);
        $launcher_jobs[$launcher_definition['id']]['jobs'][$job->id()] = $job;
        $launcher_jobs[$launcher_definition['id']]['jobs'][$job->id()]->sort = array($job->loadLatestLogEntry()->start_time);
      }
    }

    foreach ($launcher_jobs as $name => $launcher_job) {
      $launcher_job['launcher']->launchJobs($launcher_job['jobs']);
    }

    // Run standard queue processing if our own handling is disabled.
    if (!$this->configFactory->get('ultimate_cron.settings')->get('queue.enabled')) {
      $this->processQueues();
    }

    $this->setCronLastTime();

    return TRUE;
  }
}
