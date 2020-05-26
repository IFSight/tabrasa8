
# INTRODUCTION

Slick Browser provides a Slick Entity Browser and core Media Library integration
serving to enrich variant displays for the Great Entity Browser and also core
Media Library. This modules provides 2 things:

1. Entity selector (Media/File/Block/Node) such as seen at Media Library modal.
2. Entity widget display such as seen after selecting entities from a modal.


## REQUIREMENTS
1. [Entity Browser](https://drupal.org/project/entity_browser) (2.0+)
2. [Blazy](https://drupal.org/project/blazy) (2.0+)
3. [Slick](https://drupal.org/project/slick) (2.1+)


## INSTALLATION
Install the module as usual, more info can be found on:

[Installing Drupal 8 Modules](https://drupal.org/node/1897420)

Enable Slick Browser module under **Slick** package:

`/admin/modules#edit-modules-slick`


## CONFIGURATION
1. `/admin/config/content/entity_browser`  
   Browse supported Slick Browser plugins, edit/add new plugins accordingly.
   They are there just basic samples, and may not suit actual needs.

   For custom Entity Browser plugins, the module only respects plugins
   containing **slick_browser** in the name, e.g.:
   `site_slick_browser_file`, or `custom_slick_browser_media`, etc.

2. `/admin/structure/views`  
   Clone or edit a Slick Browser view, adjust the filter criteria to match
   the target field. Else regular mismatched error, e.g.:
   **This entity (node: NID) cannot be referenced.**  

   **Also adjust Views permission accordingly!**  

   **The requirements, or limitations:**
   * The view must have `slick_browser` as part of its name.
   * A **global** Views filter named **Slick Browser**.
   * Only works with **Slick Browser** views style. It may work with
     **Slick Views** or core **HTML list** style plugins, but requires
     additional adjustments.
   * Must have **Views field label** for the grid/ list (table-like) labels.
   * For non-image entities, such as node, block, etc., add a special wrapper
     class to the title part: **views-field--selection** for a quick selection
     preview. Only relevant for Multi step selection, though.

   Using/ cloning the provided samples should reduce the above steps to 0 as
   likely there are hidden things there, and not immediately obvious such as
   custom field wrapper classes within the preview Views fields, e.g.:
   **views-field--preview** for the main image preview.

3. `/admin/structure/types/manage/article/form-display`  
   etc.
   Or any **Manage form display** URL containing image/file or media widgets.
   Under **Widget** for the *Entity Browser*, or *Media Library* click the cog
   icon, and add relevant **Slick Browser** plugins.
   To disable Slick Browser widget, leave **Display style** option empty.


## RELATED MODULES
1. Core Media Library
2. [File Browser](https://drupal.org/project/file_browser)
3. [Content Browser](https://drupal.org/project/content_browser)
4. [Media Entity Browser](https://drupal.org/project/media_entity_browser)

While they are specializing in entities, Slick Browser more in UX or cosmetics.
The basic difference is Slick Browser uses Blazy Grid, Slick library, and a
little spice for quick interaction, and few little more things.


## SKINS
**To add custom skins:**  
For the Slick widget part, put them under group **widget**.
For the Slick browser Views part, put them under group **main**.
No skins for the overall form, maybe later.
The **widget** skins have different markups as meant for narrow real estate and
to avoid conflict of interests against its front-end fella.

See `Drupal\slick_browser\Plugin\slick\SlickBrowserSkin`.

### Available skins:
1. **Widget: Classic**

   Only reasonable if it has Alt or Title field enabled along with images. Works
   best with one visible slide at a time. Adds dark background color over white
   caption, only good for slider (single slide visible), not carousel
   (multiple slides visible), where small captions are placed over images.

2. **Widget: Split**

   Only reasonable if it has Alt or Title field enabled along with images. Works
   best with one visible slide at a time. Puts image and caption side by side,
   related to slide layout options.

3. **Widget: Grid**

   Grid dedicated for Entity Browser field widget.

4. **Slick Browser: Grid**

   Grid dedicated for Entity Browser View display.


## WIDGETS
Widgets are available after selecting images or entities, normally displayed
within collapsible details or fieldsets.

Slick Browser widget supports 4 **Display style**:

`CSS3 Columns, Grid Foundation, Single Preview, Slick Carousel`

* Single Preview for single-value fields (cardinality 1).
* The rest for multi-value fields (cardinality > 1).

The first three are treated as static display (w/o grid), no carousel.
Slick Browser has its own sortable elements to avoid conflict with Slick
draggability. The rest are grid items themselves acting as sortable elements.

If no **Display style** is selected, will use default Entity Browser widget
displays. Hence the only useful is the Views display, only if provided.


## FEATURES
* Core Media Library widget with configurable grid displays, etc.
* FieldWidgetDisplay plugins for image/file including core Media, Block, Node.
* WidgetSelector Tabs for various tab placements: bottom, left, right, top.
* Slick Browser Views style plugin for displaying media library.
* Views filter grid/ table-like list view switcher plugin.
* Widget plugins for both Entity Browser and File Browser widgets.
* Grid Foundation, CSS3 Columns (experimental CSS Masonry), Slick Carousel, for
  both Views style and Widget displays.
* Full screen video previews.
* Blazingly quick image/media selection/removal, er, prior to being saved.
* AJAX (Views Infinite Scroll or Views Load More) on steroids, rather than
  loading 12 images per page, load as many as you think the browser is not
  choked once saving a few AJAX requests. With Blazy delay lazy loading and
  Slick carousel dots and arrows navigation, whatever the amount of images cost
  as much as 12 visible images.

## Supports, or plays nice with:
* Core Image and File
* Core Media, Media Library, and its plugins: Facebook, Instagram, SlideShare,
  Twitter, etc.
* Inline Entity Form via EB
* DropzoneJS

The relevant Entity Browser plugins will only be enabled if the above-supported
modules, and expected config, are installed/ met earlier, or later.

Specific to core Media, Slick Browser only expects bundles: image, remote video
and local video. Other bundles will be displayed as per its View mode.
The Slick Browser Media Views permission is set to **Access Media Overview**.
Adjust it.

If your video bundle is named "moving_picture", or image bundle named "picture",
or "photo", the related plugins won't install. They can be recreated based on
your available bundles, though. Yet having the exact bundle names help save
some time to configure.

Recommended not being too creative with field naming:
**Avoid renaming or removing fields provided by core Drupal**.

Slick Browser provides a few default Views for: Block, File, Media, Content.

Use the provided samples to begin with.


## CURRENT DEVELOPMENT STATUS
Not tested with all available plugins, yet. Patches and help are appreciated.
Alpha and Beta releases are for developers only. Be aware of possible breakage.

However if it is broken, unless an update is explicitly required, clearing cache
should fix most issues during DEV phases. Prior to any update, always visit:

`/admin/config/development/performance`

And hit **Clear all caches** button once the new Slick Browser is in place.
Regenerate CSS and JS as the latest fixes may contain changes to the assets.

Have the latest or similar release Blazy and Slick to avoid trouble in the first
place. They will maintain backward-compatibility till full release.


## KNOWN ISSUES
The `[x]` means resolved, or no longer a real issue.  

* `[x]` The widget part is not working, yet. Do not use it unless helping
  development.
  False alarm. Nothing to do with Slick Browser. Please check out:

  + https://www.drupal.org/node/2745491
  + https://www.drupal.org/node/2715859
  + https://www.drupal.org/node/2644468  

* `[x]` The field UI form needs saving first, before the selected Slick Browser
  widget (Entity display plugin) form is displayed.

* `[x]` Installing samples at Drupal 8.x-3 may produce schema errors. Simply
  continue, and visit Views collection page to re-save if needed. Else ignore.
  This is because Drupal core Views changed its schema for filters at 8.x-3.
  We can wait for another branch, edit and re-save Views, or just ignore.

* `[x]` Combining Slick Carousel with grid is disabled due to bad UX. Choose
   either static grid (Grid Foundation or CSS3 Columns), or Slick Carousel
   without grid.

* `[x]` Removing image/ media results in JS error:

  `Notice:  unserialize(): Error at offset 84 of 1212`.  

  **Solutions**: It appears the issue is related to Devel Kint module.
  Uninstalling Devel Kint solves the issue.

* Few options are not functional, yet, specific to Media Library widget, such as
  `View mode`, or `Image style` options. Left there for future improvements.


## TROUBLESHOOTING
* Be sure that the Slick Browser target type is matching the current field type,
  E.g.: `Slick Browser: File` for field type `File`, `Slick Browser: Media` for
  field type `Media`, Node for Node, etc. Mismatched types will surely break.
  To understand the concept, visit `Media` entity form with bundle `Video`:
  + `/admin/structure/media/manage/video/form-display`  
  + Under Field `Video file`, choose `Entity Browser`
  + What will you select: `Slick Browser: File` or `Slick Browser: Media`?  
    Feel free to measure your understanding. Wrong choice will break somewhere,
    if not immediately. It is not that complicated if at Node form display.
* If anything related to displays look weird, or unexpected, clear cache.
  Including to update Slick skins cache if not seeing the widget skins, or
  when adding new custom skins.
* Grid Foundation is best with regular cropped image sizes and vertical.
* CSS3 Columns is best with non-vertical, and adaptiveHeight, else cropped.
  It affects the natural order of grid items, meaning confusing for UI
  sortable when used within a draggable widget. Use it if no big-deal.  
* Selections will be ignored until button **Select entities** alike is hit
  during page or tabs changes. Hitting **Add to Page** directly is possible if
  available and not changing tabs nor AJAX pages.    
* All slick widget and browser expects Arrows and Dots options enabled.
* If the provided starters are not installed due to unmet dependencies, or
  updated at the next release, install `config_update` module, and visit:

  `/admin/config/development/configuration/report/module/slick_browser`

  Hit (Right click open tab) **Import from source** to manually import them.
  Or hit **Revert to source** to revert to the factory or updated defaults.
  Repeat the steps.

  The Slick Browser starters will be available at:

  + `/admin/config/content/entity_browser`
  + `/admin/structure/views`

## TIPS
* Be sure to disable fancy options such as `Infinite`, `Draggable`, etc. which
  might conflict with jquery UI sortable and draggable for better UX.
  Specific to `Draggable`, the module provides a separate sortable and draggable
  display under **Sort** button so it won't conflict much.
* Keep navigation elements enabled: `arrows` and or `dots` for better UX.
* Image or Media Image only is best with **Display style: Slick Carousel** and
  skin **Widget: Split** to have Alt and Title fields immediately visible.
* Other Media entities, or mixed, best with `Grid Foundation` or `CSS3 Columns`
  for **Display style**.


## FAQ
Q: _What is Widget vs. Browser?_  
A: Widget is the entity list display right on the node edit form displayed
   normally within a collapsible details/fieldsets. This can be edited at any
   **Manage form display**. Browser is the entity list selector which can be
   displayed in an iFrame or modal to select entities to be put into a Widget
   display. This can be edited at **/admin/structure/views** and
   **/admin/config/content/entity_browser**.

Q: _How to sort items?_  
A: Depending on **Display style**. For `Grid Foundation` or `CSS3 Columns`,
   simply drag the grid items. For Slick Carousel, please click **Sort** button
   above the slide items, and sort based on the provided thumbnails.

Q: _I want Video file, but I see also images and videos?_  
A: The provided samples are generic. You want Browser per bundle. Please clone
   the relevant Views under `Slick Browser` namespace. Adjust Views filter
   accordingly to only filter the expected Bundle. Then select it as
   your Browser for this particular bundle. Depending on you target entity, the
   only requirement is your View must contain `slick_browser` or
   `slick_browser_media` in its name, e.g.:  

   * `My Slick Browser: File Video` for bundle Video under Media form will
     contain `slick_browser`.  
     At most cases, you don't want the provided generic sample in here.  
   * `My Slick Browser: Media Video` for bundle Video under Article form will
     contain `slick_browser_media`. But not if `My Slick Browser: Video Media`.  
     Repeat for each bundle:
     + `My Slick Browser: Media Remote Video`
     + `My Slick Browser: Media Instagram`  
     At most cases, the provided generic sample is all you need in here. You can
     use the bundle Filter instead, like what core Media Library has. Be sure to
     remove the no longer relevant exposed Filter bundle if you go this route.

Q: _After clicking Delete button, I see Confirm, but not gone when ignored?_  
A: Just click on the image or anywhere inside the widget to cancel. It is forced
   displaying to pass the behavioral tests which fail hover states.

Q: _Why is the Views pager and Slick arrows in the center of Media Library?_
A: To many possibilities and each possibility requires adjustments based on
   Entity Selections, Tabs, Buttons, etc. The center requires no extra
   adjustments. Aside from the difficulty to deal with 2 core admin themes and
   narrow modal/ iFrame window. Override existing CSS if you can stick to one
   favorite position.

Q: _I have Media Library in core, what this module offers that core doesn't?_  
A: A few little enhancements:
   configurable grid, compact displays of various media entities since core
   doesn't have to concern about cosmetic details such as file video displays,
   a convenient full screen video preview, and few more little things. Quick
   **Edit** and **Replace** link if using Entity Browser with
   **Slick Browser: Media** widget. As a little bonus, loading speed for
   **Remote video** collections.

Q: _Why this module fails tests for D9?_  
A: This module depends on Entity Browser which fails D9 as of this writing.
   Once resolved there, this module needs nothing else to pass D9, hopefully.  


## MAINTAINERS
* [Gaus Surahman](https://drupal.org/user/159062)
* [Contributors](https://www.drupal.org/node/2823779/committers)
* The CHANGELOG.txt for more helpful souls with suggestions, and bug reports.

Some is inspired by related modules, and discussions here:
https://www.drupal.org/node/2796001, https://www.drupal.org/node/2786785.
The initial concept named "Slick Widget" was created weeks before those
discussions as seen from SlickManager::$skins widget. It just has better
direction thanks to discussions. Some credits go to @kiboman for design
inspirations, and everyone else there for thorough ideas.
No designs are stolen, they are inspirations to trigger personal creativity.
The rest is just opiniated, personal taste and time limitation to cope with EB
current design challenges.

<s>This module may be transformed when core has a media library solution, or
also extending core as needed. Until then, this is a Slick Entity Browser
plugin.</s> Not much to do with Media library which is already awesome OOTB.
However added third party settings to have a configurable grid display for now,
and few little things as mentioned above.


## READ MORE
See the project page on drupal.org:

[Slick Browser](https://drupal.org/project/slick_browser)
