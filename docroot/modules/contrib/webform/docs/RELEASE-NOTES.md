Steps for creating a new release
--------------------------------

  1. Review code
  2. Review accessibility
  3. Run tests
  4. Generate release notes
  5. Tag and create a new release


1. Review code
--------------

    # Remove files that should never be reviewed.
    cd modules/sandbox/webform
    rm *.patch interdiff-*
    
[PHP](https://www.drupal.org/node/1587138)

    # Check Drupal PHP coding standards
    cd /var/www/sites/d8_webform/web
    phpcs --standard=Drupal --extensions=php,module,inc,install,test,profile,theme,css,info modules/sandbox/webform > ~/webform-php-coding-standards.txt
    cat ~/webform-php-coding-standards.txt

    # Check Drupal PHP best practices
    cd /var/www/sites/d8_webform/web
    phpcs --standard=DrupalPractice --extensions=php,module,inc,install,test,profile,theme,js,css,info modules/sandbox/webform > ~/webform-php-best-practice.txt
    cat ~/webform-php-best-practice.txt

[JavaScript](https://www.drupal.org/node/2873849)

    # Install Eslint. (One-time)
    cd /var/www/sites/d8_webform/web/core
    yarn install
    
    # Check Drupal JavaScript (ES5) legacy coding standards.
    cd /var/www/sites/d8_webform/web
    core/node_modules/.bin/eslint --no-eslintrc -c=core/.eslintrc.legacy.json --ext=.js modules/sandbox/webform > ~/webform-javascript-coding-standards.txt
    cat ~/webform-javascript-coding-standards.txt
          

[File Permissions](https://www.drupal.org/comment/reply/2690335#comment-form)

    # Files should be 644 or -rw-r--r--
    find * -type d -print0 | xargs -0 chmod 0755

    # Directories should be 755 or drwxr-xr-x
    find . -type f -print0 | xargs -0 chmod 0644

    
2. Review accessibility
-----------------------

[Pa11y](http://pa11y.org/)

Pa11y is your automated accessibility testing pal.

Notes
- Requires node 8.x+


    drush en -y webform_example_accessibility
    pa11y http://localhost/wf/webform/example_accessibility_basic
    pa11y http://localhost/wf/webform/example_accessibility_advanced
    pa11y http://localhost/wf/webform/example_accessibility_containers
    pa11y http://localhost/wf/webform/example_accessibility_wizard


3. Run tests
------------

[SimpleTest](https://www.drupal.org/node/645286)

    # Run all tests
    cd /var/www/sites/d8_webform
    php core/scripts/run-tests.sh --suppress-deprecations --url http://localhost/wf --module webform --dburl mysql://drupal_d8_webform:drupal.@dm1n@localhost/drupal_d8_webform

    # Run single tests
    cd /var/www/sites/d8_webform
    php core/scripts/run-tests.sh --suppress-deprecations --url http://localhost/wf --verbose --class "Drupal\Tests\webform\Functional\WebformListBuilderTest"

[PHPUnit](https://www.drupal.org/node/2116263)

Notes
- Links to PHP Unit HTML responses are not being printed by PHPStorm

References
- [Issue #2870145: Set printerClass in phpunit.xml.dist](https://www.drupal.org/node/2870145)
- [Lesson 10.2 - Unit testing](https://docs.acquia.com/article/lesson-102-unit-testing)

    # Export database and base URL.
    export SIMPLETEST_DB=mysql://drupal_d8_webform:drupal.@dm1n@localhost/drupal_d8_webform;
    export SIMPLETEST_BASE_URL='http://localhost/wf';

    # Execute all Webform PHPUnit tests.
    cd /var/www/sites/d8_webform/web/core
    php ../../vendor/phpunit/phpunit/phpunit --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" --group webform

    # Execute individual PHPUnit tests.
    cd /var/www/sites/d8_webform/web/core

    # Functional test.
    php ../../vendor/phpunit/phpunit/phpunit --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" ../modules/sandbox/webform/tests/src/Functional/WebformExampleFunctionalTest.php

    # Kernal test.
    php ../../vendor/phpunit/phpunit/phpunit --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" ../modules/sandbox/webform/tests/src/Kernal/Utility/WebformDialogHelperTest.php

    # Unit test.
    php ../../vendor/phpunit/phpunit/phpunit --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" ../modules/sandbox/webform/tests/src/Unit/Utility/WebformYamlTest.php

    php ../../vendor/phpunit/phpunit/phpunit --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" ../modules/sandbox/webform/tests/src/Unit/Access/WebformAccessCheckTest


4. Generate release notes
-------------------------

[Git Release Notes for Drush](https://www.drupal.org/project/grn)

    drush release-notes --nouser 8.x-5.0-VERSION 8.x-5.x


5. Tag and create a new release
-------------------------------

[Tag a release](https://www.drupal.org/node/1066342)

    git tag 8.x-5.0-VERSION
    git push --tags
    git push origin tag 8.x-5.0-VERSION

[Create new release](https://www.drupal.org/node/add/project-release/2640714)
