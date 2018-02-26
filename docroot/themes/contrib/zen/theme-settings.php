<?php
/**
 * Implements hook_form_system_theme_settings_alter() function.
 */
function zen_form_system_theme_settings_alter(&$form, \Drupal\Core\Form\FormStateInterface &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['themedev'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Theme development settings'),
  );
  $form['themedev']['zen_rebuild_registry'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Rebuild theme registry and output template debugging on every page.'),
    '#default_value' => theme_get_setting('zen_rebuild_registry'),
    '#description'   => t('During theme development, it can be very useful to continuously <a href="!link">rebuild the theme registry</a> and to output template debugging HTML comments. WARNING: this is a huge performance penalty and must be turned off on production websites.', array('!link' => 'https://drupal.org/node/173880#theme-registry')),
  );
}
