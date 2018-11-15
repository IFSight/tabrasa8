<?php

namespace Drupal\webform;

use Drupal\Core\Session\AccountInterface;

/**
 * Interface of webform access rules manager.
 */
interface WebformAccessRulesManagerInterface {

  /**
   * Check if operation is allowed through access rules for a given webform.
   *
   * @param string $operation
   *   Operation to check.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account who is requesting the operation.
   * @param \Drupal\webform\WebformInterface $webform
   *   Webform on which the operation is requested.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   Access result.
   */
  public function checkWebformAccess($operation, AccountInterface $account, WebformInterface $webform);

  /**
   * Check if operation is allowed through access rules for a submission.
   *
   * @param string $operation
   *   Operation to check.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account who is requesting the operation.
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   Webform submission on which the operation is requested.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   Access result.
   */
  public function checkWebformSubmissionAccess($operation, AccountInterface $account, WebformSubmissionInterface $webform_submission);

  /**
   * Returns the webform default access rules.
   *
   * @return array
   *   A structured array containing all the webform default access rules.
   */
  public function getDefaultAccessRules();

  /**
   * Collect metadata on known access rules.
   *
   * @return array
   *   Array that describes all known access rules. It will be keyed by access
   *   rule machine-name and will contain sub arrays with the following
   *   structure:
   *   - title: (string) Human-friendly translated string that describes the
   *     meaning of this access rule.
   *   - description: (array) Renderable array that explains what this access rule
   *     stands for. Defaults to an empty array.
   *   - roles: (string[]) Array of role IDs that should be granted this access
   *     rule by default. Defaults to an empty array.
   *   - permissions: (string[]) Array of permissions that should be granted this
   *     access rule by default. Defaults to an empty array.
   */
  public function getAccessRulesInfo();

}
