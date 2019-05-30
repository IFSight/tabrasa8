<?php

namespace Drupal\recurring_events_registration\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Registrant entities.
 */
class RegistrantViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
