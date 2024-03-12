=== Display Data on your site! Create Dynamic Content Templates from any form of data. Works with ACF, Pods, BuddyPress/ BuddyBoss===
Contributors: svenl77, konradS, themekraft, buddyforms, gfirem, marin250189
Tags: form submission data, display form data, dynamic templates, content templates, form content templates
Requires at least: 3.9
Tested up to: 6.1.1
Stable tag: 1.3.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get Full Control of your WordPress Post Content Layout. Display any kind of form-submitted data in the front end by using hooks or create dynamic content templates that you can use in your single view and standardize how a form submission will look in the front.

== Description ==

### Display any field type
The plugin will keep care of the different Field Types like ( Links, Categories, text fields, etc. ) and display them exactly as they should. You can decide if you only want the value gets displayed or with the field name.

### Create Dynamic Templates
Create Dynamic Templates to display values dynamically. These templates can be used in the Form Builder to overwrite the single view of posts, pages, or any other post type. 
 
[youtube https://youtu.be/sCGIIfmF9hY]
 
### In Gutenberg FSE, Block Editor
Display any form value in the Gutenberg Block Editor, or in FSE Themes. 

### Create Dynamic Content Templates with Gutenberg 
Conveniently use form fields data as dynamic data in your Gutenberg templates.

[youtube https://youtu.be/swDcSpn-psg]

### Use with any Pagebuilder like Elementor or DIVI
Use any template or field data in your prefeerd Page Builder to create dynamic single pages or a complete dynamic laqyout

### Create a list - Reorder Items via Drag and Drop
If you want to display multiple fields as a list in one place, You can reorder the list easily via drag and drop in the FormBuilder. Just move the field to the correct position in the form builder and they will get displayed exactly in this order.

### Hoook Into the Content
For the single view, you have 4 default options.
1. before the title
2. after the title
3. before the content
4. after the content.

### Global Hooks
Side wide, you can hook everywhere. Just enter the Hook name in the text fields.

### How to Display Form Data on the Front End in WordPress
[Display Your Website Data Anywhere You Choose!](https://themekraft.com/wordpress-solutions/display-form-data/)

###Extensive Documentation and Support
All code is neat, clean, and well-documented (inline as well as in the documentation). The BuddyForms Documentation with many how-to’s is following now!

If you still get stuck somewhere, our support gets you back on the right track. You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard!

== Installation ==

You can download and install BuddyForms Hook Fields using the built-in WordPress plugin installer. If you download BuddyForms manually,
make sure it is uploaded to "/wp-content/plugins/".

Activate BuddyForms Hook Fields in the "Plugins" admin panel using the "Activate" link. If you're using WordPress Multisite, you can optionally activate BuddyForms Hook Fields Network Wide.

== Frequently Asked Questions ==

= Can I display Post Meta fields =
Yes, you can display any post meta that is controled by a BuddyForms Post Form. This also works for existing post meta.You only need to create the form elemet and assign it to the meta so that the plugin iknow what type of data it need to create the output html for.

= Can I display User Meta fields =
= Can I display ACF Advanced Custom Fields=
= Can I display PODS fields =
= Can I display Custom Fields =
= Can I display Submitted Form Data=
= Can I display Form Fields =
= Can I display Form Data =
= Can I display use Hooks =
= Can I display it in php =
= Are there any restrictsions =


== Screenshots ==

1. **BuddyForms Hook Fields - FormBuilder "Field Options"** -  Hook your BuddyForms Form Fields via options.


== Changelog ==
= 1.3.15 - 21 Mar 2023 =
* Fixed issue with undefined variable.

= 1.3.14 - 30 Dec 2022 =
* Improved readme file description.

= 1.3.13 - 25 Dec 2022 =
* Fixed issue with shortcode loop.
* Tested up to WordPress 6.1.1

= 1.3.12 - 06 Nov 2022 =
* Updated download link in TGM class.
* Tested up to WordPress 6.1

= 1.3.11 - 04 Oct 2022 =
* Added auto activation when using bundle license.
* Tested up to WordPress 6.0.2

= 1.3.10 - 29 Aug 2022 =
* Fixed issue with field values output.
* Tested up to WordPress 6.0.1

= 1.3.9 - 26 May 2022 =
* Fixed vulnerability issue.
* Tested up to WordPress 6.0

= 1.3.8 - 17 May 2022 =
* Updated readme.txt

= 1.3.7 - 5 May 2022 =
* Fixed conflict between fields of different forms when displaying its value through shortcode.
* Added gutenberg block support.

= 1.3.6 - 23 Mar 2022 =
* Added new option to show Edit link in frontend.
* Added new shortcode to display single field in frontend.
* Tested up to WordPress 5.9

= 1.3.5 - 31 May 2021 =
* Hotfix: Fixed CSS issues for CPT other than posts. Eg: Product (WooCommerce).
* Hotfix: Fixed issue related with duplicate hooked media files on the Post Single View.

= 1.3.4 - 22 May 2021 =
* Fixed to do not show default thumbnail if no media was uploaded. 
* Improved thumbnail/preview support for PDF, Video, Audio (MP3) and Compressed files on the Upload and File fields.
* Tested up to WordPress 5.7

= 1.3.3 - 8 Mar 2021 =
* Fixed issue related with hooked fields on the single view list.
* Added improvements on the integration of Upload and File fields.
* Tested up 5.6.2

= 1.3.2 - 2 Nov 2020 =
* Fixed: Show upload field value.

= 1.3.1 - 23 April 2020 =
* Removed the limitation of the hook tab to appear in all form elements.
* Added a custom post type to handle the templates.
* Added a security to make all template private.
* Getting the form element output form buddyforms to show the table values.

= 1.3.0 - 27 March 2020 =
* Added option to use a page as template to customize the single post view with form shortcodes.

= 1.2.6 30 Sept 2019 =
* Fixed the output for the elements Category, Upload and File.

= 1.2.5 30 Sept 2019 =
* Fixed the option to output the content after/before the Title.

= 1.2.4 19 Sept 2019 =
* Added support for the File element.

= 1.2.3 -  Mar. 02 2019 =
* Freemius SDK Update

= 1.2.2 =
* Remove create function to use closures.

= 1.2.1 =
* Added a new option to display all form elements as table on the single under the post content

= 1.2 =
* Added Freemius Integration
* Fixed and issue with the dependencies check. The function tgmpa does not accepted an empty array.
* Added user_website as supported field

= 1.1.9.2 =
* Fixed and issue with the dependencies check. The function tgmpa does not accepted an empty array.

= 1.1.9.1 =
* Fixed an issue with the dependencies management. If pro was activated it still ask for the free version. Fixed now with a new default BUDDYFORMS_PRO_VERSION in the core to check if the pro is active.

= 1.1.9 =
* Add dependencies management with tgm

= 1.1.8 =
* fixed some smaller issues
* fixed some notice of undefined index

= 1.1.7 =
* Make it work with the latest version of buddyforms. the buddyforms array has changed so I adjust the code too the new structure
* Limit the hook options to supported form elements

= 1.1.6 =
* Make it work with the latest version of BuddyForms. the BuddyForms array has changed so I adjust the code too the new structure
* limit the hook options to supported form elements

= 1.1.5 =
* change the url to BuddyForms.com
* move the options to the new section addons

= 1.1.4 =
* Fixed a issue with the checkbox form element. Props to Thomas for the detailed issue Report.

= 1.1.3 =
* Reduce the query for a better performance
* Clean up the code

= 1.1.2 =
* Fixed some bugs reported by users.

= 1.1 =
* Spelling Corrections

= 1.0 =
* final 1.0 version
