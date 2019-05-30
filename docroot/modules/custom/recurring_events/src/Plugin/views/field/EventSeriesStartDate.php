<?php

namespace Drupal\recurring_events\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to show the start date of an event series.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("eventseries_start_date")
 */
class EventSeriesStartDate extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $start_date = 'N/A';

    $event = $values->_entity;
    $event_start = $event->getSeriesStart();

    if (!empty($event_start)) {
      $format = \Drupal::config('recurring_events.eventseries.config')->get('date_format');
      $start_date = $event_start->format($format);
    }
    return $start_date;
  }

}
