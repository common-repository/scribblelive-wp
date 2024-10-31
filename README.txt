=== ScribbleLive WP ===
Contributors: mghali, scribblelive, reshiftmedia
Tags: scribble, scribblelive, pinboard, timeline, liveblog, post, article
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: 1.1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The ScribbleLive WP Plugin unlocks added SEO benefits for your ScribbleLive-powered content experiences.

== Description ==

ScribbleLive’s all-in-one content marketing platform combines predictive analytics with content planning, creation, and distribution to deliver optimized business results. The platform's strengths include real-time content publishing, discovery of high impact articles and opinions, influencer analysis, social curation and moderation, content-centric analytics, and unique interactive experiences from pinboards to timelines and polls.

The ScribbleLive WP Plugin unlocks added SEO benefits while maintaining the flexibility and ease-of-use of the ScribbleLive platform.

Features:

*   Embed ScribbleLive content with easy to use shortcode.
*   Adds SEO-friendly versions of ScribbleLive embedcodes.
*   Advanced SEO tools for better control over rel=nofollow link attributes.
*   Allows users with filtered html restrictions to embed ScribbleLive embedcodes in posts, pages and custom post types .
*   Fully cached API calls for best performance.
*   Developer filters to manage and edit cache settings.

== Installation ==

1. Upload `scribble-live-wp` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use [scribble ]

This plugin adds search friendly `[scribble]` shortcode.

Usage and Examples:

`[scribble id="1234" type="board" theme="4567" ]`
The shortcode has three attributes that it supports:

* id: This is the ID of the event.
* type: This attribute accepts the following values `board`, `event`, `timeline`, `post`, `article`
* theme: This is the theme id for the embed or thread id for article embeds.

or

`[scribble src="/board/1234/4567" ]`
The shortcode also supports the data-src attribute on the official ScribbleLive embedcode.

== Frequently Asked Questions ==

= Where can I get an API key from? =

The API key can be obtained from the ScribbleLive API page. https://client.scribblelive.com/client/API.aspx

= Is it possible to set nofollow on external links? =

Yes, the nofollow attribute mode setting allows advanced SEO controls for internal and external links. Below is a breakdown for each value’s function:

A. Do not set: This function would ignore the regex and none of the links are manipulated.
B. Whitelist: All links will have rel=nofollow with the exception of links that matched against the regex rule provided in addition to relative and current site’s domain links.

= What is the nofollow REGEX Rule field use for? =

This field contains the regex rule used to test against. A generic regex example for multiple domains can be:
`/example.net|example.com/i`

= Do we need to set current sites domain in the white list regex? =

No, current site domain is automatically added to the nofollow white list.

== Screenshots ==

1. ScribbleLive WP settings options.
2. The nofollow attribute mode setting allows advanced seo controls for internal and external links.

== Changelog ==

= 1.1.0.0 =
* Security update.
* Remove deprecated functions.

= 1.0.9.6 =
* Security update.
* Test support for Wordpress 4.6.1.

= 1.0.9.5 =
* Add extra sanitization and security update.

= 1.0.9.3 =
* Add extra sanitization and security update.
* Test support for Wordpress 4.6.

= 1.0.9.2 =
* Support for SSL enabled sites.
* Add support for Wordpress 4.4.1.

= 1.0.9.1 =
* Update README.txt
* Add screenshot.

= 1.0.9 =
* Remove donate link from wordpress.org.

= 1.0.8.1 =
* Rename default file to be same as slug.

= 1.0.8 =
* Add wordpress.org assets folder.
* Add scribbleLive as a contributor.
* Publish plugin to wordpress.org repo.

= 1.0.7.1 =
* Hot fix account for &amp; in embed code.

= 1.0.7 =
* Add support for article embeds.

= 1.0.6 =
* Fix missing esc_attr.

= 1.0.5 =
* Load admin class only in admin panel.

= 1.0.4 =
* Add an extra wp_kses_post to output.
* More string and attribute output escaping.
* Fix notice link.

= 1.0.3 =
* Use wp_kses_post to cleanup output.
* Add support for single post.
* Minor bug fixes.

= 1.0.2 =
* Escape translation functions before display.
* Validate API query parameters.
* Set API transient before error check.

= 1.0.1 =
* Add ScribbleLive embed code to short code converter.
* Standardize shortcode to use `[scribble]` with fallback to `[scribblelive]`.
* Improve api availability and reduce max limit to 75.

= 1.0 =
* First version of scribblelive wp.
* Adds search friendly shortcodes for scribble embeds.


== Upgrade Notice ==

= 1.0.7.1 =
* Hot fix account for &amp; in embed code.

= 1.0.7 =
* Add support for article embeds.

= 1.0.6 =
Security update.

= 1.0.5 =
Security admin class.

= 1.0.4 =
Security and bug fixes.

= 1.0.3 =
Security update and single post support.

= 1.0.2 =
Security update and minor cache changes.

= 1.0.1 =
Add embed code converter, improve reliability and update shortcode.

= 1.0 =
First version of scribblelive wp adds search friendly shortcodes for scribble embeds.

