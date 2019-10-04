<?php

namespace Drupal\recurring_events_registration\Plugin\GroupContentEnabler;

use Drupal\group\Plugin\GroupContentEnablerBase;

/**
 * Allows Registrant content to be added to groups.
 *
 * @GroupContentEnabler(
 *   id = "registrant",
 *   label = @Translation("Registrant"),
 *   description = @Translation("Adds Registrant entities to groups."),
 *   entity_type_id = "registrant",
 *   path_key = "registrant",
 * )
 */
class Registrant extends GroupContentEnablerBase {
}
