=== Advanced Custom Fields: Leaflet Map Field ===
Contributors: unscuzzy
Tags: acf, plugin, wordpress, leaflet
Requires at least: 4.9.6
Tested up to: 4.5
Requires PHP: 5.6
Stable tag: 1.0.0
Donate link: https://unscuzzy.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a Leaflet map field to the Advanced Custom Fields plugin.

== Description ==
This plugin adds a Leaflet map field to the Advanced Custom Fields plugin.

= Create the backend field =
You can add a leaflet map field using ACF like the Google Map field.

= Diplay the map in front-end =
Uses the classic ACF `get_field('field_name')` function to get field data (lat, lng, address & zoom).

Warning, the `get_field()` function does not display the map itself, for this, use the [leaflet js map](https://leafletjs.com/examples/quick-start/) documentation directly

= Compatibility =

This ACF field type is compatible with:
* ACF 5 only (Pro & Free)

NB: If you're using the ACF 4 Free version, you can upgrade to version 5 step by step [on this page](https://www.advancedcustomfields.com/resources/upgrade-guide-version-5/)

== Installation ==
From your WordPress dashboard
1. Visit Plugins > Add New
2. Search for “Advanced Custom Fields: Leaflet Field”
3. Activate "Advanced Custom Fields: Leaflet Field" from your Plugins page
4. Create ACF new leaflet map field from "Custom Fields" menu item

== Screenshots ==
1. Backend field settings
2. Backend field example
3. Front-end output

== Frequently Asked Questions ==
= How to display the map in front-end ? =
1. Make sure you have created a "Leaflet Map" field
2. In the template file, use the ACF classic "get_field()" function, (you can add this following code in your template page for debug)
    * `<?php var_dump( get_field('my-field-name') ); ?>`
3. Go to [leaflet map documentation](https://leafletjs.com/examples/quick-start/) and draw your 1st map!


== Changelog ==
= 1.0.0 =
* Initial Release.