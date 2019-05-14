<?php

namespace Drupal\easy_breadcrumb\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\easy_breadcrumb\EasyBreadcrumbConstants;

/**
 * Build Easy Breadcrumb settings form.
 */
class EasyBreadcrumbGeneralSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'easy_breadcrumb_general_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['easy_breadcrumb.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('easy_breadcrumb.settings');

    // Details for grouping general settings fields.
    $details_general = [
      '#type' => 'details',
      '#title' => $this->t('General settings'),
      '#open' => TRUE,
    ];

    $details_advanced = [
      '#type' => 'details',
      '#title' => $this->t('Advanced settings'),
      '#open' => TRUE,
    ];

    // If never set before ensure Applies to administration pages is on.
    $applies_admin_routes = $config->get(EasyBreadcrumbConstants::APPLIES_ADMIN_ROUTES);
    if (!isset($applies_admin_routes)) {
      $applies_admin_routes = TRUE;
    }
    $details_general[EasyBreadcrumbConstants::APPLIES_ADMIN_ROUTES] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Applies to administration pages'),
      '#description' => $this->t('Uncheck to disable Easy breadcrumb for administration pages and routes like this one.'),
      '#default_value' => $applies_admin_routes,
    ];

    $details_general[EasyBreadcrumbConstants::INCLUDE_INVALID_PATHS] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include invalid paths alias as plain-text segments'),
      '#description' => $this->t('Include the invalid paths alias as plain-text segments in the breadcrumb.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::INCLUDE_INVALID_PATHS),
    ];

    $details_general[EasyBreadcrumbConstants::INCLUDE_TITLE_SEGMENT] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include the current page as a segment in the breadcrumb'),
      '#description' => $this->t('Include the current page as the last segment in the breadcrumb.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::INCLUDE_TITLE_SEGMENT),
    ];

    $details_general[EasyBreadcrumbConstants::REMOVE_REPEATED_SEGMENTS] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove repeated identical segments'),
      '#description' => $this->t('Remove segments of the breadcrumb that are identical.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::REMOVE_REPEATED_SEGMENTS),
    ];

    $details_general[EasyBreadcrumbConstants::INCLUDE_HOME_SEGMENT] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include the front page as a segment in the breadcrumb'),
      '#description' => $this->t('Include the front page as the first segment in the breadcrumb.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::INCLUDE_HOME_SEGMENT),
    ];

    $details_general[EasyBreadcrumbConstants::HOME_SEGMENT_TITLE] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title for the front page segment in the breadcrumb'),
      '#description' => $this->t('Text to be displayed as the front page segment. This field works together with the "Include the front page as a segment in the breadcrumb"-option.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::HOME_SEGMENT_TITLE),
    ];

    $details_general[EasyBreadcrumbConstants::TITLE_FROM_PAGE_WHEN_AVAILABLE] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use the real page title when available'),
      '#description' => $this->t('Use the real page title when it is available instead of always deducing it from the URL.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::TITLE_FROM_PAGE_WHEN_AVAILABLE),
    ];

    $details_general[EasyBreadcrumbConstants::USE_MENU_TITLE_AS_FALLBACK] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use menu title when available'),
      '#description' => $this->t('Use menu title instead of raw path component. The real page title setting above will take presidence over this setting. So, one or the other, but not both.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::USE_MENU_TITLE_AS_FALLBACK),
    ];

    $details_general[EasyBreadcrumbConstants::USE_PAGE_TITLE_AS_MENU_TITLE_FALLBACK] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use page title as fallback for menu title'),
      '#description' => $this->t('Use page title as fallback if menu title cannot be found. This option works when not using "real page title" above.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::USE_PAGE_TITLE_AS_MENU_TITLE_FALLBACK),
    ];

    // Formats the excluded paths array as line separated list of paths
    // before displaying them.
    $excluded_paths = $config->get(EasyBreadcrumbConstants::EXCLUDED_PATHS);

    $details_advanced[EasyBreadcrumbConstants::EXCLUDED_PATHS] = [
      '#type' => 'textarea',
      '#title' => $this->t('Paths to be excluded while generating segments'),
      '#description' => $this->t('Enter a line separated list of paths to be excluded while generating the segments.
			Paths may use simple regex, i.e.: report/2[0-9][0-9][0-9].'),
      '#default_value' => $excluded_paths,
    ];

    // Formats the excluded paths array as line separated list of paths
    // before displaying them.
    $replaced_titles = $config->get(EasyBreadcrumbConstants::REPLACED_TITLES);

    $details_advanced[EasyBreadcrumbConstants::REPLACED_TITLES] = [
      '#type' => 'textarea',
      '#title' => $this->t('Titles to be replaced while generating segments'),
      '#description' => $this->t('Enter a line separated list of titles with their replacements separated by ::.<br>
			For example TITLE::DIFFERENT_TITLE<br>This field works together with the option "Use the real page title when available" option.'),
      '#default_value' => $replaced_titles,
    ];

    // Formats the custom paths array as line separated list of paths
    // before displaying them.
    $custom_paths = $config->get(EasyBreadcrumbConstants::CUSTOM_PATHS);

    $details_advanced[EasyBreadcrumbConstants::CUSTOM_PATHS] = [
      '#type' => 'textarea',
      '#title' => $this->t('Paths to replace with custom breadcrumbs'),
      '#description' => $this->t('Enter a line separated list of internal paths followed by breadcrumb pattern.   Separate crumbs from their path with a vertical bar ("|").  Separate crumbs with double-colon ("::"). Omit the URL to display an unlinked crumb.  Fields will be trimmed to remove extra start/end spaces, so you can use them to help format your input, if desired. Replaced Titles will not be processed on custom paths. Excluded paths listed here will have breadcrumbs added.   Examples (with and without extra spacing):<br><code>/news/archive/site_launched  ::  News|/news  ::  Archive | /news/archive  ::  Site Launched<br>/your/path::LinkedCrumb1|url1::LinkedCrumb2|url2::UnlinkedCrumb3</code><br>'),
      '#default_value' => $custom_paths,
    ];

    $details_advanced[EasyBreadcrumbConstants::HOME_SEGMENT_KEEP] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display the front page segment on the front page'),
      '#description' => $this->t('If checked, the Home segment will be displayed on the front page.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::HOME_SEGMENT_KEEP),
      '#states' => [
        'visible' => [
          ':input[name="' . EasyBreadcrumbConstants::HOME_SEGMENT_TITLE . '"]' => ['empty' => FALSE],
        ],
      ],
    ];

    $details_advanced[EasyBreadcrumbConstants::TITLE_SEGMENT_AS_LINK] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make the current page title segment a link'),
      '#description' => $this->t('Prints the page title segment as a link. This option works together with the "Include the current page as a segment in the breadcrumb"-option.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::TITLE_SEGMENT_AS_LINK),
    ];

    $details_advanced[EasyBreadcrumbConstants::LANGUAGE_PATH_PREFIX_AS_SEGMENT] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make the language path prefix a segment'),
      '#description' => $this->t('On multilingual sites where a path prefix ("/en") is used, add this in the breadcrumb.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::LANGUAGE_PATH_PREFIX_AS_SEGMENT),
    ];

    $details_advanced[EasyBreadcrumbConstants::ABSOLUTE_PATHS] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use absolute path for Breadcrumb links'),
      '#description' => $this->t('By selecting, absolute paths will be used (default: false) instead of relative.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::ABSOLUTE_PATHS),
    ];

    $details_advanced[EasyBreadcrumbConstants::HIDE_SINGLE_HOME_ITEM] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Hide link to home page if it's the only breadcrumb item"),
      '#description' => $this->t('Hide the breadcrumb when it only links to the home page and nothing more.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::HIDE_SINGLE_HOME_ITEM),
    ];

    $details_advanced[EasyBreadcrumbConstants::TERM_HIERARCHY] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add parent hierarchy'),
      '#description' => $this->t('Add all taxonomy parents in the crumb for current term.'),
      '#default_value' => $config->get(EasyBreadcrumbConstants::TERM_HIERARCHY),
    ];

    $form = [];

    // Inserts the details for grouping general settings fields.
    $form[EasyBreadcrumbConstants::MODULE_NAME][] = $details_general;
    $form[EasyBreadcrumbConstants::MODULE_NAME][] = $details_advanced;

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('easy_breadcrumb.settings');

    $config
      ->set(EasyBreadcrumbConstants::APPLIES_ADMIN_ROUTES, $form_state->getValue(EasyBreadcrumbConstants::APPLIES_ADMIN_ROUTES))
      ->set(EasyBreadcrumbConstants::INCLUDE_INVALID_PATHS, $form_state->getValue(EasyBreadcrumbConstants::INCLUDE_INVALID_PATHS))
      ->set(EasyBreadcrumbConstants::EXCLUDED_PATHS, $form_state->getValue(EasyBreadcrumbConstants::EXCLUDED_PATHS))
      ->set(EasyBreadcrumbConstants::REPLACED_TITLES, $form_state->getValue(EasyBreadcrumbConstants::REPLACED_TITLES))
      ->set(EasyBreadcrumbConstants::CUSTOM_PATHS, $form_state->getValue(EasyBreadcrumbConstants::CUSTOM_PATHS))
      ->set(EasyBreadcrumbConstants::SEGMENTS_SEPARATOR, $form_state->getValue(EasyBreadcrumbConstants::SEGMENTS_SEPARATOR))
      ->set(EasyBreadcrumbConstants::INCLUDE_HOME_SEGMENT, $form_state->getValue(EasyBreadcrumbConstants::INCLUDE_HOME_SEGMENT))
      ->set(EasyBreadcrumbConstants::HOME_SEGMENT_TITLE, $form_state->getValue(EasyBreadcrumbConstants::HOME_SEGMENT_TITLE))
      ->set(EasyBreadcrumbConstants::HOME_SEGMENT_KEEP, $form_state->getValue(EasyBreadcrumbConstants::HOME_SEGMENT_KEEP))
      ->set(EasyBreadcrumbConstants::INCLUDE_TITLE_SEGMENT, $form_state->getValue(EasyBreadcrumbConstants::INCLUDE_TITLE_SEGMENT))
      ->set(EasyBreadcrumbConstants::TITLE_SEGMENT_AS_LINK, $form_state->getValue(EasyBreadcrumbConstants::TITLE_SEGMENT_AS_LINK))
      ->set(EasyBreadcrumbConstants::TITLE_FROM_PAGE_WHEN_AVAILABLE, $form_state->getValue(EasyBreadcrumbConstants::TITLE_FROM_PAGE_WHEN_AVAILABLE))
      ->set(EasyBreadcrumbConstants::LANGUAGE_PATH_PREFIX_AS_SEGMENT, $form_state->getValue(EasyBreadcrumbConstants::LANGUAGE_PATH_PREFIX_AS_SEGMENT))
      ->set(EasyBreadcrumbConstants::USE_MENU_TITLE_AS_FALLBACK, $form_state->getValue(EasyBreadcrumbConstants::USE_MENU_TITLE_AS_FALLBACK))
      ->set(EasyBreadcrumbConstants::USE_PAGE_TITLE_AS_MENU_TITLE_FALLBACK, $form_state->getValue(EasyBreadcrumbConstants::USE_PAGE_TITLE_AS_MENU_TITLE_FALLBACK))
      ->set(EasyBreadcrumbConstants::REMOVE_REPEATED_SEGMENTS, $form_state->getValue(EasyBreadcrumbConstants::REMOVE_REPEATED_SEGMENTS))
      ->set(EasyBreadcrumbConstants::ABSOLUTE_PATHS, $form_state->getValue(EasyBreadcrumbConstants::ABSOLUTE_PATHS))
      ->set(EasyBreadcrumbConstants::HIDE_SINGLE_HOME_ITEM, $form_state->getValue(EasyBreadcrumbConstants::HIDE_SINGLE_HOME_ITEM))
      ->set(EasyBreadcrumbConstants::TERM_HIERARCHY, $form_state->getValue(EasyBreadcrumbConstants::TERM_HIERARCHY))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
