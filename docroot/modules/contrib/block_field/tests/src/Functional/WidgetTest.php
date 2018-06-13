<?php

namespace Drupal\Tests\block_field\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the block field widget.
 *
 * @group block_field
 */
class WidgetTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'node',
    'user',
    'block',
    'block_field',
    'block_field_widget_test',
    'field_ui',
  ];

  /**
   * The test block node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $blockNode;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->drupalLogin($this->drupalCreateUser([
      'access administration pages',
      'access content',
      'administer content types',
      'administer node fields',
      'administer node form display',
      'administer nodes',
      'bypass node access',
    ]));

    $this->drupalPostForm('node/add/block_node', [
      'title[0][value]' => 'Block field test',
      'field_block[0][plugin_id]' => 'views_block:items-block_1',
    ], $this->t('Save'));

    $this->blockNode = $this->drupalGetNodeByTitle('Block field test');
  }

  /**
   * Test block settings are stored correctly.
   */
  public function testBlockSettingsAreStoredCorrectly() {
    $items = $this->createDummyNodes('item', 5);

    $this->drupalGet($this->blockNode->toUrl('edit-form'));
    $this->submitForm([
      'field_block[0][settings][override][items_per_page]' => 5,
    ], $this->t('Save'));

    foreach ($items as $item) {
      $this->assertSession()->pageTextContains($item->getTitle());
    }
  }

  /**
   * Test configuration form options.
   */
  public function testConfigurationFormOptions() {
    $assert = $this->assertSession();

    // Configuration form: full (the default).
    $this->drupalGet($this->blockNode->toUrl('edit-form'));
    $assert->fieldExists('field_block[0][settings][label_display]');
    $assert->fieldExists('field_block[0][settings][override][items_per_page]');
    $assert->fieldExists('field_block[0][settings][views_label_checkbox]');
    $assert->fieldExists('field_block[0][settings][views_label]');

    // Configuration form: hidden.
    $this->drupalGet('admin/structure/types/manage/block_node/form-display');
    $this->drupalPostForm(NULL, [], 'field_block_settings_edit');
    $edit = [
      'fields[field_block][settings_edit_form][settings][configuration_form]' => 'hidden',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->drupalGet($this->blockNode->toUrl('edit-form'));
    $assert->fieldNotExists('field_block[0][settings][label_display]');
    $assert->fieldNotExists('field_block[0][settings][override][items_per_page]');
    $assert->fieldNotExists('field_block[0][settings][views_label_checkbox]');
    $assert->fieldNotExists('field_block[0][settings][views_label]');
  }

  /**
   * Create dummy nodes.
   *
   * @param string $bundle
   *   The bundle type to create.
   * @param int $numberOfNodes
   *   The number of nodes to create.
   *
   * @return array
   *   And array of created nodes.
   */
  private function createDummyNodes($bundle, $numberOfNodes) {
    $nodes = [];

    for ($i = 0; $i < $numberOfNodes; $i++) {
      $nodes[] = $this->createNode(['type' => $bundle]);
    }

    return $nodes;
  }

}
