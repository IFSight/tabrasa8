<?php
/**
 * @file
 * Contains infrequently used theme registry build functions.
 */

/**
 * Implements HOOK_theme().
 *
 * We are simply using this hook as a convenient time to do some related work.
 */
function _zen_theme(&$existing, $type, $theme, $path) {
  // If we are auto-rebuilding the theme registry, warn about the feature.
  $active_theme = \Drupal::theme()->getActiveTheme()->getName();
  $flood_name = $active_theme . '.rebuild_registry_warning';
  $is_admin_route = \Drupal::service('router.admin_context')->isAdminRoute(\Drupal::routeMatch()->getRouteObject());
  if (
    // Don't display on update.php or install.php.
    !defined('MAINTENANCE_MODE')
    // Only display for theme admins.
    && \Drupal::currentUser()->hasPermission('administer themes')
    && theme_get_setting('zen_rebuild_registry')
    // Always display in the admin section, otherwise limit to three per hour.
    && ($is_admin_route || \Drupal::flood()->isAllowed($flood_name, 3))
  ) {
    \Drupal::flood()->register($flood_name);
    drupal_set_message(t('For easier theme development, the theme registry is being rebuilt on every page request. It is <em>extremely</em> important to <a href="!link">turn off this feature</a> on production websites.', array('!link' => url('admin/appearance/settings/' . $active_theme))), 'warning', FALSE);
  }

  // hook_theme() expects an array, so return an empty one.
  return array();
}
