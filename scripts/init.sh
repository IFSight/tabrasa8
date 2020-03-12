#!/bin/bash

# Initialization script for TabRasa8 project

# Remove the examples folder from plupload, which is a security risk
if [ -d "docroot/libraries/plupload/examples" ]; then
	rm -Rf docroot/libraries/plupload/examples
fi

# Remove .git directories from composer source install from Docroot
find ./vendor/drupal/coder -name .git -type d | xargs rm -rf
