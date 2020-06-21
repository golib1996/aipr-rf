=== Smart Cleanup Tools ===
Version: 4.5
Requires at least: 3.6
Tested up to: 4.6

Powerful and easy to use plugin for cleaning the database from old and unused records, transient cache and overhead. Supports multisite mode.

== Installation ==
= Requirements =
* PHP: 5.2.4 or newer
* WordPress: 3.6 or newer
* WordPress Cron Scheduler

= Basic Installation =
* Upload the `smart-cleanup-tools` folder to your `wp-content/plugins` directory.
* Login to your website administration, go to the Plugins Panel and activate Smart Cleanup Tools plugin.
* Plugin has own top level panel in the WordPress admin menu under name 'Smart Cleanup'.

== Frequently Asked Questions ==
= Does plugin works with WordPress MultiSite installations? =
Yes.

= Can I translate plugin to my language? =
Yes. POT file is provided as a base for translation. Translation files should go into Languages directory.

= What is used for scheduled jobs? =
Plugin uses WordPress own Cron Scheduler.

== Changelog ==
= 4.5 / 2016.07.13 =
* Added: cleanup tool for removing WooCommerce sessions in options table
* Updated: queries for transient and site transient cleanup tools

= 4.4 / 2015.12.27 =
* Added: removal tool for removing post meta records with no value
* Added: removal tool for removing comment meta records with no value
* Updated: several small updates to some admin side styling
* Deleted: removed cleanup tool for empty post meta removal
* Deleted: removed cleanup tool for empty comment meta removal
* Fixed: missing several translation strings in POT file

= 4.3.3 / 2015.08.24 =
* Fixed: problem with displaying details for some cleanup tools

= 4.3.2 / 2015.04.13 =
* Fixed: memory exhaused error with some cleanup operation
* Fixed: missing WP internal cache flush for some cleanups

= 4.3.1 / 2015.04.05 =
* Updated: minor changes to the cleanup worker class
* Fixed: warning for missing class name with scheduled cleanups

= 4.3 / 2014.12.08 =
* Updated: posts draft cleanup tool moved to removal panel
* Updated: better cleanup size estimation for some tools
* Updated: several minor visual changes and improvements
* Fixed: wrong ID for the toolbar activity checkbox settings
* Fixed: saving toolbar activity option for multisite

= 4.2 / 2014.08.24 =
* Added: WordPress toolbar quick cleanup access menu
* Added: toolbar quick cleanup: remove all rewrite rules
* Added: toolbar quick cleanup: remove all transients
* Added: log cleanup job run through Smart Security Tools
* Fixed: missing several translation strings in POT file
* Fixed: several styling issues related to multisite mode
* Fixed: several minor styling issues with the UI

= 4.1 / 2014.05.08 =
* Added: reset tool to remove posts edit lock keys
* Added: cron jobs can use selected reset tools too
* Added: some jobs now have additional popup information
* Updated: internal control for tools available for scheduler
* Updated: many changes to the plugin core loader class
* Updated: jQueryUI 1.10.4
* Updated: compatibility with WordPress 3.9
* Fixed: several styling issues with WordPress 3.9

= 4.0 / 2013.09.18 =
* Added: one click cleanup from plugin front page
* Added: plugin now has own admin menu and submenus
* Added: panel for filtered data removal tools
* Added: removal - attachments with missing files
* Added: removal - attachments with missing parent posts
* Added: removal - attachments that are not attached
* Added: removal - missing post types posts
* Added: removal - missing taxonomies terms
* Added: removal - unassigned taxonomy terms
* Added: removal - all available taxonomy terms
* Improved: many improvements to the plugin interface
* Improved: more information added to various panels
* Improved: removal of Akismet data removes error records
* Changed: logs deletion moved from reset to logs panel
* Changed: comments related reset tools moved to removal
* Changed: main execution moved into own worker class
* Changed: admin interface controls moved into own class
* Updated: jQueryUI Timepicker 1.4
* Fixed: detection of the network specific database tables
* Fixed: deletion of log files for network

= 3.2 / 2013.08.02 =
* Added: tool to remove terms not connected to any taxonomy
* Improved: scheduled job editor shows proper creation messages
* Fixed: problem with use of admin side only functions

= 3.1 / 2013.06.01 =
* Added: tool to remove all oEmbed cached post meta records
* Improved: cleanup results calculation of removed records
* Changed: database overhead tool is now always active by default
* Fixed: problem with removal of orphaned post revisions records

= 3.0 / 2013.05.25 =
* Added: tool to remove all orphaned relationships records
* Added: tools for import and export of plugin settings
* Added: show detailed popup analysis results for some of the tools
* Added: options to fully disable some of the cleanup tools
* Added: option to show cleanup summary for all active tools
* Added: option to auto hide all tools that are inactive
* Added: estimated size for the network mode tools
* Improved: estimate size calculation for most of the tools
* Improved: expanded descriptions for some of the tools
* Improved: look and fill for layouts of all plugin panels
* Improved: set tools dropdowns disabled if tools is disabled
* Updated: jQueryUI 1.10.3
* Updated: jQueryUI Timepicker 1.3
* Fixed: several typos and some descriptions errors

= 2.5 / 2013.04.21 =
* Added: tool to remove expired transient records
* Added: reset tool to remove akismet comments log
* Added: most tools display estimated records size
* Improved: many small tweaks to the cleanup tools
* Improved: expanded PDF user guide with more information
* Updated: jQueryUI 1.10.2
* Updated: jQueryUI Timepicker 1.2.2
* Updated: jQueryUI Multiselect 1.14pre
* Changed: WordPress 3.2 is no longer supported

= 2.1 / 2013.02.08 =
* Added: tools to remove GravityForms spam and trash records
* Added: filters and actions to hook into cleanup process
* Improved: few minor changes and code cleanup
* Improved: few more changes to the plugin styles
* Fixed: some obsolete debug code still included

= 2.0 / 2013.01.07 =
* Added: tools to remove draft and auto-draft posts records
* Added: tools to remove empty commentmeta and usermeta records
* Added: tool to remove all orphaned posts revisions
* Added: tool to remove all unapproved comments
* Added: reset tool to remove all pingback comments
* Added: reset tool to remove comments user agent data
* Added: scheduler for cleanup jobs: run once or repeatable
* Added: scheduler for cleanup jobs: any combination of tools
* Added: log into file: settings and panel for display log files
* Added: log into file: sql queries for run and checkup
* Added: log into file: reports for the cleanup executions
* Added: some tools support selection of post type for cleanup
* Added: context help panel with plugin important links
* Improved: cleanup classes optimization to use less code
* Improved: changed order for some of the cleanup tools
* Improved: few changes to the plugin styles
* Fixed: revisions removal was not counting closed posts
* Fixed: admin side path problem in some cases

= 1.0 / 2012.12.28 =
* First release
