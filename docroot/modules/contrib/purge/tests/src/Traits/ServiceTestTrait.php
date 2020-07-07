<?php

namespace Drupal\Tests\purge\Traits;

use Drupal\purge\ServiceBase;
use Drupal\purge\ServiceInterface;

/**
 * Properties and methods for services.yml exposed classes.
 *
 * @see \Drupal\Tests\purge\Kernel\KernelServiceTestBase
 */
trait ServiceTestTrait {

  /**
   * The name of the service as defined in services.yml.
   *
   * @var string
   */
  protected $serviceId;

  /**
   * Instance of the service being tested, instantiated by the container.
   *
   * @var mixed
   */
  protected $service;

  /**
   * Set up the test.
   */
  public function setUp($switch_to_memory_queue = TRUE): void {
    parent::setUp($switch_to_memory_queue);
    $this->initializeService();
  }

  /**
   * Initialize the requested service as $this->$variable (or reload).
   *
   * @param string $variable
   *   The place to put the loaded/reloaded service, defaults to $this->service.
   * @param string $service
   *   The name of the service to load, defaults to $this->serviceId.
   */
  protected function initializeService($variable = 'service', $service = NULL): void {
    if (is_null($this->$variable)) {
      if (is_null($service)) {
        $this->$variable = $this->container->get($this->serviceId);
      }
      else {
        $this->$variable = $this->container->get($service);
      }
    }
    if ($this->$variable instanceof ServiceInterface) {
      $this->$variable->reload();
    }
  }

  /**
   * Test for \Drupal\purge\ServiceBase and \Drupal\purge\ServiceInterface.
   *
   * Services not derived from \Drupal\purge\ServiceInterface, should overload
   * this test. This applies to plugin managers for instance.
   */
  public function testCodeContract(): void {
    $this->assertTrue($this->service instanceof ServiceInterface);
    $this->assertTrue($this->service instanceof ServiceBase);
  }

  /**
   * Assert that a \Iterator implementation functions as expected.
   *
   * @param string[] $expected_plugins
   *   Plugins that can be expected to be returned by the iterator.
   * @param null|string $type
   *   Check if the service is also of the given type (class name).
   */
  public function assertIterator(array $expected_plugins, $type = NULL): void {
    // Assert that the service implements PHP's \Iterator interface.
    $this->assertTrue($this->service instanceof \Iterator);
    // Iterate the service, count all items and typecheck the instances.
    $items = 0;
    foreach ($this->service as $instance) {
      if ($type) {
        $this->assertTrue($instance instanceof $type, var_export($instance->getPluginId(), TRUE));
      }
      $items++;
    }
    $this->assertEquals(count($expected_plugins), $items);
    // Assert the default states for ::current(), ::valid() and rewind().
    $this->assertFalse($this->service->current(), '::current returns FALSE');
    $this->assertFalse($this->service->valid(), '::valid returns FALSE');
    $this->assertNull($this->service->rewind(), '::rewind returns NULL');
    // Assert that hand iteration works as expected.
    $count_expected_plugins = count($expected_plugins);
    for ($i = 0; $i < $count_expected_plugins; $i++) {
      $this->assertTrue($this->service->valid(), '$this->service->valid() returns TRUE');
      $k = array_search($this->service->current()->getPluginId(), $expected_plugins);
      $this->assertTrue(is_int($k) && isset($expected_plugins[$k]), 'is_int($k) && isset($expected_plugins[$k]) returns TRUE');
      unset($expected_plugins[$k]);
      $this->assertNull($this->service->next(), '$this->service->next() returns NULL');
    }
    $this->assertTrue(empty($expected_plugins));
  }

}
