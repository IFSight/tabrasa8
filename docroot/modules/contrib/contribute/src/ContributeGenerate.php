<?php

namespace Drupal\contribute;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Variable;
use Drupal\Core\Cache\CacheBackendInterface;
use GuzzleHttp\ClientInterface;

/**
 * Class ContributeGenerate.
 *
 * Please note: This is service is used convert /core/MAINTAINERS.txt into
 * a PHP data array.
 *
 * @see contribute_get_drupal()
 */
class ContributeGenerate implements ContributeGenerateInterface {

  /**
   * The default cache bin.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The Guzzle HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The app root.
   *
   * @var string
   */
  protected $root;

  /**
   * Constructs a new ContributeGenerate object.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The default cache bin.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Guzzle HTTP client.
   * @param string $root
   *   The app root.
   */
  public function __construct(CacheBackendInterface $cache, ClientInterface $http_client, $root) {
    $this->cache = $cache;
    $this->httpClient = $http_client;
    $this->root = $root;
  }

  /**
   * {@inheritdoc}
   */
  public function drupal() {
    $content = file_get_contents($this->root . '/core/MAINTAINERS.txt');

    // Normalize new lines.
    $content = str_replace("\r\n", "\n", $content);
    $content = str_replace("\r", "\n", $content);

    // Remove returns in sentences.
    $content = preg_replace("/([a-z.-:,?])\n([a-zA-Z0-9])/ms", '\1 \2', $content);

    // Remove indenting.
    $content = preg_replace("/\n[ ]+/", "\n", $content);

    $replace = [
      // Replace sections that need to be more specific.
      'Backend' => 'Framework managers: Backend',
      'Frontend' => 'Framework managers: Frontend',
      'MySQL DB driver' => 'Database API: MySQL DB driver',
      'PostgreSQL DB driver' => 'Database API: PostgreSQL DB driver',
      'Sqlite DB driver' => 'Database API: Sqlite DB driver',
      // Fix maintainers.
      "- Marek 'mlewand' Lewandowski https://www.drupal.org/u/mlewand" => "- Marek Lewandowski 'mlewand' https://www.drupal.org/u/mlewand",
      'https://www.drupal.org/u/tim.plunkett' => 'https://www.drupal.org/u/timplunkett',
      'https://www.drupal.org/u/juampy' => 'https://www.drupal.org/u/juampynr',
      'https://www.drupal.org/u/emma.maria' => 'https://www.drupal.org/u/emmamaria',
      'https://www.drupal.org/u/claudiu.cristea' => 'https://www.drupal.org/u/claudiucristea',
      // Content.
      'BDFL' => 'Project lead',
      'This file lists' => 'This page lists',
      'Provisional membership: None at this time.' => '',
    ];
    $content = str_replace(array_keys($replace), $replace, $content);

    // Remove unneeded sections.
    $content = preg_replace('/\s(?:Framework managers|Database API)\s/', '', $content);

    $lines = explode("\n", $content);

    $data = [
      'introduction' => [
        'name' => 'introduction',
        'title' => 'About Drupal core',
        'content' => [],
        'projects' => [],
        'people' => [],
      ],
    ];
    $group_name = 'introduction';
    $project_name = '';
    foreach ($lines as $index => $line) {
      // Skip empty lines and section dividers.
      if (empty($line) || preg_match('/^-+$/', $line)) {
        continue;
      }

      // Next line.
      $next_line = $lines[$index + 1];

      // For now, skip projects that don't have maintainers.
      if (preg_match('/^- \?$/', $next_line)) {
        continue;
      }

      // Data target.
      if (!empty($project_name)) {
        $data_target =& $data[$group_name]['projects'][$project_name];
      }
      else {
        $data_target =& $data[$group_name];
      }

      if (preg_match('/^- /', $line)) {
        if (preg_match("#- (\(provisional\) )?(.*?) '([^']+)' (https://www.drupal.org/u/[^/]+)#", $line, $match)) {
          $person_provisional = $match[1] ? TRUE : FALSE;
          $person_name = $match[2];
          $person_user = $match[3];
          $person_url = $match[4];
          $data_target['people'][] = [
            'user' => $person_user,
            'name' => $person_name,
            'title' => ($person_provisional) ? '(Provisional)' : '',
            'url' => $person_url,
            'image' => $this->userPicture(basename($person_url)),
          ];
        }
        elseif ($line !== '- ?') {
          throw new \Exception('Unable to parse person.' . $line);
        }
      }
      elseif (preg_match('/^- /', $next_line)) {
        // Project.
        $project_title = $line;
        $project_name = Html::getClass($line);
        $data[$group_name]['projects'][$project_name] = [
          'title' => $project_title,
          'content' => [],
          'people' => [],
        ];
      }
      elseif (preg_match('/^-+$/', $next_line)) {
        // Group.
        $group_name = Html::getClass($line);
        $group_title = $line;
        $project_name = '';
        $data[$group_name] = [
          'name' => $group_name,
          'title' => $group_title,
          'content' => [],
          'people' => [],
          'projects' => [],
        ];
      }
      else {
        // Description.
        $data[$group_name]['content'][] = $line;
      }
    }

    $variable = Variable::export($data) . ';';
    $content = "<?php
// @codingStandardsIgnoreFile

/**
 * @file
 * This is file was generated using the Contribute module. DO NOT EDIT.
 */

function contribute_get_drupal() {
return $variable
}";

    file_put_contents(drupal_get_path('module', 'contribute') . '/includes/contribute.drupal.inc', $content);
  }

  /**
   * Get user picture from Drupal.org.
   *
   * @param string $username
   *   A Drupal.org user name.
   *
   * @return string
   *   A user picture or default picture from Drupal.org.
   */
  protected function userPicture($username) {
    $cache = $this->cache->get('contribute.user.pictures');
    $pictures = ($cache) ? $cache->data : [];

    if (!isset($pictures[$username])) {
      $uri = 'https://www.drupal.org/u/' . urlencode($username);
      $response = $this->httpClient->get($uri);
      $body = $response->getBody();
      if (preg_match('#<a href="/u/' . urlencode($username) . '"[^>]+><img src="[^"]+(/user-pictures/picture-[^\.]+.[a-z]+)#', $body, $match)) {
        $picture = 'https://www.drupal.org/files/styles/drupalorg_user_picture/public' . $match[1];
      }
      else {
        $picture = 'https://www.drupal.org/files/styles/drupalorg_user_picture/public/default-avatar.png';
      }
      $pictures[$username] = $picture;
      $this->cache->set('contribute.user.pictures', $pictures);
    }

    return $pictures[$username];
  }

}
