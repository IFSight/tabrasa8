<?php

namespace Drupal\contribute;

/**
 * Interface ContributeGenerateInterface.
 */
interface ContributeGenerateInterface {

  /**
   * Generates list of Drupal core maintainers from /core/MAINTAINERS.txt.
   *
   * @throws \Exception
   *   Throws exception when unable to parse a maintainer's information.
   */
  public function drupal();

}
