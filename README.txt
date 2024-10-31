=== Piwik PRO Tag Manager Integration ===
Contributors: piwikpro, PiotrPress
Tags: Piwik PRO, Piwik, tag manager, Piwik Tag Manager, Piwik PRO Tag Manager, analytics
Requires PHP: 7.0
Requires at least: 4.7
Tested up to: 5.3.2
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

[DEPRECATED] The plugin integrates WordPress site with Piwik PRO Tag Manager, allowing to add/modify website’s tags without the need to involve IT department.

== Description ==

[DEPRECATED] We'll be removing this plugin by the end of May 2021. Use the new [Piwik PRO](https://wordpress.org/plugins/piwik-pro/) plugin instead.

The WordPress Piwik PRO Tag Manager integration plugin allows you to include both the synchronous and asynchronous tags. When triggering the tags in a synchronous way, it adds the snippets of Piwik PRO Tag Manager’s code to the HTML head section. If you trigger them asynchronously,  it places them directly after the opening body HTML tag.

All you have to do is provide your Piwik PRO Tag Manager server’s URL and the ID of the website.

_To learn more about which type of tags you should use (e.g. asynchronous or synchronous tags), read the [Tags](https://piwik.pro/docs/tag-manager-guides/tags/ "Piwik PRO Tag Manager - Tags Documentation") section in the Piwik PRO Tag Manager's documentation._

The plugin gives you also the possibility to configure the order in which the snippets will be included. To do so, you only need to insert `wp_enqueue_script` handles as a dependencies via plugin’s settings page. However, this will work only with `wp_head` and/or `wp_body` method.

A useful addition is output buffering. Owing to this function, you can use the plugin with any WordPress theme. Use it if you are not sure whether the preferred methods `wp_head` and/or `wp_body` function has been added to your theme.

You can enable the Piwik PRO Tag Manager scripts caching and control the cache refresh interval - useful when applying the Cache-Busting strategy.

You can also enable or disable the version parameter in the query string for the scripts' URLs.

= What Makes Piwik PRO Tag Manager Stand Out =

[Piwik PRO Tag Manager](https://piwik.pro/tag-manager/ "Piwik PRO Tag Manager") is a secure, self-hosted tag management system. It comes with built-in integrations with a Piwik PRO Analytics and a range of other marketing and web analytics tag templates. It is a go to solution for businesses garnering and managing large amounts of data, as well as for those committed to data privacy.

= Key characteristics =

1. **Built-in tag templates and integrations with Piwik PRO** for you to test and fire numerous tags, and to enhance your analytics reports.
2. **API Integration** with other tools, such as: A/B tests platforms (e.g. Optimizely and VWO), web analytics tools (Google Analytics, CrazyEgg, ClickTale, etc.), UX and CRO applications (intent-triggered pop-ups or feedback widgets like Qualaroo) as well tracking and remarketing pixels (Google AdWords, Facebook).
3. Deployment of **A/B Testing Tags** with both pre-set and custom tags without Flash Of Original Content.
4. **Privacy-Compliance** with even the most rigorous privacy laws: Opt-Out and Do Not Track features, Blanket Privacy for All Tags to manage sensitive marketing and web analytics tags, Data Control & Ownership, No Piggybacking to avoid 3rd party tags.
5. **White-Labeling** to match your other tools and for reselling purposes.
6. **Test & Debug Mode** to let you verify every tag in safe environment before deployment.
7. **Triggers & Conditions Library** to fire tags any place on a page or on any visitor action you desire.
8. **Built-in tag Templates** to add both custom synchronous & asynchronous HTML tags along with custom triggers and conditions.
9. A wide range of marketing & web analytics tag templates to give you full control over all the tags, and to save time and resources.
10. Event and content tracking, custom dimensions, virtual pageviews, cross-domain tracking.
11. Variables ensuring **flexibility and dynamic customization**:

* Built-In Variables, including: URL, Cookie, Document, Data Layer, Constant, Random Number; Custom Javascript Variables.
* Campaign Parameters & Mobile-based Triggers applied based only on specific campaign parameters.
* Endless Customization - using regular expressions and logical sequences to achieve expected results.


== Installation ==

= From your WordPress Dashboard =

1. Go to 'Plugins > Add New'
2. Search for 'Piwik PRO Tag Manager Integration'
3. Activate the plugin from the Plugin section in your WordPress Dashboard.

= From WordPress.org =

1. Download 'Piwik PRO Tag Manager Integration'.
2. Upload the 'piwik-pro-tag-manager-integration' directory to your '/wp-content/plugins/' directory using your favorite method (ftp, sftp, scp, etc...)
3. Activate the plugin from the Plugin section in your WordPress Dashboard.

= Once Activated =

Visit 'Settings > Piwik PRO Tag Manager Integration', add your server's URL in the 'Server URL' field, fill in the 'Website ID' field, and then decide where you would like to place the Piwik PRO Tag Manager snippet.

**Please note**

* `wp_head` is the preferred location for firing synchronous tags (e.g. for A/B testing).
* `wp_body` is the preferred location for firing asynchronous tags.

To learn more about which type of tags you should use (e.g. asynchronous or synchronous tags), read the [Tags](https://piwik.pro/docs/tag-manager-guides/tags/ "Piwik PRO Tag Manager - Tags Documentation") section in the Piwik PRO Tag Manager's documentation.

= Multisite =

The plugin can be activated and used for just about any use case.

* Activate at the site level to load the plugin on that site only.
* Activate at the network level for full integration with all sites in your network (this is the most common type of multisite installation).

== Frequently Asked Questions ==

= How do I use the wp_body function? =

Paste the following code directly after the opening `<body>` tag in your theme:
`<?php wp_body(); ?>`

= What's the difference between placing the Piwik PRO Tag Manager snippet in the wp_head or the wp_body locations? =

* `wp_head` is the preferred location for firing synchronous tags (e.g. for A/B testing).
* `wp_body` is the preferred location for firing asynchronous tags.

_To learn more about which type of tags you should use (e.g. asynchronous or synchronous tags), read the [Tags](https://piwik.pro/docs/tag-manager-guides/tags/ "Piwik PRO Tag Manager - Tags Documentation") section in the Piwik PRO Tag Manager's documentation._

= What are minimum requirements for the plugin? =

* PHP interpreter version >= 5.3
* PHP Curl extension (for cache feature).
* Cron scheduler (for cache feature).
* Apache Server (for cache and rewrite feature).
* Apache mod_rewrite module (for cache and rewrite feature).

== Screenshots ==

1. **WordPress General Settings** - Visit 'Settings > Piwik PRO Tag Manager integration', add your server's URL in the 'Server URL' field, fill in the 'Website ID' field, and then decide where you would like to place the Piwik PRO Tag Manager snippet (`wp_head` is the preferred location for firing synchronous tags – e.g. for A/B testing and `wp_head_open` is the preferred location for firing asynchronous tags).
2. **Piwik PRO Administration Settings** - Visit 'Piwik PRO > Menu > Administration > {website} > Installation' and take note of the  Website ID you want to implement into your site. Then, write that Website ID in the 'Website ID' field in your WordPress Dashboard.

== Changelog ==

= 2.2.4 =
*Release date: 22.04.2021*

* Deprecated: We'll be removing this plugin by the end of May 2021.

= 2.2.3 =
*Release date: 05.03.2020*

* Updated: Piwik PRO Tag Manager snippet.

= 2.2.2 =
*Release date: 09.04.2019*

* Updated: Piwik PRO Tag Manager screenshot.

= 2.2.1 =
*Release date: 08.03.2018*

* Fixed: Compatibility with WooCommerce.

= 2.2.0 =
*Release date: 18.07.2017*

* Added: If the `head` and/or `body` html tags exist, check them to include a snippet by output buffering method.
* Added: If AJAX, REST and/or XMLRPC requests exist, do not include a snippet by output buffering method.

= 2.1.0 =
*Release date: 21.06.2017*

* Changed ABSPATH to WP_CONTENT_DIR & WP_CONTENT_URL to support roots/bedrock (reported by pimplaatsman).
* Changed Container UUID to Website ID.
* Added support to Piwik PRO Tag Manager version < 1.16.0
* Updated snippets templates to support Piwik PRO Marketing Suite.

= 2.0.0 =
*Release date: 28.02.2017*

* Changed Container ID to UUID.
* Renamed the function `wp_body_open` to `wp_body`.
* Updated snippets templates.
* Added the cache feature.
* Added cache refreshing with wp_cron after the time interval selected.
* Added rewrite script's url feature.
* Added ver parameter to script's url query string with timestamp feature.

= 1.0.0 =
*Release date: 30.12.2016*

* First stable version of the plugin.