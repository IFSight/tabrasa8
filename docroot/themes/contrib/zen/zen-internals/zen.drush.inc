<?php
/**
 * @file
 * Contains functions only needed for drush integration.
 */

/**
 * Implementation of hook_drush_command().
 */
function zen_drush_command() {
  $items = array();

  $items['zen'] = array(
    'description' => 'Create a theme using Zen.',
    'arguments' => array(
      'machine_name' => '[optional] A machine-readable name for your theme.',
      'name'         => 'A name for your theme.',
    ),
    'options' => array(
      'name'         => 'A name for your theme.',
      'machine-name' => '[a-z, 0-9, _] A machine-readable name for your theme.',
      'path'         => 'The path where your theme will be created. Defaults to: themes/',
      'description'  => 'A description of your theme.',
    ),
    'examples' => array(
      'drush zen "Amazing name"' => 'Create a sub-theme, using the default options.',
      'drush zen zomg_amazing "Amazing name"' => 'Create a sub-theme with a specific machine name.',
      'drush zen "Amazing name" --path=sites/default/themes --description="So amazing."' => 'Create a sub-theme in the specified directory with a custom description.',
    ),
  );

  return $items;
}

/**
 * Create a Zen sub-theme using the starter kit.
 */
function drush_zen($machine_name = NULL, $name = NULL) {

  // ***************************************************
  // Determine all the command line options given.
  // ***************************************************

  // Determine the theme name and machine name.
  if (!isset($name)) {
    // If only given one argument, it is the $name.
    if (isset($machine_name)) {
      $name = $machine_name;
      unset($machine_name);
    }
    else {
      $name = drush_get_option('name');
    }

    if (!isset($machine_name)) {
      $machine_name = drush_get_option('machine-name');
    }
  }

  if (!$name) {
    if ($machine_name) {
      $name = $machine_name;
    }
    else {
      return drush_set_error('ZEN_SUBTHEME_NAME_NOT_SPECIFIED', dt('The name of the theme was not specified.'));
      // Allow localize.drupal.org to pick up the string to translate.
      if (FALSE) t('The name of the theme was not specified.');
    }
  }

  if (!$machine_name) {
    $machine_name = $name;
  }

  // Clean up the machine name.
  $machine_name = str_replace(' ', '_', strtolower($machine_name));
  $search = array(
    '/[^a-z0-9_]/', // Remove characters not valid in function names.
    '/^[^a-z]+/',   // Functions must begin with an alpha character.
  );
  $machine_name = preg_replace($search, '', $machine_name);

  // Determine the path to the new sub-theme.
  $sub_theme_path = 'themes';
  if ($path = drush_get_option('path')) {
    $sub_theme_path = drush_trim_path($path);
  }
  $sub_theme_path = drush_normalize_path(drush_get_context('DRUSH_DRUPAL_ROOT') . '/' . $sub_theme_path . '/' . $machine_name);

  // ***************************************************
  // Error check directories, then copy STARTERKIT.
  // ***************************************************

  // Ensure the destination directory (not the sub-theme folder) exists.
  if (!is_dir(dirname($sub_theme_path))) {
    return drush_set_error('ZEN_DESTINATION_NOT_FOUND', dt('The directory "!directory" was not found.', array('!directory' => dirname($sub_theme_path))));
    // Allow localize.drupal.org to pick up the string to translate.
    if (FALSE) t('The directory "!directory" was not found.', array('!directory' => dirname($sub_theme_path)));
  }

  // Ensure the STARTERKIT directory exists.
  $starterkit_path = drush_normalize_path(drush_get_context('DRUSH_DRUPAL_ROOT') . '/' . drupal_get_path('theme', 'zen') . '/STARTERKIT');
  if (!is_dir($starterkit_path)) {
    return drush_set_error('ZEN_STARTERKIT_NOT_FOUND', dt('The STARTERKIT directory was not found in "!directory"', array('!directory' => dirname($starterkit_path))));
    // Allow localize.drupal.org to pick up the string to translate.
    if (FALSE) t('The STARTERKIT directory was not found in "!directory"', array('!directory' => dirname($starterkit_path)));
  }

  drush_print(dt('Copying files from starter kit…'));
  // Allow localize.drupal.org to pick up the string to translate.
  if (FALSE) t('Copying files from starter kit…');

  // Make a fresh copy of the original starter kit.
  if (!drush_op('drush_copy_dir', $starterkit_path, $sub_theme_path)) {
    // drush_copy_dir errors are fatal errors for our zen drush command.
    return FALSE;
  }

  // ***************************************************
  // Alter the contents of the .info.yml file.
  // ***************************************************

  drush_print(dt('Updating .info.yml file…'));
  // Allow localize.drupal.org to pick up the string to translate.
  if (FALSE) t('Updating .info.yml file…');

  $info_strings = array(
    ': Zen Sub-theme Starter Kit' => ': ' . $name,
    '# core: 8.x' => 'core: 8.x',
    "core: '8.x'\n" => '',
    "project: 'zen'\n" => '',
  );
  if ($description = drush_get_option('description')) {
    $info_strings['Read the <a href="https://drupal.org/node/873778">online docs</a> or the included README.txt on how to create a theme with Zen.'] = $description;
  }
  $info_regexs = array(
    array('pattern' => '/\# Information added by Drupal\.org packaging script on [\d-]+\n/', 'replacement' => ''),
    array('pattern' => "/version: '[^']+'\n/", 'replacement' => ''),
    array('pattern' => '/datestamp: \d+\n/', 'replacement' => ''),
  );
  drush_op('zen_file_replace', $sub_theme_path . '/STARTERKIT.info.yml', $info_strings, $info_regexs);

  // ***************************************************
  // Replace STARTERKIT in file names and contents.
  // ***************************************************

  drush_print(dt('Replacing "STARTERKIT" in all files…'));
  // Allow localize.drupal.org to pick up the string to translate.
  if (FALSE) t('Replacing "STARTERKIT" in all files…');

  // Iterate through the sub-theme directory finding files to filter.
  $directoryIterator = new \RecursiveDirectoryIterator($sub_theme_path);
  $starterKitFilter = new \RecursiveCallbackFilterIterator($directoryIterator, function ($current, $key, $iterator) {
    // Skip hidden files and directories.
    if ($current->getFilename()[0] === '.') {
      return FALSE;
    }
    // Skip node_modules and the asset-builds folder.
    else if ($current->getFilename() === 'node_modules' || $current->getFilename() === 'asset-builds') {
      return FALSE;
    }
    // Recursively go through all folders.
    if ($current->isDir()) {
      return TRUE;
    }
    else {
      // Only return Twig templates or files with "STARTERKIT" in their name.
      return strpos($current->getFilename(), '.twig') !== FALSE || strpos($current->getFilename(), 'STARTERKIT') !== FALSE;
    }
  });
  $iterator = new \RecursiveIteratorIterator($starterKitFilter);
  $sub_theme_files = array();
  foreach ($iterator as $path => $info) {
    $sub_theme_files[$info->getFilename()] = $path;
  }

  // Add more to the list of files to filter.
  $sub_theme_files['gulpfile.js'] = $sub_theme_path . '/gulpfile.js';
  $sub_theme_files['theme-settings.php'] = $sub_theme_path . '/theme-settings.php';
  $sub_theme_files['homepage.md'] = $sub_theme_path . '/components/style-guide/homepage.md';

  foreach ($sub_theme_files as $filename) {
    // Replace all occurrences of 'STARTERKIT' with the machine name of our sub theme.
    drush_op('zen_file_replace', $filename, array('STARTERKIT' => $machine_name));

    // Rename all files with STARTERKIT in their name.
    if (strpos($filename, 'STARTERKIT') !== FALSE) {
      drush_op('rename', $filename, str_replace('STARTERKIT', $machine_name, $filename));
    }
  }

  // ***************************************************
  // Notify user of the newly created theme.
  // ***************************************************

  drush_print(dt('Starter kit for "!name" created in: !path', array(
    '!name' => $name,
    '!path' => $sub_theme_path,
  )));
  // Allow localize.drupal.org to pick up the string to translate.
  if (FALSE) t('Starter kit for "!name" created in: !path', array('!name' => $name, '!path' => $sub_theme_path));
}



/**
 * Replace strings in a file.
 */
function zen_file_replace($file_path, $strings, $regexs = array()) {
  $file_path = drush_normalize_path($file_path);
  $file_contents = file_get_contents($file_path);

  if ($file_contents !== FALSE) {
    // Find text with strings.
    $find = array_keys($strings);
    $replace = $strings;
    $file_contents = str_replace($find, $replace, $file_contents);

    // Find text with regex.
    foreach ($regexs as $regex) {
      $file_contents = preg_replace($regex['pattern'], $regex['replacement'], $file_contents);
    }

    // Write to file.
    file_put_contents($file_path, $file_contents);
  }
}
