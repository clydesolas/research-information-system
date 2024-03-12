=== Posts Table Pro ===
Contributors: andykeith, barn2media
Tags: posts, table, tables, shortcode, search, sort
Requires at least: 5.0
Tested up to: 5.8.3
Requires PHP: 7.1
Stable tag: 2.4.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A simple plugin to display all your posts in a searchable and sortable table.

== Description ==

Posts Table Pro provides an easy way to list content from your WordPress site in a searchable and sortable data table.
It supports posts, pages, products, custom post types, featured images, taxonomies, custom fields and much more.

To use, simply add the shortcode `[posts_table]` to any page.

Under the hood, it uses the [jQuery DataTables](http://datatables.net/) library to provide the searching and sorting
features, as well as pagination and responsive layouts.

== Installation ==

1. Go to Plugins -> Add New -> Upload and select the plugin ZIP file (see link in Purchase Confirmation Email).
1. Activate the plugin.
1. Enter your license key under Settings -> Posts Table Pro.
1. Go to any post or page and add the shortcode `[posts_table]`.
1. See the full list of [shortcode options](https://barn2.com/kb-categories/posts-table-pro-kb/) to configure your table.

== Frequently Asked Questions ==

See refer to the [documentation](https://barn2.com/kb-categories/posts-table-pro-kb/). If you need further assistance please visit [our support center](https://barn2.com/support-center/).

== Changelog ==

= 2.4.1 =
Release date 13 January 2021

 * Fix: Date custom fields in the table are now displayed in the current language, instead of only in English.
 * Fix: A bug introduced in 2.4, where selecting posts with the `cf` option did not work if the custom field was a multi-select type such as checkbox or radio button.
 * Tested up to WordPress 5.8.3.

<!--more-->

= 2.4 =
Release date 16 November 2021

 * Tested up to WordPress 5.8.2.
 * Updated DataTables library to 1.11.3.
 * Fix: An issue using special characters in the 'cf' custom field option.
 * Fix: A PHP warning when the image file for post featured image is not found.
 * Dev: Use webpack for script bundling.
 * Dev: Replace wp_localize_script with wp_add_inline_script.

= 2.3.4 =
Release date 5 March 2021

 * Fix: Fixed a bug with the 'term' option for post tables using complex queries involving several taxonomies.
 * Fix: Fixed a bug which caused accidental display of the responsive child row when opening the featured image lightbox.
 * Fix: Improved loading of icon font to prevent render blocking.
 * Tested up to WordPress 5.7.

= 2.3.3 =
Release date 25 February 2021

 * Fix: Fixed a bug with the post totals message in certain scenarios.
 * Tweak: Minor changes to the plugin settings page and internal code library.

= 2.3.2 =
Release date 13 January 2021

 * New: Added new settings to customize the table design.
 * New: Added an 'exclude_term' shortcode option to exclude posts belonging to one or more custom taxonomy terms.
 * Prevent accidental use 'category' option when specifying custom taxonomy terms.
 * Minor improvements to dropdown filters.
 * Updated all language translations.
 * Dev: Updated DataTables to version 1.10.23.
 * Dev: Added hook 'posts_table_enable_select2' to enable or disable the Select2 library.

= 2.3.1 =
Release date 18 November 2020

 * Fix: Bug introduced in version 2.3 which caused non-Latin characters to be removed from column and filter headings.
 * Fix: Bug with post totals message when using lazy load.
 * Dev: Improve code for overriding post totals messages.

= 2.3 =
Release date 13 November 2020

 * Dropdown filters now display relevant items only depending on current results, and update automatically after making a selection (excludes lazy load).
 * Various filter improvements: option to set custom filter headings (placeholder), added search box to filters, removed the 'Filter:' label, use select2 library.
 * Better handling for ACF date fields, and remove the need to specify ACF dates in date_columns option.
 * Better post totals message based on the selected post type, and use singular form when showing 1 result.
 * Added right-to-left (RTL) language support.
 * Changed the default for the position of the page length dropdown (below the table), and pagination type (numbers only).
 * Minor improvements to settings page.
 * Tested up to WordPress 5.5.3.
 * Fix: Prevent conflicts with responsive column display when column name contains a reserved keyword (e.g. "mobile").
 * Fix: Column class filter posts_table_column_class_[column] now applies to all rows in table, not just headings.
 * Dev: Replaced FontAwesome with custom font based on IcoMoon Free icon pack (GPL license).
 * Dev: Updated DataTables to 1.10.22.

= 2.2.1 =
Release date 23 September 2020

 * Fix: Add backwards compatibility for sites running PHP 5.6 (temporary).

= 2.2 =
Release date 9 September 2020

 * New: Added a 'date_modified' column to display the post's last modified date.
 * New: Added a 'button' column which links to the single post page. A new 'button_text' option is also available which allows you to set the text for the button.
 * Added button text option and made other minor improvements to the settings page.
 * Fix: A missing jQuery dependency was causing Javascript errors on some sites.
 * Fix: The 'post_limit' setting was incorrectly applied to lazy loaded tables. It now applies to lazy load only when explicitly set in the shortcode.
 * Fix: Minor CSS issue with the + and - responsive row icons.
 * Dev: Refactored code to use new architecture and plugin license system.
 * Dev: Updated DataTables and Photoswipe libraries.
 * Dev: Removed old web font formats which are no longer required.
 * Dev: Added Composer support.

= 2.1.5 =
Release date 23 January 2020

 * Fix a bug with the sort_by option when sorting by custom taxonomy with a slug ending in "_category".
 * Combine CSS and JS assets to reduce the number of HTTP requests.
 * Update DataTables library to version 1.10.20.
 * Tested up to WordPress 5.3.2.

= 2.1.4 =
Release date 30 October 2019

 * Tested up to WordPress 5.3.
 * Fix a bug with several table options where setting in the main settings was not applied correctly.
 * Update Russian translations.
 * Refactored code to use updated plugin library.

= 2.1.3 =
Release date 7 March 2019

 * Fix: Formatting and sorting of date custom fields in EU/AU date format (dd/mm/yyyy or dd/mm/yy).
 * Fix: Ensure sorting for date custom field works correctly when 1 or more posts is missing custom field data.
 * Fix: Ensure custom taxonomies used as dates are correctly formatted when using the 'date_format' option.
 * Fix: Totals and pagination were wrong after deleting search term, when an initial 'search_term' was set and using lazy load.
 * Dev: New filters for date formatting.

= 2.1.2 =
Release date 14 November 2018

 * Added Russian translation.
 * Fix: Bug with 'links' option when using more than one value (e.g. links="title,image").
 * Fix: Prevent private post types being viewed if user doesn't have required capability.
 * Fix: Bug with 'search on click' feature.
 * Tweak: Improve validation of shortcode options.

= 2.1.1 =
Release date 19 September 2018

 * Fix: Bug with lazy load caching which caused incorrect post totals.
 * Fix: Bug with table columns being set incorrectly when not using the 'post' post type.
 * Fix: Bug with lazy load which prevented cache being loaded when sorting by date.
 * Fix: Improve generation of table IDs to avoid clashes.
 * Fix: Ensure initial search term is restored when resetting table.
 * Fix: Improve validation of columns and search term shortcode options.
 * Tweak: Image column no longer links to single product page by default.
 * Dev: Improve table caching code.
 * Dev: New filter to adjust minimum search term length.

= 2.1 =
Release date 31 August 2018

 * Fix: Fix date conversion for custom fields in dd/mm/yy format, to ensure date columns are sorted correctly.
 * Fix: Ensure private posts & pages are hidden if current user doesn't have permission to view them.
 * Fix: CSS issue which prevented FontAwesome icons displaying correctly.
 * Tweak: Update 'post_status' shortcode option to 'status', and add backwards-compatibility for old name.
 * Tweak: Remove extra database call to get posts total when using lazy load.
 * Dev: Improvements to Posts_Data_Table class - get_table and get_data methods now allow 4 possible return types.
 * Dev: Removed inline script for table config and instead use data-config and data-filters attributes on table element.
 * Dev: Replace 'self' with __CLASS__ in callable references to static methods.

= 2.0.3 =
Release date 4 June 2018

 * Fix: Ensure wpautop always runs before do_shortcode in 'content' column to prevent incorrect formatting.
 * Fix: Prevent table hooks being registered multiple times when there are several tables on one page.

= 2.0.2 =
Release date 29 May 2018

 * Fix: Potential bug with totals when using lazy load.
 * Fix: Potential infinite loop when there are posts tables within post tables.
 * Fix: Bug with display of shortcodes in 'content' column.
 * Fix: Bug with custom taxonomies used as date columns.
 * Fix: Display of audio and video shortcodes in responsive child row.
 * Fix: Potential bug when using posts table in archive template and settings were updated.
 * Fix: Bug with categories filter when the 'category' shortcode option was set.
 * Tweak: Ordering now correctly reset when table is reset.
 * Tweak: Added WPML config file.
 * Tweak: Update license code.
 * Dev: Moved shortcode processing to abstract data class.
 * Dev: Made $data_table and $hooks properties public in Posts_Data_Table.
 * Dev: Made $args property public in various classes to allow easier customization.
 * Dev: Re-added add_above() and add_below() functions to data table class.
 * Dev: New hooks 'posts_table_hooks_before_register' and 'posts_table_hooks_after_register'.
 * Dev: Tested up to WP 4.9.6.

= 2.0.1 =
Release date 15 May 2018

 * Fix: Bug with row attributes and classes when using lazy load.
 * Fix: Bug when using table with one column and column heading is blank.
 * Tweak: Improve table reset.
 * Tweak: Typo on settings page.

= 2.0 =
Release date 14 May 2018

 * New: New plugin options added to the settings page (Settings > Posts Table Pro). Use these to set table options and shortcode defaults.
 * New: Image lightbox feature - open post images in a lightbox when using the 'image' column.
 * New: Table caching added to improve performance. Disabled by default - enable from the settings page or use the 'cache' option in shortcode.
 * New: Categories and custom taxonomy filters now display in correct hierarchy (Parent -> Child -> Grandchild) structure.
 * New: 'search_term' shortcode option to set the initial table search.
 * New: Search filters now search by 'slug' to improve accuracy and avoid name clashes.
 * New: Toolbar button available in Edit Post/Page screen to insert the posts table shortcode.
 * New: Add support for Time Picker fields in Advanced Custom Fields.
 * Fix: Search filters (e.g. Categories filter) now show the correct results when a parent category/term is selected (i.e. posts which have a child category of the selected category are now included in the results).
 * Fix: Bug with lazy load which caused pagination to be set incorrectly when resetting table.
 * Fix: Bug when selecting posts by custom field in ACF and field value is stored as an array.
 * Tweak: Icons replaced with FontAwesome.
 * Tweak: Posts limit now ignored when using lazy load.
 * Tweak: Improved cross-browser support and theme compatibility.
 * Dev: All plugin translation now handled by gettext functions. Old JSON translation files have been deprecated.
 * Dev: Complete code restructure and new data classes added.
 * Dev: Updated DataTables library to 1.10.16.
 * Dev: New filters: posts_table_after_get_data, posts_table_after_get_table, posts_table_custom_table_data_<column>, posts_table_optimize_table_query.
 * Dev: Deprecated filters: posts_table_supported_languages, posts_table_get_data, post_table_get_table, posts_table_cell_data_<column>.
 * Dev: Re-added the posts table wrapper div.
 * Dev: Various other code fixes and improvements.

= 1.6 =
Release date 15 December 2017

 * You can now pass just the custom field key to the "cf" option to retrieve posts which have that custom field (regardless of value).
 * Code restructure and performance improvements.
 * Portuguese translation.
 * Tested in latest version of WordPress (4.9.1).
 * Fix: Allow ACF fields which store multiple values (e.g. checkbox fields) to be used as selectors with the "cf" option.
 * Fix: Bug when using column names containing a dot "." with lazy load enabled.
 * Tweak: Additional CSS classes and post ID now added to each row in table.
 * Tweak: Added 'posts_table_open_posts_in_new_tab' filter.
 * Tweak: Changed DataTables error reporting.
 * Tweak: New hooks for post table tax query and meta query.
 * Tweak: Removed additional wrapper div so simplify HTML structure.

= 1.5.5 =
Release date 1 October 2017

 * New: Add support for ACF date_time_picker field.
 * Fix: Ensure videos are always responsive on smaller screens.
 * Fix: Prevent bug when using two of the same column in table.
 * Fix: Potential PHP error when handling date_picker fields in ACF.

= 1.5.4 =
Release date 5 September 2017

 * New: Changed names of all shortcode options beginning 'display_'. Old options still supported for backwards compatibility.
 * Fix: Warning generated on post type validation.
 * Fix: Bug with tax query when using '+' indicator in 'term' option.
 * Tweak: Added post type and post ID as CSS classes to each row, and added 'posts_table_row_classes' and 'posts_table_row_attributes' filters.
 * Tweak: Update documentation links and other minor changes.

= 1.5.3 =
Release date 22 August 2017

 * Added 'numeric_terms' option - set to true if you use categories/terms which have numeric slugs.
 * Fix: Allow shortcodes to work in all custom field columns.
 * Fix: Bug with formatting of date custom fields.
 * Fix: Support for custom field date columns held as UNIX timestamps.
 * Fix: Selecting by custom field (using 'cf' option) wasn't working when used for Events Calendar events.
 * Fix: Conflict with Hero Knowledge Base plugin.
 * Tweak: Added hook to allow ordering to be enabled/disabled for specific columns.
 * Tweak: Update DataTables to 1.10.15.

= 1.5.2 =
Release date 16 April 2017

 * New: Added support for ACF Repeater fields.
 * New: Added template tags ptp_product_table and ptp_the_product_table for easier use in theme templates.
 * Fix: Ensure width of image column matches the image size used.
 * Fix: Compatibility with date picker fields ACF Pro.
 * Fix: Bug with custom column names appearing in search filter drop-downs.
 * Tweak: Update DataTables to 1.10.13
 * Tweak: Removed $html parameter from 'posts_table_shortcode_output' filter.
 * Tweak: Added filters for separator used in item lists (e.g. post categories).

= 1.5.1 =
Release date 23 February 2017

 * Fix: Bug with search filters and search on click when using lazy load option.
 * Fix: Intermittent bug with boolean shortcode options.
 * Fix: Bug with search filters which prevented first search term in table being added to list.
 * Tweak: Ensure shortcode doesn't run in admin.
 * Tweak: Ensure shortcode doesn't run on search results page.

= 1.5 =
Release date 13 February 2017

 * Fix: Bug with responsive display functions (responsive_display="modal" or "child_row_visible").
 * Fix: Hidden columns are no loner shown in modal child row display.
 * Fix: Bug with date format for date custom fields.
 * Fix: Search filters for hidden columns.
 * Fix: Bug with license code.
 * Tweak: Restructure Javascript array functions to prevent issues with other frameworks.
 * Tweak: Added filter 'posts_table_acf_value' for ACF custom field values.
 * Tweak: Added filter 'posts_table_inline_config' to modify table config.
 * Tweak: Replaced ID with Posts_Data_Table object in filter hooks.

= 1.4.9 =
Release date 8 February 2017

 * Fix: If search filter is present as column in table, use column data as items for drop-down.
 * Fix: Bug with custom taxonomy column heading.
 * Tweak: Change inline table config code.
 * Tweak: Added filter 'posts_table_shortcode_output' to filter the table shortcode HTML.

= 1.4.8 =
Release date 1 February 2017

 * New: Added 'display_reset_button' option to show or hide the reset button above the table.
 * New: Added column classes to every row in table to allow easier styling of table contents.
 * New: Deactivate license button on plugin settings page to make it easier to switch sites or move from development to production.
 * Fix: Change ACF code to ensure any theme hooks run.
 * Fix: Bug with select, text area and WYSIWYG fields in ACF.
 * Fix: Bug with display of search filters on mobile.
 * Fix: Name of Polish translation file for datatables.
 * Fix: Bug when selecting posts from several categories with '+' (AND) operator.
 * Fix: Restrict search filters to show only child terms when 'category', 'tag' or 'term' option specified.
 * Tweak: Ability to set a blank column heading. Use "blank" after column name, e.g. columns="price:blank".
 * Tweak: Added 'posts_table_search_label', 'posts_table_filter_label' and 'posts_table_reset_button' filter to allow those elements to be customised.
 * Tweak: Added various filters to filter all data in the table (price, add to cart, variations, attributes, etc.)
 * Tweak: Format URL custom fields as links.
 * Tweak: Adjust table styles to enable easier theme customisation.
 * Tweak: Improved support for ACF fields.
 * Tweak: Changed custom field filter to 'posts_table_data_custom_field' (old filter still valid but deprecated).
 * Tweak: Moved inline table config directly below table element.

= 1.4.7 =
Release date 19 January 2017

 * Fix: Bug with when sorting custom field date columns in the format "May 2016".
 * Fix: Bug with custom field dates stored in European format (d/m/y).
 * Fix: Bug with 'tags' column which prevented tags from being displayed.
 * Tweak: Add dashicons dependency for plugin stylesheet.

= 1.4.6 =
Release date 18 January 2017

 * New: The 'filters' option is now much more flexible - you can include categories, tags or any custom taxonomy, even if they are not
displayed as a column in your table. For example, filters="categories,tax:staff_location" or filters="true".
 * New: Search filters are now supported for lazy loaded tables.
 * New: Custom fields or taxonomies which represent dates can now be sorted in correct date order. Use new option "date_columns" to
specify which additional columns should be treated as dates, e.g. date_columns="cf:start_date,tax:staff_date".
 * New: 'term' option accepts one taxonomy as well as multiple taxonomies in a single list, e.g. term="product_tag:cool,awesome,great" or
term="product_tag:cool,staff_location:london".
 * New: Polish translation (credit: Marcin Wasilewski).
 * Fix: Fixed table layout issue when displaying videos in table.
 * Fix: Bug with media shortcodes when using lazy_load option.
 * Fix: Format of date picker fields in Advanced Custom Fields. Will use 'date_format' option if specified, otherwise the display format in ACF.
 * Fix: Prevent first column from hiding on smaller screens when using responsive_control="inline".
 * Fix: Bug with drop-down filter selection.
 * Fix: Bug with sort_by option for custom fields.
 * Tweak: Table now scrolls to top when clicking a category, tag, author, etc. in the table and search_on_click="true".
 * Tweak: Added filter 'posts_table_custom_field_value' to filter custom field values in table.
 * Tweak: Add filter for column header class - 'posts_table_column_class_ ' + the column name.
 * Tweak: Added table ID to most filters to allow more specific filtering.
 * Tweak: Updated license activation code.
 * Tweak: Re-structure Javacript code.
 * Tweak: Change styling and location of reset button, and other minor styling improvements.

= 1.4.5 =
Release date 25 November 2016

* Added fitVids.js to enable responsive video for tables displaying media files.
* Fix bug with CSS table layout.
* Tested with WordPress 4.7.

= 1.4.4 =
Release date 24 November 2016

* Added support for search filters (drop-down lists) to filter table by category, tag, author and custom taxonomy columns. Use filters="true" to enable.
* Added Reset button next to search box to clear the current table search.
* New option 'shortcodes' to allow shortcode content to be displayed in the table (content and excerpt columns).
* Added support for WordPress embeds using [video], [audio], and [playlist] shortcodes. Use shortcodes="true" to enable.
* Added four new options to show or hide the page length drop-down, search box, results totals and pagination.
Options are 'display_page_length', 'display_search_box', 'display_totals' and 'display_pagination' and each
can be set to either 'top', 'bottom', 'both' or 'false' to hide the element.
* Fix bug with 'search on click' feature for categories, tags, etc. Search is now restricted to the column clicked on.
* Fix compatibility issue with WordPress versions below 4.5.
* Styling improvements.

= 1.4.2 =
Release date 8 November 2016

* Added 'exclude_category' option to allow entire post categories to be excluded from table. Use category slugs or IDs.
* Added 'include' option to display only the posts specified by their IDs
* Added 'author' option to restrict posts in table to a specific author (or list of authors).
* Added 'post_limit' option to limit the total number of posts displayed in the table.
* Added 'auto_width' option to enable or disable the automatic column width calculations (default: true)
* Added 'paging_type' option to allow control over pagination style used - see documentation for details
* The 'category', 'tag' and 'term' options now accept IDs as as well as slugs.
* Fixed bug with custom fields which have mixed case or uppercase keys.
* Added Italian translation (credit: Libri Ehret).

= 1.4.1 =
Release date 3 November 2016

* Added $post object as parameter to 'posts_table_custom_data' filter
* Bug fix with sort_by option

= 1.4 =
Release date 3 November 2016

* Added new 'excerpt' column to show the post excerpt. New option 'excerpt_length' sets the number of words to use for the excerpt.
* Table can now be sorted by the following: menu_order (use 'order' option set for page/post), id (sort by post ID), comment_count,
modified (last modified date), rand (random ordering), author, name (order by post slug), and type (order by post type).
* Allow custom columns to be added to table - use filter 'posts_table_custom_data_<column>' to populate custom columns
* Allow image_size to be set using one number for both width and height (e.g. image_size=50)
* Added new responsive options - responsive_control, responsive_display, and column_breakpoints. These allow more control over the
display when viewed on smaller screens. See documentation for details.
* Changed default table columns if not viewing posts
* Allow featured image column to link to the relevant post or page (use links="image" or links="all")
* Allow categories or tags to be used in links option
* The 'Show <x> entries' drop-down now includes an 'All' option to display all rows
* The 'Show <x> entries' drop-down above table now defaults to the 'rows_per_page' setting in the shortcode
* Renamed 'ajax' option to 'lazy_load'. ajax option is still supported for backwards-compatibility.
* Improved the table styling
* Fixed bug with Advanced Custom Field values for date_picker, wysiwyg and text_area fields
* Added documentation and contact support links to settings page
* Updated license validation code
* Other code improvements and bug fixes

= 1.3.1 =
Release date 23 September 2016

* Add support for all Relational fields in ACF (i.e Page Link, Post Object, Relationship, Taxonomy and User fields)
* Added Dutch translation (credit: Alex Van Haren)
* Fix bug for non-standard date formats for Date Picker fields in ACF
* Fix bug in license key activation

= 1.3 =
Release date 10 September 2016

* Added full support for Advanced Custom Fields plugin
* Added new shortcode options 'no_posts_message' and 'no_posts_filtered_message' to allow custom messages when no posts are found (English language only)
* Added new shortcode option 'date_format' to allow custom date format to be used - see documentation for examples
* Allow image_size to use format "50x50" as well as "50,50"
* Allow column widths to be specified in pixels as well as a percentage
* Updated EDD Updater which handles the plugin updates
* Updated license key code
* Fixed conflict with Hide Featured Image plugin
* Update language files
* New filters added and other minor tweaks

= 1.2.1 =
Release date 17 August 2016

* Fix bug with widths attribute that was preventing custom column widths being set

= 1.2 =
Release date 11 August 2016

* Allow custom headings to be set for columns (see documentation for examples)
* Allow all post/page content to be shown in the table. To enable this, set content_length to '-1'
* Update to version 1.10.12 of DataTables
* Changed text domain and fixed loading of translation files
* Add settings link to Plugins page
* Fix: javascript bug with 'search on click' feature
* Fix: bug preventing featured images working when AJAX enabled
* Fix: removed use filter_input() for server variables as not always reliable

= 1.1 =
Release date 23 May 2016

* Added support for featured images and new 'image_size' attribute.

= 1.0 =
Release date 20 May 2016

* Initial release.