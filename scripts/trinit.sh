#!/bin/bash

# Initialization script for TabRasa8 project

cp ./scripts/settings.php ./docroot/sites/default/settings.php
chmod 0644 ./docroot/sites/default/settings.php
mkdir -p ./docroot/modules/custom
touch ./docroot/modules/custom/.gitkeep
mkdir -p ./docroot/themes/custom
touch ./docroot/themes/custom/.gitkeep

# Dropping the database
#drush sql-drop -y

# Reinitializing the database
#drush si standard --account-name=if-admin -y