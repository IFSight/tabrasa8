<?php

namespace Drupal\purge_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\purge\Plugin\Purge\Queue\QueueServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for queue configuration forms.
 */
class QueueFormController extends ControllerBase {

  /**
   * The 'purge.queue' service.
   *
   * @var \Drupal\purge\Plugin\Purge\Queue\QueueServiceInterface
   */
  protected $purgeQueue;

  /**
   * Construct the QueuerFormController.
   *
   * @param \Drupal\purge\Plugin\Purge\Queue\QueueServiceInterface $purge_queue
   *   The purge queue service.
   */
  public function __construct(QueueServiceInterface $purge_queue) {
    $this->purgeQueue = $purge_queue;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('purge.queue'));
  }

  /**
   * Render the queue detail form.
   *
   * @return array
   *   The render array.
   */
  public function detailForm() {
    return $this->formBuilder()->getForm(
      "\Drupal\purge_ui\Form\PluginDetailsForm",
      ['details' => $this->purgeQueue->getDescription()]
    );
  }

  /**
   * Route title callback.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function detailFormTitle() {
    $id = current($this->purgeQueue->getPluginsEnabled());
    return $this->purgeQueue->getPlugins()[$id]['label'];
  }

}
