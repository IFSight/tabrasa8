<?php

namespace Drupal\recurring_events\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Excluded dates entity.
 *
 * @ConfigEntityType(
 *   id = "excluded_dates",
 *   label = @Translation("Excluded dates"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\recurring_events\ExcludedDatesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\recurring_events\Form\ExcludedDatesForm",
 *       "edit" = "Drupal\recurring_events\Form\ExcludedDatesForm",
 *       "delete" = "Drupal\recurring_events\Form\ExcludedDatesDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\recurring_events\ExcludedDatesHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "excluded_dates",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/events/series/excluded_dates/{excluded_dates}",
 *     "add-form" = "/admin/structure/events/series/excluded_dates/add",
 *     "edit-form" = "/admin/structure/events/series/excluded_dates/{excluded_dates}/edit",
 *     "delete-form" = "/admin/structure/events/series/excluded_dates/{excluded_dates}/delete",
 *     "collection" = "/admin/structure/events/series/excluded_dates"
 *   }
 * )
 */
class ExcludedDates extends ConfigEntityBase implements ExcludedDatesInterface {

  /**
   * The Excluded dates ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Excluded dates label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Excluded dates start date.
   *
   * @var string
   */
  protected $start;


  /**
   * The Excluded dates end date.
   *
   * @var string
   */
  protected $end;

  /**
   * {@inheritdoc}
   */
  public function start() {
    return isset($this->start) ? $this->start : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function end() {
    return isset($this->end) ? $this->end : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setStart($start) {
    $this->start = $start;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnd($end) {
    $this->end = $end;
    return $this;
  }

}
