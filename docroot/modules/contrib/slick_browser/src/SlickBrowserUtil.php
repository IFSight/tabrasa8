<?php

namespace Drupal\slick_browser;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Template\Attribute;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides common utility methods.
 */
class SlickBrowserUtil {

  /**
   * Defines the scope for the form elements.
   */
  public static function scopedFormElements() {
    return [
      'caches'            => FALSE,
      'grid_form'         => TRUE,
      'responsive_image'  => FALSE,
      'image_style_form'  => TRUE,
      'thumb_positions'   => TRUE,
      'nav'               => TRUE,
      'style'             => TRUE,
      'no_arrows'         => TRUE,
      'no_dots'           => TRUE,
      'no_ratio'          => TRUE,
      'view_mode'         => 'slick_browser',
      '_browser'          => TRUE,
      '_thumbnail_effect' => [],
    ];
  }

  /**
   * Prepare settings.
   */
  public static function buildThirdPartySettings($plugin, $defaults = []) {
    $defaults = $defaults ?: array_merge(SlickBrowserDefault::widgetSettings(), $plugin->getSettings());
    $settings = array_merge($defaults, $plugin->getThirdPartySettings('slick_browser'));
    return array_merge(SlickBrowserDefault::entitySettings(), $settings);
  }

  /**
   * Add wrappers around the self-closed input element for styling, or iconing.
   *
   * JS is easier, but hence to avoid FOUC.
   */
  public static function wrapButton(array &$input, $key = '', $access = NULL) {
    $css = str_replace('_button', '', $key);
    $css = str_replace('_', '-', $css);
    $title = str_replace(['_', '-'], ' ', $css);

    $attributes = new Attribute();
    $attributes->setAttribute('title', new TranslatableMarkup('@title', ['@title' => Unicode::ucfirst($title)]));
    $attributes->addClass(['button-wrap', 'button-wrap--' . $css]);

    if (isset($access)) {
      $attributes->addClass([$access ? 'is-btn-visible' : 'is-btn-hidden']);
    }

    $content = '';
    if ($key == 'remove' || $key == 'remove_button') {
      // @todo: Use JS, not AJAX, for removal - button--sb data-target="remove".
      $content .= '<span class="button--wrap__mask">&nbsp;</span><span class="button--wrap__confirm">' . new TranslatableMarkup('Confirm') . '</span>';
      $attributes->addClass(['button-wrap--confirm']);
    }

    $input['#prefix'] = '<span' . $attributes . '>' . $content;
    $input['#suffix'] = '</span>';
    $input['#attributes']['class'][] = 'button--' . $css;
  }

  /**
   * Returns a group of buttons.
   */
  public static function buildButtons(array $data) {
    $buttons = [];
    foreach ($data as $key) {
      $text = $key == 'info' ? '?' : '&#43;';
      $title = str_replace(['_', '-'], ' ', $key);

      $attributes = new Attribute();
      $attributes->setAttribute('title', new TranslatableMarkup('@title', ['@title' => Unicode::ucfirst($title)]));
      $attributes->setAttribute('type', 'button');
      $attributes->setAttribute('tabindex', '0');
      $attributes->addClass(['button', 'button--' . $key]);

      $buttons[$key] = [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#attributes' => $attributes,
        '#value' => $text,
      ];
    }

    return [
      '#type' => 'inline_template',
      '#template' => '{{ prefix | raw }}{{ buttons }}{{ suffix | raw }}',
      '#context' => [
        'buttons' => $buttons,
        'prefix' => '<div class="button-group button-wrap button-group--grid">',
        'suffix' => '</div>',
      ],
    ];
  }

}
