<?php

namespace Drupal\recurring_events\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class EventInstanceSettingsForm.
 *
 * @ingroup recurring_events
 */
class EventInstanceSettingsForm extends ConfigFormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'eventinstance_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['recurring_events.eventinstance.config'];
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('recurring_events.eventinstance.config')
      ->set('date_format', $form_state->getValue('date_format'))
      ->set('limit', $form_state->getValue('limit'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Define the form used for EventInstance settings.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('recurring_events.eventseries.config');

    $php_date_url = Url::fromUri('https://secure.php.net/manual/en/function.date.php');
    $php_date_link = Link::fromTextAndUrl($this->t('PHP date/time format'), $php_date_url);

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Event Display'),
      '#open' => TRUE,
    ];

    $form['display']['date_format'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Instance Date Format'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the @link used when listing event dates. Default is F jS, Y h:iA.', [
        '@link' => $php_date_link->toString(),
      ]),
      '#default_value' => $config->get('date_format'),
    ];

    $form['display']['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Event Instance Items'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the number of items to show per page in the default event instance listing table.'),
      '#default_value' => $config->get('limit'),
    ];

    return parent::buildForm($form, $form_state);
  }

}
