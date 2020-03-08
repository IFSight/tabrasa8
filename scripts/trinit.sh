#!/bin/bash

# Initialization script for TabRasa8 project

# Copy the settings.php file to docroot/sites/default/settings.php
if [ ! -f "docroot/sites/default/settings.php" ]; then
	echo "Copying settings.php..."
	cp ./drupal/settings.php ./docroot/sites/default/settings.php
	chmod 0644 ./docroot/sites/default/settings.php
fi

# Copy the settings.local.php file to docroot/sites/default/settings.php

if [ $TRAVIS ]; then
  if [ ! -f "docroot/sites/default/settings.local.php " ]; then
  	echo "Copying Travis settings.local.php..."
  	chmod 0755 ./docroot/sites/default
	cp ./drupal/settings.travis.php ./docroot/sites/default/settings.local.php
	chmod 0644 ./docroot/sites/default/settings.local.php
  fi
fi

if [ ! -f "docroot/sites/default/settings.local.php" ]; then
	echo "Copying local dev build settings.local.php..."
	chmod 0755 ./docroot/sites/default
	cp ./drupal/settings.local.php ./docroot/sites/default/settings.local.php
	chmod 0644 ./docroot/sites/default/settings.local.php
fi

# Create the custom modules and themes directories
mkdir -p ./docroot/modules/custom
touch ./docroot/modules/custom/.gitkeep
mkdir -p ./docroot/themes/custom
touch ./docroot/themes/custom/.gitkeep

# Remove the examples folder from plupload, which is a security risk
if [ -d "docroot/libraries/plupload" ]; then
	rm -Rf docroot/libraries/plupload/examples
fi

# Set up local files directories for both public and private files and
# add .htaccess files to them to suppress errors on status page
if [ ! -d "docroot/sites/default/files/private" ]; then
	mkdir -p docroot/sites/default/files/private
	cp ./drupal/htaccess.private ./docroot/sites/default/files/private/.htaccess
fi
if [ ! -f "docroot/sites/default/files/.htaccess" ]; then
	cp ./drupal/htaccess.public ./docroot/sites/default/files/.htaccess
fi

# Remove .git directories from composer source install from Docroot
find ./docroot -name .git -type d | xargs rm -rf
