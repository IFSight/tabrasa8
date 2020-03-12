<?php

namespace Drupal\if_varnish_purge_tags\Plugin\Purge\TagsHeader;

use Drupal\purge\Plugin\Purge\TagsHeader\TagsHeaderInterface;
use Drupal\purge\Plugin\Purge\TagsHeader\TagsHeaderBase;


/**
 * Sets and formats the default response header with cache tags.
 *
 * @PurgeTagsHeader(
 *   id = "if_varnish_tagsheader",
 *   header_name = "Cache-Tags",
 * )
 */
class CacheTags extends TagsHeaderBase implements TagsHeaderInterface {

  /**
   * {@inheritdoc}
   */
  public function getValue(array $tags) {
    return implode(' ', array_map(function($tag) {return "#$tag#";}, $tags));
  }

}
