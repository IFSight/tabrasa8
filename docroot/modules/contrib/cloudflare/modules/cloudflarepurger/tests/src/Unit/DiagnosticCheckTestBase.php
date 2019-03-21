<?php

namespace Drupal\Tests\cloudflarepurger\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\KeyValueStore\KeyValueMemoryFactory;
use Drupal\Core\Cache\MemoryBackend;
use Drupal\Core\Lock\NullLockBackend;
use Drupal\Core\State\State as CoreState;
use Drupal\cloudflare\State as CloudFlareState;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Tests that purge_requirements() passes on our diagnostic checks.
 */
abstract class DiagnosticCheckTestBase extends UnitTestCase {

  /**
   * The dependency injection container.
   *
   * @var \Drupal\Core\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * Tracks Drupal states.
   *
   * @var \Drupal\Core\state\StateInterface
   */
  protected $drupalState;

  /**
   * Tracks rate limits associated with CloudFlare Api.
   *
   * @var \Drupal\cloudflare\CloudFlareStateInterface
   */
  protected $cloudflareState;

  /**
   * Provides timestamps.
   *
   * @var \Drupal\cloudflare\Timestamp
   */
  protected $timestampStub;

  /**
   * Provides check for composer dependencies.
   *
   * @var \Drupal\cloudflare\CloudFlareComposerDependenciesCheckInterface
   */
  protected $composerDependencyStub;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->drupalState = new CoreState(new KeyValueMemoryFactory(), new MemoryBackend('test'), new NullLockBackend());
    $this->timestampStub = $this->getMockBuilder('Drupal\cloudflare\Timestamp')
      ->disableOriginalConstructor()
      ->getMock();
    $this->cloudflareState = new CloudFlareState($this->drupalState, $this->timestampStub);

    $this->container = new ContainerBuilder();
    $this->container->set('string_translation', $this->getStringTranslationStub());

    $this->composerDependencyStub = $this->getMock('\Drupal\cloudflare\CloudFlareComposerDependenciesCheckInterface');
    $this->composerDependencyStub->expects($this->any())
      ->method('check')
      ->will($this->returnValue(TRUE));
    $this->container->set('cloudflare.composer_dependency_check', $this->composerDependencyStub);

    \Drupal::setContainer($this->container);
  }

}
