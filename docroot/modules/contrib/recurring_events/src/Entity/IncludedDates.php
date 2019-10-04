<?php

namespace Drupal\recurring_events\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Included dates entity.
 *
 * @ConfigEntityType(
 *   id = "included_dates",
 *   label = @Translation("Included dates"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\recurring_events\IncludedDatesListBuilder",
 *     "form" = {
 *       "add" = "Drupal\recurring_events\Form\IncludedDatesForm",
 *       "edit" = "Drupal\recurring_events\Form\IncludedDatesForm",
 *       "delete" = "Drupal\recurring_events\Form\IncludedDatesDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\recurring_events\IncludedDatesHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "included_dates",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/events/series/included_dates/{included_dates}",
 *     "add-form" = "/admin/structure/events/series/included_dates/add",
 *     "edit-form" = "/admin/structure/events/series/included_dates/{included_dates}/edit",
 *     "delete-form" = "/admin/structure/events/series/included_dates/{included_dates}/delete",
 *     "collection" = "/admin/structure/events/series/included_dates"
 *   }
 * )
 */
class IncludedDates extends ConfigEntityBase implements IncludedDatesInterface {

  /**
   * The Included dates ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Included dates label.
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
