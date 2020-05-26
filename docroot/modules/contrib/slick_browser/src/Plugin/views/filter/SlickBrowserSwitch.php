<?php

namespace Drupal\slick_browser\Plugin\views\filter;

use Drupal\Core\Template\Attribute;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * A special handler to display grid/list view switcher handlers.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("slick_browser_switch")
 */
class SlickBrowserSwitch extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $alwaysMultiple = TRUE;

  /**
   * {@inheritdoc}
   */
  // phpcs:ignore -- this is Drupal core stuff, kindly ignore
  public $no_operator = TRUE;

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function canExpose() {
    return $this->isApplicable();
  }

  /**
   * Checks if we are dealing with the known.
   */
  public function isApplicable() {
    // @todo check if any ::isApplicable() method alike to not display at all.
    // @todo check for other displays if any doable: Mason, GridStack, etc.
    // $eb = $this->displayHandler->getPluginId() === 'entity_browser';
    return $this->isSlick() || $this->view->getStyle()->getPluginId() === 'html_list';
  }

  /**
   * Checks if we are dealing with Slick.
   */
  public function isSlick() {
    // Supports both Slick Browser and Slick Views.
    return strpos($this->view->getStyle()->getPluginId(), 'slick') !== FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['exposed']['default'] = TRUE;
    $options['switch']['default'] = 'both';
    $options['active']['default'] = 'grid';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // @todo make it generic enough outside Slick Browser.
    $options = [
      'grid' => $this->t('Grid'),
      'list' => $this->t('List'),
    ];

    $form['switch'] = [
      '#type'          => 'select',
      '#default_value' => $this->options['switch'],
      '#title'         => $this->t('Slick Browser'),
      '#options'       => $options + ['both' => $this->t('Both Grid and List')],
      '#description'   => $this->t('Some display may not always be suitable to have both switchers. Choose one accordingly.'),
    ];

    $form['active'] = [
      '#type'          => 'select',
      '#default_value' => $this->options['active'],
      '#title'         => $this->t('The first active'),
      '#options'       => $options,
      '#description'   => $this->t('Choose the first active switcher when both is present.'),
      '#states'        => ['visible' => ['select[name*="[switch]"]' => ['value' => 'both']]],
    ];

    // This filter is JS driven, disable irrelevant options.
    $form['expose_button']['#disabled'] = TRUE;
    $form['expose_button']['checkbox']['checkbox']['#description'] = $this->t('Slick Browser requires Views to expose filter.');
    $form['expose']['label']['#access'] = FALSE;
    $form['expose']['description']['#access'] = FALSE;
    $form['expose']['required']['#access'] = FALSE;
    $form['expose']['remember']['#access'] = FALSE;
    $form['expose']['remember_roles']['#access'] = FALSE;
    $form['expose']['identifier']['#weight'] = 100;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);

    $form['value'] = [
      '#type' => 'hidden',
      '#default_value' => $this->value,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildExposedForm(&$form, FormStateInterface $form_state) {
    parent::buildExposedForm($form, $form_state);

    $settings  = $this->buildSettings();
    $switchers = ['count' => '0'];
    $active    = empty($settings['active']) ? 'grid' : $settings['active'];

    if ($switch = $settings['switch']) {
      $form['#attributes']['class'][] = 'form--sb-exposed-' . $switch;

      if ($switch === 'both') {
        $switchers = ['list' => 'List', 'grid' => 'Grid'];
      }
      else {
        $switchers[$switch] = $switch;
        $active = $switch;
      }
    }

    if (empty($switchers)) {
      return;
    }

    $switchers['help'] = '?';
    $buttons = [];
    foreach ($switchers as $key => $title) {
      $attributes = new Attribute();

      $attributes->setAttribute('type', 'button');
      $attributes->setAttribute('data-target', $key);
      $classes = [
        'button',
        'button--view',
        'button--' . $key,
        ($key == $active ? 'is-sb-active' : ''),
      ];
      $attributes->addClass($classes);

      $buttons[] = [
        '#markup' => '<button' . $attributes . '>' . $this->t('@title', ['@title' => $title]) . '</button>',
        '#allowed_tags' => ['button'],
      ];
    }

    // Allows JS contents, .slick__arrow, inserted as part of the switcher.
    // @todo $items['arrows'] = [
    // @todo   '#markup' => '<nav role="navigation" class="slick__arrow button-group button-group--icon button-group--static" id="slick-arrows"><button type="button" data-role="none" class="slick-prev button slick-arrow" aria-label="'. $this->t('Previous') . '">'. $this->t('Previous') . '</button><button type="button" data-role="none" class="slick-next button slick-arrow" aria-label="'. $this->t('Next') . '">'. $this->t('Next') . '</button></nav>',
    // @todo   '#allowed_tags' => ['button', 'nav'],
    // @todo ];
    $items['switchers'] = [
      '#type' => 'container',
      'items' => $buttons,
      '#attributes' => [
        'class' => ['button-group', 'button-group--icon'],
      ],
    ];

    if ($settings['switch'] != 'both') {
      $items['#attributes']['class'][] = 'visually-hidden';
    }

    $form['sb_viewswitch'] = [
      '#type' => 'container',
      'items' => $items,
      '#attributes' => [
        'class' => ['sb__viewswitch'],
        'id' => 'sb-viewswitch',
      ],
      '#settings' => $settings,
      '#weight' => 121,
    ];

    $form['sb_zoom'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['sb__zoom'],
        'id' => 'sb-zoom',
      ],
      '#weight' => 122,
    ];

    $form['#sb_settings'] = $settings;

    // Attach slick browser view library.
    $form['#attached']['library'][] = 'slick_browser/viewswitch';

    // Supports plain core HTML list style plugin, if not using Slick carousel.
    // @todo: Figure out the best way to deal with non-SB Views style.
    if (!$this->isSlick()) {
      $form['#attached']['library'][] = 'blazy/grid';
      $form['#attached']['library'][] = 'slick/slick.main.grid';
    }
  }

  /**
   * Build settings.
   */
  public function buildSettings() {
    $settings = [];
    foreach (['active', 'switch'] as $key) {
      $settings[$key] = isset($this->options[$key]) ? $this->options[$key] : '';
    }
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function acceptExposedInput($input) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  public function adminSummary() {
    if (!$this->isApplicable()) {
      return $this->t('Not applicable! Use it with Slick Browser. Please remove.');
    }
    return empty($this->options['exposed']) ? $this->t('Exposed filter must be enabled!') : $this->options['switch'];
  }

}
