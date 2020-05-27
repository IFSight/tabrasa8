<?php

namespace Drupal\slick_browser;

use Drupal\slick\SlickDefault;

/**
 * Defines shared plugin default settings for field widget and Views style.
 */
class SlickBrowserDefault extends SlickDefault {

  /**
   * Returns the selection entity display plugin settings.
   */
  public static function baseFieldWidgetDisplaySettings() {
    return [
      '_context'           => 'widget',
      'entity_type'        => '',
      'display'            => '',
      'selection_position' => 'bottom',
    ];
  }

  /**
   * Returns the views style plugin settings.
   */
  public static function viewsSettings() {
    return [
      'vanilla' => TRUE,
    ] + parent::imageSettings();
  }

  /**
   * Returns the form mode widget plugin settings.
   */
  public static function widgetSettings() {
    return [
      'image_style'  => 'slick_browser_preview',
      'media_switch' => 'media',
      'ratio'        => 'fluid',
    ] + self::baseFieldWidgetDisplaySettings() + self::viewsSettings();
  }

  /**
   * Returns the widget common buttons.
   */
  public static function widgetButtons() {
    return ['preview_link', 'edit_button', 'remove_button', 'replace_button'];
  }

  /**
   * Returns the supported third party widgets.
   */
  public static function thirdPartyWidgets() {
    return ['entity_browser_file', 'media_library_widget'];
  }

}
