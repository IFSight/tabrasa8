<?php

declare(strict_types=1);

namespace PackageVersions;

use Composer\InstalledVersions;
use OutOfBoundsException;

class_exists(InstalledVersions::class);

/**
 * This class is generated by composer/package-versions-deprecated, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 *
 * @deprecated in favor of the Composer\InstalledVersions class provided by Composer 2. Require composer-runtime-api:^2 to ensure it is present.
 */
final class Versions
{
    /**
     * @deprecated please use {@see self::rootPackageName()} instead.
     *             This constant will be removed in version 2.0.0.
     */
    const ROOT_PACKAGE_NAME = 'drupal/drupal';

    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    const VERSIONS          = array (
  'asm89/stack-cors' => '1.3.0@b9c31def6a83f84b4d4a40d35996d375755f0e08',
  'bower-asset/plupload' => 'v3.1.2@d1aee329d3ed75491a9c8d1e9eecd96464fc8b76',
  'composer/installers' => 'v1.10.0@1a0357fccad9d1cc1ea0c9a05b8847fbccccb78d',
  'composer/semver' => '1.5.1@c6bea70230ef4dd483e6bbcab6005f682ed3a8de',
  'cweagans/composer-patches' => '1.7.0@ae02121445ad75f4eaff800cc532b5e6233e2ddf',
  'd8-contrib-modules/cloudflarephpsdk' => '1.0.0-alpha5@4bd00bacdb3aa56e88cbd871c3db7925eb0a9e66',
  'doctrine/annotations' => 'v1.4.0@54cacc9b81758b14e3ce750f205a393d52339e97',
  'doctrine/cache' => 'v1.6.2@eb152c5100571c7a45470ff2a35095ab3f3b900b',
  'doctrine/collections' => 'v1.4.0@1a4fb7e902202c33cce8c55989b945612943c2ba',
  'doctrine/common' => 'v2.7.3@4acb8f89626baafede6ee5475bc5844096eba8a9',
  'doctrine/inflector' => 'v1.2.0@e11d84c6e018beedd929cff5220969a3c6d1d462',
  'doctrine/lexer' => '1.0.2@1febd6c3ef84253d7c815bed85fc622ad207a9f8',
  'drupal/admin_toolbar' => '2.4.0@8.x-2.4',
  'drupal/adminimal_theme' => '1.6.0@8.x-1.6',
  'drupal/allowed_formats' => '1.3.0@8.x-1.3',
  'drupal/better_exposed_filters' => '3.0.0-alpha6@8.x-3.0-alpha6',
  'drupal/blazy' => '2.1.0@8.x-2.1',
  'drupal/block_field' => '1.0.0-rc1@8.x-1.0-rc1',
  'drupal/captcha' => '1.1.0@8.x-1.1',
  'drupal/cloudflare' => '1.0.0-beta2@8.x-1.0-beta2',
  'drupal/components' => '1.1.0@8.x-1.1',
  'drupal/config_filter' => '1.8.0@8.x-1.8',
  'drupal/config_ignore' => '2.3.0@8.x-2.3',
  'drupal/config_split' => '1.7.0@8.x-1.7',
  'drupal/core' => '8.9.13@a53db77b55a035453d7229e0c3069f8591cb4cb6',
  'drupal/core-composer-scaffold' => '8.9.13@c902d07cb49ef73777e2b33a39e54c2861a8c81d',
  'drupal/core-recommended' => '8.9.13@7a940fd5b64d2b22366680e2a60d96bf2c10089d',
  'drupal/ctools' => '3.4.0@8.x-3.4',
  'drupal/date_popup' => '1.1.0@8.x-1.1',
  'drupal/easy_breadcrumb' => '1.15.0@8.x-1.15',
  'drupal/elasticsearch_connector' => '6.0.0-alpha3@8.x-6.0-alpha3',
  'drupal/embed' => '1.4.0@8.x-1.4',
  'drupal/entity' => '1.2.0@8.x-1.2',
  'drupal/entity_browser' => '2.5.0@8.x-2.5',
  'drupal/entity_embed' => '1.1.0@8.x-1.1',
  'drupal/entity_reference_revisions' => '1.8.0@8.x-1.8',
  'drupal/environment_indicator' => '3.7.0@8.x-3.7',
  'drupal/field_group' => '3.1.0@8.x-3.1',
  'drupal/geolocation' => '1.11.0@8.x-1.11',
  'drupal/google_analytics' => '2.5.0@8.x-2.5',
  'drupal/inline_entity_form' => '1.0.0-rc8@8.x-1.0-rc8',
  'drupal/libraries' => '3.0.0-beta1@8.x-3.0-beta1',
  'drupal/linkit' => '5.0.0-beta12@8.x-5.0-beta12',
  'drupal/mailsystem' => '4.3.0@8.x-4.3',
  'drupal/menu_block' => '1.6.0@8.x-1.6',
  'drupal/metatag' => '1.16.0@8.x-1.16',
  'drupal/paragraphs' => '1.12.0@8.x-1.12',
  'drupal/pathauto' => '1.8.0@8.x-1.8',
  'drupal/plupload' => '1.0.0-beta2@8.x-1.0-beta2',
  'drupal/purge' => '3.0.0@8.x-3.0',
  'drupal/purge_queuer_url' => '1.0.0@8.x-1.0',
  'drupal/recaptcha' => '2.5.0@8.x-2.5',
  'drupal/redirect' => '1.6.0@8.x-1.6',
  'drupal/redis' => '1.5.0@8.x-1.5',
  'drupal/role_delegation' => '1.1.0@8.x-1.1',
  'drupal/scheduler' => '1.3.0@8.x-1.3',
  'drupal/search_api' => '1.19.0@8.x-1.19',
  'drupal/simple_sitemap' => '3.9.0@8.x-3.9',
  'drupal/slick' => '2.2.0@8.x-2.2',
  'drupal/slick_browser' => '2.1.0@8.x-2.1',
  'drupal/smtp' => '1.0.0@8.x-1.0',
  'drupal/social_media_links' => '2.8.0@8.x-2.8',
  'drupal/token' => '1.9.0@8.x-1.9',
  'drupal/twig_tweak' => '2.9.0@8.x-2.9',
  'drupal/ultimate_cron' => '2.0.0-alpha5@8.x-2.0-alpha5',
  'drupal/varnish_purge' => '1.12.0@8.x-1.12',
  'drupal/viewsreference' => '1.7.0@8.x-1.7',
  'drupal/webform' => '6.0.2@6.0.2',
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
  'ifsight/paladin' => '1.1@1.1',
  'kenwheeler/slick-carousel' => '1.8.1@v1.8.1',
  'laminas/laminas-diactoros' => '1.8.7p2@6991c1af7c8d2c8efee81b22ba97024781824aaa',
  'laminas/laminas-escaper' => '2.6.1@25f2a053eadfa92ddacb609dcbbc39362610da70',
  'laminas/laminas-feed' => '2.12.2@8a193ac96ebcb3e16b6ee754ac2a889eefacb654',
  'laminas/laminas-stdlib' => '3.2.1@2b18347625a2f06a1a485acfbc870f699dbe51c6',
  'laminas/laminas-zendframework-bridge' => '1.0.4@fcd87520e4943d968557803919523772475e8ea3',
  'makinacorpus/php-lucene' => '1.0.2@31ecc79dd750a1f82c0aacacd79117ee94d2bfb0',
  'masterminds/html5' => '2.3.0@2c37c6c520b995b761674de3be8455a381679067',
  'nodespark/des-connector' => '6.x-dev@ca8373258620ce3d0b728e6fe75efecfdfbc73ea',
  'oomphinc/composer-installers-extender' => 'v1.1.2@ca1c4b16b0905c81d1e77e608f36a2eff1a56f56',
  'paragonie/random_compat' => 'v9.99.99@84b4dfb120c6f9b4ff7b3685f9b8f1aa365a0c95',
  'pear/archive_tar' => '1.4.12@19bb8e95490d3e3ad92fcac95500ca80bdcc7495',
  'pear/console_getopt' => 'v1.4.3@a41f8d3e668987609178c7c4a9fe48fecac53fa0',
  'pear/pear-core-minimal' => 'v1.10.10@625a3c429d9b2c1546438679074cac1b089116a7',
  'pear/pear_exception' => 'v1.0.1@dbb42a5a0e45f3adcf99babfb2a1ba77b8ac36a7',
  'phpmailer/phpmailer' => 'v6.2.0@e38888a75c070304ca5514197d4847a59a5c853f',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.3@0f73288fd15629204f9d42b7055f72dacbe811fc',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'react/promise' => 'v2.8.0@f3cff96a19736714524ca0dd1d4130de73dbbbc4',
  'ruflin/elastica' => '6.1.1@333dff62346adf835c0a0fd198e8c9f9ec8da65a',
  'squizlabs/php_codesniffer' => '3.5.8@9d583721a7157ee997f235f327de038e7ea6dac4',
  'stack/builder' => 'v1.0.5@fb3d136d04c6be41120ebf8c0cc71fe9507d750a',
  'symfony-cmf/routing' => '1.4.1@fb1e7f85ff8c6866238b7e73a490a0a0243ae8ac',
  'symfony/class-loader' => 'v3.4.41@e4636a4f23f157278a19e5db160c63de0da297d8',
  'symfony/console' => 'v3.4.41@bfe29ead7e7b1cc9ce74c6a40d06ad1f96fced13',
  'symfony/debug' => 'v3.4.41@518c6a00d0872da30bd06aee3ea59a0a5cf54d6d',
  'symfony/dependency-injection' => 'v3.4.41@e39380b7104b0ec538a075ae919f00c7e5267bac',
  'symfony/event-dispatcher' => 'v3.4.41@14d978f8e8555f2de719c00eb65376be7d2e9081',
  'symfony/http-foundation' => 'v3.4.41@fbd216d2304b1a3fe38d6392b04729c8dd356359',
  'symfony/http-kernel' => 'v3.4.44@27dcaa8c6b18c75df9f37badeb4d3564ffaa1326',
  'symfony/polyfill-ctype' => 'v1.17.0@e94c8b1bbe2bc77507a1056cdb06451c75b427f9',
  'symfony/polyfill-iconv' => 'v1.17.0@c4de7601eefbf25f9d47190abe07f79fe0a27424',
  'symfony/polyfill-intl-idn' => 'v1.17.0@3bff59ea7047e925be6b7f2059d60af31bb46d6a',
  'symfony/polyfill-mbstring' => 'v1.17.0@fa79b11539418b02fc5e1897267673ba2c19419c',
  'symfony/polyfill-php56' => 'v1.17.0@e3c8c138280cdfe4b81488441555583aa1984e23',
  'symfony/polyfill-php70' => 'v1.17.0@82225c2d7d23d7e70515496d249c0152679b468e',
  'symfony/polyfill-php72' => 'v1.17.0@f048e612a3905f34931127360bdd2def19a5e582',
  'symfony/polyfill-util' => 'v1.17.0@4afb4110fc037752cf0ce9869f9ab8162c4e20d7',
  'symfony/process' => 'v3.4.41@8a895f0c92a7c4b10db95139bcff71bdf66d4d21',
  'symfony/psr-http-message-bridge' => 'v1.1.2@a33352af16f78a5ff4f9d90811536abf210df12b',
  'symfony/routing' => 'v3.4.41@e0d43b6f9417ad59ecaa8e2f799b79eef417387f',
  'symfony/serializer' => 'v3.4.41@0db90db012b1b0a04fbb2d64ae9160871cad9d4f',
  'symfony/translation' => 'v3.4.41@b0cd62ef0ff7ec31b67d78d7fc818e2bda4e844f',
  'symfony/validator' => 'v3.4.41@5fb88120a11a75e17b602103a893dd8b27804529',
  'symfony/yaml' => 'v3.4.41@7233ac2bfdde24d672f5305f2b3f6b5d741ef8eb',
  'twig/twig' => 'v1.42.5@87b2ea9d8f6fd014d0621ca089bb1b3769ea3f8e',
  'typo3/phar-stream-wrapper' => 'v3.1.4@e0c1b495cfac064f4f5c4bcb6bf67bb7f345ed04',
  'wikimedia/composer-merge-plugin' => 'v1.4.1@81c6ac72a24a67383419c7eb9aa2b3437f2ab100',
  'behat/behat' => 'v3.7.0@08052f739619a9e9f62f457a67302f0715e6dd13',
  'behat/gherkin' => 'v4.7.1@987bcdc3d29ba433e6bd4b1db4ae59737ba3dacd',
  'behat/mink' => 'v1.8.1@07c6a9fe3fa98c2de074b25d9ed26c22904e3887',
  'behat/mink-browserkit-driver' => 'v1.3.4@e3b90840022ebcd544c7b394a3c9597ae242cbee',
  'behat/mink-extension' => '2.3.1@80f7849ba53867181b7e412df9210e12fba50177',
  'behat/mink-goutte-driver' => 'v1.2.1@8b9ad6d2d95bc70b840d15323365f52fcdaea6ca',
  'behat/mink-selenium2-driver' => 'dev-master@4a4d1afdb107b9b41e4375a68635825d4c6ab5e9',
  'behat/transliterator' => 'v1.3.0@3c4ec1d77c3d05caa1f0bf8fb3aae4845005c7fc',
  'chi-teck/drupal-code-generator' => '1.33.1@5f814e980b6f9cf1ca8c74cc9385c3d81090d388',
  'composer/ca-bundle' => '1.2.9@78a0e288fdcebf92aa2318a8d3656168da6ac1a5',
  'composer/composer' => '1.10.20@e55d297525f0ecc805c813a0f63a40114fd670f6',
  'composer/package-versions-deprecated' => '1.11.99.1@7413f0b55a051e89485c5cb9f765fe24bb02a7b6',
  'composer/spdx-licenses' => '1.5.5@de30328a7af8680efdc03e396aad24befd513200',
  'composer/xdebug-handler' => '1.4.5@f28d44c286812c714741478d968104c5e604a1d4',
  'consolidation/annotated-command' => '2.12.1@0ee361762df2274f360c085e3239784a53f850b5',
  'consolidation/config' => '1.2.1@cac1279bae7efb5c7fb2ca4c3ba4b8eb741a96c1',
  'consolidation/filter-via-dot-access-data' => '1.0.0@a53e96c6b9f7f042f5e085bf911f3493cea823c6',
  'consolidation/log' => '1.1.1@b2e887325ee90abc96b0a8b7b474cd9e7c896e3a',
  'consolidation/output-formatters' => '3.5.1@0d38f13051ef05c223a2bb8e962d668e24785196',
  'consolidation/robo' => '1.4.13@fd28dcca1b935950ece26e63541fbdeeb09f7343',
  'consolidation/self-update' => '1.2.0@dba6b2c0708f20fa3ba8008a2353b637578849b4',
  'consolidation/site-alias' => '3.0.1@fd40a03f80f8fd4684b10bef8c8c4ec5a9a9bf26',
  'consolidation/site-process' => '2.1.0@f3211fa4c60671c6f068184221f06f932556e443',
  'container-interop/container-interop' => '1.2.0@79cbf1341c22ec75643d841642dd5d6acd83bdb8',
  'dflydev/dot-access-data' => 'v1.1.0@3fbd874921ab2c041e899d044585a2ab9795df8a',
  'dnoegel/php-xdg-base-dir' => 'v0.1.1@8f8a6e48c5ecb0f991c2fdcf5f154a47d85f9ffd',
  'doctrine/instantiator' => '1.4.0@d56bf6102915de5702778fe20f2de3b2fe570b5b',
  'drupal/coder' => '8.3.12@719ddb16aec2e5da4ce274bf3bf8450caef564d4',
  'drupal/core-dev' => '8.9.13@36370b3f42911c09ffb35f08fc72853d20e6efd7',
  'drupal/devel' => '2.1.0@8.x-2.1',
  'drupal/drupal-driver' => 'v2.1.0@ebc7fc3cbaa0b2eb7bcb100d1302dadb67a9de29',
  'drupal/drupal-extension' => 'v4.1.0@f611a70aaa2d1ef6b3fdae9c35dc573508c7d648',
  'drupal/paladin' => '1.0@8.x-1.0',
  'drush/drush' => '10.3.6@fc985a95c6010e04891a2dbcf3f39984b8c9ef0a',
  'fabpot/goutte' => 'v3.2.3@3f0eaf0a40181359470651f1565b3e07e3dd31b8',
  'genesis/behat-fail-aid' => '2.5.3@d2003483676a6a0166867b1a0c03d1afbc850300',
  'grasmash/expander' => '1.0.0@95d6037344a4be1dd5f8e0b0b2571a28c397578f',
  'grasmash/yaml-expander' => '1.4.0@3f0f6001ae707a24f4d9733958d77d92bf9693b1',
  'instaclick/php-webdriver' => '1.4.7@b5f330e900e9b3edfc18024a5ec8c07136075712',
  'jcalderonzumba/gastonjs' => 'v1.2.0@575a9c18d8b87990c37252e8d9707b29f0a313f3',
  'jcalderonzumba/mink-phantomjs-driver' => 'v0.3.3@008f43670e94acd39273d15add1e7348eb23848d',
  'jean85/pretty-package-versions' => '1.5.1@a917488320c20057da87f67d0d40543dd9427f7a',
  'justinrainbow/json-schema' => '5.2.10@2ba9c8c862ecd5510ed16c6340aa9f6eadb4f31b',
  'league/container' => '2.4.1@43f35abd03a12977a60ffd7095efd6a7808488c0',
  'mglaman/drupal-check' => '1.1.6@a539f52631ed3099b84ff70f620932d428aa49a5',
  'mglaman/phpstan-drupal' => '0.12.7@9c53a7be3f1b92766046469e2bf2e5f1cbfa0276',
  'mikey179/vfsstream' => 'v1.6.8@231c73783ebb7dd9ec77916c10037eff5a2b6efe',
  'myclabs/deep-copy' => '1.10.2@776f831124e9c62e1a2c601ecc52e776d8bb7220',
  'nette/finder' => 'v2.5.2@4ad2c298eb8c687dd0e74ae84206a4186eeaed50',
  'nette/neon' => 'v3.2.1@a5b3a60833d2ef55283a82d0c30b45d136b29e75',
  'nette/utils' => 'v3.2.1@2bc2f58079c920c2ecbb6935645abf6f2f5f94ba',
  'nikic/php-parser' => 'v4.10.4@c6d052fc58cb876152f89f532b95a8d7907e7f0e',
  'phar-io/manifest' => '1.0.3@7761fcacf03b4d4f16e7ccb606d4879ca431fcf4',
  'phar-io/version' => '2.0.1@45a2ec53a73c70ce41d55cedef9063630abaf1b6',
  'phpdocumentor/reflection-common' => '2.2.0@1d01c49d4ed62f25aa84a747ad35d5a16924662b',
  'phpdocumentor/reflection-docblock' => '5.2.2@069a785b2141f5bcf49f3e353548dc1cce6df556',
  'phpdocumentor/type-resolver' => '1.4.0@6a467b8989322d92aa1c8bf2bebcc6e5c2ba55c0',
  'phpspec/prophecy' => '1.12.2@245710e971a030f42e08f4912863805570f23d39',
  'phpstan/phpstan' => '0.12.71@d508fa3b0ecc5fc91ac70c6c7ac2862f968ba2b5',
  'phpstan/phpstan-deprecation-rules' => '0.12.6@46dbd43c2db973d2876d6653e53f5c2cc3a01fbb',
  'phpunit/php-code-coverage' => '6.1.4@807e6013b00af69b6c5d9ceb4282d0393dbb9d8d',
  'phpunit/php-file-iterator' => '2.0.3@4b49fb70f067272b659ef0174ff9ca40fdaa6357',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '2.1.3@2454ae1765516d20c4ffe103d85a58a9a3bd5662',
  'phpunit/php-token-stream' => '3.1.2@472b687829041c24b25f475e14c2f38a09edf1c2',
  'phpunit/phpunit' => '7.5.20@9467db479d1b0487c99733bb1e7944d32deded2c',
  'psy/psysh' => 'v0.10.6@6f990c19f91729de8b31e639d6e204ea59f19cf3',
  'roave/security-advisories' => 'dev-master@f7d723a10c7cb36e11430182f5813ecb1b887da0',
  'sebastian/code-unit-reverse-lookup' => '1.0.2@1de8cd5c010cb153fcd68b8d0f64606f523f7619',
  'sebastian/comparator' => '3.0.3@1071dfcef776a57013124ff35e1fc41ccd294758',
  'sebastian/diff' => '3.0.3@14f72dd46eaf2f2293cbe79c93cc0bc43161a211',
  'sebastian/environment' => '4.2.4@d47bbbad83711771f167c72d4e3f25f7fcc1f8b0',
  'sebastian/exporter' => '3.1.3@6b853149eab67d4da22291d36f5b0631c0fd856e',
  'sebastian/global-state' => '2.0.0@e8ba02eed7bbbb9e59e43dedd3dddeff4a56b0c4',
  'sebastian/object-enumerator' => '3.0.4@e67f6d32ebd0c749cf9d1dbd9f226c727043cdf2',
  'sebastian/object-reflector' => '1.1.2@9b8772b9cbd456ab45d4a598d2dd1a1bced6363d',
  'sebastian/recursion-context' => '3.0.1@367dcba38d6e1977be014dc4b22f47a484dac7fb',
  'sebastian/resource-operations' => '2.0.2@31d35ca87926450c44eae7e2611d45a7a65ea8b3',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'seld/jsonlint' => '1.8.3@9ad6ce79c342fbd44df10ea95511a1b24dee5b57',
  'seld/phar-utils' => '1.1.1@8674b1d84ffb47cc59a101f5d5a3b61e87d23796',
  'sirbrillig/phpcs-variable-analysis' => 'v2.10.2@0775e0c683badad52c03b11c2cd86a9fdecb937a',
  'symfony/browser-kit' => 'v3.4.47@9590bd3d3f9fa2f28d34b713ed4765a8cc8ad15c',
  'symfony/config' => 'v4.4.19@2c4c7827a7e143f5cf375666641b0f448eab8802',
  'symfony/css-selector' => 'v3.4.47@da3d9da2ce0026771f5fe64cb332158f1bd2bc33',
  'symfony/dom-crawler' => 'v3.4.47@ef97bcfbae5b384b4ca6c8d57b617722f15241a6',
  'symfony/filesystem' => 'v3.4.47@e58d7841cddfed6e846829040dca2cca0ebbbbb3',
  'symfony/finder' => 'v3.4.47@b6b6ad3db3edb1b4b1c1896b1975fb684994de6e',
  'symfony/lock' => 'v3.4.47@8d451ed419a3d5d503bd491437b447fd4c549ceb',
  'symfony/phpunit-bridge' => 'v3.4.47@120273ad5d03a8deee08ca9260e2598f288f2bac',
  'symfony/polyfill-php80' => 'v1.22.0@dc3063ba22c2a1fd2f45ed856374d79114998f91',
  'symfony/var-dumper' => 'v4.4.19@a1eab2f69906dc83c5ddba4632180260d0ab4f7f',
  'theseer/tokenizer' => '1.2.0@75a63c33a8577608444246075ea0af0d052e452a',
  'vanare/behat-cucumber-json-formatter' => 'v1.1.3@b28ae403404be4c74c740a3948d9338fc51443a3',
  'webflo/drupal-finder' => '1.2.2@c8e5dbe65caef285fec8057a4c718a0d4138d1ee',
  'webmozart/assert' => '1.9.1@bafc69caeb4d49c39fd0779086c03a3738cbb389',
  'webmozart/path-util' => '2.3.0@d939f7edc24c9a1bb9c0dee5cb05d8e859490725',
  'drupal/drupal' => 'dev-sec-updates@38134d945aada8dfe30f996d620d2bf0b44af6f7',
);

    private function __construct()
    {
    }

    /**
     * @psalm-pure
     *
     * @psalm-suppress ImpureMethodCall we know that {@see InstalledVersions} interaction does not
     *                                  cause any side effects here.
     */
    public static function rootPackageName() : string
    {
        if (!class_exists(InstalledVersions::class, false) || !InstalledVersions::getRawData()) {
            return self::ROOT_PACKAGE_NAME;
        }

        return InstalledVersions::getRootPackage()['name'];
    }

    /**
     * @throws OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     * @psalm-pure
     *
     * @psalm-suppress ImpureMethodCall we know that {@see InstalledVersions} interaction does not
     *                                  cause any side effects here.
     */
    public static function getVersion(string $packageName): string
    {
        if (class_exists(InstalledVersions::class, false) && InstalledVersions::getRawData()) {
            return InstalledVersions::getPrettyVersion($packageName)
                . '@' . InstalledVersions::getReference($packageName);
        }

        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}
