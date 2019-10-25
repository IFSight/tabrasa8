#!/bin/bash

# Install composer.
cd ~
if [ ! -f "composer.phar" ]; then
  php72 -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php72 composer-setup.php
  php72 -r "unlink('composer-setup.php');"
fi

# Install packages.
php72 composer.phar global require drupal/coder

# Set Drupal coding standards.
~/.config/composer/vendor/bin/phpcs --config-set installed_paths ~/.config/composer/vendor/drupal/coder/coder_sniffer
~/.config/composer/vendor/bin/phpcs --config-set default_standard Drupal

# Show colors.
~/.config/composer/vendor/bin/phpcs --config-set colors 1

# Error and warning severities.
~/.config/composer/vendor/bin/phpcs --config-set error-severity 5
~/.config/composer/vendor/bin/phpcs --config-set warning-severity 5

# Don't ignore warnings on exit. Set to 1 if you don't want warnings to signify
# a failure.
~/.config/composer/vendor/bin/phpcs --config-set ignore_warnings_on_exit 0
