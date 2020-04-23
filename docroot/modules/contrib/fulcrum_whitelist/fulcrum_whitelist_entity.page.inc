<?php

/**
 * @file
 * Contains fulcrum_whitelist_entity.page.inc.
 *
 * Page callback for Fulcrum Whitelist Entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Fulcrum Whitelist Entity templates.
 *
 * Default template: fulcrum_whitelist_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_fulcrum_whitelist_entity(array &$variables) {
  // Fetch FulcrumWhitelistEntity Entity Object.
  $fulcrum_whitelist_entity = $variables['elements']['#fulcrum_whitelist_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
