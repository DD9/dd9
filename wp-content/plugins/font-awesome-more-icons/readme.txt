=== Font Awesome More Icons ===
Contributors: jr00ck
Plugin URI: http://blog.webguysaz.com/font-awesome-more-icons-wordpress-plugin/
Donate link: http://blog.webguysaz.com/donate/
Tags: icons, font-awesome, font-awesome-more, fontstrap, font icon, UI, icon font, bootstrap
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 3.5
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html


Easily use the Font Awesome icons in WordPress but with MORE icons and MORE features using HTML, shortcodes, or TinyMCE plugin.

== Description ==

Font Awesome More (Fontstrap) provides easy use of all icons from the [Font Awesome](http://fortawesome.github.io/Font-Awesome/) set, but with [MORE icons](http://blog.webguysaz.com/font-awesome-more-fontstrap-icon-additions/) from the [Fontstrap](http://gregoryloucas.github.io/Font-Awesome-More/) extension set and with MORE features. The icons are infinitely scalable and screen reader compatible.

A full list of all 400+ icons is available here:

[Font Awesome icons](http://fortawesome.github.io/Font-Awesome/icons/)  
[Font Awesome More (Fontstrap) icons](http://blog.webguysaz.com/font-awesome-more-icon-list-plugin-version-3-3/)

To use any of the Font Awesome More icons on your WordPress site you have three options:

= HTML Option =

All code examples on the Font Awesome site apply: [http://gregoryloucas.github.io/Font-Awesome-More/#examples](http://gregoryloucas.github.io/Font-Awesome-More/#examples)

**Examples**

WordPress icon <i class="icon-wordpress"></i>

`<i class="icon-wordpress"></i>`

Google icon 2x size

`<i class="icon-google icon-2x"></i>`

Google Chrome icon large size

`<i class="icon-chrome icon-large"></i>`

= Shortcode Option =

Don't want to worry about HTML tags?  You can use a shortcode in your posts, pages and even widgets to display a Font Awesome More icon.

*Note:* In my plugin (as of 3.5), the "icon-" prefix is no longer needed in shortcode attributes. This is in preperation for the [Font Awesome 4.0](http://blog.fontawesome.io/2013/06/04/upcoming-changes-in-font-awesome-3.2-and-4.0/) release that will change the prefixes. Therefore, I highly recommend that you only use the shortcode options for icons, and not HTML, so that the plugin can handle the prefixes for you and your icons will not break when Font Awesome 4.0 comes out.


WordPress icon

`[icon name=wordpress]`

Google icon 2x size

`[icon name=google size=2x]`

Google Chrome icon with title and no space after icon

`[icon name=chrome title="Google Chrome" space=false]`

PayPal icon using shortcode within PHP instead of using the HTML option (e.g. within your theme/template files)

`<?php echo do_shortcode('[icon name=paypal]'); ?>`

= New Shortcode Options since 3.5 =

Now you can turn off the automatic spacing after an icon with "space=false". See screenshots for an example of results.

In preperation for [Font Awesome 4.0](http://blog.fontawesome.io/2013/06/04/upcoming-changes-in-font-awesome-3.2-and-4.0/), you can also leave off the "icon-" prefix on your shortcode options, as that prefix will be changing. The plugin will automatically add the appropriate prefix for you.

Evernote icon with no trailing space (note no "icon-" prefix)

`[icon name=evernote space=false]`

= New Shortcode Options since 3.4 =

Now you can easily set the size and a title/alt text to icons within shortcodes. Size options are large, 2x, 3x, or 4x.


WordPress icon (large size)

`[icon name="wordpress" size="large"]`

Google icon (3x size)

`[icon name="google" size="3x"]`

Google Chrome icon (4x size with title text)

`[icon name="chrome" size="4x" title="Use Google Chrome"]`


= Credits =

* The Font Awesome & Font Awesome More (Fontstrap) font is licensed under the [SIL Open Font License](http://scripts.sil.org/OFL).

* Font Awesome & Font Awesome More (Fontstrap) CSS, LESS, and SASS files are licensed under the [MIT License](http://opensource.org/licenses/mit-license.html).

* The Font Awesome & Font Awesome More (Fontstrap) pictograms are licensed under the [CC BY 3.0 License](http://creativecommons.org/licenses/by/3.0/).

* [Font Awesome](http://fortawesome.github.com/Font-Awesome) is a product by Dave Gandy

* [Font Awesome More](http://gregoryloucas.github.io/Font-Awesome-More/) (Fontstrap) is a product of Gregory Loucas.

* The rights to each pictogram in the social and corporate extensions are either trademarked or copyrighted by the respective company.

* This plugin is based off of [Font Awesome Icons](http://rachelbaker.me/font-awesome-icons-wordpress-plugins/) by Rachel Baker.

= Author =

*   [Web Guys](http://webguysaz.com)

= Icons =

[Font Awesome icons](http://fortawesome.github.io/Font-Awesome/icons/)  
[Font Awesome More (Fontstrap) icons](http://blog.webguysaz.com/font-awesome-more-fontstrap-icon-additions/)

== Installation ==

1. Upload Font Awesome More Icons to the `/wp-content/plugins/` directory.

1. Activate the plugin through the 'Plugins' menu in WordPress.

1. Add shortcode to your posts, pages and even widgets to display a Font Awesome icon. You can use the handy drop-down in WordPress Editor window to browse and click the icon you want and instantly have the shortcode inserted into your post/page for you.

**Example:**

`[icon name=pencil]`


1. You can use HTML by adding the appropiate class to the `<i>` element.

All code examples on the Font Awesome site apply: [http://fortawesome.github.com/Font-Awesome/#code](http://fortawesome.github.com/Font-Awesome/#code)

1. You can use shortcodes in posts, pages, and widgets by passing the shortcode attributes.

**Example:**

`[icon name="wordpress" title="WordPress" size="2x"]`

== Frequently Asked Questions ==

= How is the plugin different from Rachel Baker’s Font Awesome Icons? =

Rachel’s plugin contains all the icons from Font Awesome. This plugin contains all of those icons plus the additional icons provided by Font Awesome More (Fontstrap), which includes important icons like PayPal, YouTube, Skype, and WordPress, to name a few. It also has a few more features like title/alt text and size options for your icons via shortcodes.

= Does this plugin require a separate Font Awesome installation/plugin and Font Awesome More installation/plugin? =

No, this plugin includes all icon sets from both [Font Awesome](http://fortawesome.github.com/Font-Awesome/) and [Font Awesome More](http://gregoryloucas.github.io/Font-Awesome-More/).

= Is this plugin compatible with Rachel Baker's Font Awesome Icons Plugin? =

No. You must deactivate and/or remove Font Awesome Icons plugin before activating this plugin, which is a superset of Font Awesome Icons.

= Does this plugin require Twitter Bootstrap? =

No. It is completely independent of Twitter Bootstrap.

= Are there any settings for this plugin? =

Nope. No settings page is created. Just activate and start using. No configuration required.

== Screenshots ==

1. HTML code examples
1. Shortcode examples
1. Shortcode example with no space option
1. TinyMCE drop-down
1. Font Awesome More-specific icons not included in standard Font Awesome icon set

== Changelog ==

= 3.5 =
* New: Shortcode option to remove whitespace after icon with "space=false"
* New: "icon-" prefix no longer needed in shortcodes, in preparation for upcoming Font Awesome 4.0 changes

= 3.4.1 =
* Fixed: reinserted automatic whitespacing after icons

= 3.4 =
* New: Easy size option in shortcodes. Increase the icon size by using "size=" "large" (33% increase), "2x", "3x", or "4x" in shortcodes.
* New: Easy title option in shortcodes. Add a title (alt) attribute to icons to make text appear upon mouse hover of icon by using "title=[text]" in shortcodes.
* Examples: see examples of the new size and title attributes by visiting http://blog.webguysaz.com/font-awesome-more-plugin-update-to-3-4/

= 3.3 =

* Fixed: Better handling of Font Awesome CDN while on https: (especially in Chrome)
* Fixed: Removed duplicates from TinyMCE where new Font Awesome icons superseded older Font Awesome More icons
* Fixed: Renamed any Font Awesome More icons where new Font Awesome icons had the same name but icon was significantly different
* New: Added latest revisions from main Font Awesome plugin (with a few fixes noted below)
* New: Added TinyMCE editor plugin, making it possible for the user to select font awesome glyphs from a drop-down list within the content editor.
* Fixed: Insert shortcode into WordPress TinyMCE editor instead of icon (WYSIWYG doesn't handle icon with text well)
* Fixed: Added missing icons from drop-down for new 3.2 Font Awesome icons
* New: Added version number constant to cache bust assets for future plugin updates. (Thanks @rscarvalho)

= 3.2.1 =

* Updated Font Awesome to 3.2.1, which includes 58 new icons

= 3.1.1 =

* Updated Font Awesome to 3.1.1, which includes 54 new icons
* Load Font Awesome CSS and font files from Bootstrap CDN (significantly smaller plugin size)

= 3.0.2 =

* Initial release

== Upgrade Notice ==

= 3.5 =
In preparation for the upcoming Font Awesome 4.0 release, this update removes the necessity to prefix shortcode options with "icon-". Please use shortcode options instead of HTML where possible to maintain compatibility with the future release.