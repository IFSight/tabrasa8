<?php

namespace Drupal\Tests\masquerade\Functional;

/**
 * Tests caching for masquerade.
 *
 * @group masquerade
 */
class MasqueradeCacheTest extends MasqueradeWebTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'masquerade',
    'user',
    'block',
    'node',
    'dynamic_page_cache',
    'toolbar',
  ];

  /**
   * Tests caching for the user switch block.
   */
  public function testMasqueradeSwitchBlockCaching() {
    // Create two masquerade users.
    $umberto = $this->drupalCreateUser([
      'masquerade as any user',
      'access content',
    ], 'umberto');
    $nelle = $this->drupalCreateUser([
      'masquerade as any user',
      'access content',
    ], 'nelle');

    // Add the Masquerade block to the sidebar.
    $masquerade_block = $this->drupalPlaceBlock('masquerade');

    // Login as Umberto.
    $this->drupalLogin($umberto);
    $this->drupalGet('<front>');
    $this->assertBlockAppears($masquerade_block);

    // Masquerade as Nelle.
    $edit = ['masquerade_as' => $nelle->getAccountName()];
    $this->drupalPostForm(NULL, $edit, $this->t('Switch'));
    $this->drupalGet('<front>');
    $this->assertNoBlockAppears($masquerade_block);

    // Logout, and log in as Nelle.
    $this->drupalLogout();
    $this->drupalLogin($nelle);
    $this->drupalGet('<front>');
    $this->assertBlockAppears($masquerade_block);
  }

  /**
   * Tests caching for the Unmasquerade link in the admin toolbar.
   */
  public function testMasqueradeToolbarLinkCaching() {
    // Create a test user with toolbar access.
    $test_user = $this->drupalCreateUser([
      'access content',
      'access toolbar',
    ]);

    // Login as admin and masquerade as the test user to have the page cached
    // as the test user.
    $this->drupalLogin($this->admin_user);
    $this->masqueradeAs($test_user);
    $this->assertSession()->linkExists('Unmasquerade');
    // We only check here for the session cache context, because it is present
    // alongside with session.is_masquerading and the latter is optimized away.
    // So only the session cache context remains.
    $this->assertCacheContext('session');

    // Login as the test user and make sure the Unmasquerade link is not visible
    // and the cache context is correctly set.
    $this->drupalLogin($test_user);
    $this->assertSession()->linkNotExists('Unmasquerade');
    $this->assertCacheContext('session.is_masquerading');
  }

}
