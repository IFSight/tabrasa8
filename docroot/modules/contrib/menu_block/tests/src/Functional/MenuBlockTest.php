<?php

namespace Drupal\Tests\menu_block\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests for the menu_block module.
 *
 * @group menu_block
 */
class MenuBlockTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'block',
    'menu_block',
    'menu_block_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * An administrative user to configure the test environment.
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected $adminUser;

  /**
   * The menu link plugin manager.
   *
   * @var \Drupal\Core\Menu\MenuLinkManagerInterface
   */
  protected $menuLinkManager;

  /**
   * The block storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blockStorage;

  /**
   * The block view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $blockViewBuilder;

  /**
   * The menu link content storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $menuLinkContentStorage;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * An array containing the menu link plugin ids.
   *
   * @var array
   */
  protected $links;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->menuLinkManager = \Drupal::service('plugin.manager.menu.link');
    $this->blockStorage = \Drupal::service('entity_type.manager')
      ->getStorage('block');
    $this->blockViewBuilder = \Drupal::service('entity_type.manager')
      ->getViewBuilder('block');
    $this->menuLinkContentStorage = \Drupal::service('entity_type.manager')
      ->getStorage('menu_link_content');
    $this->moduleHandler = \Drupal::moduleHandler();

    $this->links = $this->createLinkHierarchy();

    // Create and log in an administrative user.
    $this->adminUser = $this->drupalCreateUser([
      'administer blocks',
      'access administration pages',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Creates a simple hierarchy of links.
   */
  protected function createLinkHierarchy() {
    // First remove all the menu links in the menu.
    $this->menuLinkManager->deleteLinksInMenu('main');

    // Then create a simple link hierarchy:
    // - parent menu item
    //   - child-1 menu item
    //     - child-1-1 menu item
    //     - child-1-2 menu item
    //   - child-2 menu item.
    $base_options = [
      'provider' => 'menu_block',
      'menu_name' => 'main',
    ];

    $parent = $base_options + [
      'title' => 'parent menu item',
      'link' => ['uri' => 'internal:/menu-block-test/hierarchy/parent'],
    ];
    /** @var \Drupal\menu_link_content\MenuLinkContentInterface $link */
    $link = $this->menuLinkContentStorage->create($parent);
    $link->save();
    $links['parent'] = $link->getPluginId();

    $child_1 = $base_options + [
      'title' => 'child-1 menu item',
      'link' => ['uri' => 'internal:/menu-block-test/hierarchy/parent/child-1'],
      'parent' => $links['parent'],
    ];
    $link = $this->menuLinkContentStorage->create($child_1);
    $link->save();
    $links['child-1'] = $link->getPluginId();

    $child_1_1 = $base_options + [
      'title' => 'child-1-1 menu item',
      'link' => ['uri' => 'internal:/menu-block-test/hierarchy/parent/child-1/child-1-1'],
      'parent' => $links['child-1'],
    ];
    $link = $this->menuLinkContentStorage->create($child_1_1);
    $link->save();
    $links['child-1-1'] = $link->getPluginId();

    $child_1_2 = $base_options + [
      'title' => 'child-1-2 menu item',
      'link' => ['uri' => 'internal:/menu-block-test/hierarchy/parent/child-1/child-1-2'],
      'parent' => $links['child-1'],
    ];
    $link = $this->menuLinkContentStorage->create($child_1_2);
    $link->save();
    $links['child-1-2'] = $link->getPluginId();

    $child_2 = $base_options + [
      'title' => 'child-2 menu item',
      'link' => ['uri' => 'internal:/menu-block-test/hierarchy/parent/child-2'],
      'parent' => $links['parent'],
    ];
    $link = $this->menuLinkContentStorage->create($child_2);
    $link->save();
    $links['child-2'] = $link->getPluginId();

    return $links;
  }

  /**
   * Checks if all menu block configuration options are available.
   */
  public function testMenuBlockFormDisplay() {
    $this->drupalGet('admin/structure/block/add/menu_block:main');
    $this->assertSession()->pageTextContains('Initial visibility level');
    $this->assertSession()->pageTextContains('Number of levels to display');
    $this->assertSession()->pageTextContains('Expand all menu links');
    $this->assertSession()->pageTextContains('Fixed parent item');
    $this->assertSession()
      ->pageTextContains('Make the initial visibility level follow the active menu item.');
    $this->assertSession()->pageTextContains('Theme hook suggestion');
  }

  /**
   * Checks if all menu block settings are saved correctly.
   */
  public function testMenuBlockUi() {
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[level]' => 2,
      'settings[depth]' => 6,
      'settings[expand]' => TRUE,
      'settings[parent]' => 'main:',
      'settings[follow]' => TRUE,
      'settings[follow_parent]' => 'active',
      'settings[suggestion]' => 'main',
      'region' => 'primary_menu',
    ], 'Save block');

    $block = $this->blockStorage->load($block_id);
    $block_settings = $block->get('settings');
    $this->assertSame(2, $block_settings['level']);
    $this->assertSame(6, $block_settings['depth']);
    $this->assertTrue($block_settings['expand']);
    $this->assertSame('main:', $block_settings['parent']);
    $this->assertTrue($block_settings['follow']);
    $this->assertSame('active', $block_settings['follow_parent']);
    $this->assertSame('main', $block_settings['suggestion']);
  }

  /**
   * Tests the menu_block level option.
   */
  public function testMenuBlockLevel() {
    // Add new menu block.
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[level]' => 1,
      'region' => 'primary_menu',
    ], 'Save block');

    // Check if the parent menu item is visible, but the child menu items not.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');
    $this->assertSession()->pageTextNotContains('child-2 menu item');

    $this->drupalPostForm('admin/structure/block/manage/' . $block_id, [
      'settings[level]' => 2,
    ], 'Save block');

    // Check if the menu items of level 2 are visible, but not the parent menu
    // item.
    $this->drupalGet('menu-block-test/hierarchy/parent');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-2 menu item');
    $this->assertSession()->pageTextNotContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');
  }

  /**
   * Tests the menu_block depth option.
   */
  public function testMenuBlockDepth() {
    // Add new menu block.
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[level]' => 1,
      'settings[depth]' => 1,
      'settings[expand]' => TRUE,
      'region' => 'primary_menu',
    ], 'Save block');

    // Check if the parent menu item is visible, but the child menu items not.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');
    $this->assertSession()->pageTextNotContains('child-2 menu item');

    $this->drupalPostForm('admin/structure/block/manage/' . $block_id, [
      'settings[depth]' => 2,
    ], 'Save block');

    // Check if the menu items of level 2 are visible, but not the parent menu
    // item.
    $this->drupalGet('menu-block-test/hierarchy/parent');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-2 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');

    $this->drupalPostForm('admin/structure/block/manage/' . $block_id, [
      'settings[depth]' => 0,
    ], 'Save block');

    // Check if the menu items of level 2 are visible, but not the parent menu
    // item.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-2 menu item');
    $this->assertSession()->pageTextContains('child-1-1 menu item');
  }

  /**
   * Tests the menu_block expand option.
   */
  public function testMenuBlockExpand() {
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[level]' => 1,
      'settings[expand]' => TRUE,
      'region' => 'primary_menu',
    ], 'Save block');

    // Check if the parent menu item is visible, but the child menu items not.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-1-1 menu item');
    $this->assertSession()->pageTextContains('child-2 menu item');

    $this->drupalPostForm('admin/structure/block/manage/' . $block_id, [
      'settings[expand]' => FALSE,
    ], 'Save block');

    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');
    $this->assertSession()->pageTextNotContains('child-2 menu item');
  }

  /**
   * Tests the menu_block parent option.
   */
  public function testMenuBlockParent() {
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[parent]' => 'main:' . $this->links['parent'],
      'region' => 'primary_menu',
    ], 'Save block');

    $this->drupalGet('<front>');
    $this->assertSession()->pageTextNotContains('parent menu item');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');

    $this->drupalPostForm('admin/structure/block/manage/' . $block_id, [
      'settings[parent]' => 'main:' . $this->links['child-1'],
    ], 'Save block');

    $this->assertSession()->pageTextNotContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-1-1 menu item');
    $this->assertSession()->pageTextContains('child-1-2 menu item');
  }

  /**
   * Tests the menu_block follow and follow_parent option.
   */
  public function testMenuBlockFollow() {
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[follow]' => TRUE,
      'settings[follow_parent]' => 'child',
      'region' => 'primary_menu',
    ], 'Save block');

    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');

    $this->drupalGet('menu-block-test/hierarchy/parent');
    $this->assertSession()->pageTextNotContains('parent menu item');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-2 menu item');

    $this->drupalGet('menu-block-test/hierarchy/parent/child-1');
    $this->assertSession()->pageTextNotContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextContains('child-1-1 menu item');
    $this->assertSession()->pageTextContains('child-1-2 menu item');
    $this->assertSession()->pageTextNotContains('child-2 menu item');

    $this->drupalPostForm('admin/structure/block/manage/' . $block_id, [
      'settings[follow_parent]' => 'active',
    ], 'Save block');

    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextNotContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');

    $this->drupalGet('menu-block-test/hierarchy/parent');
    $this->assertSession()->pageTextContains('parent menu item');
    $this->assertSession()->pageTextContains('child-1 menu item');
    $this->assertSession()->pageTextNotContains('child-1-1 menu item');
  }

  /**
   * Tests the menu_block suggestion option.
   */
  public function testMenuBlockSuggestion() {
    $block_id = 'main';
    $this->drupalPostForm('admin/structure/block/add/menu_block:main', [
      'id' => $block_id,
      'settings[label]' => 'Main navigation',
      'settings[label_display]' => FALSE,
      'settings[suggestion]' => 'mainnav',
      'region' => 'primary_menu',
    ], 'Save block');

    /** @var \Drupal\block\BlockInterface $block */
    $block = $this->blockStorage->load($block_id);
    $plugin = $block->getPlugin();

    // Check theme suggestions for block template.
    $variables = [];
    $variables['elements']['#configuration'] = $plugin->getConfiguration();
    $variables['elements']['#plugin_id'] = $plugin->getPluginId();
    $variables['elements']['#id'] = $block->id();
    $variables['elements']['#base_plugin_id'] = $plugin->getBaseId();
    $variables['elements']['#derivative_plugin_id'] = $plugin->getDerivativeId();
    $variables['elements']['content'] = [];
    $suggestions = $this->moduleHandler->invokeAll('theme_suggestions_block', [$variables]);

    $base_theme_hook = 'menu_block';
    $hooks = [
      'theme_suggestions',
      'theme_suggestions_' . $base_theme_hook,
    ];
    $this->moduleHandler->alter($hooks, $suggestions, $variables, $base_theme_hook);

    $this->assertSame($suggestions, [
      'block__menu_block',
      'block__menu_block',
      'block__menu_block__main',
      'block__main',
      'block__menu_block__mainnav',
    ], 'Found expected block suggestions.');

    // Check theme suggestions for menu template.
    $variables = [
      'menu_name' => 'main',
      'menu_block_configuration' => $plugin->getConfiguration(),
    ];
    $suggestions = $this->moduleHandler->invokeAll('theme_suggestions_menu', [$variables]);

    $base_theme_hook = 'menu';
    $hooks = [
      'theme_suggestions',
      'theme_suggestions_' . $base_theme_hook,
    ];
    $this->moduleHandler->alter($hooks, $suggestions, $variables, $base_theme_hook);
    $this->assertSame($suggestions, [
      'menu__main',
      'menu__mainnav',
    ], 'Found expected menu suggestions.');
  }

}
