=== DWS WordPress Core ===
Contributors: Antonius Hegyes, Dushan Terzikj, Fatine Tazi
Requires at least: 5.3
Tested up to: 5.3
Requires PHP: 7.3
Stable tag: 2.2.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

=== Changelog ===

= 2.2.0 =
* Use the Guzzle library to get external files.

= 2.1.2 =
* Bug-fixing in the local version functionality.

= 2.1.1 =
* Having an email and a phone number is now optional.

= 2.1.0 =
* Refactored the contact class
* Whitelabeling feature
* Local Functionality Template get_version major updates

= 2.0.4 =
* More wrapper classes

= 2.0.3 =
* Further extensions to the settings interface.

= 2.0.2 =
* Extended the Settings Interface

= 2.0.1 =
* Included Rewindable Generator class

= 2.0.0 =
* Code became agnostic to the options plugin used. Now supports: ACF5 Pro and CMB2.
* Fixed a bug in the admin notices class.
* Added the options framework select in the main DWS Core Settings page.
* Added which plugins have to be installed and activated depending on the selected framework.
* Updated the PUC library to v4.8.1

= 1.5.3 =
* Clear transients option.

= 1.5.2 =
* Updated ACF plugin.
* Fixed HTML parsing errors about ids with spaces.

= 1.5.1 =
* Cached ACF options definition. Huge speed improvement!

= 1.5.0 =
* Updated libraries.
* Made local code files have their own translatable domain by WPML.
* Better handling of ACF custom CSS and JS to disable or hide fields.

= 1.4.1 =
* DWS Modules as a whole now get loaded before DWS Plugins.
* Updated the ACF library

= 1.4.0 =
* Add an install notice when the DWS Core has been copied to the filesystem for the first time.
* Added reinstall button and some content in the DWS dashboard.
* Add frontend and backend support (ajax_url).
* Fixed conflict between UpdraftPlus and PluginUpdateChecker.
* Regular DWS plugins now also load files from the modules folder.

= 1.3.3 =
* Modify publish/submit action button on Custom Extensions page.
* More modular control over the core settings

= 1.3.2 =
* Added instructions to overridable templates.
* Replaced plain text areas with syntax-highlighting code areas for Global JS and Global CSS.

= 1.3.1 =
* Added en_US translations for compatibility with WPML

= 1.3.0 =
* Added compatibility with WPML (granted, by changing some WPML String Translation plugin files)
* Improved plugin data detection.

= 1.2.5 =
* Updated translations.

= 1.2.4 =
* Added global CSS and JS options.

= 1.2.3 =
* Fixed bug with admin notices.
* Temporarily removed the clearing of the target directory for DWS upgrades.

= 1.2.2 =
* Added a new class to fancy messages overrrides.

= 1.2.1 =
* No longer adds the WP timezone to ACF datetimepicker value when converting it to timestamp.

= 1.2.0 =
* Updates and installation of plugins, including dws plugins and dws modules.
* New custom permissions for the DWS menus.
* Trigger DWS installation on DWS plugins update and install.

= 1.1.0 =
* Added infrastructure for all top-level functionalities to register their GitHub repository links.

= 1.0.1 =
* Prepared folder structure for automatic updates.

= 1.0.0 =
* First official release.