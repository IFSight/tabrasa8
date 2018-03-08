<?php

namespace Drupal\fulcrum_whitelist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FulcrumWhitelistController.
 */
class FulcrumWhitelistController extends ControllerBase {

  /**
   * Whitelist.
   *
   * @return string
   *   Return whitelist page.
   */
  public function whitelist($authtoken) {
    // don't cache this page
    \Drupal::service('page_cache_kill_switch')->trigger();

    $config = \Drupal::config('fulcrum_whitelist.fulcrumwhitelistconfig');

    // make sure we are configured
    if (
      ($env_abbr = $this->env_abbr()) &&
      isset($env_abbr) &&
      isset($env_abbr->abbr) &&
      $env_abbr->abbr != 'unkn'
    ) {
      $selectSQL = $this->t($this->name_email_from_token(), ['@authtoken' => $authtoken]);

      // lookup user by token, make sure there user and token is enabled
      if ((
        $account = \Drupal::database()->query($selectSQL)->fetchObject()) &&
        isset($account->mail)
      ) {
        // curl the whitelist with http://172.17.0.16:18888/scvw/prd/1.2.3.4/foo@bar.com
        $params = [
          '@host' => $config->get('whitelist_host'),
          '@port' => $config->get('port'),
          '@abbr' => $env_abbr->abbr,
          '@env'  => $env_abbr->env,
          '@ip'   => $_SERVER['HTTP_X_CLIENT_IP'], //\Drupal::request()->getClientIp(),
          '@mail' => $account->mail,
        ];

        $url = $this->t('http://@host:@port/@abbr/@env/@ip/@mail', $params);

        // add watchdog
        \Drupal::logger('fulcrum_whitelist')
          ->notice(
            $this->t(
              'Whitelist UID: @uid mail: @mail url: @url',
              [
                '@url'  => $url,
                '@uid'  => $account->uid,
                '@mail' => $account->mail,
              ]
            )
          );

        $result = file_get_contents($url);

        // return the themed wait redirect
        return $this->dechrome_page($config->get('wait_text'), TRUE);
      } else {
        // add watchdog
        \Drupal::logger('fulcrum_whitelist')
          ->notice(
            $this->t(
              'Whitelist attempt failed for token: @authtoken',
              ['@authtoken' => $authtoken]
            )
          );

        return $this->dechrome_page($config->get('fail_text'));
      }
    }

    return $this->dechrome_page($config->get('misconf_text'));
  }

  /**
   * Docs.
   *
   * @return string
   *   Return whitelist docs.
   */
  public function docs() {
    // don't cache this page
    // \Drupal::service('page_cache_kill_switch')->trigger();

    // Get the current user
    $user = \Drupal::currentUser();
    $config = \Drupal::config('fulcrum_whitelist.fulcrumwhitelistconfig');

    $docs_intro = \Drupal::service('renderer')->renderRoot($docs_intro_build);
    $docs_user  = \Drupal::service('renderer')->renderRoot($docs_user_build);

    if ($user->hasPermission('View unpublished Fulcrum Whitelist Entity entities')) {
      $docs_admin = $config->get('docs_admin');
    }

    $build = [
      'page' => [
        '#theme'  => 'docs',
        '#docs_intro'  => $config->get('docs_intro'),
        '#docs_user'   => $config->get('docs_user'),
        '#docs_admin'  => $docs_admin,
      ]
    ];

    return $build;
  }

  private function env_abbr() {
    if (
      ($fconf = json_decode($_SERVER['FULCRUM_CONF'])) &&
      isset($fconf->env) &&
      ($config = \Drupal::config('fulcrum_whitelist.fulcrumwhitelistconfig'))
    ) {
      return (object)[
        'env'  => $fconf->env,
        'abbr' => $config->get('whitelist_abbr'),
      ];
    }

    return false;
  }

  private function dechrome_page($message, $inc_js = FALSE) {
    $js = '';

    if ($inc_js) {
      $config = \Drupal::config('fulcrum_whitelist.fulcrumwhitelistconfig');

      $js_build = [
        'page' => [
          '#theme' => 'javascript',
          '#delay' => $config->get('delay')
        ]
      ];

      $js = \Drupal::service('renderer')->renderRoot($js_build);
    }

    // return the themed wait redirect
    $build = [
      'page' => [
        '#theme'      => 'dechrome',
        '#message'    => $message,
        '#js'         => $js
      ]
    ];

    $html = \Drupal::service('renderer')->renderRoot($build);
    $response = new Response();
    $response->setContent($html);

    return $response;
  }

  private function name_email_from_token() {
    return <<< SQL
      SELECT u.uid, u.mail
      FROM {users_field_data} u
      JOIN {fulcrum_whitelist_entity__field_user} w ON u.uid = w.field_user_target_id
      JOIN {fulcrum_whitelist_entity} e ON w.entity_id = e.id AND e.status = 1
      WHERE u.status = 1
      AND e.name = '@authtoken'
SQL;
  }
}
