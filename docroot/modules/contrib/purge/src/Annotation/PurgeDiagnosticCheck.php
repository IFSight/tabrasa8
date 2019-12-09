<?php

namespace Drupal\purge\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PurgeDiagnosticCheck annotation object.
 *
 * @Annotation
 */
class PurgeDiagnosticCheck extends Plugin {

  /**
   * The plugin ID of the diagnostic check.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the check.
   *
   * @var \Drupal\Core\Annotation\Translation
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of what the check does.
   *
   * @var \Drupal\Core\Annotation\Translation
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * Required queue plugins.
   *
   * When your diagnostic check is specific for a certain queue plugin(s) you
   * can bind it to these plugins. This check will then only get loaded when
   * any of these specified queues are in active use.
   *
   * @var array
   *
   * @code
   * dependent_queue_plugins = {"memory", "file"}
   * @endcode
   */
  public $dependent_queue_plugins = []; // phpcs:ignore -- annotation property!

  /**
   * Required purger plugins.
   *
   * When your diagnostic check is specific for a certain purger plugin(s) you
   * can bind it to these plugins. This check will then only get loaded when
   * any of these specified purgers are in active use.
   *
   * @var array
   *
   * @code
   * dependent_purger_plugins = {"mypurger"}
   * @endcode
   */
  public $dependent_purger_plugins = []; // phpcs:ignore -- annotation property!

}
