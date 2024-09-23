=== Weaver Show Posts ===
Plugin Name: Weaver Show Posts
Plugin URI: http://weavertheme.com/plugins/
Author URI: http://weavertheme.com/about/
Contributors: wpweaver
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: weaver theme, settings, save, subthemes
Text Domain: show-posts
Requires at least: 7.2
Tested up to: 6.3
Stable tag: 1.8.1

== Description ==

This plugin provides a very flexible way to show posts anywhere on a site using a shortcode. It works with any theme. Weaver Show Posts is by far the most flexible with an easy to use interface to select which posts to show.

Note: this plugin was formerly named ATW Show Posts. There is no difference in functionality between the versions.

= Easy to select which posts to show =

There are several WordPress plugins that allow you to display selected posts via shortcode on your pages, other posts, or in widgets.

This plugin provides a powerful interactive admin page that allows you to specify exactly which posts you want displayed. The specifications are called a "filter", and you can define as many filters as you need to display different posts via the shortcode. Includes support for custom post types and taxonomies.


= Style of Displayed Posts =

Weaver Show Posts will normally display posts using its own basic display functions. These can be easily styled to match the rest
of your site by defining Custom CSS rules in the Custom CSS option.

But Weaver Show Posts can go way beyond that. For many Themes, you can elect to use the native Theme Post formatting function.
The posts displayed by Show Posts will match other posts displayed by your theme. If you are using the Weaver Xtreme or Weaver II
themes, there is even more integrated display of posts.

= Show Posts in a Text Widget =

You can add [show_posts] to the standard Text Widget. If your theme or other plugin doesn't add this capability, Weaver Show Posts
includes an option to allow the Text Widget to support shortcodes.

= The Shortcode =

The form of the shortcode is:

[show_posts filter=filter-name]


== Installation ==

Please use the WordPress Plugins:Install page to install this plugin.

== Frequently Asked Questions ==

= How do I specify which posts to display =

1. Define a filter that selects which posts you want. This can be by category, tag, date, and many other options.
2. Add the shortcode [show_posts] wherever you want the posts to display.


== Changelog ==
= 1.8.1 =
* Fix: multisite permissions

= 1.8 =
* Fix: string validation, translation name

= 1.7 =
* Update: Wp 6.2 Compatibility

= 1.6 =
* Update: WP 6.1 compatibility message

= 1.5.2 =
* Update: WP 5.7 compatibility message
* Fix: Nonce message

= 1.5.1 =
* Update: WP 5.6

= 1.5 =
* Update: compliance with WP 5.3

= 1.4 =
* Never released - skipped to get show posts and show sliders at same revision level

= 1.3.18 =
* New: added 'wvr_show_posts_no_posts' filter

= 1.3.17 =
* New: support for Absolute Weaver

= 1.3.16 =
* Fix: clear styling issue for 3 column display

= 1.3.15 =
* Fix: Don't add schema.org to posts when in a post slider for Weaver Xtreme Schema Tags

= 1.3.14 =
*Tweak - added compatibility tweak for Weaver Xtreme schema tags

= 1.3.13 =
* WP 4.9 compatibility update
* Fix: Changed Featured Image links to point to single page view of post and not media lib image

= 1.3.12 =
* WP 4.8 compatibility update
* Minor tweaks

= 1.3.11 =
* Minor optimization of custom CSS

= 1.3.10 =
* WP 4.7 compatibility tested to update
* Minor fix

= 1.3.9 =
* Fix: Version number problems

= 1.3.8 =
* Fix: was using Weaver Xtreme Plus library. Caused issue with customizer.

= 1.3.7 =
* Update: compatibility version for WP 4.6
* New: Show Posts Widget - use with page builders!

= 1.3.6 =
* Fix: Show Posts Help document had missing information on posts_per_page
* Fix: changed styling from .content-2-col to .atw-content-2-col
* New: "Menu/Custom Post Order" sorting

= 1.3.5 =
* Fix: issue with no_top_clear show_posts arg
* Tweak: removed overflow:hidden to .atw-show-posts - compatibility issues

= 1.3.4 =
* New - added don't clear:both option for start of list
* Tweak - added overflow:hidden to .atw-show-posts

= 1.3.3 =
* Fix: 1.3.2 tweak was incorrect, fixed

= 1.3.2 =
* Tweak: better the_content compatibility
* Change: text domain changed to 'show-posts'

= 1.3.1 =
* Tweak: compatibility with the_content and some other themes

= 1.3 =
* Update to WP 4.3 compatibility (no changes)

= 1.2.4 =
* New: Duplicate Filter Definition
* Fix: past year filter value

= 1.2.2 =
* Fix: changed the multi-column handling. The <span> rule removed completely for different approacly. Multi-column
layout should now work for both standard and n-th child styling.

= 1.2.1 =
* Tweak: change multi-column post display layout to <span> instead of <div> to allow n-th child to work
* Tweak: add rule for phones and small tablets for multi-column and single column post display depending on device

= 1.2 =
* Change: Changed name from Weaver Show Posts to Weaver Show Posts
* New: Added Save/Restore Filter Settings

= 1.1.1 =
* Fixed return key behavior on show sliders tab

= 1.1 =
* Fixed Multi-Site admin menu

= 1.0.7 =
* Changed to inline CSS over the dynamically generated CSS

= 1.0.6 =
* More support for Weaver X
* Removed experimental feature

= 1.0.5 =
* Added support for Weaver X

= 1.0.4 =
* Added support for sliding pagers for [ show_sliders ] Posts sliders

= 1.0.3 =
* Fixed show='' (blank) to work with Weaver correctly
* Fixed wording on the "By Post Slug" option
* Fixed "Enter/Submit" on "text" input

= 1.0.2 =
* Revised plugin description
* Fixed bug with blank show slider filter specification
* Added "No posts found." message if no posts are found.
* Fixed bug with shortcode support for text widgets


= 1.0 & 1.0.1 =
* The need for 1.0.1 was because the 1.0 upload to wordpress.org failed.

= 0.9 =
* First release.
