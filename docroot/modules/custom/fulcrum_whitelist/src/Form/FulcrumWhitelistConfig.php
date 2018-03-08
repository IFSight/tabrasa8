<?php

namespace Drupal\fulcrum_whitelist\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FulcrumWhitelistConfig.
 */
class FulcrumWhitelistConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'fulcrum_whitelist.fulcrumwhitelistconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fulcrum_whitelist_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('fulcrum_whitelist.fulcrumwhitelistconfig');
    $form['whitelist_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Whitelist Host'),
      '#maxlength' => 256,
      '#size' => 64,
      '#default_value' => $config->get('whitelist_host'),
    ];
    $form['port'] = [
      '#type' => 'number',
      '#title' => $this->t('Port'),
      '#default_value' => $config->get('port'),
    ];
    $form['token_process_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Token Process Limit'),
      '#default_value' => $config->get('token_process_limit'),
    ];
    $form['delay'] = [
      '#type' => 'number',
      '#title' => $this->t('Delay seconds for servers to all whitelist'),
      '#default_value' => $config->get('delay'),
    ];
    $form['whitelist_abbr'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Whitelist Abbreviation'),
      '#maxlength' => 4,
      '#size' => 6,
      '#default_value' => $config->get('whitelist_abbr'),
    ];
    $form['wait_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Wait Text'),
      '#maxlength' => 256,
      '#size' => 64,
      '#default_value' => $config->get('wait_text'),
    ];
    $form['fail_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Failure Text'),
      '#maxlength' => 256,
      '#size' => 64,
      '#default_value' => $config->get('fail_text'),
    ];
    $form['misconf_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Misconfiguration Text'),
      '#maxlength' => 256,
      '#size' => 64,
      '#default_value' => $config->get('misconf_text'),
    ];
    $form['docs_intro'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Usage Introduction'),
      '#default_value' => $config->get('docs_intro'),
    ];
    $form['docs_user'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Usage User Section'),
      '#default_value' => $config->get('docs_user'),
    ];
    $form['docs_admin'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Usage Admin Section'),
      '#default_value' => $config->get('docs_admin'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('fulcrum_whitelist.fulcrumwhitelistconfig')
      ->set('whitelist_host', $form_state->getValue('whitelist_host'))
      ->set('port', $form_state->getValue('port'))
      ->set('token_process_limit', $form_state->getValue('token_process_limit'))
      ->set('delay', $form_state->getValue('delay'))
      ->set('whitelist_abbr', $form_state->getValue('whitelist_abbr'))
      ->set('wait_text', $form_state->getValue('wait_text'))
      ->set('fail_text', $form_state->getValue('fail_text'))
      ->set('misconf_text', $form_state->getValue('misconf_text'))
      ->set('docs_intro', $form_state->getValue('docs_intro'))
      ->set('docs_user', $form_state->getValue('docs_user'))
      ->set('docs_admin', $form_state->getValue('docs_admin'))
      ->save();
  }

}
