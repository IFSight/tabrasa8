<?php

namespace Drupal\slick_browser;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\entity_browser\Entity\EntityBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements SlickBrowserAlterInterface.
 */
class SlickBrowserAlter implements SlickBrowserAlterInterface {

  use StringTranslationTrait;

  /**
   * Constructs a SlickBrowserAlter instance.
   */
  public function __construct(SlickBrowserInterface $slick_browser) {
    $this->slickBrowser = $slick_browser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('slick_browser')
    );
  }

  /**
   * Implements hook_form_alter().
   */
  public function formAlter(&$form, FormStateInterface &$form_state, $form_id) {
    $form['#attached']['library'][] = 'slick_browser/form';
    $form['#attributes']['class'][] = 'sb sb--wrapper form form--sb blazy clearfix';
    $form['#prefix'] = '<a id="sb-target" tabindex="-1"></a>';

    // Adds header, and footer wrappers to hold navigations and thumbnails.
    $this->addHeaderFooter($form);

    // "Select entities" button.
    $widget = $this->addWidgetSelectEntities($form);

    // Adds Slick Browser library, and helper data attributes or classes.
    $this->addFooterHint($form, $form_state, $widget);

    // Selected items.
    $this->addSelectionDisplay($form);

    // Extracts SB plugin IDs from the form ID. Any better?
    $this->addWidgetClasses($form, $form_id, $widget);
  }

  /**
   * Adds header and footer wrappers to hold navigations and thumbnails.
   */
  private function addHeaderFooter(array &$form) {
    foreach (['header', 'footer', 'counter'] as $key) {
      $form[$key] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['sb__aside', 'sb__' . $key],
        ],
        '#weight' => $key == 'header' ? -9 : -8,
      ];

      // Adds Blazy marker for lazyloading selection thumbnails.
      if ($key == 'footer') {
        $form['footer']['#attributes']['data-blazy'] = '';
      }
    }
  }

  /**
   * Adds widget "Select entities" button.
   */
  private function addWidgetSelectEntities(array &$form) {
    $view = FALSE;
    $select_title = $this->t('Select');
    if (isset($form['widget'])) {
      if (isset($form['widget']['actions'])) {
        $form['widget']['actions']['#weight'] = -7;
        if (!empty($form['widget']['actions']['submit']['#value'])) {
          $select_title = $form['widget']['actions']['submit']['#value'];
        }
        $form['widget']['actions']['#attributes']['class'][] = 'button-group button-group--select button-group--text';
        $form['header']['actions'] = $form['widget']['actions'];
        unset($form['widget']['actions']);
      }

      // Adds relevant classes for the current step identified by active widget.
      foreach (Element::children($form['widget']) as $widget) {
        $widget_css = str_replace('_', '-', $widget);
        $form['#attributes']['class'][] = 'form--' . $widget_css;
        $form['#attributes']['data-dialog'] = $widget_css;

        if ($widget == 'view') {
          $view = TRUE;
          break;
        }
      }
    }
    return ['view' => $view, 'title' => $select_title];
  }

  /**
   * Adds widget Selected items.
   */
  private function addSelectionDisplay(array &$form) {
    if (!empty($form['selection_display'])) {
      // Attach Blazy here once to avoid multiple Blazy invocationa.
      $form['#attached']['library'][] = 'blazy/load';

      // Must be below .entities-list due to sibling selector.
      if (isset($form['selection_display']['ajax_commands_handler'])) {
        $form['selection_display']['ajax_commands_handler']['#weight'] = 201;
      }

      // Wraps self-closed input elements for easy styling, or iconing.
      foreach (['show_selection', 'use_selected'] as $key) {
        if (isset($form['selection_display'][$key])) {
          $input = &$form['selection_display'][$key];
          $access = $form['selection_display'][$key]['#access'];

          // Enforces visibility regarless empty or not, we use JS, not AJAX,
          // for faster selections.
          $form['selection_display'][$key]['#attributes']['class'][] = $access ? 'is-btn-visible' : 'is-btn-hidden';
          $form['selection_display'][$key]['#access'] = TRUE;

          // Enforces visibility for dynamic items, and let JS takes care of it.
          if ($key == 'show_selection') {
            SlickBrowserUtil::wrapButton($input, $key);
          }
          if ($key == 'use_selected') {
            $form['selection_display'][$key]['#weight'] = -9;
            $form['selection_display'][$key]['#attributes']['class'][] = 'button--primary';
            $form['header']['actions'][$key] = $form['selection_display'][$key];

            unset($form['selection_display'][$key]);
          }
        }
      }

      if (isset($form['selection_display']['selected'])) {
        $form['selection_display']['selected']['#weight'] = 200;

        // Wraps self-closed input elements for easy styling, or iconing.
        foreach (Element::children($form['selection_display']['selected']) as $key) {
          if (isset($form['selection_display']['selected'][$key]['remove_button'])) {
            $input = &$form['selection_display']['selected'][$key]['remove_button'];
            SlickBrowserUtil::wrapButton($input, 'remove');
          }
        }
      }

      $form['footer']['selection_display'] = $form['selection_display'];
      unset($form['selection_display']);
    }
    else {
      $form['#attributes']['class'][] = 'is-no-selection';
    }
  }

  /**
   * Adds widget footer hint.
   */
  private function addFooterHint(array &$form, FormStateInterface $form_state, $widget) {
    $storage = $form_state->getStorage();
    $validators = isset($storage['entity_browser']['validators']) ? $storage['entity_browser']['validators'] : [];
    $target_type = isset($validators['entity_type']['type']) ? $validators['entity_type']['type'] : '';

    $form['#attributes']['data-target-type'] = $target_type;

    // @todo figure out why sometimes not available here?
    $cardinality = isset($validators['cardinality']['cardinality']) ? $validators['cardinality']['cardinality'] : -1;
    $cardinality = $cardinality ?: (isset($storage['entity_browser']['widget_context']['cardinality']) ? $storage['entity_browser']['widget_context']['cardinality'] : $cardinality);
    $form['#attributes']['data-cardinality'] = $cardinality;

    $count = $cardinality == -1 ? 'Unlimited' : $cardinality;
    $text = $this->formatPlural($count, 'One item allowed.', '@count items allowed.');
    if ($cardinality != -1) {
      $text .= ' ' . $this->t('Remove one to select another.');
    }
    $hints[] = $text;

    $hints[] = $this->t('Hit <b>@select</b> button to temporarily store selection. Otherwise lost on changing pages, or tabs.', ['@select' => $widget['title']]);
    if (isset($form['selection_display']['use_selected'])) {
      $hints[] = $this->t('Hit <b>@add</b> to store and add to the page quickly.', ['@add' => $form['selection_display']['use_selected']['#value']]);
    }
    $hints[] = $this->t('Swipe left/right or top/bottom to navigate between sub-pages based on arrow directions if available.');

    $classes = ['sb__cardinality', 'sb__help', 'visible-help'];
    $form['footer']['cardinality_hint'] = [
      '#theme' => 'item_list',
      '#items' => $hints,
      '#attributes' => ['class' => $classes],
      '#weight' => -9,
    ];

    // Adds empty attributes.
    if (empty($storage['entity_browser']['selected_entities'])) {
      $form['#attributes']['class'][] = 'is-sb-empty';
    }
  }

  /**
   * Adds widget classes.
   */
  private function addWidgetClasses(array &$form, $form_id, $widget) {
    $id = str_replace(['entity_browser_', '_form'], '', $form_id);

    // Adds contextual classes based on SB entity browser entity.
    if ($eb = EntityBrowser::load($id)) {
      // Modal, iframe, etc.
      $form['#attributes']['class'][] = 'form--' . str_replace('_', '-', $eb->getDisplay()->getPluginId());
      $form['#attributes']['class'][] = 'form--' . str_replace('_', '-', $eb->getWidgetSelector()->getPluginId());

      // Entity display plugins: slick_browser_file, slick_browser_media, etc.
      // Has selection_position.
      $config_selection = $eb->getSelectionDisplay()->getConfiguration();

      // Has buttons_position, tabs_position.
      $config_selector = $eb->getWidgetSelector()->getConfiguration();

      $selections = '';
      // @todo empty for file?
      if (!empty($config_selection) && !empty($config_selection['display_settings']['selection_position'])) {
        $selections = $config_selection['display_settings']['selection_position'];

        // BC for over-bottom replaced with just bottom to avoid complexity.
        if ($selections == 'over-bottom') {
          $selections = 'bottom';
        }
        $form['#attributes']['class'][] = 'form--selection-' . $selections;
        $form['#attributes']['class'][] = in_array($selections, ['left', 'right']) ? 'form--selection-v' : 'form--selection-h';
      }

      if (!empty($config_selector['tabs_position'])) {
        $buttons = $config_selector['buttons_position'];
        $tabs = $config_selector['tabs_position'];
        $tabs_pos = '';

        // Tabs at sidebars within selection display.
        if (($tabs == 'left' && $selections == 'left') || ($tabs == 'right' && $selections == 'right')) {
          $tabs_pos = 'footer';
        }
        // Tabs at header along with navigation buttons/ arrows.
        elseif (($tabs == 'bottom' && $buttons == 'bottom') || ($tabs == 'top' && $buttons == 'top')) {
          $tabs_pos = 'header';
        }

        $form['#attributes']['data-tabs-pos'] = $tabs_pos;

        // Adds classes to identify the amount of tabs, etc.
        if (isset($widget['view']) && !empty($form['widget_selector'])) {
          $count = count(Element::children($form['widget_selector']));
          $form['#attributes']['class'][] = $count > 2 ? 'form--tabs-stacked' : 'form--tabs-inline';
        }
      }
    }
  }

  /**
   * Implements hook_theme_suggestions_alter().
   */
  public function themeSuggestionsAlter(array &$suggestions, array $variables, $hook) {
    $settings = isset($variables['element']['#settings']) ? $variables['element']['#settings'] : [];

    if (!empty($settings['display']) && $settings['display'] == 'main') {
      foreach (['slick', 'slick_grid', 'slick_slide', 'slick_vanilla'] as $item) {
        if ($hook == $item) {
          // Uses the same template for slide and vanilla.
          $suggestions[] = $hook == 'slick_slide' ? 'slick_vanilla__browser' : $hook . '__browser';
        }
      }
    }
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter().
   */
  public function formViewsUiAddHandlerFormAlter(&$form, FormStateInterface &$form_state, $form_id) {
    $view = $form_state->get('view');

    // Excludes Slick Browser filter from not-easily doable style plugins.
    $executable = $view->getExecutable();
    $plugins = ['blazy', 'slick', 'slick_browser', 'html_list'];
    if ($executable && !in_array($executable->getStyle()->getPluginId(), $plugins)) {
      unset($form['options']['name']['#options']['views.slick_browser_switch']);
    }
  }

  /**
   * Implements hook_form_views_exposed_form_alter().
   */
  public function formViewsExposedFormAlter(&$form, FormStateInterface $form_state) {
    if (strpos($form['#id'], 'views-exposed-form-slick-browser-media') !== FALSE) {
      if (isset($form['bundle'])) {
        $form['bundle']['#type'] = 'radios';
        $form['bundle']['#weight'] = 120;
        $form['bundle']['#title_display'] = 'hidden';
        $form['bundle']['#attributes']['class'][] = 'sb__radios';

        if (isset($form['bundle']['#options']['All'])) {
          $form['bundle']['#options']['All'] = $this->t('- All -');
        }

        $form['#attributes']['class'][] = 'sb__autosubmit';
        $form['#attached']['library'][] = 'slick_browser/autosubmit';
      }
    }
  }

  /**
   * Implements hook_field_widget_third_party_settings_form().
   *
   * Specific to Image with file entity, not provided by EB.
   * This form is loaded at /admin/structure/ENTITY/manage/BUNDLE/form-display.
   * Cases:
   *   - EB File with cardinality 1 or > 1, not -1, via entity_browser_file.
   *   - Media Library via media_library_widget, -1, etc.
   * Those not in cases are loaded via EntityBrowserFieldWidgetDisplay plugin.
   */
  public function widgetThirdPartySettingsForm(WidgetInterface $plugin, FieldDefinitionInterface $field_definition, $form_mode, $form, FormStateInterface $form_state) {
    $cardinality = $field_definition->getFieldStorageDefinition()->getCardinality();
    $field_settings = $field_definition->getSettings();
    $definition = [
      'cardinality'      => $cardinality,
      'settings'         => SlickBrowserUtil::buildThirdPartySettings($plugin),
      'field_name'       => $field_definition->getname(),
      'field_settings'   => $field_settings,
      'target_type'      => isset($field_settings['target_type']) ? $field_settings['target_type'] : $field_definition->getTargetEntityTypeId(),
      'view_mode'        => 'slick_browser',
      'widget_settings'  => $plugin->getSettings(),
      'plugin_id_widget' => $plugin->getPluginId(),
    ] + SlickBrowserUtil::scopedFormElements();

    $element = [];
    $this->slickBrowser->buildSettingsForm($element, $definition);

    return $element;
  }

}
