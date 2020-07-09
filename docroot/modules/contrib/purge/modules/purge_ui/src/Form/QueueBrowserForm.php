<?php

namespace Drupal\purge_ui\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\purge\Plugin\Purge\Queue\QueueServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The queue data browser.
 */
class QueueBrowserForm extends FormBase {
  use CloseDialogTrait;

  /**
   * The 'purge.queue' service.
   *
   * @var \Drupal\purge\Plugin\Purge\Queue\QueueServiceInterface
   */
  protected $purgeQueue;

  /**
   * The number of items to show in the data table.
   *
   * @var int
   */
  protected $numberOfItems = 15;

  /**
   * Construct a QueueBrowserForm object.
   *
   * @param \Drupal\purge\Plugin\Purge\Queue\QueueServiceInterface $purge_queue
   *   The purge queue service.
   */
  final public function __construct(QueueServiceInterface $purge_queue) {
    $this->purgeQueue = $purge_queue;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('purge.queue'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'purge_ui.queue_browser_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#prefix'] = '<div id="browserwrapper">';
    $form['#suffix'] = '</div>';

    // This is rendered as a modal dialog, so we need to set some extras.
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    // Store paging information in form state so we can easily update it.
    if (is_null($form_state->get('pages'))) {
      $form_state->set('pages', $this->purgeQueue->selectPageMax());
      $form_state->set('page', 1);
    }
    $pages = $form_state->get('pages');
    $page = $form_state->get('page');

    // Define a anonymous function with which we can easily add buttons.
    $button = function ($overrides = []) {
      return $overrides + [
        '#type' => 'submit',
        '#name' => 'page',
        '#submit' => [[$this, 'submitForm']],
        '#ajax' => [
          'callback' => '::submitForm',
          'wrapper' => 'browserwrapper',
        ],
      ];
    };

    // Generate the table filled with the paged data.
    $header = [
      ['data' => $this->t('Type')],
      ['data' => $this->t('State')],
      ['data' => $this->t('Expression')],
    ];
    $form['wrapper']['table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => [],
    ];
    $this->purgeQueue->selectPageLimit($this->numberOfItems);
    foreach ($this->purgeQueue->selectPage($page) as $immutable) {
      $form['wrapper']['table']['#rows'][] = [
        'data' => [
          $immutable->getPluginDefinition()['label'],
          $immutable->getStateStringTranslated(),
          $immutable->getExpression(),
        ],
      ];
    }
    if (empty($form['wrapper']['table']['#rows'])) {
      $form['wrapper']['table'] = [
        '#markup' => $this->t("Your queue is empty."),
      ];
    }

    // Build a pager, as '#theme' => 'pager' doesn't work in AJAX modals.
    $form['pager'] = [];
    $form['pager']['page']['first'] = $button([
      '#value' => '<<',
      '#access' => $page > 4,
    ]);
    $links = 2;
    $start = (($page - $links) > 0) ? $page - $links : 1;
    $end = (($page + $links) < $pages) ? $page + $links : $pages;
    for ($i = $start; $i <= $end; $i++) {
      $form['pager']['page'][$i] = $button([
        '#value' => $i,
        '#button_type' => $page == $i ? 'primary' : '',
      ]);
    }
    $form['pager']['page']['last'] = $button([
      '#value' => ">> $pages",
      '#access' => $page < ($pages - 4),
    ]);
    if (count($form['pager']['page']) === 3) {
      unset($form['pager']);
    }

    // Define the close button and return the form definition.
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['close'] = [
      '#ajax' => ['callback' => '::closeDialog'],
      '#value' => $this->t('Close'),
      '#button_type' => 'primary',
      '#type' => 'submit',
      '#weight' => -10,
    ];
    $form['actions']['refresh'] = $button(['#value' => $this->t("Refresh")]);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $op = (string) $form_state->getValue('page');
    $page = $form_state->get('page');
    $pages = $form_state->get('pages');

    if (is_numeric($op)) {
      $page = (int) $op;
    }
    elseif ($op == '<<') {
      $page = 1;
    }
    elseif ($op == ">> $pages") {
      $page = $pages;
    }

    $form_state->set('page', $page);
    $form_state->setRebuild();
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#browserwrapper', $form));
    return $form;
  }

}
