<?php

namespace Drupal\Tests\purge\Unit\Logger;

use Drupal\purge\Logger\LoggerService;
use Drupal\Tests\purge\Unit\FixGetConfigFactoryStubTrait;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\purge\Logger\LoggerService
 *
 * @group purge
 */
class LoggerServiceTest extends UnitTestCase {
  use FixGetConfigFactoryStubTrait;

  /**
   * Default configuration.
   *
   * @var array[]
   */
  protected $defaultConfig = [
    LoggerService::CONFIG => [
      LoggerService::CKEY => [
        ['id' => 'exists', 'grants' => [1, 2, 3]],
        ['id' => 'foo', 'grants' => [1, 2, 3]],
        ['id' => 'foobar', 'grants' => [1, 2, 3]],
        ['id' => 'foobarbaz', 'grants' => [1, 2, 3]],
      ],
    ],
  ];

  /**
   * The tested LoggerService object.
   *
   * @var \Drupal\purge\Logger\LoggerService
   */
  protected $loggerService;

  /**
   * The mocked channel part factory.
   *
   * @var \PHPUnit_Framework_MockObject_MockObject|\Drupal\purge\Logger\LoggerChannelPartFactoryInterface
   */
  protected $loggerChannelPartFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->loggerChannelPartFactory = $this->createMock('\Drupal\purge\Logger\LoggerChannelPartFactoryInterface');
    $this->loggerChannelPartFactory->method('create')
      ->willReturn($this->createMock('\Drupal\purge\Logger\LoggerChannelPartInterface'));
  }

  /**
   * @covers ::destruct
   *
   * @dataProvider providerTestDestruct()
   */
  public function testDestruct($expect_write, $call = NULL, $arguments = []): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $config_factory
      ->expects($expect_write ? $this->once() : $this->never())
      ->method('getEditable')
      ->with($this->stringContains('purge.logger_channels'));
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    if (!is_null($call)) {
      call_user_func_array([$service, $call], $arguments);
    }
    $service->destruct();
  }

  /**
   * Provides test data for testDestruct().
   */
  public function providerTestDestruct(): array {
    return [
      [FALSE],
      [TRUE, 'get', ['newid']],
      [FALSE, 'get', ['exists']],
      [FALSE, 'deleteChannels', ['doesnotexist']],
      [TRUE, 'deleteChannel', ['exists']],
      [TRUE, 'deleteChannels', ['ex']],
      [TRUE, 'setChannel', ['new', [1, 2, 3]]],
      [TRUE, 'setChannel', ['exists', []]],
    ];
  }

  /**
   * @covers ::deleteChannel
   *
   * @dataProvider providerTestDeleteChannel()
   */
  public function testDeleteChannel($id, $exists): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $this->assertEquals($exists, $service->hasChannel($id));
    $this->assertEquals(NULL, $service->deleteChannel($id));
    $this->assertEquals(FALSE, $service->hasChannel($id));
  }

  /**
   * Provides test data for testDeleteChannel().
   */
  public function providerTestDeleteChannel(): array {
    return [
      ['exists', TRUE],
      ['foobarbaz', TRUE],
      ['doesnotexists', FALSE],
    ];
  }

  /**
   * @covers ::deleteChannels
   *
   * @dataProvider providerTestDeleteChannels()
   */
  public function testDeleteChannels($id_starts_with, $has, $hasnot): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    foreach ($has as $id) {
      $this->assertEquals(TRUE, $service->hasChannel($id));
    }
    foreach ($hasnot as $id) {
      $this->assertEquals(TRUE, $service->hasChannel($id));
    }
    $this->assertEquals(NULL, $service->deleteChannels($id_starts_with));
    foreach ($has as $id) {
      $this->assertEquals(TRUE, $service->hasChannel($id));
    }
    foreach ($hasnot as $id) {
      $this->assertEquals(FALSE, $service->hasChannel($id));
    }
  }

  /**
   * Provides test data for testDeleteChannels().
   */
  public function providerTestDeleteChannels(): array {
    return [
      ['E', ['foo', 'foobar', 'foobarbaz', 'exists'], []],
      ['e', ['foo', 'foobar', 'foobarbaz'], ['exists']],
      ['ex', ['foo', 'foobar', 'foobarbaz'], ['exists']],
      ['exi', ['foo', 'foobar', 'foobarbaz'], ['exists']],
      ['exis', ['foo', 'foobar', 'foobarbaz'], ['exists']],
      ['exist', ['foo', 'foobar', 'foobarbaz'], ['exists']],
      ['exists', ['foo', 'foobar', 'foobarbaz'], ['exists']],
      ['foobarbaz', ['exists', 'foo', 'foobar'], ['foobarbaz']],
      ['foobarba', ['exists', 'foo', 'foobar'], ['foobarbaz']],
      ['foobarb', ['exists', 'foo', 'foobar'], ['foobarbaz']],
      ['foobar', ['exists', 'foo'], ['foobar', 'foobarbaz']],
      ['fooba', ['exists', 'foo'], ['foobar', 'foobarbaz']],
      ['foob', ['exists', 'foo'], ['foobar', 'foobarbaz']],
      ['foo', ['exists'], ['foo', 'foobar', 'foobarbaz']],
      ['fo', ['exists'], ['foo', 'foobar', 'foobarbaz']],
      ['f', ['exists'], ['foo', 'foobar', 'foobarbaz']],
    ];
  }

  /**
   * @covers ::get
   *
   * @dataProvider providerTestGet()
   */
  public function testGet($id): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $uncached = $service->get($id);
    $this->assertInstanceOf('\Drupal\purge\Logger\LoggerChannelPartInterface', $uncached);
    $cached = $service->get($id);
    $this->assertInstanceOf('\Drupal\purge\Logger\LoggerChannelPartInterface', $cached);
    $this->assertEquals(spl_object_hash($uncached), spl_object_hash($cached));
  }

  /**
   * Provides test data for testGet().
   */
  public function providerTestGet(): array {
    return [
      ['exists'],
      ['doesnotexists'],
    ];
  }

  /**
   * @covers ::getChannels
   */
  public function testGetChannels(): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $channels_conf = $this->defaultConfig[LoggerService::CONFIG][LoggerService::CKEY];
    $channels = $service->getChannels();
    $this->assertTrue(is_array($channels));
    $this->assertEquals(count($channels), count($channels_conf));
    $this->assertEquals($channels, $channels_conf);
  }

  /**
   * @covers ::hasChannel
   *
   * @dataProvider providerTestHasChannel()
   */
  public function testHasChannel($id, $shouldexist): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $this->assertEquals($service->hasChannel($id), $shouldexist);
  }

  /**
   * Provides test data for testHasChannel().
   */
  public function providerTestHasChannel(): array {
    return [
      ['exists', TRUE],
      ['foo', TRUE],
      ['foobar', TRUE],
      ['foobarbaz', TRUE],
      ['fo', FALSE],
      ['doesnotexists', FALSE],
    ];
  }

  /**
   * @covers ::setChannel
   *
   * @dataProvider providerTestSetChannel()
   */
  public function testSetChannel($id, $preexists): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $this->assertEquals($preexists, $service->hasChannel($id));
    $this->assertEquals(NULL, $service->setChannel($id, [1, 2, 3]));
    $this->assertEquals(TRUE, $service->hasChannel($id));
  }

  /**
   * Provides test data for testSetChannel().
   */
  public function providerTestSetChannel(): array {
    return [
      ['exists', TRUE],
      ['foobarbaz', TRUE],
      ['doesnotexists', FALSE],
      ['alsofake', FALSE],
    ];
  }

  /**
   * @covers ::setChannel
   * @dataProvider providerTestSetChannelIdException()
   */
  public function testSetChannelIdException($id): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $this->expectException(\LogicException::class);
    $this->expectExceptionMessage('The given ID is empty or not a string!');
    $service->setChannel($id);
  }

  /**
   * Provides test data for testSetChannelIdException().
   */
  public function providerTestSetChannelIdException(): array {
    return [
      [''],
      [1],
    ];
  }

  /**
   * @covers ::setChannel
   * @dataProvider providerTestSetChannelGrantsException()
   */
  public function testSetChannelGrantsException($id, $grants): void {
    $config_factory = $this->getConfigFactoryStub($this->defaultConfig);
    $service = new LoggerService($config_factory, $this->loggerChannelPartFactory);
    $this->expectException(\LogicException::class);
    $this->expectExceptionMessage('Passed grant is invalid!');
    $service->setChannel($id, $grants);
  }

  /**
   * Provides test data for testSetChannelGrantsException().
   */
  public function providerTestSetChannelGrantsException(): array {
    return [
      ['id1', [-1]],
      ['id2', [10]],
    ];
  }

}
