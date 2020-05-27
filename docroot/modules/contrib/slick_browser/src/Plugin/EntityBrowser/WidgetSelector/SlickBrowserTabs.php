<?php

namespace Drupal\slick_browser\Plugin\EntityBrowser\WidgetSelector;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_browser\Plugin\EntityBrowser\WidgetSelector\Tabs;

/**
 * Displays entity browser widgets as tabs.
 *
 * @EntityBrowserWidgetSelector(
 *   id = "slick_browser_tabs",
 *   label = @Translation("Slick Browser: Tabs"),
 *   description = @Translation("Displays entity browser widgets as tabs.")
 * )
 */
class SlickBrowserTabs extends Tabs {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'tabs_position' => 'left',
      'buttons_position' => 'bottom',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getForm(array &$form = [], FormStateInterface &$form_state = NULL) {
    $element = parent::getForm($form, $form_state);
    $element['#attached']['library'][] = 'slick_browser/tabs';

    if (empty($this->configuration['tabs_position'])) {
      return;
    }

    // Adds attributes the parent $form element.
    $form['#attributes']['class'][] = 'form--tabs';

    foreach (['buttons', 'tabs'] as $key) {
      if (isset($this->configuration[$key . '_position'])) {
        $form['#attributes']['class'][] = 'form--' . $key . '-' . $this->configuration[$key . '_position'];
      }
    }

    if (in_array($this->configuration['tabs_position'], ['left', 'right'])) {
      $form['#attributes']['class'][] = 'form--tabs-v';
    }
    if (in_array($this->configuration['tabs_position'], ['bottom', 'top'])) {
      $form['#attributes']['class'][] = 'form--tabs-h';
    }

    return $element;
  }

  /**
   * Overrides PluginFormInterface::buildConfigurationForm().
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['tabs_position'] = [
      '#type'    => 'select',
      '#title'   => $this->t('Tabs position'),
      '#options' => [
        'left'   => $this->t('Left'),
        'right'  => $this->t('Right'),
        'bottom' => $this->t('Bottom'),
        'top'    => $this->t('Top'),
      ],
      '#default_value' => isset($this->configuration['tabs_position']) ? $this->configuration['tabs_position'] : 'left',
      '#description'   => $this->t('Left and Right positions are more suitable for large displays such as Modal. With more tab items, it is better to use Left or Right position. Basically if Tabs Left, Selection should be Left. If Tabs Right, Selection should be Right.'),
    ];

    $form['buttons_position'] = [
      '#type'    => 'select',
      '#title'   => $this->t('Buttons position'),
      '#options' => [
        'bottom' => $this->t('Bottom'),
        'top'    => $this->t('Top'),
      ],
      '#default_value' => isset($this->configuration['buttons_position']) ? $this->configuration['buttons_position'] : 'top',
      '#description'   => $this->t('With more tab items, the main button navigation may collide especially within small iframes. Choose different position.'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['buttons_position'] = isset($values['buttons_position']) ? $values['buttons_position'] : '';
    $this->configuration['tabs_position'] = isset($values['tabs_position']) ? $values['tabs_position'] : '';
  }

}
