<?php

namespace Drupal\contribute\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Class ContributeCommunityController.
 */
class ContributeCommunityController extends ControllerBase {

  /**
   * Returns Drupal core maintainers.
   *
   * @return array
   *   A renderable array containing Drupal core maintainers.
   */
  public function drupal() {
    // \Drupal::service('contribute.generate')->drupal();
    module_load_include('inc', 'contribute', 'includes/contribute.drupal');
    $data = contribute_get_drupal();
    return $this->buildGroups($data);
  }

  /**
   * Returns Drupal Association staff and board members.
   *
   * @return array
   *   A renderable array containing Drupal Association staff and board members.
   */
  public function association() {
    module_load_include('inc', 'contribute', 'includes/contribute.association');
    $data = contribute_get_association();
    return $this->buildGroups($data);
  }

  /**
   * Returns contributed module and theme project maintainers.
   *
   * @return array
   *   A renderable array containing contributed module and theme
   *   project maintainers.
   */
  public function projects() {
    module_load_include('inc', 'contribute', 'includes/contribute.projects');
    $data = contribute_get_projects();
    return $this->buildGroups($data);
  }

  /*   * ************************************************************************* */

  /**
   * Build groups.
   *
   * @param array $data
   *   An associative array containing groups and projects.
   *
   * @return array
   *   A renderable array representing a status report grouped section.
   */
  protected function buildGroups(array $data) {
    foreach ($data as &$group) {
      $group += ['content' => [], 'people' => []];

      $group['description'] = [
        'content' => $this->buildDescription($group['content']),
        'people' => $this->buildPeople($group['people']),
      ];

      $group['items'] = [];
      foreach ($group['projects'] as $index => $project) {

        $project += ['content' => [], 'people' => [], 'links' => []];

        $group['items'][$index] = [
          'title' => $project['title'],
          'description' => [
            'people' => $this->buildPeople($project['people']),
            'links' => $this->buildLinks($project['links']),
          ],
        ];
      }
    }

    return [
      '#theme' => 'contribute_status_report_grouped',
      '#grouped_requirements' => $data,
    ];
  }

  /**
   * Build description.
   *
   * @param array $paragraphs
   *   An array of paragraphs.
   *
   * @return array
   *   A renderable array containing paragraphs.
   */
  protected function buildDescription(array $paragraphs) {
    $build = [];
    foreach ($paragraphs as $paragraph) {
      $paragraph = _filter_url($paragraph, (object) ['settings' => ['filter_url_length' => 255]]);
      $build[] = [
        '#markup' => $paragraph,
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }
    return $build;
  }

  /**
   * Build links.
   *
   * @param array $links
   *   An array of links.
   *
   * @return array
   *   A renderable array containing links.
   */
  protected function buildLinks(array $links) {
    if (empty($links)) {
      return [];
    }

    $build = [
      '#prefix' => '<div class="contribute-links">',
      '#suffix' => '</div>',
    ];
    foreach ($links as $link) {
      $build[] = [
        '#type' => 'link',
        '#title' => $link['title'],
        '#url' => $link['url'],
      ];
    }
    return $build;
  }

  /**
   * Build people.
   *
   * @param array $people
   *   An array of people.
   *
   * @return array
   *   A renderable array containing people.
   */
  protected function buildPeople(array $people) {
    if (empty($people)) {
      return [];
    }

    $build = [
      '#prefix' => '<div class="contribute-people">',
      '#suffix' => '</div>',
    ];
    foreach ($people as $person) {
      // Default default values.
      $person += [
        'user' => '',
        'name' => '',
        'title' => '',
        'url' => '',
        'image' => '',
      ];

      // Url.
      $url = Url::fromUri($person['url']);

      // Caption.
      $caption = [
        '#type' => 'link',
        '#title' => $person['name'],
        '#url' => $url,
        '#suffix' => ($person['title']) ? '<br/>' . $person['title'] : '',
      ];

      // Node (image).
      $node = ($person['image']) ? [
        '#type' => 'link',
        '#title' => [
          '#type' => 'html_tag',
          '#tag' => 'img',
          '#attributes' => [
            'src' => $person['image'],
            'alt' => $person['name'],
            'width' => '80',
          ],
        ],
        '#url' => $url,
      ] : NULL;

      $build[] = [
        '#theme' => 'contribute_person',
        '#caption' => $caption,
        '#node' => $node,
      ];
    }

    return $build;
  }

}
