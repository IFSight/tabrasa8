<?php

namespace Drupal\slick_browser;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\entity_browser\Entity\EntityBrowser;
use Drupal\blazy\BlazyGrid;

/**
 * Implements SlickBrowserWidgetInterface.
 */
class SlickBrowserWidget extends SlickBrowserAlter implements SlickBrowserWidgetInterface {

  /**
   * Implements hook_field_widget_WIDGET_TYPE_form_alter().
   */
  public function fieldWidgetFormAlter(&$element, FormStateInterface $form_state, $context) {
    $settings = [];
    $plugin_id = $context['widget']->getPluginId();

    // Always assumes no "Display style" of SB widgets is enabled.
    if ($plugin_id == 'entity_browser_entity_reference') {
      $settings = $this->widgetEntityBrowserEntityReferenceFormAlter($element, $context);
    }
    elseif ($plugin_id == 'entity_browser_file') {
      $settings = $this->widgetEntityBrowserFileFormAlter($element, $context);
    }
    elseif ($plugin_id == 'media_library_widget') {
      $settings = $this->widgetMediaLibraryWidgetFormAlter($element, $context);
    }

    // Ony proceed if we are conciously allowed via "Display style" option.
    // This settings may contain configurable third party settings.
    if (empty($settings)) {
      return;
    }

    // We are here because we are allowed to.
    // Build common settings to all supported plugins.
    // The non-image field type has 'display_field', 'description'.
    $settings = array_merge($this->widgetSettings($element, $context), array_filter($settings));
    $settings['plugin_id_field_widget_display'] = isset($settings['plugin_id_field_widget_display']) ? $settings['plugin_id_field_widget_display'] : '';
    $settings['use_label'] = $settings['plugin_id_field_widget_display'] == 'slick_browser_label';
    $settings['use_slick'] = isset($settings['style']) && $settings['style'] == 'slick';
    $settings['use_grid'] = !empty($settings['style']) && !empty($settings['grid']) && in_array($settings['style'], ['column', 'grid']);
    $settings['use_autosubmit'] = $settings['entity_type_id'] == 'media';

    // EB only respects field_widget_display with cardinality -1. If not, we
    // may need to override its display. Only concerns about File entity.
    // The _processed flag means SB plugin output is already processed at plugin
    // level, no further work with its display output is needed from here on.
    $file = isset($settings['target_type']) && $settings['target_type'] == 'file';
    $unlimited = isset($settings['cardinality']) && $settings['cardinality'] == -1;
    $settings['_processed'] = $file && !$unlimited ? FALSE : TRUE;

    // Don't bother if using label.
    if (empty($settings['use_label'])) {
      // Yet only modify for EB, not core Media Library.
      if (!empty($settings['_eb'])) {
        $this->widgetDisplayEntityBrowser($element, $settings, $context);
      }
    }

    // Specific for EB, it might add own theme to style entity browser elements.
    $this->widgetElement($element, $settings, $context);
    $this->widgetAttach($element, $settings);
  }

  /**
   * Modifies the available widget settings.
   */
  private function widgetSettings(array &$element, $context) {
    $plugin = $context['widget'];
    $items = $context['items'];
    $field = $items->getFieldDefinition();
    $entity = $items->getEntity();
    $settings = [];
    $settings['_internal']['widget'] = $plugin->getSettings();
    $field_settings = $settings['_internal']['field'] = $field->getSettings();

    foreach (['alt_field', 'title_field', 'target_type'] as $key) {
      $settings[$key] = isset($field_settings[$key]) ? $field_settings[$key] : FALSE;
    }

    $settings['plugin_id_widget'] = $settings['_internal']['widget']['plugin_id'] = $plugin->getPluginId();
    $settings['bundle'] = $entity->bundle();
    $settings['cardinality'] = $field->getFieldStorageDefinition()->getCardinality();
    $settings['field_name'] = $context['items']->getName();
    $settings['field_type'] = $field->getType();
    $settings['entity_type_id'] = $entity->getEntityTypeId();
    $settings['_eb'] = $settings['_ml'] = FALSE;
    $settings['use_media'] = $settings['use_grid'] = $settings['use_modal'] = $settings['use_tabs'] = FALSE;
    $settings['media_switch'] = 'media';
    $settings['ratio'] = 'fluid';
    $settings['thumbnail_style'] = empty($settings['thumbnail_style']) ? 'slick_browser_thumbnail' : $settings['thumbnail_style'];

    // Entity Browser integration.
    if (in_array($settings['plugin_id_widget'], ['entity_browser_entity_reference', 'entity_browser_file'])) {
      $this->widgetEntityBrowserSettings($element, $settings, $context);
    }

    ksort($settings);
    return $settings;
  }

  /**
   * Modifies the available entity browser widget settings.
   */
  private function widgetEntityBrowserSettings(array &$element, array &$settings, $context) {
    $widget_settings = $context['widget']->getSettings();
    $settings['_eb'] = TRUE;
    $settings['_browser'] = TRUE;

    if (!empty($widget_settings['preview_image_style']) && empty($settings['image_style'])) {
      $settings['image_style'] = $widget_settings['preview_image_style'];
    }

    // Chances are SB browsers within iframes/modals, even if no SB widgets.
    // Or using any of SB field_widget_display.
    // Only EntityReferenceBrowserWidget has field_widget_display, not FBW.
    $widget = empty($settings['plugin_id_field_widget_display']) ? '' : $settings['plugin_id_field_widget_display'];
    $settings['use_media'] = $widget == 'slick_browser_file' || $widget == 'slick_browser_media';

    // Load relevant assets based on the chosen SB browsers plugins.
    if (!empty($widget_settings['entity_browser'])) {
      $id = $widget_settings['entity_browser'];
      if ($eb = EntityBrowser::load($id)) {

        // Entity display plugins: slick_browser_file, slick_browser_media, etc.
        $settings['_internal']['selection_display'] = $eb->getSelectionDisplay()->getConfiguration();
        $settings['_internal']['selection_display']['plugin_id'] = $settings['plugin_id_selection_display'] = $eb->getSelectionDisplay()->getPluginId();
        $settings['_internal']['widget_selector'] = $eb->getWidgetSelector()->getConfiguration();
        $settings['_internal']['widget_selector']['plugin_id'] = $settings['plugin_id_widget_selector'] = $eb->getWidgetSelector()->getPluginId();
        $settings['_internal']['widget_display'] = $eb->getDisplay()->getConfiguration();
        $settings['_internal']['widget_display']['plugin_id'] = $settings['plugin_id_display'] = $eb->getDisplay()->getPluginId();

        // Selection displays: modal, iframe, form, etc.
        $settings['use_modal'] = $settings['plugin_id_display'] == 'modal';
        $settings['use_tabs'] = $settings['plugin_id_widget_selector'] == 'slick_browser_tabs';
      }
    }

    if (isset($element['current'])) {
      $children = Element::children($element['current']);
      $settings['count'] = count($children);
      if (isset($element['current']['items'])) {
        $settings['count'] = count($element['current']['items']);
      }
    }
  }

  /**
   * Modifies the widget form element.
   */
  private function widgetElement(array &$element, array &$settings, $context) {
    // Build the SB widgets, nothing to do with SB browsers here on.
    // This used to be "Slick Widget", moved into "Slick Browser".
    $classes = isset($element['#attributes']['class']) ? $element['#attributes']['class'] : [];
    $sb_classes = ['sb', 'sb--wrapper', 'sb--launcher'];
    $element['#attributes']['class'] = array_merge($sb_classes, $classes);
    $attributes = &$element['#attributes'];
    $attributes['class'][] = empty($settings['use_modal']) ? 'sb--wrapper-inline' : 'sb--wrapper-modal';
    $attributes['class'][] = $settings['style'] == 'slick' && !empty($settings['skin']) ? 'sb--skin--' . str_replace('_', '-', $settings['skin']) : 'sb--skin--static';
    if ($settings['use_autosubmit']) {
      $attributes['class'][] = 'sb--autoselect';
    }

    // Media Library integration: plugin_id_widget = media_library_widget.
    if (isset($element['selection'])) {
      $this->widgetMediaLibraryElement($element, $settings, $context);
    }
    // Entity Browser integration has property current.
    elseif (isset($element['current'])) {
      $this->widgetEntityBrowserElement($element, $settings, $context);
    }

    $element['#attributes']['data-sb-bundle'] = $settings['bundle'];
    $element['#attributes']['data-sb-entity-type-id'] = $settings['entity_type_id'];
    $element['#attributes']['data-sb-field-type'] = $settings['field_type'];
    $element['#attributes']['data-sb-target-type'] = $settings['target_type'];
    $element['#attributes']['data-sb-plugin-id-widget'] = $settings['plugin_id_widget'];
    $element['#attributes']['data-sb-entity-browser'] = isset($settings['entity_browser']) ? $settings['entity_browser'] : '';
    $element['#attributes']['data-sb-cardinality'] = isset($settings['cardinality']) ? $settings['cardinality'] : 0;
  }

  /**
   * Modifies the available media library widget settings.
   *
   * Unlike EB with full override, this is all we do with Media Library for now.
   * Basically making the Media Library grid configurable, and blazy-enabled.
   */
  private function widgetMediaLibraryElement(array &$element, array &$settings, $context) {
    $settings['_ml'] = $settings['use_media'] = $settings['use_grid'] = TRUE;
    if (isset($element['selection'])) {
      $children = Element::children($element['selection']);
      $settings['count'] = count($children);
      $attributes = &$element['selection']['#attributes'];

      BlazyGrid::attributes($attributes, $settings);
      // Cannot use content_attributes, not all core themes have it at fieldset.
      $attributes['class'][] = 'sb sb--widget';
      if ($children) {
        foreach ($children as $delta) {
          $settings['delta'] = $delta;

          $element['selection'][$delta]['#attributes']['class'][] = 'grid grid--' . $delta;
          // Respects anyone adding a suffix here.
          if (isset($element['selection']['#suffix'])) {
            $element['selection']['#suffix'] .= '<div class="sb__zoom"></div>';
          }
          else {
            $element['selection']['#suffix'] = '<div class="sb__zoom"></div>';
          }

          if (!empty($element['selection'][$delta]['rendered_entity'])) {
            $rendered = &$element['selection'][$delta]['rendered_entity'];
            if ($entity = $rendered['#media']) {
              $cache = $rendered['#cache'];
              $settings['_processed'] = FALSE;
              $this->widgetItemDisplay($element, $settings, $entity);
              $rendered['#cache'] = $cache;
            }
          }
        }
      }
    }
  }

  /**
   * Modifies the entity browser widget identified by element current.
   */
  private function widgetEntityBrowserElement(array &$element, array &$settings, $context) {
    // Prevents collapsed details from breaking lazyload.
    if (empty($element['#open'])) {
      $element['#open'] = TRUE;
      $element['#attributes']['class'][] = 'sb--wrapper-hidden';
    }

    $element['current']['#settings']       = $settings;
    $element['current']['#attributes']     = [];
    $element['current']['#theme_wrappers'] = [];
    $element['current']['#theme']          = 'slick_browser';

    // Removes table markups for regular divities.
    if ($settings['plugin_id_widget'] == 'entity_browser_file') {
      unset($element['current']['#type'], $element['current']['#header'], $element['current']['#tabledrag']);
    }
  }

  /**
   * Provides asset attachments.
   */
  private function widgetAttach(array &$element, array $settings) {
    // Enforce Blazy to work with hidden element such as with EB selection.
    $load = $this->slickBrowser->blazyManager()->attach($settings);
    $load['drupalSettings']['blazy']['loadInvisible'] = TRUE;
    $load['library'][] = 'slick_browser/widget';

    foreach (['autosubmit', 'grid', 'modal', 'slick', 'tabs'] as $key) {
      if (!empty($settings['use_' . $key])) {
        $load['library'][] = 'slick_browser/' . $key;
      }
    }

    // Disable tabledrag, including FBW table CSS, for Slick/ CSS grid.
    if ($settings['plugin_id_widget'] == 'entity_browser_file') {
      $attachments = $load;
    }
    else {
      $attachments = isset($element['current'], $element['current']['#attached']) ? NestedArray::mergeDeep($element['current']['#attached'], $load) : $load;
    }

    $element['#attached'] = isset($element['#attached']) ? NestedArray::mergeDeep($element['#attached'], $attachments) : $attachments;
  }

  /**
   * Implements hook_field_widget_WIDGET_TYPE_form_alter().
   */
  private function widgetEntityBrowserEntityReferenceFormAlter(array &$element, $context) {
    $widget_settings = $context['widget']->getSettings();
    if (empty($widget_settings['field_widget_display']) || strpos($widget_settings['field_widget_display'], 'slick_browser') === FALSE) {
      return FALSE;
    }

    $settings = $widget_settings['field_widget_display_settings'];
    $settings['plugin_id_field_widget_display'] = $widget_settings['field_widget_display'];
    return empty($settings['style']) ? FALSE : array_merge(SlickBrowserDefault::entitySettings(), $settings);
  }

  /**
   * Implements hook_field_widget_WIDGET_TYPE_form_alter().
   */
  private function widgetEntityBrowserFileFormAlter(array &$element, $context) {
    $widget_settings = $context['widget']->getSettings();
    if (empty($widget_settings['entity_browser']) || strpos($widget_settings['entity_browser'], 'slick_browser') === FALSE) {
      return FALSE;
    }

    // Allows Slick Browser to remove File Browser empty table.
    $settings = SlickBrowserUtil::buildThirdPartySettings($context['widget']);
    return empty($settings['style']) ? FALSE : $settings;
  }

  /**
   * Implements hook_field_widget_WIDGET_TYPE_form_alter().
   */
  private function widgetMediaLibraryWidgetFormAlter(array &$element, $context) {
    // Allows to provide configurable grids.
    $settings = SlickBrowserUtil::buildThirdPartySettings($context['widget']);
    return empty($settings['style']) ? FALSE : $settings;
  }

  /**
   * Prepare entity displays for entity browser.
   *
   * EB only respects field_widget_display with cardinality -1. If not, we
   * may need to override its display such as for single-value file/ media.
   */
  private function widgetDisplayEntityBrowser(array &$element, array $settings, $context) {
    // The items property is for entity, not file image, except cardinality -1.
    // Cannot rely on $context['items'] for AJAX results. The entity_browser
    // property is only available for:
    // File, indexed by entity ID: cardinality -1 and > 1, but not 1,
    // Media, grouped by items property: cardinality -1, but not 1, nor > 1.
    $entities = isset($element['entity_browser']) ? $element['entity_browser']['#default_value'] : [];
    if (empty($entities) && isset($element['current'])) {
      if ($children = Element::children($element['current'])) {

        // Maybe empty, but items is always set.
        // File is never here.
        // Media, grouped by items property: cardinality 1, > 1, not -1.
        if (isset($element['current']['items']) && $children = Element::children($element['current']['items'])) {
          foreach ($children as $delta) {
            $settings['delta'] = $delta;
            if (isset($element['current']['items'][$delta]['display']['#entity']) && $entity = $element['current']['items'][$delta]['display']['#entity']) {
              $this->widgetItemDisplay($element, $settings, $entity);
            }
          }
        }
        else {
          // Indexed by entity ID.
          // File, indexed by entity ID: cardinality 1.
          foreach ($children as $delta => $item_id) {
            $settings['delta'] = $delta;
            if ($settings['field_type'] == 'image') {
              $this->widgetImageDisplay($element['current'][$item_id], $settings);
            }
            else {
              if ($entity = $this->slickBrowser->blazyManager()->entityLoad($item_id, $settings['target_type'])) {
                $this->widgetItemDisplay($element, $settings, $entity);
              }
            }
          }
        }
      }
    }
    else {
      // Hence we have entities, except single file image.
      // File, indexed by entity ID: cardinality -1 and > 1, but not 1,
      // Media, grouped by items property: cardinality -1, but not 1, nor > 1.
      foreach ($entities as $delta => $entity) {
        $settings['delta'] = $delta;
        $this->widgetItemDisplay($element, $settings, $entity);
      }
    }
  }

  /**
   * Overrides image style since preview is not always available.
   */
  private function widgetImageDisplay(array &$item, array $settings) {
    if (!empty($settings['image_style']) && isset($item['display'], $item['display']['#style_name'])) {
      foreach (['height', 'uri', 'width'] as $key) {
        if (isset($item['display']['#' . $key])) {
          $settings[$key] = $item['display']['#' . $key];
        }
      }
      $build = ['settings' => $settings];
      $item['display'] = $this->slickBrowser->blazyManager()->getBlazy($build);
    }
  }

  /**
   * Prepares entity item display, applicable to EB and Media Library.
   */
  private function widgetItemDisplay(array &$element, array $settings, $entity) {
    $translation = $this->slickBrowser->blazyManager()->getEntityRepository()->getTranslationFromContext($entity);
    $label = $translation ? $translation->label() : $entity->label();
    $delta = $settings['delta'];
    $display = $content = [];
    $settings['bundle'] = $entity->bundle();

    // If not already processed, proceed. Processed means plugin is respected
    // which is currently not the case given different cardinality for File.
    if (empty($settings['_processed'])) {
      /** @var \Drupal\file\Entity\File $entity */
      $data = $this->slickBrowser->blazyEntity()->oembed()->getImageItem($entity);
      $data['settings'] = isset($data['settings']) ? array_merge($settings, $data['settings']) : $settings;

      if (isset($element['selection'])) {
        $data['settings']['item_id'] = 'sb';
        $data['wrapper_attributes']['class'][] = 'grid__content';
        $data['media_attributes']['class'][] = 'sb__preview media-library-item__preview js-media-library-item-preview';
        $data['postscript']['sb_label']['#markup'] = '<div class="sb__label">' . $label . '</div>';
      }

      $display = $this->slickBrowser->blazyEntity()->build($data, $entity, $label);

      $settings = $data['settings'];
      $display['#settings'] = isset($display['#settings']) ? array_merge($settings, $display['#settings']) : $settings;
    }

    // EB put them in items property for cardinality -1 + entity, not image.
    // Do not modify anything if already processed such as cardinality -1.
    if (isset($element['current'])) {
      if (isset($element['current']['items'])) {
        $content = &$element['current']['items'][$delta];
      }
      else {
        $content = &$element['current'][$entity->id()];
      }

      if ($settings['field_type'] == 'image') {
        $this->widgetImageDisplay($content, $settings);
      }
      elseif ($display) {
        $content['display'] = $display;
      }
      // Processed or not, provide labels.
      $content['label']['#markup'] = '<div class="sb__label">' . $label . '</div>';
    }
    elseif (isset($element['selection'])) {
      $content = &$element['selection'][$delta];
      if ($display) {
        $content['rendered_entity'] = $display;
      }
    }
  }

}
