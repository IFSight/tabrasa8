<?php

namespace Drupal\slick_browser;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\blazy\BlazyEntityInterface;
use Drupal\slick\Form\SlickAdminInterface;
use Drupal\slick\SlickFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a Slick Browser.
 */
class SlickBrowser implements SlickBrowserInterface {

  use StringTranslationTrait;

  /**
   * The slick admin.
   *
   * @var \Drupal\slick\Form\SlickAdminInterface
   */
  protected $slickAdmin;

  /**
   * The slick field formatter manager.
   *
   * @var \Drupal\slick\SlickFormatterInterface
   */
  protected $formatter;

  /**
   * The slick field formatter manager.
   *
   * @var \Drupal\blazy\BlazyEntityInterface
   */
  protected $blazyEntity;

  /**
   * The current page path.
   *
   * @var string
   */
  protected $currentPath;

  /**
   * Constructs a SlickBrowser instance.
   */
  public function __construct(BlazyEntityInterface $blazy_entity, SlickAdminInterface $slick_admin, SlickFormatterInterface $formatter) {
    $this->blazyEntity = $blazy_entity;
    $this->slickAdmin = $slick_admin;
    $this->formatter = $formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('blazy.entity'),
      $container->get('slick.admin'),
      $container->get('slick.formatter')
    );
  }

  /**
   * Returns the slick admin service.
   */
  public function slickAdmin() {
    return $this->slickAdmin;
  }

  /**
   * Returns the slick manager.
   */
  public function manager() {
    return $this->slickAdmin->manager();
  }

  /**
   * Returns the slick formatter.
   */
  public function formatter() {
    return $this->formatter;
  }

  /**
   * Returns the blazy manager.
   */
  public function blazyManager() {
    return $this->formatter;
  }

  /**
   * Returns the blazy entity.
   */
  public function blazyEntity() {
    return $this->blazyEntity;
  }

  /**
   * Defines common widget form elements.
   */
  public function buildSettingsForm(array &$form, $definition) {
    $cardinality = isset($definition['cardinality']) ? $definition['cardinality'] : '';
    $plugin_id_widget = isset($definition['plugin_id_widget']) ? $definition['plugin_id_widget'] : '';
    $plugin_id_widget = isset($definition['plugin_id_entity_display']) ? $definition['plugin_id_entity_display'] : $plugin_id_widget;
    $target_type = isset($definition['target_type']) ? $definition['target_type'] : '';

    // Build form elements.
    $this->slickAdmin->buildSettingsForm($form, $definition);

    $form['#attached']['library'][] = 'slick_browser/admin';

    // Slick Browser can display a plain static grid or slick carousel.
    if (isset($form['style'])) {
      $form['style']['#description'] = $this->t('Either <strong>CSS3 Columns</strong> (experimental pure CSS Masonry) or <strong>Grid Foundation</strong> requires Grid. Difference: <strong>Columns</strong> is best with irregular image sizes. <strong>Grid</strong> with regular ones.');

      if (strpos($plugin_id_widget, 'browser') !== FALSE) {
        $form['style']['#options']['slick'] = $this->t('Slick Carousel');
        $form['style']['#description'] .= ' ' . $this->t('Both do not carousel unless choosing <strong>Slick carousel</strong>. Requires the above relevant "Entity browser" plugin containing "Slick Browser" in the name, otherwise useless.');
      }
      $form['style']['#description'] .= ' ' . $this->t('Leave empty to disable Slick Browser widget.');

      // Single image preview should only have one option.
      if ($cardinality == 1) {
        $form['style']['#options'] = [];
        $form['style']['#options']['single'] = $this->t('Single Preview');
      }
    }

    // Use a specific widget group skins to avoid conflict with frontend.
    if (isset($form['skin'])) {
      $form['skin']['#options'] = $this->slickAdmin->getSkinsByGroupOptions('widget');
      $form['skin']['#description'] .= ' <br>' . $this->t('<b>Widget: Split</b> is best for Image field which has Alt and Title defined so that the display can be split/ shared with image preview. <br><b>Widget: Grid</b> for multi-valu fields, not single.');
    }

    // Removes Grid Browser which is dedicated for the browser, not widget.
    if (isset($form['optionset']) && isset($form['optionset']['#options']['grid_browser'])) {
      unset($form['optionset']['#options']['grid_browser']);
    }

    if (isset($form['view_mode'])) {
      $form['view_mode']['#weight'] = 22;
      $form['view_mode']['#description'] = $this->t('Will fallback to this view mode, else entity label. Be sure to enable and configure the view mode. Leave it Default if unsure.');
    }

    if (isset($form['image_style'])) {
      // The media_library_widget has no image style defined.
      $form['image_style']['#description'] = $this->t('Choose image style for the preview, if applicable. If any defined above, this will override it.');
    }
    if (isset($form['grid'])) {
      $form['grid']['#description'] = $this->t('The amount of block grid columns for large monitors 64.063em - 90em.');
    }

    if (isset($form['thumbnail_style'])) {
      $form['thumbnail_style']['#description'] = $this->t('Required if Optionset thumbnail is provided. Leave empty to not use thumbnails.');
    }

    if ($target_type && !in_array($target_type, ['file', 'media'])) {
      unset($form['image_style'], $form['thumbnail_style']);
    }

    // Exclude fancy features.
    $excludes = [
      'media_switch',
      'elevatezoomplus',
      'layout',
      'ratio',
      'box_style',
      'thumbnail_effect',
      'preserve_keys',
      'visible_items',
    ];
    foreach ($excludes as $key) {
      if (isset($form[$key])) {
        unset($form[$key]);
      }
    }
  }

  /**
   * Implements hook_preprocess_views_view().
   */
  public function preprocessViewsView(&$variables) {
    if ($plugin_id = $variables['view']->getStyle()->getPluginId()) {
      if ($plugin_id == 'slick_browser') {
        $variables['attributes']['class'][] = 'sb view--sb';

        // Adds class based on entity type ID for further styling.
        if ($entity_type = $variables['view']->getBaseEntityType()->id()) {
          $variables['attributes']['class'][] = 'view--' . str_replace('_', '-', $entity_type);
        }

        // Adds class based on pager to position it either fixed, or relative.
        if ($pager_id = $variables['view']->getPager()->getPluginId()) {
          $variables['attributes']['class'][] = 'view--pager-' . str_replace('_', '-', $pager_id);
        }
      }
    }

    // Adds the active grid/ list (table-like) class regardless style plugin.
    if (isset($variables['view']->exposed_widgets['#sb_settings'])) {
      $variables['attributes']['class'][] = 'view--sb-' . $variables['view']->exposed_widgets['#sb_settings']['active'];
    }
  }

  /**
   * Implements hook_preprocess_blazy().
   */
  public function preprocessBlazy(&$variables) {
    $settings = &$variables['settings'];
    if (!empty($settings['_sb_views'])) {
      $variables['postscript']['sb_buttons'] = SlickBrowserUtil::buildButtons(['select', 'info']);
    }
  }

}
