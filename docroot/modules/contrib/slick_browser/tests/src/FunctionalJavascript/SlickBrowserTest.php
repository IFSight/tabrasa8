<?php

namespace Drupal\Tests\slick_browser\FunctionalJavascript;

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\FunctionalJavascriptTests\DrupalSelenium2Driver;
use Drupal\Tests\entity_browser\FunctionalJavascript\EntityBrowserWebDriverTestBase;

/**
 * Tests the Slick Browser JavaScript using Selenium, or Chromedriver.
 *
 * @requires module dropzonejs_eb_widget
 *
 * @group slick_browser
 */
class SlickBrowserTest extends EntityBrowserWebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'seven';

  /**
   * {@inheritdoc}
   */
  protected $minkDefaultDriverClass = DrupalSelenium2Driver::class;

  /**
   * {@inheritdoc}
   */
  protected $testImage;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'filter',
    'field',
    'user',
    'media',
    'inline_entity_form',
    'entity_browser',
    'entity_browser_entity_form',
    'entity_browser_test',
    'dropzonejs',
    'dropzonejs_eb_widget',
    'blazy',
    'blazy_test',
    'slick',
    'slick_browser',
    'slick_browser_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected static $userPermissions = [
    // @todo refine based on actual browsers to test against.
    'access slick_browser_file entity browser pages',
    'create article content',
    'access content',
    'access content overview',
    'create media',
    'access media overview',
    'access files overview',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->root = $this->container->get('app.root');
    $this->fileSystem = $this->container->get('file_system');
    $this->testPluginId = 'slick_browser';

    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
    $form_display = $this->container->get('entity_type.manager')
      ->getStorage('entity_form_display')
      ->load('node.article.default');

    $settings = [
      'style' => 'grid',
      'image_style' => 'slick_browser_preview',
      'grid' => 3,
      'grid_medium' => 2,
      'grid_small' => 1,
    ];
    $form_display->setComponent('field_reference', [
      'type' => 'entity_browser_entity_reference',
      'settings' => [
        'entity_browser' => 'slick_browser_file',
        'field_widget_display' => 'slick_browser_file',
        'field_widget_remove' => TRUE,
        'field_widget_replace' => TRUE,
        'open' => TRUE,
        'selection_mode' => 'selection_append',
        // This is expected by file, media, node entities.
        'field_widget_display_settings' => $settings,
      ],
      // This is expected by image, or core media library.
      'third_party_settings' => [
        'slick_browser' => $settings,
      ],
    ])->save();

    $account = $this->drupalCreateUser(static::$userPermissions);
    $this->drupalLogin($account);

    $this->testImage = $this->createDummyImage('llama.png');
  }

  /**
   * Tests that selecting files in the view works even with direct selection.
   */
  public function testSlickBrowserFileDirectSelection() {
    $this->drupalGet('node/add/article');

    // Ensures Slick Widget exists.
    $this->assertSession()->elementExists('css', '.sb--widget');

    // Open the browser and select a file.
    // @todo $this->drupalGet('entity-browser/iframe/slick_browser_file');
    $this->getSession()->switchToIFrame('entity_browser_iframe_slick_browser_file');

    $this->waitForAjaxToFinish();

    // Wait another moment, iframe build is slow.
    $this->assertSession()->elementNotExists('css', '.grid.is-checked');

    // Wait a moment.
    $result = $this->assertSession()->waitForElement('css', '.grid--0');
    $this->assertNotEmpty($result);
    $this->assertCheckboxExistsByValue('file:' . $this->testImage->id());

    $this->getSession()->getPage()->find('css', '.grid--0')->press();

    $this->assertSession()->elementExists('css', '.grid--0.is-checked');

    // Ensures AJAX is triggered to insert the image into the page.
    $this->getSession()->getPage()->pressButton('Add to Page');

    // Switch back to the main page.
    $this->getSession()->switchToIFrame();
    $this->waitForAjaxToFinish();

    // Ensures image is inserted into the page.
    $result = $this->assertSession()->waitForElement('css', '.sb__sortitem');
    $this->assertNotEmpty($result);
    $this->assertSession()->elementExists('css', '.sb__sortitem');
    $this->assertSession()->pageTextContains('llama.png');

    // Tests the Delete functionality.
    // Cases:
    // - Cardinality 1, relies on AJAX to rebuild link post removal.
    // - Cardinality > 1 or -1, has always Media library link, quick removal.
    $this->assertSession()->buttonExists('Remove');

    $this->getSession()->getPage()->find('css', '.button--wrap__mask')->press();
    $this->getSession()->getPage()->find('css', '.button--wrap__confirm')->press();

    $this->waitForAjaxToFinish();

    $result = $this->assertSession()->waitForElement('css', '.sb__sortitem');
    $this->assertEmpty($result);
    $this->assertSession()->pageTextNotContains('llama.png');
    $this->assertSession()->elementNotExists('css', '.sb__sortitem');
  }

  /**
   * Tests that selecting files in the view works even with delay selection.
   */
  public function testSlickBrowserFileDelaySelection() {
    $this->drupalGet('node/add/article');

    // Ensures Slick Widget exists.
    $this->assertSession()->elementExists('css', '.sb--widget');

    // Open the browser and select a file.
    $this->getSession()->switchToIFrame('entity_browser_iframe_slick_browser_file');

    $this->waitForAjaxToFinish();

    // Wait another moment, iframe build is slow.
    $this->assertSession()->elementNotExists('css', '.grid.is-checked');

    $result = $this->assertSession()->waitForElement('css', '.grid--0');
    $this->assertNotEmpty($result);
    $this->assertCheckboxExistsByValue('file:' . $this->testImage->id());

    $this->getSession()->getPage()->find('css', '.grid--0')->press();

    $this->assertSession()->elementExists('css', '.grid--0.is-checked');

    // Delays selection.
    $this->getSession()->getPage()->pressButton('Select files');
    $this->waitForAjaxToFinish();

    // Ensures selected files were not gone.
    $this->assertSession()->elementExists('css', '.grid--0.was-checked');

    // Ensures AJAX is triggered to insert the image into the page.
    $this->getSession()->getPage()->pressButton('Add to Page');

    // Switch back to the main page.
    $this->getSession()->switchToIFrame();
    $this->waitForAjaxToFinish();

    // Ensures image is inserted into the page.
    $result = $this->assertSession()->waitForElement('css', '.sb__sortitem');
    $this->assertNotEmpty($result);

    $this->assertSession()->elementExists('css', '.sb__sortitem');
    $this->assertSession()->pageTextContains('llama.png');
  }

  /**
   * Returns the created image file.
   */
  protected function createDummyImage($name = '', $source = '') {
    $path   = $this->root . '/sites/default/files/simpletest/' . $this->testPluginId;
    $name   = empty($name) ? $this->testPluginId . '.png' : $name;
    $source = empty($source) ? $this->root . '/core/misc/druplicon.png' : $source;
    $uri    = $path . '/' . $name;

    if (!is_file($uri)) {
      $this->prepareTestDirectory();
      $this->fileSystem->saveData($source, $uri, FileSystemInterface::EXISTS_REPLACE);
    }

    $uri = 'public://simpletest/' . $this->testPluginId . '/' . $name;
    $this->dummyUri = $uri;
    $item = File::create([
      'uri' => $uri,
      'filename' => $name,
    ]);
    $item->setPermanent();
    $item->save();

    return $item;
  }

  /**
   * Prepares test directory to store screenshots, or images.
   */
  protected function prepareTestDirectory() {
    $this->testDirPath = $this->root . '/sites/default/files/simpletest/' . $this->testPluginId;
    $this->fileSystem->prepareDirectory($this->testDirPath, FileSystemInterface::CREATE_DIRECTORY);
  }

}
