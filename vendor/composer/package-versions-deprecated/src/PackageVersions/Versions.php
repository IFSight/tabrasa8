<?php

declare(strict_types=1);

namespace PackageVersions;

use OutOfBoundsException;

/**
 * This class is generated by composer/package-versions-deprecated, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    const ROOT_PACKAGE_NAME = 'drupal/drupal';
    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    const VERSIONS          = array (
  'alchemy/zippy' => '0.4.9@59fbeefb9a249122867ef25e53addfcce31850d7',
  'asm89/stack-cors' => '1.3.0@b9c31def6a83f84b4d4a40d35996d375755f0e08',
  'bower-asset/plupload' => 'v3.1.2@d1aee329d3ed75491a9c8d1e9eecd96464fc8b76',
  'composer/installers' => 'v1.9.0@b93bcf0fa1fccb0b7d176b0967d969691cd74cca',
  'composer/semver' => '1.5.1@c6bea70230ef4dd483e6bbcab6005f682ed3a8de',
  'cweagans/composer-patches' => '1.6.7@2e6f72a2ad8d59cd7e2b729f218bf42adb14f590',
  'd8-contrib-modules/cloudflarephpsdk' => '1.0.0-alpha5@4bd00bacdb3aa56e88cbd871c3db7925eb0a9e66',
  'dflydev/dot-access-configuration' => 'v1.0.3@2e6eb0c8b8830b26bb23defcfc38d4276508fc49',
  'dflydev/dot-access-data' => 'v1.1.0@3fbd874921ab2c041e899d044585a2ab9795df8a',
  'dflydev/placeholder-resolver' => 'v1.0.2@c498d0cae91b1bb36cc7d60906dab8e62bb7c356',
  'dnoegel/php-xdg-base-dir' => 'v0.1.1@8f8a6e48c5ecb0f991c2fdcf5f154a47d85f9ffd',
  'doctrine/annotations' => 'v1.4.0@54cacc9b81758b14e3ce750f205a393d52339e97',
  'doctrine/cache' => 'v1.6.2@eb152c5100571c7a45470ff2a35095ab3f3b900b',
  'doctrine/collections' => 'v1.4.0@1a4fb7e902202c33cce8c55989b945612943c2ba',
  'doctrine/common' => 'v2.7.3@4acb8f89626baafede6ee5475bc5844096eba8a9',
  'doctrine/inflector' => 'v1.2.0@e11d84c6e018beedd929cff5220969a3c6d1d462',
  'doctrine/lexer' => '1.0.2@1febd6c3ef84253d7c815bed85fc622ad207a9f8',
  'drupal/admin_toolbar' => '2.3.0@8.x-2.3',
  'drupal/adminimal_theme' => '1.5.0@8.x-1.5',
  'drupal/allowed_formats' => '1.3.0@8.x-1.3',
  'drupal/better_exposed_filters' => '3.0.0-alpha6@8.x-3.0-alpha6',
  'drupal/blazy' => '2.1.0@8.x-2.1',
  'drupal/block_field' => '1.0.0-rc1@8.x-1.0-rc1',
  'drupal/captcha' => '1.1.0@8.x-1.1',
  'drupal/cloudflare' => '1.0.0-beta2@8.x-1.0-beta2',
  'drupal/components' => '1.1.0@8.x-1.1',
  'drupal/config_filter' => '1.5.0@8.x-1.5',
  'drupal/config_split' => '1.4.0@8.x-1.4',
  'drupal/console' => '1.9.4@04522b687b2149dc1f808599e716421a20d50a5b',
  'drupal/console-core' => '1.9.4@cc6f50c6ac8199140224347c862df75fd2d2f5ed',
  'drupal/console-en' => '1.9.4@30813a832fdb1244e84cbcc012cd103d5e9d673d',
  'drupal/console-extend-plugin' => '0.9.3@ad8e52df34b2e78bdacfffecc9fe8edf41843342',
  'drupal/core' => '8.9.1@e8ee964c562870381876e85d3f5eaaf8c8ecc9ee',
  'drupal/core-composer-scaffold' => '8.9.1@07cdfe2799789fc0c2d0e3e1ba64cb5e2a973ece',
  'drupal/core-recommended' => '8.9.1@41042f9eaa35b027e6b2c42fa03edcb85da54a06',
  'drupal/ctools' => '3.4.0@8.x-3.4',
  'drupal/date_popup' => '1.1.0@8.x-1.1',
  'drupal/easy_breadcrumb' => '1.12.0@8.x-1.12',
  'drupal/elasticsearch_connector' => '6.0.0-alpha2@8.x-6.0-alpha2',
  'drupal/embed' => '1.4.0@8.x-1.4',
  'drupal/entity' => '1.0.0@8.x-1.0',
  'drupal/entity_browser' => '2.5.0@8.x-2.5',
  'drupal/entity_embed' => '1.1.0@8.x-1.1',
  'drupal/entity_reference_revisions' => '1.8.0@8.x-1.8',
  'drupal/environment_indicator' => '3.7.0@8.x-3.7',
  'drupal/field_group' => '3.1.0@8.x-3.1',
  'drupal/geolocation' => '1.11.0@8.x-1.11',
  'drupal/google_analytics' => '2.5.0@8.x-2.5',
  'drupal/inline_entity_form' => '1.0.0-rc6@8.x-1.0-rc6',
  'drupal/libraries' => '3.0.0-alpha1@8.x-3.0-alpha1',
  'drupal/linkit' => '5.0.0-beta11@8.x-5.0-beta11',
  'drupal/mailsystem' => '4.3.0@8.x-4.3',
  'drupal/menu_block' => '1.6.0@8.x-1.6',
  'drupal/metatag' => '1.13.0@8.x-1.13',
  'drupal/paladin' => '1.0@8.x-1.0',
  'drupal/paragraphs' => '1.12.0@8.x-1.12',
  'drupal/pathauto' => '1.8.0@8.x-1.8',
  'drupal/plupload' => '1.0.0-beta2@8.x-1.0-beta2',
  'drupal/purge' => '3.0.0@8.x-3.0',
  'drupal/purge_queuer_url' => '1.0.0@8.x-1.0',
  'drupal/recaptcha' => '2.5.0@8.x-2.5',
  'drupal/redirect' => '1.6.0@8.x-1.6',
  'drupal/redis' => '1.4.0@8.x-1.4',
  'drupal/scheduler' => '1.3.0@8.x-1.3',
  'drupal/search_api' => '1.17.0@8.x-1.17',
  'drupal/simple_sitemap' => '3.7.0@8.x-3.7',
  'drupal/slick' => '2.2.0@8.x-2.2',
  'drupal/slick_browser' => '2.1.0@8.x-2.1',
  'drupal/smtp' => '1.0.0-rc3@8.x-1.0-rc3',
  'drupal/social_media_links' => '2.6.0@8.x-2.6',
  'drupal/token' => '1.7.0@8.x-1.7',
  'drupal/twig_tweak' => '2.6.0@8.x-2.6',
  'drupal/ultimate_cron' => '2.0.0-alpha4@8.x-2.0-alpha4',
  'drupal/varnish_purge' => '1.12.0@8.x-1.12',
  'drupal/viewsreference' => '1.4.0@8.x-1.4',
  'drupal/webform' => '5.19.0@8.x-5.19',
  'easyrdf/easyrdf' => '0.9.1@acd09dfe0555fbcfa254291e433c45fdd4652566',
  'egulias/email-validator' => '2.1.17@ade6887fd9bd74177769645ab5c474824f8a418a',
  'elasticsearch/elasticsearch' => 'v6.7.2@9ba89f905ebf699e72dacffa410331c7fecc8255',
  'guzzlehttp/guzzle' => '6.5.4@a4a1b6930528a8f7ee03518e6442ec7a44155d9d',
  'guzzlehttp/promises' => 'v1.3.1@a59da6cf61d80060647ff4d3eb2c03a2bc694646',
  'guzzlehttp/psr7' => '1.6.1@239400de7a173fe9901b9ac7c06497751f00727a',
  'guzzlehttp/ringphp' => '1.1.1@5e2a174052995663dd68e6b5ad838afd47dd615b',
  'guzzlehttp/streams' => '3.0.0@47aaa48e27dae43d39fc1cea0ccf0d84ac1a2ba5',
  'ifsight/fulcrum_whitelist' => '1.1.0@1.1.0',
  'ifsight/if_admin' => '0.1@v0.1',
  'ifsight/if_helper' => '0.1@v0.1',
  'ifsight/if_varnish_purge_tags' => '0.2@0.2',
  'kenwheeler/slick-carousel' => '1.8.1@v1.8.1',
  'laminas/laminas-diactoros' => '1.8.7p2@6991c1af7c8d2c8efee81b22ba97024781824aaa',
  'laminas/laminas-escaper' => '2.6.1@25f2a053eadfa92ddacb609dcbbc39362610da70',
  'laminas/laminas-feed' => '2.12.2@8a193ac96ebcb3e16b6ee754ac2a889eefacb654',
  'laminas/laminas-stdlib' => '3.2.1@2b18347625a2f06a1a485acfbc870f699dbe51c6',
  'laminas/laminas-zendframework-bridge' => '1.0.4@fcd87520e4943d968557803919523772475e8ea3',
  'makinacorpus/php-lucene' => '1.0.2@31ecc79dd750a1f82c0aacacd79117ee94d2bfb0',
  'masterminds/html5' => '2.3.0@2c37c6c520b995b761674de3be8455a381679067',
  'nikic/php-parser' => 'v4.6.0@c346bbfafe2ff60680258b631afb730d186ed864',
  'nodespark/des-connector' => '6.x-dev@abdbfc805ce296354988b5869888dc44a52d5da7',
  'oomphinc/composer-installers-extender' => 'v1.1.2@ca1c4b16b0905c81d1e77e608f36a2eff1a56f56',
  'paragonie/random_compat' => 'v9.99.99@84b4dfb120c6f9b4ff7b3685f9b8f1aa365a0c95',
  'pear/archive_tar' => '1.4.9@c5b00053770e1d72128252c62c2c1a12c26639f0',
  'pear/console_getopt' => 'v1.4.3@a41f8d3e668987609178c7c4a9fe48fecac53fa0',
  'pear/pear-core-minimal' => 'v1.10.10@625a3c429d9b2c1546438679074cac1b089116a7',
  'pear/pear_exception' => 'v1.0.1@dbb42a5a0e45f3adcf99babfb2a1ba77b8ac36a7',
  'phpmailer/phpmailer' => 'v6.1.6@c2796cb1cb99d7717290b48c4e6f32cb6c60b7b3',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.3@0f73288fd15629204f9d42b7055f72dacbe811fc',
  'psy/psysh' => 'v0.10.4@a8aec1b2981ab66882a01cce36a49b6317dc3560',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'react/promise' => 'v2.8.0@f3cff96a19736714524ca0dd1d4130de73dbbbc4',
  'ruflin/elastica' => '6.1.1@333dff62346adf835c0a0fd198e8c9f9ec8da65a',
  'stack/builder' => 'v1.0.5@fb3d136d04c6be41120ebf8c0cc71fe9507d750a',
  'stecman/symfony-console-completion' => '0.11.0@a9502dab59405e275a9f264536c4e1cb61fc3518',
  'symfony-cmf/routing' => '1.4.1@fb1e7f85ff8c6866238b7e73a490a0a0243ae8ac',
  'symfony/class-loader' => 'v3.4.41@e4636a4f23f157278a19e5db160c63de0da297d8',
  'symfony/config' => 'v3.4.42@cd61db31cbd19cbe4ba9f6968f13c9076e1372ab',
  'symfony/console' => 'v3.4.41@bfe29ead7e7b1cc9ce74c6a40d06ad1f96fced13',
  'symfony/css-selector' => 'v3.4.42@9ccf6e78077a3fc1596e6c7b5958008965a11518',
  'symfony/debug' => 'v3.4.41@518c6a00d0872da30bd06aee3ea59a0a5cf54d6d',
  'symfony/dependency-injection' => 'v3.4.41@e39380b7104b0ec538a075ae919f00c7e5267bac',
  'symfony/dom-crawler' => 'v3.4.42@c3086a58a66b2a519c0b7ac80539a3727609ea9c',
  'symfony/event-dispatcher' => 'v3.4.41@14d978f8e8555f2de719c00eb65376be7d2e9081',
  'symfony/filesystem' => 'v3.4.42@0f625d0cb1e59c8c4ba61abb170125175218ff10',
  'symfony/finder' => 'v3.4.42@5ec813ccafa8164ef21757e8c725d3a57da59200',
  'symfony/http-foundation' => 'v3.4.41@fbd216d2304b1a3fe38d6392b04729c8dd356359',
  'symfony/http-kernel' => 'v3.4.41@e4e4ed6c008c983645b4eee6b67d8f258cde54df',
  'symfony/polyfill-ctype' => 'v1.17.0@e94c8b1bbe2bc77507a1056cdb06451c75b427f9',
  'symfony/polyfill-iconv' => 'v1.17.0@c4de7601eefbf25f9d47190abe07f79fe0a27424',
  'symfony/polyfill-intl-idn' => 'v1.17.0@3bff59ea7047e925be6b7f2059d60af31bb46d6a',
  'symfony/polyfill-mbstring' => 'v1.17.0@fa79b11539418b02fc5e1897267673ba2c19419c',
  'symfony/polyfill-php56' => 'v1.17.0@e3c8c138280cdfe4b81488441555583aa1984e23',
  'symfony/polyfill-php70' => 'v1.17.0@82225c2d7d23d7e70515496d249c0152679b468e',
  'symfony/polyfill-php72' => 'v1.17.0@f048e612a3905f34931127360bdd2def19a5e582',
  'symfony/polyfill-php80' => 'v1.17.1@4a5b6bba3259902e386eb80dd1956181ee90b5b2',
  'symfony/polyfill-util' => 'v1.17.0@4afb4110fc037752cf0ce9869f9ab8162c4e20d7',
  'symfony/process' => 'v3.4.41@8a895f0c92a7c4b10db95139bcff71bdf66d4d21',
  'symfony/psr-http-message-bridge' => 'v1.1.2@a33352af16f78a5ff4f9d90811536abf210df12b',
  'symfony/routing' => 'v3.4.41@e0d43b6f9417ad59ecaa8e2f799b79eef417387f',
  'symfony/serializer' => 'v3.4.41@0db90db012b1b0a04fbb2d64ae9160871cad9d4f',
  'symfony/translation' => 'v3.4.41@b0cd62ef0ff7ec31b67d78d7fc818e2bda4e844f',
  'symfony/validator' => 'v3.4.41@5fb88120a11a75e17b602103a893dd8b27804529',
  'symfony/var-dumper' => 'v4.4.10@56b3aa5eab0ac6720dcd559fd1d590ce301594ac',
  'symfony/yaml' => 'v3.4.41@7233ac2bfdde24d672f5305f2b3f6b5d741ef8eb',
  'twig/twig' => 'v1.42.5@87b2ea9d8f6fd014d0621ca089bb1b3769ea3f8e',
  'typo3/phar-stream-wrapper' => 'v3.1.4@e0c1b495cfac064f4f5c4bcb6bf67bb7f345ed04',
  'webflo/drupal-finder' => '1.2.0@123e248e14ee8dd3fbe89fb5a733a6cf91f5820e',
  'webmozart/assert' => '1.9.0@9dc4f203e36f2b486149058bade43c851dd97451',
  'webmozart/path-util' => '2.3.0@d939f7edc24c9a1bb9c0dee5cb05d8e859490725',
  'wikimedia/composer-merge-plugin' => 'v1.4.1@81c6ac72a24a67383419c7eb9aa2b3437f2ab100',
  'behat/behat' => 'v3.7.0@08052f739619a9e9f62f457a67302f0715e6dd13',
  'behat/gherkin' => 'v4.6.2@51ac4500c4dc30cbaaabcd2f25694299df666a31',
  'behat/mink' => 'v1.8.1@07c6a9fe3fa98c2de074b25d9ed26c22904e3887',
  'behat/mink-browserkit-driver' => 'v1.3.4@e3b90840022ebcd544c7b394a3c9597ae242cbee',
  'behat/mink-extension' => '2.3.1@80f7849ba53867181b7e412df9210e12fba50177',
  'behat/mink-goutte-driver' => 'v1.2.1@8b9ad6d2d95bc70b840d15323365f52fcdaea6ca',
  'behat/mink-selenium2-driver' => 'dev-master@312a967dd527f28980cce40850339cd5316da092',
  'behat/transliterator' => 'v1.3.0@3c4ec1d77c3d05caa1f0bf8fb3aae4845005c7fc',
  'composer/ca-bundle' => '1.2.7@95c63ab2117a72f48f5a55da9740a3273d45b7fd',
  'composer/composer' => '1.10.8@56e0e094478f30935e9128552188355fa9712291',
  'composer/package-versions-deprecated' => '1.8.1@b9805885293f3957ee0dd42616ac6915c4ac9a4b',
  'composer/spdx-licenses' => '1.5.3@0c3e51e1880ca149682332770e25977c70cf9dae',
  'composer/xdebug-handler' => '1.4.2@fa2aaf99e2087f013a14f7432c1cd2dd7d8f1f51',
  'doctrine/instantiator' => '1.3.1@f350df0268e904597e3bd9c4685c53e0e333feea',
  'drupal/coder' => '8.3.9@d51e0b8c6561e21c0545d04b5410a7bed7ee7c6b',
  'drupal/core-dev' => '8.9.1@36370b3f42911c09ffb35f08fc72853d20e6efd7',
  'drupal/devel' => '2.1.0@8.x-2.1',
  'drupal/drupal-driver' => 'v2.1.0@ebc7fc3cbaa0b2eb7bcb100d1302dadb67a9de29',
  'drupal/drupal-extension' => 'v4.1.0@f611a70aaa2d1ef6b3fdae9c35dc573508c7d648',
  'fabpot/goutte' => 'v3.2.3@3f0eaf0a40181359470651f1565b3e07e3dd31b8',
  'genesis/behat-fail-aid' => '2.5.3@d2003483676a6a0166867b1a0c03d1afbc850300',
  'instaclick/php-webdriver' => '1.4.7@b5f330e900e9b3edfc18024a5ec8c07136075712',
  'jcalderonzumba/gastonjs' => 'v1.2.0@575a9c18d8b87990c37252e8d9707b29f0a313f3',
  'jcalderonzumba/mink-phantomjs-driver' => 'v0.3.3@008f43670e94acd39273d15add1e7348eb23848d',
  'jean85/pretty-package-versions' => '1.3.0@e3517fb11b67e798239354fe8213927d012ad8f9',
  'justinrainbow/json-schema' => '5.2.10@2ba9c8c862ecd5510ed16c6340aa9f6eadb4f31b',
  'mglaman/drupal-check' => '1.1.2@eaee2c8b03bf3bb8aff190b9000d12e0c3bea87b',
  'mglaman/phpstan-drupal' => '0.12.4@4a74b797251562081715bb086a49d460c61a8783',
  'mikey179/vfsstream' => 'v1.6.8@231c73783ebb7dd9ec77916c10037eff5a2b6efe',
  'myclabs/deep-copy' => '1.10.1@969b211f9a51aa1f6c01d1d2aef56d3bd91598e5',
  'nette/finder' => 'v2.5.2@4ad2c298eb8c687dd0e74ae84206a4186eeaed50',
  'nette/neon' => 'v3.1.2@3c3dcbc6bf6c80dc97b1fc4ba9a22ae67930fc0e',
  'nette/utils' => 'v3.1.2@488f58378bba71767e7831c83f9e0fa808bf83b9',
  'phar-io/manifest' => '1.0.3@7761fcacf03b4d4f16e7ccb606d4879ca431fcf4',
  'phar-io/version' => '2.0.1@45a2ec53a73c70ce41d55cedef9063630abaf1b6',
  'phpdocumentor/reflection-common' => '2.2.0@1d01c49d4ed62f25aa84a747ad35d5a16924662b',
  'phpdocumentor/reflection-docblock' => '5.1.0@cd72d394ca794d3466a3b2fc09d5a6c1dc86b47e',
  'phpdocumentor/type-resolver' => '1.3.0@e878a14a65245fbe78f8080eba03b47c3b705651',
  'phpspec/prophecy' => '1.11.0@8ff0384cd5d87e038297e79d85c99e4b2dcf0e61',
  'phpstan/phpstan' => '0.12.32@d03863f504c8432b3de4d1881f73f6acb8c0e67c',
  'phpstan/phpstan-deprecation-rules' => '0.12.4@9b4b8851fb5d59fd0eed00fbe9c22cfc328e0187',
  'phpunit/php-code-coverage' => '6.1.4@807e6013b00af69b6c5d9ceb4282d0393dbb9d8d',
  'phpunit/php-file-iterator' => '2.0.2@050bedf145a257b1ff02746c31894800e5122946',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '2.1.2@1038454804406b0b5f5f520358e78c1c2f71501e',
  'phpunit/php-token-stream' => '3.1.1@995192df77f63a59e47f025390d2d1fdf8f425ff',
  'phpunit/phpunit' => '7.5.20@9467db479d1b0487c99733bb1e7944d32deded2c',
  'roave/security-advisories' => 'dev-master@239e50ae0e1906d32b32ed58bc7d15c81bfc06f0',
  'sebastian/code-unit-reverse-lookup' => '1.0.1@4419fcdb5eabb9caa61a27c7a1db532a6b55dd18',
  'sebastian/comparator' => '3.0.2@5de4fc177adf9bce8df98d8d141a7559d7ccf6da',
  'sebastian/diff' => '3.0.2@720fcc7e9b5cf384ea68d9d930d480907a0c1a29',
  'sebastian/environment' => '4.2.3@464c90d7bdf5ad4e8a6aea15c091fec0603d4368',
  'sebastian/exporter' => '3.1.2@68609e1261d215ea5b21b7987539cbfbe156ec3e',
  'sebastian/global-state' => '2.0.0@e8ba02eed7bbbb9e59e43dedd3dddeff4a56b0c4',
  'sebastian/object-enumerator' => '3.0.3@7cfd9e65d11ffb5af41198476395774d4c8a84c5',
  'sebastian/object-reflector' => '1.1.1@773f97c67f28de00d397be301821b06708fca0be',
  'sebastian/recursion-context' => '3.0.0@5b0cd723502bac3b006cbf3dbf7a1e3fcefe4fa8',
  'sebastian/resource-operations' => '2.0.1@4d7a795d35b889bf80a0cc04e08d77cedfa917a9',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'seld/jsonlint' => '1.8.0@ff2aa5420bfbc296cf6a0bc785fa5b35736de7c1',
  'seld/phar-utils' => '1.1.1@8674b1d84ffb47cc59a101f5d5a3b61e87d23796',
  'squizlabs/php_codesniffer' => '3.5.5@73e2e7f57d958e7228fce50dc0c61f58f017f9f6',
  'symfony/browser-kit' => 'v3.4.42@1467e0c7cf0c5c2c08dc9b45ca0300ac3cd3a824',
  'symfony/lock' => 'v3.4.42@c5374725a61b25cd24ec1615b0707a2aa0cefe5a',
  'symfony/phpunit-bridge' => 'v3.4.42@ac355e1e9ebde4cf6ef5187f5cf4b43001f9a29f',
  'theseer/tokenizer' => '1.1.3@11336f6f84e16a720dae9d8e6ed5019efa85a0f9',
  'vanare/behat-cucumber-json-formatter' => 'v1.1.3@b28ae403404be4c74c740a3948d9338fc51443a3',
  'drupal/drupal' => 'dev-tabrasa-fix@3f3567b440b87001456f5ebac453aac7d77ed12a',
);

    private function __construct()
    {
    }

    /**
     * @throws OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     * @psalm-pure
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}
