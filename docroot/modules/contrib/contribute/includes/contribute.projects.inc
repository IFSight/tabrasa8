<?php

/**
 * @file
 * Drupal projects data.
 */

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\Core\Utility\ProjectInfo;

/**
 * Get Drupal modules and themes data.
 *
 * @return array
 *   An associative array containing Drupal modules and themese data.
 */
function contribute_get_projects() {
  $project_info = new ProjectInfo();

  $data = [];

  $modules = system_get_info('module');
  if ($projects = _contribute_get_projects('module', $modules)) {
    $data['modules'] = [
      'title' => 'Modules',
      'content' => [
        'Your Drupal system can gain many new capabilities when you install community-created modules--plug-ins that give your system more tools to work with.',
      ],
      'projects' => $projects,
    ];
  }

  $themes = [];
  $theme_data = \Drupal::service('theme_handler')->rebuildThemeData();
  $project_info->processInfoList($themes, $theme_data, 'theme', TRUE);
  if ($projects = _contribute_get_projects('theme', $themes)) {
    $data['themes'] = [
      'title' => 'Themes',
      'content' => [
        'Themes allow you to change the look and feel of your Drupal site. You can use themes contributed by others or create your own to share with the community.',
      ],
      'projects' => $projects,
    ];
  }

  return $data;
}

/**
 * Get projects.
 *
 * @param string $type
 *   Type of project (modules or themes).
 * @param array $extensions
 *   An array of project extensions and information.
 *
 * @return array
 *   An associative array of projects.
 */
function _contribute_get_projects($type, array $extensions) {
  $projects = [];
  foreach ($extensions as $module_machine_name => $extension) {
    // We don't want the modules that don't come from drupal.org, comes from
    // core or are submodules.
    if (!empty($extension['project']) && $extension['project'] != 'drupal' && $extension['project'] == $module_machine_name) {
      // Get composer data.
      $composer_json = drupal_get_path($type, $module_machine_name) . '/composer.json';
      $composer_data = (file_exists($composer_json)) ? Json::decode(file_get_contents($composer_json)) : [];

      $project = [
        'title' => $extension['name'],
        'people' => [],
      ];

      // Get people.
      if (isset($composer_data['authors'])) {
        foreach ($composer_data['authors'] as $author) {
          if (isset($author['homepage']) && strpos($author['homepage'], '/committers') === FALSE) {
            $project['people'][] = [
              'name' => $author['name'],
              'title' => (isset($author['role'])) ? $author['role'] : '',
              'url' => (isset($author['homepage'])) ? $author['homepage'] : '',
            ];
          }
        }
      }

      // Get links.
      $links['homepage'] = [
        'title' => t('Homepage'),
        'url' => Url::fromUri('https://www.drupal.org/project/' . $module_machine_name),
      ];
      $links['issues'] = [
        'title' => t('Issues'),
        'url' => Url::fromUri('https://www.drupal.org/project/issues/' . $module_machine_name),
      ];
      $links['source'] = [
        'title' => t('Source'),
        'url' => Url::fromUri('https://cgit.drupalcode.org/' . $module_machine_name),
      ];
      $links['committers'] = [
        'title' => t('Committers'),
        'url' => Url::fromUri('https://www.drupal.org/node/' . $module_machine_name . '/committers'),
      ];
      if (isset($composer_data['homepage'])) {
        $links['homepage'] = [
          'title' => t('Homepage'),
          'url' => Url::fromUri($composer_data['homepage']),
        ];
      }
      if (isset($composer_data['support'])) {
        unset($composer_data['support']['irc']);
        foreach ($composer_data['support'] as $support_type => $support_uri) {
          $links[$support_type]['url'] = Url::fromUri($support_uri);
        }
      }
      $project['links'] = $links;

      $projects[] = $project;
    }
  }
  return $projects;
}
