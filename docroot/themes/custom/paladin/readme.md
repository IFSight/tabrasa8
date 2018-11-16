# Paladin Theme for Drupal 8

Paladin is a Drupal 8 theme that leverages the inverted triangle CSS concept and atomic design for a super-lightweight and componentized experience. The structure of Paladin's SASS is largely inspired by Lindsay Grizzard's article on creating CSS systems: https://medium.com/gusto-design/creating-the-perfect-css-system-fa38f5bcdd9e.

## Installation

Before using Paladin, you need to run `npm install` in the root of the paladin directory (usually /docroot/themes/custom/paladin) to ensure all appropriate node packages are loaded.

## Compiling SASS

If you don't feel like reading the thrilling contents of the gulpfile.js, you can run `gulp styles` to compile CSS to SASS or `gulp watch` to watch for changes in the sass directory.

## SASS Structure and ITCSS

Inverted triangle CSS is structured in order of least to most specificity. The SASS directory in Paladin is structured as follows:

* 0-vendor: Vendor-specific styles. So far we have none of these but this is a living document! We may kill this. Who knows! Exciting times in theme development.
* 1-settings-tools: Colors, fonts, other globals.
* 2-generic: resets
* 3-atoms: the building blocks/bare HTML elements (p, h1, img) and smaller bits that make up the larger ones
* 4-molecules: groups of atoms that work together (menus, utilities)
* 5-organisms: larger groups of molecules (header, footer, views).
* 6-helpers: the most specific classes for columns, color classes, alignment, and shapes.

All of the folders contain SCSS partials that are imported into a main SCSS file, with the exception of organisms (which has some individual SASS files, more on this below). Then the SCSS files are compiled into a single CSS directory with their associated sourcemaps. These files can be referenced in libraries.

### The organisms directory

Everything in the organisms directory that should be globally applied follows the same structure of a SCSS partial being imported into the global-components.scss file. The difference here is that there are certain organisms that do not need to be applied globally. For example, an accordion library can be attached to an accordion template and used only when needed. Therefore, these files exist as standalone, non-partial SCSS files that can be referenced in their own libraries for efficient attachment.

### Placeholder for rscss??????