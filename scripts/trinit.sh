#!/bin/bash

# Initialization script for TabRasa8 project

chmod 0777 ./docroot/sites/default/settings.php
if [ ! -f "docroot/sites/default/settings.php" ]; then
	cp ./drupal/settings.php ./docroot/sites/default/settings.php
fi
chmod 0644 ./docroot/sites/default/settings.php

chmod 0777 ./docroot/sites/default/settings.local.php
if [ ! -f "docroot/sites/default/settings.local.php" ]; then
	cp ./drupal/settings.local.php ./docroot/sites/default/settings.local.php
fi
chmod 0644 ./docroot/sites/default/settings.local.php

mkdir -p ./docroot/modules/custom
touch ./docroot/modules/custom/.gitkeep
mkdir -p ./docroot/themes/custom
touch ./docroot/themes/custom/.gitkeep

# Remove all .git repo directories from vendor folder
if [ -d "docroot/libraries/plupload" ]; then
	rm -Rf docroot/libraries/plupload/examples
fi

if [ ! -d "docroot/sites/default/files/private" ]; then
	mkdir -p docroot/sites/default/files/private
	cp ./drupal/htaccess.private ./docroot/sites/default/files/private/.htaccess
fi
# and from Docroot
find ./docroot -name .git -type d -prune
