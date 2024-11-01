=== Simple Sermon Media & Podcast===
Contributors: elimtaft,
Donate link: https://www.elitaft.com/technology/simple-sermon-media-podcast/
Tags: sermons, media, player
Requires at least: 5.4.2
Requires PHP: 7.0
Tested up to: 5.7.2
Stable tag: 1.0.13
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides media and podcast support for video and audio sermons.

=== Description ===

Note: This plugin currently only supports YouTube videos and MP3 files, but additional formats could certainly be added.

This plugin provides a "Sermons" post type that can be used to share video and audio sermons.  When a sermon post is viewed, the video and audio are displayed in a user-friendly, tabbed format for "Video," "Audio," and "Download" (which allows the download of MP3 sermons).

It also provides a podcast RSS feed with an options page for various settings for the podcast.

This currently does exactly what I need it to do, but I am very much open to suggestions and (especially) contributions by others who would like to expand it.

=== Installation ===

1. Download the sermon-media.zip file
1. Login to manage your WordPress site
1. From the WordPress Dashboard, click "Plugins."
1. Click "Add New" at the top of the page.
1. Click "Upload Plugin" at the top of the page
1. Click "Browse," and select the sermon-media.zip file
1. Click "Install Now"
1. Click "Activate"
1. Enter the settings to use for the media and podcast features.
 
=== Bonus ===

If you also install the [BLB ScriptTagger](https://www.blueletterbible.org/webtools/BLB_ScriptTagger.cfm) plugin, it can convert your Bible passages into hyperlinks that display that passage in a pop-up window when you hover over them with your mouse.

== Frequently Asked Questions ==

T.B.D.

== Upgrade Notice ==

No upgrades yet :)

== Screenshots ==

1. Example Archive View
2. Example Sermon Media Video Display
3. Example Sermon Media Audio Display
4. Example Sermon Media Audio Download
5. Example Sermon Posts Admin View
6. Example Add / Edit Sermon Post View
7. Example Options Page

=== Changelog ===

= 1.0.13 =
* 1 June 2021
* Ensuring compatibility up to WordPress 5.7.2

= 1.0.12 =
* 19 August 2020
* Fixing typos in the README

= 1.0.11 =
* 18 August 2020
* Fixing a bug with duplicate saving logic.  Updating the WordPress banner.

= 1.0.10 =
* 18 August 2020
* Fixing logical error in rewrite rules; some refactring also.

= 1.0.9 =
* 18 August 2020
* Fixing the README.txt.  AGAIN.

= 1.0.8 =
* 18 August 2020
* Fixing the README.txt so the plugin displays correctly on the WordPress plugin's site.

= 1.0.7 =
* 18 August 2020
* Attempting to fix the images so they will display on the WordPress plugins site.

= 1.0.6 =
* 18 August 2020
* Removing Dependency on Permalink Manager Lite
* Fixed some issues with saving meta fields and only displaying video / audio
  if the user entered them.

= 1.0.5 =
* 14 August 2020
* Adding nonce to form, fixing email sanitation, checking user capabilities.

= 1.0.4 =
* 14 August 2020
* Fixed undefined variable

= 1.0.3 =
* 14 August 2020
* Adding Sanitization, Escaping, and Validation to form fields
* Adding namespace
* Fixing the RSS Feed refresh
* Refactoring

= 1.0.2 =
* 12 August 2020
* Fixed type in admin field

= 1.0.1 =
* 12 August 2020
* Fixed podcast explicit value

= 1.0.0 =
* 2 August 2020
* Initial version
