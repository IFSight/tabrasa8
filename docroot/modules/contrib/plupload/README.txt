This module integrates the Plupload library (available from http://plupload.com)
with Drupal forms. To install the Plupload library:

1. Download it (version 2.1.1 or later) from
   https://github.com/moxiecode/plupload/releases.
2. Unzip it into libraries folder, so that there's a
   libraries/plupload/js/plupload.full.min.js file, in addition to the other
   files included in the library.
3. Remove "examples" folder from libraries folder as it could constitute a
   security risk to your site. See http://drupal.org/node/1895328 and
   http://drupal.org/node/1189632 for more info.

If you would like to use an alternate library location, you can add

  $conf['plupload.settings']['library_path'] = PATH/TO/PLUPLOAD;

to your settings.php file.

At this time, this module only provides a 'plupload' form element type that
other modules can use for providing multiple file upload capability to their
forms. It does not provide any end-user functionality on its own.

---=== For developers ===---

Plupload from element can be used like this:

$form['my_element'] = array(
  '#type' => 'plupload',
  '#title' => t('Upload files'),
  '#description' => t('This multi-upload widget uses Plupload library.'),
  '#autoupload' => TRUE,
  '#autosubmit' => TRUE,
  '#submit_element' => '#id-of-your-submit-element',
  '#upload_validators' => array(
    'file_validate_extensions' => array('jpg jpeg gif png txt doc xls pdf ppt pps odt ods odp'),
    'my_custom_file_validator' => array('some validation criteria'),
  );
  '#plupload_settings' => array(
    'runtimes' => 'html5',
    'chunk_size' => '1mb',
  ),
  '#event_callbacks' => array(
    'FilesAdded' => 'Drupal.mymodule.filesAddedCallback',
    'UploadComplete' => 'Drupal.mymodule.uploadCompleteCallback',
  ),
);

There are few optional properties of this array that have special meaning:

-  #autoupload: set this to TRUE if you want Plupload to start uploading
  immediately after files are added.
  Defaults to FALSE.

-  #autosubmit: set this to TRUE if you want Plupload to autosubmit
  your form after automatic upload has finished.
  Defaults to FALSE.
  Has to be used in combination with #autoupload.

-  #submit_element: specify which submit element Plupload shall use to submit
  the form. Can also be used in combination with #autoupload and #autosubmit.
  See: http://drupal.org/node/1935256

- #upload_validators - an array of validation function/validation criteria pairs,
  that will be passed to file_validate().
  Defaults to:
  '#upload_validators' => array(
    'file_validate_extensions' => array('jpg jpeg gif png txt doc xls pdf ppt pps odt ods odp'),
  );

- #plupload_settings - array of settings, that will be passed to Plupload library.
  See: http://www.plupload.com/documentation.php
  Defaults to:
  '#plupload_settings' => array(
    'runtimes' => 'html5,flash,html4',
    'url' => url('plupload-handle-uploads', array('query' => array('plupload_token' => drupal_get_token('plupload-handle-uploads')))),
    'max_file_size' => file_upload_max_size() . 'b',
    'chunk_size' => '1mb',
    'unique_names' => TRUE,
    'flash_swf_url' => file_create_url($library_path . '/js/plupload.flash.swf'),
    'silverlight_xap_url' => file_create_url($library_path . '/js/plupload.silverlight.xap'),
  ),

- #event_callbacks - array of callbacks that will be passed to js.
  See full documentation about events in Plupload library:
  http://www.plupload.com/example_events.php
