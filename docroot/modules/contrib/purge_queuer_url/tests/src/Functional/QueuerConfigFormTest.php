<?php

namespace Drupal\Tests\purge_queuer_url\Functional;

use Drupal\purge_queuer_url\Form\ConfigurationForm;
use Drupal\Tests\purge_ui\Functional\Form\Config\QueuerConfigFormTestBase;

/**
 * Tests \Drupal\purge_queuer_url\Form\ConfigurationForm.
 *
 * @group purge_queuer_url
 */
class QueuerConfigFormTest extends QueuerConfigFormTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['purge_queuer_url'];

  /**
   * {@inheritdoc}
   */
  protected $pluginId = 'urlpath';

  /**
   * {@inheritdoc}
   */
  protected $formClass = ConfigurationForm::class;

  /**
   * {@inheritdoc}
   */
  protected $formId = 'purge_queuer_url.configuration_form';

  /**
   * {@inheritdoc}
   */
  public function testSaveConfigurationSubmit(): void {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet($this->getPath());
    // Assert the standard fields and their default values.
    $this->assertSession()->fieldExists('edit-queue-paths');
    $this->assertSession()->checkboxNotChecked('edit-queue-paths');
    $this->assertSession()->fieldExists('edit-host-override');
    $this->assertSession()->checkboxNotChecked('edit-host-override');
    $this->assertSession()->fieldExists('edit-host');
    $this->assertSession()->fieldValueEquals('edit-host', '');
    $this->assertSession()->fieldExists('edit-scheme-override');
    $this->assertSession()->checkboxNotChecked('edit-scheme-override');
    $this->assertSession()->fieldExists('edit-scheme');
    $this->assertSession()->fieldValueEquals('edit-scheme', 'http');
    $this->assertRaw('Clear traffic history');
    // Test that direct configuration changes are reflected properly.
    $this->config('purge_queuer_url.settings')
      ->set('queue_paths', TRUE)
      ->set('host_override', TRUE)
      ->set('host', 'foobar.baz')
      ->set('scheme_override', TRUE)
      ->set('scheme', 'https')
      ->save();
    $this->drupalGet($this->getPath());
    $this->assertSession()->checkboxChecked('edit-queue-paths');
    $this->assertSession()->checkboxChecked('edit-host-override');
    $this->assertSession()->fieldValueEquals('edit-host', 'foobar.baz');
    $this->assertSession()->checkboxChecked('edit-scheme-override');
    $this->assertSession()->fieldValueEquals('edit-scheme', 'https');
  }

}
