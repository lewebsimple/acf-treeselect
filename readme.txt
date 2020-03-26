=== Advanced Custom Fields: Tree Select ===
Contributors: Pascal Martineau <pascal@lewebsimple.ca>
Tags: acf, treeselect, field
Requires at least: 3.5
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hierarchical select field for ACF.

= Compatibility =

This ACF field type is compatible with:
* ACF 5

== Installation ==

1. Copy the `acf-treeselect` folder into your `wp-content/plugins` folder
2. Activate the Advanced Custom Fields: Tree Select plugin via the plugins admin page
3. Create a new field via ACF and select the Tree Select type
4. Please refer to the description for more info regarding the field type settings

== Changelog ==

= 0.2.1 =
* Add composer.json

= 0.2.0 =
* Values hierarchy defined using parent, value and label
* Field option to allow parent or force last level selection
* Dynamically show / hide select inputs based on user selection
* Return formatted values as unordered list, span elements or array

= 0.1.0 =
* Initial Release.
