=== GravityWP - List Datepicker ===
Contributors: gravitywp
Tags: gravity forms
Requires at least: 5.0
Tested up to: 6.1
Requires PHP: 7.0
Stable tag: 2.0.8
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds a datepicker input to the Gravity Forms List field.

== Description ==

The List Datepicker Add-on for Gravity Forms allows you to add a column of datepicker inputs to a List Field.

= Features =
* Add a datepicker input to a List Field column.

== Installation ==

Upload the plugin files to the `/wp-content/plugins/gravitywp-list-datepicker` directory, or install the plugin through the WordPress plugins screen directly.

== Changelog ==

= 2.0.8 =
- Fix another initialization issue for Gravity Forms 2.7+.

= 2.0.7 =
- Fix initialization issue for Gravity Forms 2.7+.

= 2.0.6 =
- Update dependencies to fix a security issue in a third party library.
- Fix translations for placeholders when rows are added.

= 2.0.5 =
* Accessibility improvements.
* Fix compatibility issue with other plugins adding rows.

= 2.0.4 =
* Fix incorrect version number.

= 2.0.3 =
* Fix PHP error on plugin settings screen.
* Add icon to settings screen.

= 2.0.2 =
* Fix Datepicker settings not instantly showing/hiding. 

= 2.0.1 =
* Fix default date not initialing on new row.
* Fix some date formats not working on single column lists.

= 2.0 =
* Fully rewritten version of the discontinued original "Date Picker in List Fields for Gravity Forms" addon by Adrian Gordon.
* It should be backwards compatible with the original plugin settings for multiple column list fields. We are not sure about the single column list fields, as it seemed they were not supported by the original plugin.
* Supports Gravity Forms 2.5+ new frontend markup. Note: Legacy markup (GF 2.4 and earlier) is not supported.
