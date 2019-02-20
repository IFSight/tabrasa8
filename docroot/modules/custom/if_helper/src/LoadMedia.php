<?php

namespace Drupal\if_helper;

use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

/**
 * Class LoadMedia.
 *
 * @package Drupal\if_helper
 */
class LoadMedia {
  /**
   * Load Media object from a media target id.
   *
   * @param string $target_id
   *   The target ID of the media object.
   */
  public static function loadMediaFromTarget($target_id) {
    $media = Media::load($target_id);
    if ($media) {
      // Load the image field on the media object.
      $media = $media->get('field_media_image')->first()->getValue();
      // Get the alt txt.
      $alt_txt = $media['alt'];
      // Now we can load a file entity!
      $file = File::load($media['target_id']);
      // Confirm we have a file entity to work with.
      if ($file) {
        // Get the file URI and do the rest with Twig.
        $file_uri = $file->getFileUri();
        return ['file_uri' => $file_uri, 'alt_txt' => $alt_txt];
      }
    }
  }

  /**
   * Load an image URI from a Media reference inside of a node term reference.
   *
   * @param string $term_reference
   *   The taxonomy term reference field on the node.
   * @param string $media_reference
   *   The media reference field in the taxonomy term.
   */
  public static function loadMediaFromTerm($term_reference, $media_reference) {
    $node = \Drupal::routeMatch()->getParameter('node');

    // Get the Section taxonomy ID.
    $tid = $node->$term_reference->target_id;
    // Load the term.
    $term = Term::load($tid);
    // Load the media reference from the term.
    $media = $term->$media_reference->target_id;
    // Load the media object.
    $media = Media::load($media);
    if ($media) {
      // Load the image field on the media object.
      $media = $media->get('field_media_image')->first()->getValue();
      $alt_txt = $media['alt'];
      // Now we can load a file entity!
      $file = File::load($media['target_id']);
      // Confirm we have a file entity to work with.
      if ($file) {
        // Get the file URI and do the rest with Twig.
        $file_uri = $file->getFileUri();
        return ['file_uri' => $file_uri, 'alt_txt' => $alt_txt];
      }
    }
  }

  /**
   * Load an image URI from a Media reference field on a node.
   *
   * @param string $image_field
   *   The image field on the node (media reference).
   */
  public static function loadImageFromNode($image_field) {

    // Load the media reference from the image field.
    $media = $image_field[0]['target_id'];
    // Load the media object.
    $media = Media::load($media);
    if ($media) {
      // Load the image field on the media object.
      $media = $media->get('field_media_image')->first()->getValue();
      $alt_txt = $media['alt'];
      // Now we can load a file entity!
      $file = File::load($media['target_id']);
      // Confirm we have a file entity to work with.
      if ($file) {
        // Get the file URI and do the rest with Twig.
        $file_uri = $file->getFileUri();
        return ['file_uri' => $file_uri, 'alt_txt' => $alt_txt];
      }
    }
  }

}
