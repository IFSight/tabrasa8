<?php

namespace Drupal\if_helper_captcha\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultCaptchaConfigForm.
 */
class DefaultCaptchaConfigForm extends ConfigFormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'default_captcha_config';
  }

  /**
   * Define the form used for if_helper_captcha.
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

    $config = $this->config('if_helper_captcha.settings');

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="message-wrap">' . $this->t('When the box below is checked, a captcha will automatically be added to every new webform on creation.') . '</div>',
      '#weight' => -1,
    ];
    $form['enable_captcha'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable captchas by default.'),
      '#default_value' => $config->get('enable_captcha'),
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
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
    $enable_captcha = $form_state->getValue('enable_captcha');
    $config = $this->config('if_helper_captcha.settings')
      ->set('enable_captcha', $enable_captcha)
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'if_helper_captcha.settings',
    ];
  }

}
