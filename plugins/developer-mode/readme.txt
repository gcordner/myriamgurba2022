=== Developer Mode ===
Contributors: jesper800
Donate link: http://www.jepps.nl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: developer,client,developer mode,hide menu,admin,menu,disable updates,disable update,hide plugins,plugin
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 0.4.1.3

Limit access to the WordPress admin panel for your clients. Block functionality like updating plugins and viewing menu items for administrators, while keeping all these options for the developer users. The developer mode plugin automatically adds a developer user role, allowing you to keep in control of the entire system while making sure your clients can only use what they need.

== Description ==

If you develop WordPress websites for your clients, this is the plugin for you! Developer Mode makes it possible to easily disable certain parts of the admin panel for your clients, while keeping full control over all admin panel functionality for yourself and other developers. For example, you can hide certain menu items for non-developer users and even hide certain plugins from the plugins overview page.

The Developer Mode plugin allows your clients to be presented with a clean WordPress admin interface with only the functionality they need - and nothing more. You don't want to allow your clients to fiddle with plugins, but you probably don't want them to mess with your Advanced Custom Fields or Option Tree settings either. Let Developer Mode do the dirty work for you, and clean up your client's admin interface with just a few mouse clicks!

= Introduction =
Let's face it: a big part of the WordPress admin panel is not suited for your clients. Some of your clients might not know enough about WordPress to have as many options as you as a developer need, and some parts of your website are just not meant to be tampered with by them. This is where the Developer Mode plugin steps in: it allows you to disable certain parts of the admin functionality in the admin panel, such as updating plugins, viewing menu items and disabling specific plugins.

= Main options =
*	Hide admin menu items for non-developer users, but not for yourself
*	Hide certain plugins from the plugin menu so non-developer users can't disable them, while allowing them to enable, disable and install other plugins
*	Disable core, plugin or theme updates for non-developer users

= Feature requests =
If you have any feature requests for this plugin to allow you to clean up the admin interface for your clients even more, please drop us a message in the support forum and we will consider it for the next version! Suggestions are much appreciated!

== Frequently Asked Questions ==

= How does it work? =
The Developer Mode plugin automatically adds a new user role ("developer") to the existing WordPress user roles. You can assign yourself this role, ensuring you have all functionality that is available. As a developer, you can disable certain parts of the admin panel for administrators and other users.

= Are there any translations available? =
For websites in another language than English, the plugin has translation files included for the following languages:

*	Dutch

== Installation ==

1. Upload the folder `developer-mode` to your plugins folder
1. Activate the plugin Developer Mode through the plugins panel in the admin panel.
1. Assign yourself and any other users you like the Developer role (this can be done in one click if no users are developer yet)
1. Configure the plugin via the settings screen (Settings -> Developer Mode)

That's all there is to it!

== Screenshots ==

1. A very extensive admin panel with many options your clients don't need can be reduced to a clean admin interface with all your client needs for maintaining his website.
2. Hiding specific menu items from your clients admin interface is as easy as checking some checkboxes.
3. If you have a plugin that is essential to the core of your website, you don't want your clients disabling that plugin.

== Changelog ==

= 0.4.1.3 =

* Minor bug fixes

= 0.4.1.2 =

* Minor bug fixes

= 0.4.1.1 =

* Minor bug fixes


= 0.4.1 =

* Added the administrator capability to the developer on install and on update to ensure that plugins with out-dated capability handling will not break with the Developer Mode plugin

= 0.4 =

* Optional prevention of managing developer users by non-developer users (i.e. administrators, editors, etc. can not create, edit or delete users with the developer role)
* Added check all/uncheck all/toggle all functionality to the Admin Menu and Plugins pages
* Moved screenshots to assets folder

= 0.3.2 =

* Added functionality to automatically add newly created administrator capabilities to the developer role

= 0.3.1 =

* Tested with latest versions of WordPress and updated "Tested up to"

= 0.3 =

* Made some changes to the option structure of the plugin
* Added uninstallation feature to remove the developer role and settings
* Corrected the least required WordPress version from 3.0 to 3.1

= 0.2.1 =

* Fixed bug that caused problems during activation because of the absence of the lib/functions.php file

= 0.2 =

* Added support for disabling the admin bar on the frontend for non-developer users

= 0.1 =

* Initial release