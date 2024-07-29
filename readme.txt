=== BW Coupon ===
Contributors: veto
Tags: coupon, pdf, products, woocommerce
Requires at least: 4.7
Tested up to: 6.6.1
Stable tag: 1.5.3
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
WooCommerce Seller can create PDF coupons for sale. Customer will get an attached PDF coupon after purchase the coupon.

== Description ==

WooCommerce Seller can create PDF coupons for sale. Customer will get an attached PDF coupon after purchase the coupon.

Homepage: https://github.com/myridia/bw-coupon


 
= Plugin/ Theme Support =
*At this time the plugin out of the box supports also links to settings pages of some bbPress 2.x specific plugins:*
 
* Plugin: ["GD bbPress Attachments" (free, by Dev4Press)](http://wordpress.org/extend/plugins/gd-bbpress-attachments/)
* Plugin: ["bbPress Post Toolbar" (free, by Jason Schwarzenberger)](http://wordpress.org/extend/plugins/bbpress-post-toolbar/)
* Plugin: ["bbPress Antispam" (free, by Daniel Huesken)](http://wordpress.org/extend/plugins/bbpress-antispam/)
* Plugin: ["bbPress reCaptcha" (free, by Pippin Williamson)](http://wordpress.org/extend/plugins/bbpress-recaptcha/)
* Plugin: ["bbPress Moderation" (free, by Ian Haycox)](http://wordpress.org/extend/plugins/bbpressmoderation/)
* Plugin: ["bbPress2 BBCode" (free, by Anton Channing + bOingball + Viper007Bond)](http://wordpress.org/extend/plugins/bbpress-bbcode/)
* Plugin: ["bbPress2 Shortcode Whitelist" (free, by Anton Channing)](http://wordpress.org/extend/plugins/bbpress2-shortcode-whitelist/)
* Plugin: ["bbConverter" (free, by anointed + AWJunkies)](http://wordpress.org/extend/plugins/bbconverter/)
* Plugin: ["WP SyntaxHighlighter" (free, by redcocker)](http://wordpress.org/extend/plugins/wp-syntaxhighlighter/)
* *Your plugin/ theme? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*
 
= Special Features =
* Not only supporting official bbPress 2.x sites ALSO third-party and user links - so just the whole bbPress 2.x ecosystem :)
* Link to downloadable German language packs - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* Link to official German bbPress forum - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* *NOTE:* I would be happy to add more language/locale specific resources and more useful third-party links - just contact me!
 
= Localization =
* English (default) - always included
* German - always included
* .pot file (`bbpaba.pot`) for translators is also always included :)
* *Your translation? - [Just send it in](http://genesisthemes.de/en/contact/)*
 
Credit where credit is due: This plugin here is inspired and based on the work of Remkus de Vries @defries and his awesome "WooThemes Admin Bar Addition" plugin.
 
[A plugin from deckerweb.de and GenesisThemes](http://genesisthemes.de/en/)
 
= Feedback =
* I am open for your suggestions and feedback - Thank you for using or trying out one of my plugins!
* Drop me a line [@deckerweb](http://twitter.com/#!/deckerweb) on Twitter
* Follow me on [my Facebook page](http://www.facebook.com/deckerweb.service)
* Or follow me on [+David Decker](http://deckerweb.de/gplus) on Google Plus ;-)
 
= More =
* [Also see my other plugins](http://genesisthemes.de/en/wp-plugins/) or see [my WordPress.org profile page](http://profiles.wordpress.org/users/daveshine/)
* Tip: [*GenesisFinder* - Find then create. Your Genesis Framework Search Engine.](http://genesisfinder.com/)
 
== Installation ==
 
1. Upload the entire `bbpress-admin-bar-addition` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your admin bar and enjoy using the new links there :)
4. Go and manage your forum :)
 
== Frequently Asked Questions ==
 
= Does this plugin work with newest WP version and also older versions? =
Yes, this plugin works really fine with WordPress 3.3!
It also works great with WP 3.2 - and also should with WP 3.1 - but we only tested extensively with WP 3.3 and 3.2. So you always should run the latest WordPress version for a lot of reasons.
 
= How are new resources being added to the admin bar? =
Just drop me a note on [my Twitter @deckerweb](http://twitter.com/deckerweb) or via my contact page and I'll add the link if it is useful for admins/ webmasters and the bbPress community.
 
= How could my plugin/extension or theme options page be added to the admin bar links? =
This is possible of course and highly welcomed! Just drop me a note on [my Twitter @deckerweb](http://twitter.com/deckerweb) or via my contact page and we sort out the details!
Particularly, I need the admin url for the primary options page (like so `wp-admin/admin.php?page=foo`) - this is relevant for both, plugins and themes. For themes then I also need the correct name defined in the stylesheet (like so `Footheme`) and the correct folder name (like so `footheme-folder`) because this would be the template name when using with child themes. (I don't own all the premium stuff myself yet so you're more than welcomed to help me out with these things. Thank you!)
 
= There are still some other plugins for bbPress 2.x out there why aren't these included by default? =
Simple answer: The settings of these add-ons are added directly to the bbPress main settings page and have no anchor to link to. So linking/ adding is just not possible.
 
== Screenshots ==
 
1. bbPress Admin Bar Addition in default state (running with WordPress 3.3 here)
2. bbPress Admin Bar Addition in action - primary level (running with WordPress 3.3 here)
3. bbPress Admin Bar Addition in action - a secondary level (running with WordPress 3.3 here)
 
== Changelog ==
 
= 1.3 =
* Added plugin support for "bbPress Antispam" (free, by Daniel Huesken)
* Added plugin support for "bbPress Moderation" (free, by Ian Haycox)
* Added plugin support for "WP SyntaxHighlighter" (free, by redcocker)
* Minor code/ code documenation tweaks
* Updated readme.txt file - added new "Toolbar" wording introduced with WordPress 3.3 (formerly known as the Admin Bar)
* Updated German translations and also the .pot file for all translators!
* Added banner image on WordPress.org for better plugin branding :)
 
= 1.2 =
* Added plugin support for "bbConverter" (free, by anointed + AWJunkies)
* Added new external resource - "Hooks, Filters and Components for bbPress 2.0" at etivite.com
* Added new external resource - "Getting Started with bbPress" by Smashing Magazine
* Fixed display of first-level icon on mouse-hover with WordPress 3.3 - props to [Dominik Schilling](http://wpgrafie.de/) [@ocean90](http://twitter.com/#!/ocean90) for great help with the CSS!
* Updated the screenshots with fixed first-level icon
* Updated and improved readme.txt file
* Updated German translations and also the .pot file for all translators!
* Now I'd call this some fully optimized release - enjoy :-)
 
= 1.1 =
* Added link to topic tag "bbpress-plugin" in the official bbPress Forum
* Corrected a wrong link (free WP.org forum)
* Minor code tweaks
* Fixed some ugly typos (Mmh, happens sometimes...)
* Updated German translations and also the .pot file for all translators!
 
= 1.0 =
* Initial release
 
== Upgrade Notice ==
 
= 1.3 =
Added plugin support for 3 more third-party plugins. A few minor code/ documentation tweaks, updated readme.txt file and also updated .pot file for translators together with German translations.
 
= 1.2 =
Added plugin support for bbConverter as well as 2 new resources. Fixed first-level icon display in WP 3.3. Updated readme.txt file, screenshots and also .pot file for translators together with German translations.
 
= 1.1 =
Added link to topic tag "bbpress-plugin" in official forum. Corrected a wrong link and added minor code tweaks, also fixed some ugly typos. Updated .pot file for translators and German translations.
 
= 1.0 =
Just released into the wild.
 
== Translations ==
 
* English - default, always included
* German: Deutsch - immer dabei! [Download auch via deckerweb.de](http://deckerweb.de/material/sprachdateien/bbpress-forum/#bbpress-admin-bar-addition)
 
*Note:* All my plugins are localized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization" plugin](http://wordpress.org/extend/plugins/codestyling-localization/) and for validating the ["Poedit Editor"](http://www.poedit.net/).
 
== Additional Info ==
**Idea Behind / Philosophy:** Just a little leightweight plugin for all the bbPress Forum managers out there to make their daily forum admin life a bit easier. I'll try to add more plugin/theme support if it makes some sense. So stay tuned :).
 
== Credits ==
* Thanx to [Dominik Schilling](http://wpgrafie.de/) [@ocean90](http://twitter.com/#!/ocean90) for great help with the CSS for the first level icon in WordPress 3.3!
