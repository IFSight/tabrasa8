<?php

/**
 * @file
 * Provides views data for slick_browser.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function slick_browser_views_data_alter(&$data) {
  $data['views']['slick_browser_switch'] = [
    'title' => t('Slick Browser: View Switcher'),
    'help' => t('The Slick Browser grid/list (table-like) view switcher.'),
    'filter' => [
      'id' => 'slick_browser_switch',
    ],
  ];
}

/**
 * Implements hook_views_plugins_style_alter().
 */
function slick_browser_views_plugins_style_alter(array &$plugins) {
  $plugins['slick_browser'] = [
    'id'             => 'slick_browser',
    'label'          => t('Slick Browser'),
    'description'    => t('Display the results in a Slick Browser.'),
    'class'          => 'Drupal\slick_browser\Plugin\views\style\SlickBrowserViews',
    'display_types'  => ['normal'],
    'help'           => t('Works best with filter Slick Browser.'),
    'parent'         => 'parent',
    'plugin_type'    => 'style',
    'register_theme' => FALSE,
    'short_title'    => '',
    'theme'          => 'slick_wrapper',
    'title'          => t('Slick Browser'),
    'provider'       => 'slick_browser',
  ];
}
