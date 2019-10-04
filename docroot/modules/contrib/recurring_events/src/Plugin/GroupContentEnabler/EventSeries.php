<?php

namespace Drupal\recurring_events\Plugin\GroupContentEnabler;

use Drupal\group\Plugin\GroupContentEnablerBase;

/**
 * Allows EventSeries content to be added to groups.
 *
 * @GroupContentEnabler(
 *   id = "event_series",
 *   label = @Translation("Event Series"),
 *   description = @Translation("Adds Event Series entities to groups."),
 *   entity_type_id = "eventseries",
 *   path_key = "eventseries",
 * )
 */
class EventSeries extends GroupContentEnablerBase {
}
