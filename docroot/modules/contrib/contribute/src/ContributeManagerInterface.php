<?php

namespace Drupal\contribute;

/**
 * Interface ContributeManagerInterface.
 */
interface ContributeManagerInterface {

  /**
   * Get account status.
   *
   * @return array
   *   An associative array containing account status.
   */
  public function getAccount();

  /**
   * Get membership status.
   *
   * @return array
   *   An associative array containing membership status.
   */
  public function getMembership();

  /**
   * Get contribution status.
   *
   * @return array
   *   An associative array containing contribution status.
   */
  public function getContribution();

  /**
   * Get Drupal core maintainers.
   *
   * @return array
   *   An associative array containing Drupal core maintainers.
   */
  public function getDrupal();

  /**
   * Get Drupal association staff.
   *
   * @return array
   *   An associative array containing Drupal association staff.
   */
  public function getAssociation();

  /**
   * Get Drupal project maintainers.
   *
   * @return array
   *   An associative array containing Drupal project maintainers.
   */
  public function getProjects();

  /**
   * Get a random person from the Drupal community.
   *
   * @param string $type
   *   The type of person. (drupal or association)
   *
   * @return array
   *   An associative array containing a random person from the Drupal
   *   community.
   */
  public function getPerson($type);

  /**
   * Get contribute status.
   *
   * @return bool
   *   The contribute status.
   */
  public function getStatus();

  /**
   * Get account type.
   *
   * @return string|null
   *   Get the account type.
   */
  public function getAccountType();

  /**
   * Get account id.
   *
   * @return string|null
   *   Get the account id.
   */
  public function getAccountId();

  /**
   * Set account type.
   *
   * @param string|null $account_type
   *   The account type.
   */
  public function setAccountType($account_type);

  /**
   * Set account id.
   *
   * @param string|null $account_id
   *   The account id.
   */
  public function setAccountId($account_id);

}
