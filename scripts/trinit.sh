#!/bin/bash

# Initialization script for TabRasa8 project

chmod 0777 ./docroot/sites/default/settings.php
cp ./scripts/settings.php ./docroot/sites/default/settings.php
chmod 0644 ./docroot/sites/default/settings.php
mkdir -p ./docroot/modules/custom
touch ./docroot/modules/custom/.gitkeep
mkdir -p ./docroot/themes/custom
touch ./docroot/themes/custom/.gitkeep

# Remove all .git repo directories from vendor folder
find ./vendor -name .git -type d -prune

# and from Docroot
find ./docroot -name .git -type d -prune
