<?php

namespace Drupal\ultimate_cron\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests that scheduler plugins are discovered correctly.
 *
 * @group ultimate_cron
 */
class LoggerWebTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['ultimate_cron', 'ultimate_cron_logger_test'];

  /**
   * A user with permissions to administer and run cron jobs.
   *
   * @var \Drupal\user\Entity\User $user
   */
  protected $user;

  /**
   * Flag to control if errors should be ignored or not.
   *
   * @var bool
   */
  protected $ignoreErrors = FALSE;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->createUser([
      'administer ultimate cron',
      'view cron jobs',
      'run cron jobs',
    ]);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests that the logger handles an exception correctly.
   */
  public function testLoggerException() {

    \Drupal::state()->set('ultimate_cron_logger_test_cron_action', 'exception');

    // Run cron to get an exception from ultimate_cron_logger_test module.
    $this->cronRun();

    // Check that the error message is displayed in its log page.
    $this->drupalGet('admin/config/system/cron/jobs/logs/ultimate_cron_logger_test_cron');
    $this->assertRaw('/core/misc/icons/e32700/error.svg');
    $this->assertRaw('<em class="placeholder">Exception</em>: Test cron exception in <em class="placeholder">ultimate_cron_logger_test_cron()</em> (line');
  }

  /**
   * Tests that the logger handles an exception correctly.
   */
  public function testLoggerFatal() {

    \Drupal::state()->set('ultimate_cron_logger_test_cron_action', 'fatal');

    // Run cron to get an exception from ultimate_cron_logger_test module.
    $this->ignoreErrors = TRUE;
    $this->cronRun();
    $this->ignoreErrors = TRUE;

    // Check that the error message is displayed in its log page.
    $this->drupalGet('admin/config/system/cron/jobs/logs/ultimate_cron_logger_test_cron');
    $this->assertRaw('/core/misc/icons/e32700/error.svg');
    $this->assertRaw('Call to undefined function call_to_undefined_function');

    // Empty the logfile, our fatal errors are expected.
    $filename = DRUPAL_ROOT . '/' . $this->siteDirectory . '/error.log';
    file_put_contents($filename, '');
  }

  /**
   * Tests that the logger handles long message correctly.
   */
  public function testLoggerLongMessage() {

    \Drupal::state()->set('ultimate_cron_logger_test_cron_action', 'long_message');

    // Run cron to get a long message log from ultimate_cron_logger_test.
    $this->cronRun();

    // Check that the long log message is properly trimmed.
    $this->drupalGet('admin/config/system/cron/jobs/logs/ultimate_cron_logger_test_cron');
    $xpath = version_compare(\Drupal::VERSION, '8.5', '>=') ? '/html/body/div/div/main/div/div/table/tbody/tr/td[4]' : '/html/body/div/main/div/div/table/tbody/tr/td[4]';
    // The last 2 chars from xpath are not related to the message.
    $this->assertTrue(strlen(substr($this->xpath($xpath)[0], 0, -2)) == 5000);
    $this->assertRaw('This is a v…');
  }

  /**
   * Tests that the logger handles an exception correctly.
   */
  public function testLoggerLogWarning() {

    \Drupal::state()->set('ultimate_cron_logger_test_cron_action', 'log_warning');

    // Run cron to get an exception from ultimate_cron_logger_test module.
    $this->cronRun();

    // Check that the error message is displayed in its log page.
    $this->drupalGet('admin/config/system/cron/jobs/logs/ultimate_cron_logger_test_cron');
    $this->assertRaw('/core/misc/icons/e29700/warning.svg');
    $this->assertRaw('This is a warning message');
  }


  /**
   * Tests that the logger handles an exception correctly.
   */
  public function testLoggerNormal() {
    // Run cron to get an exception from ultimate_cron_logger_test module.
    $this->cronRun();

    // Check that the error message is displayed in its log page.
    $this->drupalGet('admin/config/system/cron/jobs/logs/ultimate_cron_logger_test_cron');
    $this->assertRaw('/core/misc/icons/73b355/check.svg');
    $this->assertText('Launched in thread 1');
  }

  /**
   * Reads headers and registers errors received from the tested site.
   *
   * Overriden to not report fatal errors if $this->ignoreErrors is set to TRUE.
   *
   * @param $curlHandler
   *   The cURL handler.
   * @param $header
   *   An header.
   *
   * @see _drupal_log_error()
   */
  protected function curlHeaderCallback($curlHandler, $header) {
    // Header fields can be extended over multiple lines by preceding each
    // extra line with at least one SP or HT. They should be joined on receive.
    // Details are in RFC2616 section 4.
    if ($header[0] == ' ' || $header[0] == "\t") {
      // Normalize whitespace between chucks.
      $this->headers[] = array_pop($this->headers) . ' ' . trim($header);
    }
    else {
      $this->headers[] = $header;
    }

    // Errors are being sent via X-Drupal-Assertion-* headers,
    // generated by _drupal_log_error() in the exact form required
    // by \Drupal\simpletest\WebTestBase::error().
    if (!$this->ignoreErrors && preg_match('/^X-Drupal-Assertion-[0-9]+: (.*)$/', $header, $matches)) {
      // Call \Drupal\simpletest\WebTestBase::error() with the parameters from
      // the header.
      call_user_func_array(array(&$this, 'error'), unserialize(urldecode($matches[1])));
    }

    // Save cookies.
    if (preg_match('/^Set-Cookie: ([^=]+)=(.+)/', $header, $matches)) {
      $name = $matches[1];
      $parts = array_map('trim', explode(';', $matches[2]));
      $value = array_shift($parts);
      $this->cookies[$name] = array('value' => $value, 'secure' => in_array('secure', $parts));
      if ($name === $this->getSessionName()) {
        if ($value != 'deleted') {
          $this->sessionId = $value;
        }
        else {
          $this->sessionId = NULL;
        }
      }
    }

    // This is required by cURL.
    return strlen($header);
  }

}
