<?php

//
// namespace Drupal\fulcrum_whitelist\Form;
//
// use Drupal\Core\Form\FormBase;
// use Drupal\Core\Form\FormStateInterface;
//
// /**
//  * Class FulcrumWhitelistEntitySettingsForm.
//  *
//  * @ingroup fulcrum_whitelist
//  */
// class FulcrumWhitelistEntitySettingsForm extends FormBase {
//
//   /**
//    * Returns a unique string identifying the form.
//    *
//    * @return string
//    *   The unique string identifying the form.
//    */
//   public function getFormId() {
//     return 'fulcrumwhitelistentity_settings';
//   }
//
//   /**
//    * Form submission handler.
//    *
//    * @param array $form
//    *   An associative array containing the structure of the form.
//    * @param \Drupal\Core\Form\FormStateInterface $form_state
//    *   The current state of the form.
//    */
//   public function submitForm(array &$form, FormStateInterface $form_state) {
//     // Empty implementation of the abstract submit class.
//   }
//
//   /**
//    * Defines the settings form for Fulcrum Whitelist Entity entities.
//    *
//    * @param array $form
//    *   An associative array containing the structure of the form.
//    * @param \Drupal\Core\Form\FormStateInterface $form_state
//    *   The current state of the form.
//    *
//    * @return array
//    *   Form definition array.
//    */
//   public function buildForm(array $form, FormStateInterface $form_state) {
//     $form['fulcrumwhitelistentity_settings']['#markup'] = 'Settings form for Fulcrum Whitelist Entity entities. Manage field settings here.';
//     return $form;
//   }
//
// }
