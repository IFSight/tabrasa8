<?php

namespace Drupal\varnish_purger\Plugin\Purge\Purger;

use Drupal\purge\Plugin\Purge\Purger\PurgerInterface;
use Drupal\purge\Plugin\Purge\Invalidation\InvalidationInterface;
use Drupal\varnish_purger\Plugin\Purge\Purger\VarnishPurgerBase;

/**
 * Varnish Bundled Purger
 *
 * @PurgePurger(
 *   id = "varnishbundled",
 *   label = @Translation("Varnish Bundled Purger"),
 *   configform = "\Drupal\varnish_purger\Form\VarnishBundledPurgerForm",
 *   cooldown_time = 0.0,
 *   description = @Translation("Configurable purger that sends a single HTTP request for a set of invalidation instructions."),
 *   multi_instance = TRUE,
 *   types = {},
 * )
 */
class VarnishBundledPurger extends VarnishPurgerBase implements PurgerInterface {

  /**
   * {@inheritdoc}
   */
  public function invalidate(array $invalidations) {

    // Create a simple closure to mass-update states on the objects.
    $set_state = function($state) use($invalidations) {
      foreach ($invalidations as $invalidation) {
        $invalidation->setState($state);
      }
    };

    // Build up a single HTTP request, execute it and log errors.
    $token_data = ['invalidations' => $invalidations];
    $uri = $this->getUri($token_data);
    $opt = $this->getOptions($token_data);

    try {
      $this->client->request($this->settings->request_method, $uri, $opt);
      $set_state(InvalidationInterface::SUCCEEDED);
    }
    catch (\Exception $e) {
      $set_state(InvalidationInterface::FAILED);

      // Log as much useful information as we can.
      $headers = $opt['headers'];
      unset($opt['headers']);
      $debug = json_encode(str_replace("\n", ' ', [
        'msg' => $e->getMessage(),
        'uri' => $uri, 'method' => $this->settings->request_method,
        'guzzle_opt' => $opt, 'headers' => $headers]));
      $this->logger()->emergency("item failed due @e, details (JSON): @debug",
        ['@e' => get_class($e), '@debug' => $debug]);
    }
  }

}
