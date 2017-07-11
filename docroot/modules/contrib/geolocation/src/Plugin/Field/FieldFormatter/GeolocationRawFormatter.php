<?php

namespace Drupal\geolocation\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geolocation_raw' formatter.
 *
 * @FieldFormatter(
 *   id = "geolocation_raw",
 *   module = "geolocation",
 *   label = @Translation("DEPRECATED - Use token instead - Geolocation Raw"),
 *   field_types = {
 *     "geolocation"
 *   }
 * )
 * @deprecated
 */
class GeolocationRawFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'value' => 'lat',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['value'] = [
      '#title' => $this->t('Raw value'),
      '#type' => 'radios',
      '#options' => [
        'lat' => $this->t('Latitude'),
        'lng' => $this->t('Longitude'),
        'lat_sin' => $this->t('Precalculated latitude sine'),
        'lat_cos' => $this->t('Precalculated latitude cosine'),
        'lng_rad' => $this->t('Precalculated radian longitude'),
      ],
      '#default_value' => $this->getSetting('value'),
      '#description' => $this->t('Renders a single raw value.'),
      '#required' => TRUE,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Raw value: @item', ['@item' => $this->getSetting('value')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->{$this->settings['value']},
      ];
    }

    return $element;
  }

}
