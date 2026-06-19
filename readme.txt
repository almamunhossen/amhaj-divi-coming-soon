=== Amhaj Divi Coming Soon ===
Contributors: almamunhossen
Tags: coming soon, maintenance, divi, maintenance mode, auto update
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display a selected page as a Coming Soon page while allowing admins full access. Designed to work seamlessly with Divi and other page builders and update automatically from GitHub.

== Description ==

Amhaj Divi Coming Soon is a lightweight, clean, and highly efficient WordPress plugin designed to put your website into "Coming Soon" or "Maintenance" mode. It allows administrators full access to build and preview the site while redirecting public visitors to a designated Coming Soon landing page, integrates a built-in GitHub auto-updater, and features a premium settings panel.

= Key Features =
* Simple Enable/Disable Toggle (modern iOS-style switch).
* Custom Page Selector (choose any WordPress page as the landing page).
* Admin Bypass (administrators can navigate and preview all pages seamlessly).
* GitHub Auto-Updater (checks releases from GitHub, caches API queries for 12 hours for zero overhead, and allows manual checks).
* Security Hardened (strict capability checks, nonce security validation, output escaping).
* AJAX, Cron, and REST API protection (ensures background tasks and API requests are not blocked).
* Zero performance overhead.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/amhaj-divi-coming-soon` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the **Settings > Amhaj Coming Soon** screen to select your Coming Soon page and enable the mode.

== Frequently Asked Questions ==

= Does this work with themes other than Divi? =
Yes! It is fully compatible with any WordPress theme or page builder. It is named Divi Coming Soon because it was optimized to let developers use the Divi visual builder on the front end without being redirected.

= Will search engine bots be blocked? =
The plugin handles redirects safely using `wp_safe_redirect`. If you want search engines to crawl or not crawl, you can customize page indexing on the selected Coming Soon page using SEO plugins.

== Screenshots ==

1. Settings page under Settings > Amhaj Coming Soon.

== Changelog ==

= 1.0.1 =
* Settings page and redirection fixes.

= 1.0.0 =
* Initial release.
