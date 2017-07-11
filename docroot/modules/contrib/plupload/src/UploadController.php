<?php

/**
 * @file
 * Contains \Drupal\plupload\UploadController.
 */

namespace Drupal\plupload;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Plupload upload handling route.
 */
class UploadController implements ContainerInjectionInterface {

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request object.
   */
  protected $request;

  /**
   * Stores temporary folder URI.
   *
   * This is configurable via the configuration variable. It was added for HA
   * environments where temporary location may need to be a shared across all
   * servers.
   *
   * @var string
   */
  protected $temporaryUploadLocation;

  /**
   * Filename of a file that is being uploaded.
   *
   * @var string
   */
  protected $filename;

  /**
   * Constructs plupload upload controller route controller.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request object.
   */
  public function __construct(Request $request) {
    $this->request = $request;
    $this->temporaryUploadLocation = \Drupal::config('plupload.settings')->get('temporary_uri');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('request_stack')->getCurrentRequest());
  }

  /**
   * Handles Plupload uploads.
   */
  public function handleUploads() {
    // @todo: Implement file_validate_size();
    try {
      $this->prepareTemporaryUploadDestination();
      $this->handleUpload();
    }
    catch (UploadException $e) {
      return $e->getErrorResponse();
    }

    // Return JSON-RPC response.
    return new JsonResponse(
      array(
        'jsonrpc' => '2.0',
        'result' => NULL,
        'id' => 'id',
      ),
      200
    );
  }

  /**
   * Prepares temporary destination folder for uploaded files.
   *
   * @return bool
   *   TRUE if destination folder looks OK and FALSE otherwise.
   *
   * @throws \Drupal\plupload\UploadException
   */
  protected function prepareTemporaryUploadDestination() {
    $writable = file_prepare_directory($this->temporaryUploadLocation, FILE_CREATE_DIRECTORY);
    if (!$writable) {
      throw new UploadException(UploadException::DESTINATION_FOLDER_ERROR);
    }

    // Try to make sure this is private via htaccess.
    file_save_htaccess($this->temporaryUploadLocation, TRUE);
  }

  /**
   * Reads, checks and return filename of a file being uploaded.
   *
   * @throws \Drupal\plupload\UploadException
   */
  protected function getFilename() {
    if (empty($this->filename)) {
      try {
        // @todo this should probably bo OO.
        $this->filename = _plupload_fix_temporary_filename($this->request->request->get('name'));
      }
      catch (InvalidArgumentException $e) {
        throw new UploadException(UploadException::FILENAME_ERROR);
      }

      // Check the file name for security reasons; it must contain letters, numbers
      // and underscores followed by a (single) ".tmp" extension. Since this check
      // is more stringent than the one performed in plupload_element_value(), we
      // do not need to run the checks performed in that function here. This is
      // fortunate, because it would be difficult for us to get the correct list of
      // allowed extensions to pass in to file_munge_filename() from this point in
      // the code (outside the form API).
      if (!preg_match('/^\w+\.tmp$/', $this->filename)) {
        throw new UploadException(UploadException::FILENAME_ERROR);
      }
    }

    return $this->filename;
  }

  /**
   * Handles multipart uploads.
   *
   * @throws \Drupal\plupload\UploadException
   */
  protected function handleUpload() {
    /* @var $multipart_file \Symfony\Component\HttpFoundation\File\UploadedFile */
    $is_multipart = strpos($this->request->headers->get('Content-Type'), 'multipart') !== FALSE;

    // If this is a multipart upload there needs to be a file on the server.
    if ($is_multipart) {
      $multipart_file = $this->request->files->get('file', array());
      // TODO: Not sure if this is the best check now.
      // Originally it was:
      // if (empty($multipart_file['tmp_name']) || !is_uploaded_file($multipart_file['tmp_name'])) {
      if (!$multipart_file->getPathname() || !is_uploaded_file($multipart_file->getPathname())) {
        throw new UploadException(UploadException::MOVE_ERROR);
      }
    }

    // Open temp file.
    if (!($out = fopen($this->temporaryUploadLocation . $this->getFilename(), $this->request->request->get('chunk', 0) ? 'ab' : 'wb'))) {
      throw new UploadException(UploadException::OUTPUT_ERROR);
    }

    // Read binary input stream.
    $input_uri = $is_multipart ? $multipart_file->getRealPath() : 'php://input';
    if (!($in = fopen($input_uri, 'rb'))) {
      throw new UploadException(UploadException::INPUT_ERROR);
    }

    // Append input stream to temp file.
    while ($buff = fread($in, 4096)) {
      fwrite($out, $buff);
    }

    // Be nice and keep everything nice and clean.
    fclose($in);
    fclose($out);
    if ($is_multipart) {
      drupal_unlink($multipart_file->getRealPath());
    }
  }

}
