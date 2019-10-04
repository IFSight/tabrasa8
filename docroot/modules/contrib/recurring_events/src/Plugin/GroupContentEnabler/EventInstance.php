<?php

namespace Drupal\recurring_events\Plugin\GroupContentEnabler;

use Drupal\group\Plugin\GroupContentEnablerBase;

/**
 * Allows EventInstance content to be added to groups.
 *
 * @GroupContentEnabler(
 *   id = "event_instance",
 *   label = @Translation("Event Instance"),
 *   description = @Translation("Adds Event Instance entities to groups."),
 *   entity_type_id = "eventinstance",
 *   path_key = "eventinstance",
 * )
 */
class EventInstance extends GroupContentEnablerBase {
}
