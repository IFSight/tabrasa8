<?php

/**
 * @file
 * Hooks and preprocess functions for the Slick Browser module.
 */

use Drupal\Core\Render\Element;
use Drupal\Component\Serialization\Json;
use Drupal\slick_browser\SlickBrowserDefault;
use Drupal\slick_browser\SlickBrowserUtil;

/**
 * Prepares variables for slick-browser.html.twig templates.
 *
 * @todo: Core image, Focal Point, Crop integration outside EB.
 */
function template_preprocess_slick_browser(&$variables) {
  $element  = $variables['element'];
  $widgets  = $build = $content = $draggables = $thumbs = [];
  $settings = &$element['#settings'];

  // Get our list of widgets in order (needed when the form comes back after
  // preview or failed validation).
  // Image is stored as indexed children, media as items.
  $element = empty($element['items']) ? $element : $element['items'];
  foreach (Element::children($element) as $key) {
    $widgets[] = &$element[$key];
  }

  // Sort the items if required.
  if (isset($settings['plugin_id_widget']) && $settings['plugin_id_widget'] != 'entity_browser_entity_reference') {
    usort($widgets, '_field_multiple_value_form_sort_helper');
  }

  // Provides basic settings.
  $count               = count($widgets);
  $settings['count']   = $count;
  $settings['_id']     = $element['#id'];
  $settings['_upload'] = isset($element['#file_upload_delta']);

  // Massage settings.
  _slick_browser_massage_settings($variables, $settings);

  foreach ($widgets as $delta => &$widget) {
    $settings['delta'] = $delta;
    // EB uses 'display' while core Image 'preview', get them consistent here.
    if (!empty($settings['_eb'])) {
      if (isset($widget['display'])) {
        $widget['preview'] = $widget['display'];
        unset($widget['display']);
      }
      if (isset($widget['#attributes']['class']) && is_array($widget['#attributes']['class'])) {
        $widget['#attributes']['class'] = array_diff($widget['#attributes']['class'], ['item-container', 'draggable']);
      }
    }
    else {
      // Save the uploading row for last, for image widget, not entity browser.
      if (isset($widget['#files']) && empty($widget['#files'])) {
        if (isset($element['#file_upload_title'])) {
          $widget['#title'] = $element['#file_upload_title'];
          $widget['#description'] = slick_browser()->manager()->getRenderer()->renderPlain($element['#file_upload_description']);
        }

        foreach (Element::children($widget) as $sub_key) {
          if (!in_array($sub_key, ['display', 'fids', 'upload', 'upload_button'])) {
            unset($widget[$sub_key]);
          }
        }

        continue;
      }
    }

    // Masages widget value.
    _slick_browser_massage_widget($widget, $settings);

    // Pass $thumbs to container.
    if (isset($widget['thumb'])) {
      hide($widget['thumb']);
      $thumbs[] = $widget['thumb'];
    }

    // Build the widget slides.
    $build['items'][$delta] = $widget;

    // We don't do early rendering, hide em all now.
    foreach (Element::children($widget) as $sub_key) {
      hide($widget[$sub_key]);
    }

    // Place _weight and thumb into sortable element.
    if ($settings['_sortable']) {
      $draggables[] = _slick_browser_get_draggable($widget, $settings);
    }
  }

  // Build the thumbnails.
  if ($settings['nav'] && $thumbs) {
    _slick_browser_build_thumb($build, $thumbs, $settings);
  }
  else {
    $settings['nav'] = FALSE;
  }

  // Pass variables to template.
  $content['header'] = $content['main'] = [];

  // Build buttons.
  $buttons = _slick_browser_get_buttons($settings);

  // Build draggable.
  $content['draggable'] = _slick_browser_build_draggables($buttons, $draggables);

  $build['settings'] = $settings;
  if ($settings['count'] > 1) {
    $build['options']['initialSlide'] = $settings['_sbend'];
  }
  if (!empty($build['items'])) {
    // Provide Blazy attributes for the containing element.
    $blazy_data = '';
    $preview = isset($build['items'][0]['preview']) ? $build['items'][0]['preview'] : [];

    if (!empty($preview['#uri'])) {
      // @todo $item = isset($preview['#item']) ? $preview['#item'] : NULL;
      $settings['_uri'] = $settings['first_uri'] = $preview['#uri'];

      // Supports multi-serving images.
      $blazy_data = empty($settings['blazy_data']) ? '' : $settings['blazy_data'];
      $build['settings']['blazy_data'] = $blazy_data;
    }

    $variables['attributes']['data-blazy'] = $blazy_data ? Json::encode($blazy_data) : '';

    switch ($settings['style']) {
      case 'column':
      case 'grid':
        $content['main'] = slick_browser()->manager()->buildGrid($build['items'], $settings)[0]['slide'];

        // Prevents collapsed details from breaking lazyload.
        $load = slick_browser()->manager()->attach($settings);
        $load['drupalSettings']['blazy']['loadInvisible'] = TRUE;
        $content['main']['#attached'] = $load;
        break;

      case 'single':
      case 'slick':
        $content['main'] = slick_browser()->manager()->build($build);
        break;
    }

    $content['header'] = _slick_browser_build_buttons($buttons);
  }

  unset($element['#theme_wrappers']);

  $variables['content']  = $content;
  $variables['settings'] = $settings;
  $variables['element']  = $element;
}

/**
 * Prepares variables for slick-vanilla--browser.html.twig templates.
 */
function template_preprocess_slick_vanilla__browser(&$variables) {
  $settings = &$variables['settings'];
  $item = $variables['item'];

  if (empty($item)) {
    return;
  }

  $attributes = &$variables['attributes'];
  if (!isset($attributes['data-row-id'])) {
    $attributes['data-row-id'] = $variables['delta'];
  }

  // Group item for easy placement while considering the different
  // structures between core Image and Entity browser. Reset item.
  $variables['item'] = [];
  foreach (Element::children($item) as $child) {
    $sub_item[$child] = &$item[$child];

    $fid = 0;
    if (in_array($child, ['upload', 'upload_button'])) {
      unset($sub_item[$child]);
    }

    foreach (SlickBrowserDefault::widgetButtons() + ['display_field'] as $sub_key) {
      if (isset($sub_item[$sub_key])) {
        if (!empty($sub_item[$sub_key]['#access'])) {
          $variables['item']['action'][$sub_key] = $sub_item[$sub_key];
        }
        unset($sub_item[$sub_key]);
      }
    }

    if (!empty($settings['grid']) && isset($sub_item['_weight'])) {
      show($sub_item['_weight']);
      $variables['item']['action']['_weight'] = $sub_item['_weight'];
      unset($sub_item['_weight']);
    }

    if (isset($sub_item['meta'])) {
      // Must not use show() to avoid dup.
      $variables['item']['meta'] = $sub_item['meta'];
      unset($sub_item['meta']);
    }

    if (isset($sub_item['#files'])) {
      $file = reset($sub_item['#files']);
      $fid = $file->id();
      $variables['item']['meta']['file_' . $fid]['filename']['#suffix'] = '<span class="file-size"> [' . format_size($file->getSize()) . ']</span> ';
    }

    $meta = [
      'fids',
      'focal_point',
      'width',
      'height',
      'alt',
      'title',
      'file_' . $fid,
      'filename',
    ];
    foreach ($meta as $sub_key) {
      if (isset($sub_item[$sub_key])) {
        $variables['item']['meta'][$sub_key] = $sub_item[$sub_key];
        unset($sub_item[$sub_key]);
      }
    }

    // Respects what we know nothing about.
    if (isset($sub_item[$child])) {
      $variables['item'][$child] = $sub_item[$child];
      unset($sub_item[$child]);
    }
  }
}

/**
 * Massages settings.
 */
function _slick_browser_massage_settings(array &$variables, array &$settings) {
  // This is because empty file upload is counted one.
  $count = $settings['count'];
  $multiple = $settings['cardinality'] > 1 || $settings['cardinality'] == -1;
  if ($settings['_upload']) {
    $settings['count'] = $count > 1 ? ($count - 1) : $count;
  }
  if (!$multiple) {
    $settings['count'] = 1;
  }

  // Vanilla is on, rebuild own logic to support asnavfor as needed.
  $field_name_css       = str_replace('_', '-', $settings['field_name']);
  $settings['id']       = 'sb-slick-' . $field_name_css;
  $settings['vanilla']  = $settings['blazy'] = $settings['_widget'] = TRUE;
  $settings['noscript'] = $settings['_grid'] = FALSE;
  $settings['display']  = 'main';
  $settings['item_id']  = 'slide';
  $settings['lazy']     = 'blazy';
  $settings['nav']      = !empty($settings['optionset_thumbnail']) && $settings['count'] > 1 && $multiple;
  $settings['_text']    = !empty($settings['alt_field']) || !empty($settings['title_field']);

  if ($settings['style'] == 'slick') {
    $settings['grid'] = $settings['grid_small'] = $settings['grid_medium'] = $settings['visible_items'] = '';
  }
  elseif ($settings['style'] == 'grid' || $settings['style'] == 'column') {
    $settings['unslick'] = TRUE;
    $settings['_grid'] = TRUE;

    // Provides sensible defaults for the ignorant who doesn't provide Grid as
    // otherwise confusingly invisible items.
    if (empty($settings['grid'])) {
      $settings['grid']        = 3;
      $settings['grid_medium'] = 2;
      $settings['grid_small']  = 1;
    }
  }

  // Pass the settings to template.
  $settings['skin_widget']   = empty($settings['skin']) ? '' : $settings['skin'];
  $settings['_weight_class'] = $settings['_id'] . '-weight';
  $settings['_sortable']     = empty($settings['grid']) && $settings['count'] > 1 && $multiple;

  // Update attributes.
  $settings['_sbend'] = $settings['_upload'] ? ($count - 2) : ($count - 1);
  if ($settings['count'] > 1) {
    $variables['attributes']['data-end'] = $settings['_sbend'];
  }

  // Defines ID if not provided.
  $variables['attributes']['id'] = $settings['_id'];
  $variables['attributes']['data-drupal-selector'] = $settings['_id'];
}

/**
 * Massages widget value.
 */
function _slick_browser_massage_widget(array &$widget, array $settings) {
  // Merge with item settings.
  $settings = isset($widget['preview']['#settings']) ? array_merge($settings, $widget['preview']['#settings']) : $settings;
  $settings = isset($widget['preview']['#build']['settings']) ? array_merge($settings, $widget['preview']['#build']['settings']) : $settings;

  // Provide ImageItem.
  $item = isset($widget['preview']['#item']) ? $widget['preview']['#item'] : NULL;
  $item = isset($widget['preview']['#build']['item']) ? $widget['preview']['#build']['item'] : $item;

  // Adds thumbnail elements for each entity.
  if (!empty($settings['uri'])) {
    if (empty($widget['preview']['#uri'])) {
      $widget['preview']['#uri'] = $settings['uri'];
    }

    // Add small thumbnails for either asNavFor, or custom draggable elements.
    $settings['thumbnail_style'] = empty($settings['thumbnail_style']) ? 'slick_browser_thumbnail' : $settings['thumbnail_style'];
    $widget['thumb'] = slick_browser()->formatter()->getThumbnail($settings, $item);
  }

  // Self-closed elements cannot be iconized, add the wrappers.
  foreach (SlickBrowserDefault::widgetButtons() as $button) {
    if (isset($widget[$button])) {
      SlickBrowserUtil::wrapButton($widget[$button], $button);
    }
  }

  // Delay rendering of the weight selector, so that can be rendered later.
  if (isset($widget['_weight'])) {
    if (empty($settings['grid'])) {
      hide($widget['_weight']);
    }
    $widget['_weight']['#attributes']['class'] = [$settings['_weight_class'], 'sb__weight'];
    $widget['_weight']['#attributes']['data-slick-index'] = $settings['delta'];
    $widget['_weight']['#wrapper_attributes']['class'][] = 'visually-hidden';
  }

  // Arrange the row without the normal form_element wrappers.
  unset($widget['#theme'], $widget['#theme_wrappers']);

  // Makes grids draggable.
  if (!empty($settings['grid'])) {
    $widget['#attributes']['tabindex'] = -1;
    $widget['#attributes']['class'][] = 'sb__sortitem js-form-managed-file form-managed-file';

    // If using BlazyGrid, not Slick grid.
    if (isset($settings['delta'])) {
      $widget['#attributes']['class'][] = 'grid--' . $settings['delta'];
    }

    if (!empty($settings['target_type'])) {
      $widget['#attributes']['class'][] = 'grid--' . str_replace('_', '-', $settings['target_type']);
    }
  }

  // Pass to theme_slick_grid(), theme_slick_slide() as plain array.
  if (isset($widget['#attributes'])) {
    $widget['attributes'] = $widget['#attributes'];
  }
}

/**
 * Provides thumb navigation if so configured.
 */
function _slick_browser_build_thumb(array &$build, array $thumbs, array $settings) {
  foreach ($thumbs as $delta => &$thumb) {
    show($thumb);

    $slide[$settings['item_id']]['#markup'] = slick_browser()->manager()->getRenderer()->render($thumb);
    $build['thumb']['options']['initialSlide'] = $settings['_sbend'];
    $build['thumb']['items'][$delta] = $slide;
    unset($slide);
  }
}

/**
 * Prepares buttons if any.
 */
function _slick_browser_get_buttons(array $settings) {
  $buttons = [];
  if (isset($settings['crop_list'])) {
    $buttons['crop'] = 'Crop';
  }

  if ($settings['cardinality'] != 1) {
    if ($settings['_text']) {
      $buttons['caption'] = 'Text';
    }

    $buttons['removeall'] = 'Remove all';
  }

  return $buttons;
}

/**
 * Builds buttons if any.
 */
function _slick_browser_build_buttons(array $buttons) {
  $actions = [];
  if ($buttons) {
    foreach ($buttons as $key => $title) {
      $actions[$key . '_button'] = [
        '#type' => 'button',
        '#value' => t('@title', ['@title' => $title]),
        '#submit' => [],
        '#attributes' => [
          'class' => ['button--sb', 'button--js', 'button--' . $key],
          'data-target' => $key,
        ],
      ];
    }
  }
  return $actions;
}

/**
 * Returns a draggable item.
 */
function _slick_browser_get_draggable(array &$widget, array $settings) {
  $draggable = $attributes = [];
  if (isset($widget['_weight']) && empty($settings['grid'])) {
    show($widget['_weight']);
    $draggable[] = $widget['_weight'];
    unset($widget['_weight']);
  }

  // Provides the draggable thumbnails, if any.
  if (isset($widget['thumb'])) {
    if ($settings['_sortable']) {
      show($widget['thumb']);
      $draggable[] = $widget['thumb'];
    }

    unset($widget['thumb']);
  }

  if (!isset($widget['thumb']) && isset($widget['filename'])) {
    // At least we need something for custom draggable elements.
    $filename = is_string($widget['filename']) ? $widget['filename'] : $widget['filename']['#markup'];
    $draggable[] = ['#markup' => '<span class="slide__filename">' . $filename . '</span>'];
  }

  if (isset($widget['#attributes'])) {
    $attributes = $widget['#attributes'];
  }

  $attributes['class'][] = 'slide sb__sortitem';
  if (!isset($attributes['data-row-id'])) {
    $attributes['data-row-id'] = $settings['delta'];
  }

  return $draggable ? [
    '#markup' => slick_browser()->manager()->getRenderer()->render($draggable),
    '#wrapper_attributes' => $attributes,
  ] : [];
}

/**
 * Provides draggable items if any.
 */
function _slick_browser_build_draggables(array &$buttons, array $draggables = []) {
  $content = [];
  if ($draggables) {
    $content = [
      '#theme' => 'item_list',
      '#items' => $draggables,
      '#wrapper_attributes' => [
        'class' => ['sb__sortlist'],
      ],
      '#attributes' => [
        'class' => ['sb__sortable', 'clearfix'],
      ],
    ];

    $buttons += [
      'sort' => 'Sort',
      'done' => 'Done',
    ];
  }
  return $content;
}
