# GitLab CI Pipelines

This project serves as a way of sharing standardized GitLab CI pipeline tests
for Drupal 8 websites.

## Installation

This package should be installed with composer, however, in order to do so a
number of changes will need to be made to the project.

### Remove existing files

When we originally rolled out the GitLab CI Pipelines for D8 sites we did so on
a per-site basis, meaning each site has a copy of certain files, but they are
not centralized in anyway, making a change to one would require updating all our
D8 websites manually. In order to switch over to the a composer-managed solution
we need to remove the existing code.

#### /.gitlab-ci.yml

There is a file within the repository root of D8 sites called `.gitlab-ci.yml`
which controls which tests to run, and points into a `.gitlab-ci` directory also
in the repo's root. This yml file will need to be removed:

`git rm .gitlab-ci.yml`

#### /.gitlab-ci/*

The .gitlab-ci directory houses all the scripts that are run by the GitLab CI
pipeline runners. In order to manage these scripts using composer we must remove
this directory:

`git rm -rf .gitlab-ci`

### Modify composer.json

We also need to modify composer.json in order to start managing this code with
composer. 

#### Add Installer Path

Typically packages downloaded by composer are either put in Drupal paths
(modules, themes etc) if they are Drupal packages, or in the vendor directory if
they are not, we need to be able to place this package's code in the repo root.

Our standard installer paths configuration in composer.json is like so:

```
"installer-paths": {
    "core": ["type:drupal-core"],
    "modules/contrib/{$name}": ["type:drupal-module"],
    "profiles/contrib/{$name}": ["type:drupal-profile"],
    "themes/contrib/{$name}": ["type:drupal-theme"],
    "drush/contrib/{$name}": ["type:drupal-drush"],
    "modules/custom/{$name}": ["type:drupal-custom-module"],
    "themes/custom/{$name}": ["type:drupal-custom-theme"]
},
```

We need to modify this as follows:

```
"installer-paths": {
    "core": ["type:drupal-core"],
    "modules/contrib/{$name}": ["type:drupal-module"],
    "profiles/contrib/{$name}": ["type:drupal-profile"],
    "themes/contrib/{$name}": ["type:drupal-theme"],
    "drush/contrib/{$name}": ["type:drupal-drush"],
    "modules/custom/{$name}": ["type:drupal-custom-module"],
    "themes/custom/{$name}": ["type:drupal-custom-theme"],
    "../{$name}": ["type:root-package"],
},
```

The `root-package` package type was created for this project to be able to
control where the package gets installed.

### Composer Require

Once those changes have been made we can perform a composer require:

`composer require ifsight/gitlab-ci-pipelines`


**NOTE: At this time the above composer command will not work, this is reliant
upon the statis composer package repository being created, and a repository
definition being added to the composer file. Also, if we move to the Drupal 8
recommended composer file, we will not need to define the path as `../{$name}`
but rather could use `{$name}` instead, as the composer.json file will live in
the repository root.**