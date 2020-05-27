<?php

namespace Drupal\slick_browser\Plugin\slick;

use Drupal\slick\SlickSkinPluginBase;

/**
 * Provides slick browser skins.
 *
 * @SlickSkin(
 *   id = "slick_browser_skin",
 *   label = @Translation("Slick browser skin")
 * )
 */
class SlickBrowserSkin extends SlickSkinPluginBase {

  /**
   * Sets the slick skins.
   *
   * @inheritdoc
   */
  protected function setSkins() {
    // If you copy this file, be sure to add base_path() before any asset path
    // (css or js) as otherwise failing to load the assets. Your module can
    // register paths pointing to a theme. Check out slick.api.php for details.
    $slick = base_path() . drupal_get_path('module', 'slick');
    $path = base_path() . drupal_get_path('module', 'slick_browser');

    $skins = [
      'sb-classic' => [
        'name' => 'Widget: Classic',
        'description' => $this->t('Only reasonable if it has Alt or Title field enabled along with images. Works best with one visible slide at a time. Adds dark background color over white caption, only good for slider (single slide visible), not carousel (multiple slides visible), where small captions are placed over images.'),
        'css' => [
          'theme' => [
            $path . '/css/theme/slick.theme--sb-classic.css' => [],
          ],
        ],
      ],
      'sb-split' => [
        'name' => 'Widget: Split',
        'description' => $this->t('Only reasonable if it has Alt or Title field enabled along with images. Works best with one visible slide at a time. Puts image and caption side by side, related to slide layout options.'),
        'css' => [
          'theme' => [
            $path . '/css/theme/slick.theme--sb-split.css' => [],
          ],
        ],
      ],
      'sb-grid-w' => [
        'name' => 'Widget: Grid',
        'description' => $this->t('Grid dedicated for Entity Browser field widget.'),
        'css' => [
          'theme' => [
            $slick . '/css/theme/slick.theme--grid.css' => [],
          ],
        ],
      ],
      'sb-grid' => [
        'name' => 'Slick Browser: Grid',
        'description' => $this->t('Grid dedicated for Entity Browser View display.'),
        'group' => 'main',
        'css' => [
          'theme' => [
            $slick . '/css/theme/slick.theme--grid.css' => [],
            $path . '/css/theme/slick.theme--sb-grid.css' => [],
          ],
        ],
      ],
    ];

    foreach ($skins as $key => $skin) {
      $skins[$key]['provider'] = 'slick_browser';
      if (!isset($skins[$key]['group'])) {
        $skins[$key]['group'] = 'widget';
      }
    }

    return $skins;
  }

}
