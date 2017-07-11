<?php

/**
 * @file
 * Contains \Drupal\plupload\UploadException.
 */

namespace Drupal\plupload;


use Symfony\Component\HttpFoundation\JsonResponse;

class UploadException extends \Exception {

  /**
   * Error with input stream.
   */
  const INPUT_ERROR = 101;

  /**
   * Error with output stream.
   */
  const OUTPUT_ERROR = 102;

  /**
   * Error moving uploaded file.
   */
  const MOVE_ERROR = 103;

  /**
   * Error with destination folder.
   */
  const DESTINATION_FOLDER_ERROR = 104;

  /**
   * Error with temporary file name.
   */
  const FILENAME_ERROR = 105;

  /**
   * Code to error message mapping.
   *
   * @param array $code
   */
  public $errorMessages = array(
    self::INPUT_ERROR => 'Failed to open input stream.',
    self::OUTPUT_ERROR => 'Failed to open output stream.',
    self::MOVE_ERROR => 'Failed to move uploaded file.',
    self::DESTINATION_FOLDER_ERROR => 'Failed to open temporary directory.',
    self::FILENAME_ERROR => 'Invalid temporary file name.',
  );

  /**
   * Constructs UploadException.
   *
   * @param int $code
   *   Error code.
   */
  public function __construct($code) {
    $this->code = $code;
    $this->message = $this->errorMessages[$this->code];
  }

  /**
   * Generates and returns JSON response object for the error.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response object.
   */
  public function getErrorResponse() {
    return new JsonResponse(
      array(
        'jsonrpc' => '2.0',
        'error' => array(
          'code' => $this->code,
          'message' => $this->errorMessages[$this->code],
        ),
        'id' => 'id',
      ),
      500
    );
  }

}
