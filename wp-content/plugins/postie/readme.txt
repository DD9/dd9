=== Postie ===
Contributors: WayneAllen
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HPK99BJ88V4C2
Author URI: http://allens-home.com/
Plugin URI: http://PostiePlugin.com/
Tags: e-mail, email
Requires at least: 2.8
Tested up to: 3.4.2
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Postie plugin allows you to blog via e-mail, including many advanced
features not found in WordPress's default post by e-mail feature.

== Description ==
Postie offers many advanced features for posting to your blog via e-mail,
including the ability to assign categories by name, included pictures and
videos, and automatically strip off signatures. It also has support for both
imap and pop3, with the option for ssl with both.  For usage notes, see the
[other notes](other_notes) page

= What's new? = 

* 1.4.5 (2012.11.14)
    * Fixed bug in XSS attack vulnerability code. Thanks to R Reid http://blog.strictly-software.com/2012/03/fixing-postie-plugin-for-wordpress-to.html
    * Fixed bug where emails with multiple categories has the incorrect title
    * Fixed bugs where PHP setting were not being changed correctly - thanks to Peter Chester http://tri.be/author/peter/
    * New maintainer

* 1.4.4 (2012.08.10)
    * Fixed possible XSS attack vulnerability 

* 1.4.3 (2011.12.12)
    * Removed get_user_by function to make compatible with wp 3.3 - now requires
 2.8+

* 1.4.2 (2011.01.29)
    * Fixed mailto link bug (thanks to Jason McNeil) 
    * Fixed bug with attachments with non-ascii characters in filename (thanks to
      mtakada)
    * checking for socket errors when checking mail (thanks elysian)
    * fixed issue with multiple files not being inserted correctly
    * Added support for ISO 8859-15 (thanks paolog)
    * fixed sql injection problem (thanks Jose P. Espinal for pointing it out)

* 1.4.1 (2010.06.18)
    * Images appear in correct order when using images append = false
    * Images are sorted in order of filename before inserting into post
    * Fixed formatting problem with wordpress_default image template
    * Captions now correctly work with wordpress >3.0 and <3.0
    * Fixed auto_gallery feature
    * Default port is now 110
    * Added more configuration tests
    * Added background color to settings page to make input boxes more visible
    * Removed extra quote character in captions from #img# placeholders (thanks
      SteelD for pointing out the error)
    * Added support for big5 and gb-1232 encodings (thanks Chow)
    * Fixed issue with configurations items stored as arrays, which caused
      problems with validating authorized addresses
    * Fixed bug with replaceImageCIDs function
    * On hosts which allow it, we set max execution time to 300 seconds and
      memory_limit to infinity to allow processing of large e-mails (especially
      with large attachments)

== Installation ==
* Either:
    * Put the postie.zip file in wp-content/plugins/ and unzip it
* Or:
    * Use the automatic installer (WP 2.7+)
* Login to WordPress as an administrator
* Goto the Plugins tab in the WordPress Admin Site
* Activate "Postie"
* Goto to the "Settings" tab and click on the sub-tab "Postie" to configure it.
* Make sure you enter the mailserver information correctly, including the type
  of connection and the port number. Common port configurations:
  * pop3: 110 
  * pop3-ssl: 995
  * imap: 143
  * imap-ssl: 993
* (Postie ignores the settings under Settings->Writing->Writing-by-Email)

= Automating checking e-mail =

By default, postie checks for new e-mail every 30 minutes. You can select from
a number of different checking intervals in the settings page, under the
mailserver tab.

If you would prefer to have more fine-grained control of how postie checks
for mail, you can also set up a crontab. This is for advanced users only.
If your site runs on a UNIX/linux server, and you have shell access, you can
enable mail checking using cron; if you don't know anything about cron, skip
to the cronless postie section.

Setup a cronjob to pull down the get\_mail.php
Examples:

*/5 * * * * /usr/bin/lynx --source http://blog.robfelty.com/wp-content/plugins/postie/get\_mail.php >/dev/null 2>&1

This fetches the mail every five minutes with lynx 

*/10 * * * * /usr/bin/wget -O /dev/null http://blog.robfelty.com/wp-content/plugins/postie/get\_mail.php >/dev/null 2>&1

This fetches the mail every ten minutes with wget 

== Usage ==
* If you put in :start - the message processing won't start until it sees that string
* If you put in :end - the message processing will stop once it sees that string
* Posts can be delayed by adding a line with delayXdXhXm where X is a number.
  *    delay:1d - 1 day
  *    delay:1h - 1 hour
  *    delay:1m - 1 minute
  *    delay:1d2h4m - 1 day 2 hours 4m
* By putting comments:X in your message you can control if comments are allowed
   *   comments:0 - means closed
   *   comments:1 - means open
   *   comments:2 - means registered only
* Replying to an e-mail gets posted as a comment. 
  * For example, you e-mailed a post with the subject line "foo".
    If you then send an e-mail with the subject line "Re: foo", it will
    get posted as a comment to the "foo" post. This works by the subject
    line, so if you have two posts with titles "foo", then the comment
    will get placed in the more recent post.
* Custom excerpt
  * You can include a custom excerpt of an e-mail by putting it between
    :excerptstart and :excerptend
    * You can include images in the excerpt by using the shortcode #eimg1#,
      #eimg2# etc.

= Category and tag handling =
* If you put a category name in the subject with a : it will be used
  as the category for the post
* If you put a category id number in the subject with a : it will
  be used as the category for the post
* If you put the first part of a category name it will be posted in
  the first category that the system finds that matches - so if you put

  Subject: Gen: New News

  The system will post that in General.

* All of the above also applies if you put the category in brackets []
* Using [] or you can post to multiple categories at once

  Subject: [1] [Mo] [Br] My Subject

  On my blog it would post to General (Id 1), Moblog, and Brewing all at one time

* Using - or you can post to multiple categories at once

  Subject: -1- -Mo- -Br- My Subject

  On my blog it would post to General (Id 1), Moblog, and Brewing all at one time
* You can add tags by adding a line in the body of the message like so:
  tags: foo, bar
* You can also set a default tag to be applied if no tags are included.

= Image Handling =
* Allows you to attach images to your email and automatically post
  them to your blog
* You can publish images in the text of your message by using #img1#
  #img2# - each one will be replaced with the HTML for the image
  you attached
* Captions - you can also add a caption like so:

    * #img1 caption='foo'#
    * #img2 caption='bar'#
  
  Or, if you use IPTC captions, this caption will be used  (adding a caption
  in many photo editing programs (for example Picasa), will add an IPTC caption)

* Image templates
  Postie now uses the default wordpress image template, but you can specify a
different one if you wish.

  You can also specify a custom image template. I use the following custom
template:

  <div class='imageframe alignleft'><a href='{IMAGE}'><img src="{THUMBNAIL}"
  alt="{CAPTION}" title="{CAPTION}" 
  class="attachment" /></a><div
class='imagecaption'>{CAPTION}</div></div>
     
    * {THUMBNAIL} gets replaced with the url to the thumbnail image
    * {MEDIUM} gets replaced with the url to the medium-sized image
    * {LARGE} gets replaced with the url to the large-sized image
    * {FULL} gets replaced with the url to the full-sized image
    * {FILENAME} gets replaced with the absolute path to the full-size image
    * {RELFILENAME} gets replaced with the relative path to the full-size image
    * {CAPTION} gets replaced with the caption you specified (if any)
    * {WIDTH} gets replaced with width of the photo
    * {HEIGHT} gets replaced with the height of the photo

= Interoperability =
* If your mail client doesn't support setting the subject (nokia) you
  can do so by putting #your title here# at the begining of your message
* POP3,POP3-SSL,IMAP,IMAP-SSL now supported - last three require
  php-imap support
* The program understands enough about mime to not duplicate post
  if you send an HTML and plain text message
* Automatically confirms that you are installed correctly

== Screenshots ==

1. Postie options (showing video and audio templates)

== Frequently Asked Questions ==

= Postie won't connect to my mailserver. Why Not? =

Make sure the port you are using is open. For example, bluehost seems to block
ports 993 and 995 (for pop3-ssl and imap-ssl) by default. I have heard that
you can request that they open them for you ( you might have to pay extra). 

You can check for open ports with the following command on your server:
netstat -ln|grep -E ':::(993|995|143)'

If nothing shows up, then the ports are not open.

= How can I get postie to display inline images? =

Make sure that you send e-mail formatted as html (richtext), and set postie to
prefer html messages (in the message tab of the postie settings)

= Mail is not showing up right when I send html (rich formatted) e-mail! =

Make sure you set the preferred text type to html

= Do I need to any code to my theme for postie to work? =

No. 

= I read somewhere to add an iframe to my footer. Should I do this? =

No. Do not add an iframe in your footer to get postie to check mail
periodically. To check e-mail periodically, either set-up a cron job, or use
cronless postie. See installation instructions

= My mail host requires SSL, but postie will not allow me to select pop3-ssl or imap-ssl =

You must have php-imap installed on your server for this to work. Ask your
hosting provider about this.

= Can I use postie to check a gmail account? =

Yes. You can use either pop3-ssl or imap-ssl with a gmail account. Before
attempting to use with postie, make sure that you enable pop or imap in your
gmail preferences.

* Pop3 settings:
    * protocol - pop3-ssl
    * server - pop.gmail.com
    * port - 995
    * userid - your username (e.g. if your e-mail address is foo@gmail.com,
      this would be just foo)
    * password - your password 
* IMAP settings:
    * protocol - imap-ssl
    * server - imap.gmail.com
    * port - 993
    * userid - your username (e.g. if your e-mail address is foo@gmail.com,
      this would be just foo)
    * password - your password 

= My posts show up as being posted by 'admin' instead of me. Why? =

If your admin account is linked to bar@gmail.com, and you send mail from
bar@gmail.com, it will show up as being posted by admin. If you have a
wordpress user named "John Doe", which is linked to johndoe@gmail.com, make
sure that you send e-mails from johndoe@gmail.com. It doesn't matter which
e-mail address postie is checking. That is, if you send mail from
johndoe@gmail.com to foo@gmail.com, it gets posted as "John Doe". 

If you send an e-mail to your postie address from an e-mail address that is no
t linked to a wordpress user, it will get posted as admin.

= Images aren't showing up at all? =

There are a couple possible reasons for this. First, check to see if you can
add an image through wordpress's normal posting mechanism. If not, then there
is probably 1 or 2 problems:
1. Your server does not have the php-gd library installed. Ask your hosting
provider about this.

2. Your wp-content/uploads directory is not writable by the webserver. Make
sure that it is

= Can I delete the wp-files directory needed by postie version <1.3.0? =

If you have posts published already by older versions of postie, getting rid
of those directories will delete any files you might have had associated with
those old posts. If you don't have any such posts, then you can safely delete
them.

= How can I get rid of stupid stuff my e-mail provider adds to my messages? =

To strip off stuff that they add at the beginning of a message, start your
post with :start

To strip off stuff that they add at the end of a message, end your
post with :end

= How can I add custom attachment icons? =

Simply upload the icons you want to the postie/icons/custom directory. You
must name the icons according to the following scheme:
{filetype}-{size}.png

For example, for word documents, you could use:

`doc-32.png`

for a 32x32 pixel icon. (You can actually create any size icon you want, but
if you name it 32, then it will only be used if you select to use size 32
icons)

See the other directories in icons for more examples.

Currently the following filetypes are supported:

* doc - microsoft word (including docx)
* ppt - microsoft powerpoint (including pptx)
* xls - microsoft excel (including xlsx)
* numbers - iWork numbres spreadsheet
* pages - iWork pages document
* key - iWork keynote presentation
* pdf - portable document format
* rtf - rich text format
* txt - plain text document

= Can I add special text to the body of the post when using postie? =

Yes. You can create your own function, and use the postie_post filter.
Two short examples are included in the filterPostie.php.sample file

= Can I add special text to the title of the post when using postie? =

Yes. You can create your own function, and use the postie_post filter.
Two short examples are included in the filterPostie.php.sample file

= Can I select tags or categories based on the content of the e-mail? =

Yes. You can create your own function, and use the postie_post filter.
See the filterPostie.php.sample file for examples.

= Is the IMAP extension required for postie? =

The IMAP extension is not required, but it is strongly recommended, especially
is you are using non-English text. There is more information on php.net about
installing the IMAP extension. If you have control over your server, it is
often not hard to install. 

On Ubuntu, try
sudo apt-get install php5-imap

On Fedora, try
sudo yuminstall php-imap

The IMAP extension is known to be installed on the following popular webhosts:
* Dreamhost

= How can I embed youtube or vimeo videos? =

Simply put the url in the body of your e-mail. (Make sure that you have the
option to convert url into links turned on)

== CHANGELOG ==

= 1.4.5 = 
* TODO - fix corruption of rtf attachments
* TODO - add port checking in tests
* TODO - non-image uploads get ignored in content when using autogallery - see
  replaceimageplaceholders

= 1.4.4 (2012.08.10) =
* Fixed possible XSS attack vulnerability 

= 1.4.3 =
* Removed get_user_by function to make compatible with wp 3.3 - now requires
 2.8+

= 1.4.2 (2011.01.29) =
* Fixed mailto link bug (thanks to Jason McNeil) 
* Fixed bug with attachments with non-ascii characters in filename (thanks to
  mtakada)
* checking for socket errors when checking mail (thanks elysian)
* fixed issue with multiple files not being inserted correctly
* Added support for ISO 8859-15 (thanks paolog)
* fixed sql injection problem (thanks Jose P. Espinal for pointing it out)
* Fixed namespace clashing for get_config function

= 1.4.1 (2010.06.18) =
* Images appear in correct order when using images append = false
* Fixed formatting problem with wordpress_default image template
* Captions now correctly work with wordpress >3.0 and <3.0
* Fixed auto_gallery feature
* Default port is now 110
* Added more configuration tests
* Added background color to settings page to make input boxes more visible
* Removed extra quote character in captions from #img# placeholders (thanks
  SteelD for pointing out the error)
* Added support for big5 and gb-1232 encodings (thanks Chow)
* Fixed issue with configurations items stored as arrays, which caused
  problems with validating authorized addresses
* Fixed bug with replaceImageCIDs function
* On hosts which allow it, we set max execution time to 300 seconds and
  memory_limit to infinity to allow processing of large e-mails (especially
  with large attachments)
* Images are sorted in order of filename before inserting into post

= 1.4 (2010.04.25) =  
* Now using wordpress settings api (thanks for much help from Andrew S)
* Cronless postie is now integrated with postie instead of a separate plugin
* filterPostie.php moved to filterPostie.php.sample
* Can use fetchmails.php to fetch mail from multiple mailboxes
* Fixed problem with embedding youtube videos from html (richtext) e-mail
* Added support for embedding vimeo vidoes
* Fixed problem with selecting "none" as icon set for attachments (thanks
  tonyvitali)
* Fixed problems with cronless postie settings
* Fixed bug with embedding youtube and vimeo videos whose ID contains a -
  (thanks Jim Kehoe)
* Post_author is now included with attachments
* fixed confirmation_email settings so that now you can select between sender,
  admin, both, or none (thanks to redsalmon for pointing out bug)
* Added option to automatically insert galleries
* Updated FAQ and readme

= 1.3.4 (2009.10.05) =
* Fixed problem with images not posting under cron
* Fixed issue with disappearing password

= 1.3.3 (2009.09.11) =
* Fixed problem with double titles
* Fixed error in wp-mu
* Cronless postie now correctly updates when changing the setting in the
  postie settings
* Small fix in handling of names of attachments (thanks to Teejot)
* Fixed delay option (thanks to redbrandonk)
* Cronless option value is now correctly deleted when deactivating the
  cronless postie plugin

= 1.3.2 (2009.08.27) =
* tags are now always an array, even if no default tags are set 
* Subject is showing up again if you do not have the IMAP extension
  installed
* More information on the IMAP extension and more user-friendly
  installation
* Fixed problems with smtp server settings in 1.3.1
* Added russian translation (thanks to fatcow.com)

= 1.3.1 (2009.08.24) =
  * Changed GetContent filter to postie_post
  * Added database upgrade hook on activation
  * Fixed bug where content would be empty if trying to remove signature,
    and signature list was emtpy
  * Updated FAQ and readme

= 1.3.0 (2009.08.14) =
  * Features
      * Added mpeg4 to default list of videotypes
      * Added support for KOI8-R character set (cyrillic)
      * Added support for iso-8859-2 character set (eastern european)
      * Added option to include custom icons for attachments
      * Added option to send confirmation message to sender
      * Enhanced e-mails for unauthorized users
      * Added option to send unauthorized e-mail back to sender
      * Added option to only allow e-mails from a specified list of smtp
        servers
      * Added option to use shortcode for embedding videos (works with the
        videos plugin http://www.daburna.de/download/videos-plugin.zip
      * Better handling of comment authors (thanks to Petter for suggestion)
      * Simplified message options (now includes an advanced options section)
      * Added filter ability for post content
  * Bug fixes
      * No longer including wp-config.php
      * If tmpdir is not writable, try a different tmpdir
      * More subject encoding fixes
      * Updated image templates, which were causing problems for cron
      * Fixed in text captions
      * Fixed SQL problems when updating options
      * Fixed name clashes with other plugins
      * Fixed custom image field

=  1.3.beta (2009.07.01) =
  * Mores fixes for character issues in subject
  * Now handling Windows-1256 (arabic) character set
  * Fixed image uploading on windows servers
  * Fixed replying to message adds comment
  * Uploading pictures via MMS should now work
  * Fixed some issues with e-mails from outloook 12
  * Greatly reduced number of database queries
  * No longer requiring config_handler.php

=  1.3.alpha (2009.06.05) =
  * Now using default wordpress image and upload handling, which means:
      * No more creating special directories for postie
      * No more confusion about imagemagick
      * Can now use the [gallery] feature of wordpress
      * Attachments are now connected to posts in the database
      * All image resizing uses wordpress's default settings (under media)
  * Configuration, settings and documentation improvements
      * Completely redesigned settings page (mostly thanks to Rainman)
      * Reset configuration no longer deletes mailserver settings
      * Now including help files and faq directly in settings page
  * More media features
      * Automatically turn links to youtube into an embedded player
      * Added option to embed audio files with custom templates
      * Video options are now template based
      * Image options are now solely template based, with several new default
        templates
  * Bug fixes
      * Uploading images from vodafone phones should now work
      * Correctly handling Windows-1252 encoding
      * Correctly handling non-ascii characters in subject line

=  1.2.3 (2009.05.17) =
  * Fixed headers already sent bug
  * Converted shortcode `<?` to proper `<?php` (thanks brack)
  * Deleting mails after processing again

=  1.2.2 (2009.05.15) =
  * Show empty categories for default category in options
  * Image scaling fixed so that the smaller value of max image width and max
    image height is used
  * Fixed some issues with parsing html e-mail
  * Got rid of stupid mime tag (thanks Jeroen)
  * No longer adding slashes before calling wp_insert_post
  * When using custom image field, each image has a unique key


=  1.2.1 (2009.05.07) =
  * Got rid of stupid version checking
  * Improved cronless postie instructions and configuration
  * Internationalization is working now
  * Dutch localization (thanks to gvmelle http://gvmelle.com )
  * Fixed caption bug when using image magick
  * Added option to not filter new lines (when using markdown syntax)
  * Fixed autoplay option
  * Can now use wildcards in excluding filenames
  * Producing better quality thumbnails (thanks to robcarey)

=  1.2 (2009.04.22) =
  * More video options:
      * Can embed 3gp, mp4, mov videos
      * Can specify video width, video height, player width, and player height
        in the settings page
      * Can specify custom image template
  * Image handling improvements:
      * Only downscale images, not up-scale (thanks Jarven)
      * More custom image template options
      * IPTC captions now also work when not resizing images
      * Added option to use custom field for images (for Datapusher)
      * Fixed some issues with image templates and line break handling
      * Custom image template now works even when not resizing images
  * Documentation improvements:
      * Added links to settings, forum, and readme in plugin description
      * Updated readme (thanks to Venkatraman Dhamodaran)
      * Added better instructions on how to use cronless postie
  * Text processing improvements:
      * Added option to automatically convert urls into links
      * Added feature to include a custom excerpt
  * Miscellaneous improvements
      * Improved internationalization (thanks to Håvard Broberg
        (nanablag@nanablag.com))
  * Bug Fixes
      * Removed debugging info in get_mail.php (security issue) thanks to 
        [Jens]( http://svalgaard.net/jens/)
      * No longer directly including pluggable.php (should
        prevent conflicts with other plugins such as registerplus


=  1.1.5 (2009.03.10) =
  * Added option to have postie posts be pending review, published, or draft
  * Settings panel only shows up for administrators
  * Need not be user "admin" to modify settings or to post from non-registered
    users
  * Can now set administrator name. Authorized e-mail addresses which don't
    have a user get posted under this name
  * Will use IPTC captions if available
  * Added option to replace newline characters with <br />

=  1.1.4 (2009.03.06) =
  * Added more image options (open in new window, custom image template)
  * can now add captions to images
  * Can now add tags (including default tag option)

=  1.1.3 (2009.02.20) =
  * Fixed delayed posting
  * updated readme some

=  1.1.2 (2008.07.12) =
  * now maintained by Robert Felty
  * allow negative delays
  * will glean author information from forwarded or redirected e*mails
  * replying to an e*mail adds a comment to a post
  * fixed category handling to work with taxonomy
  * fixed one syntax error
  * added option to wrap posts and comments in <pre%gt; tags


=  1.1.1 =

Below is all the of the version information. As far as I can tell there once was a guy named John Blade. He took some of the original wp-mail.php code 
and started hacking away on it. He actually got pretty far. About the time I discovered WordPress and his little hack - called WP-Mail at the time - he 
went on a vacation or something. There were some problems with the script, and it was missing some features I wanted. I hacked away at it and got it 
into a place where it did what I wanted. I started posting about it since I figured other people might want the features. 

John didn't release any more versions at least up til July 2005. So I started accepting submissions and feature requests from people to help make the 
code better. In June/July 2005 I discovered a little plugin by Chris J Davis (http://www.chrisjdavis.org/cjd-notepad/) called notepad. I added a small 
feature to it (basically a bookmarklet).  In the process I started looking at his code and realized how much you could do with the plugin system 
available in Word Press.

So I decided to make an offical fork. I put up an article on my blog asking for new names. I picked Postie.  I then modified the code to be a proper 
plugin.  And the rest is history :)

* BUGFIX -problem with subject
* BUGFIX -cronless postie typo

=  1.1 =
* FEATURE: Updated and tested with WordPress 2.1
* BUGFIX:Removed deprecated functions
* FEATURE: Cronless Postie now uses the WordPress native Psuedo Cron.

=  1.0 =
* BUGFIX: TestWPVersion broke with 2.1
* FEATURE: end: now marks the end of a message (Dan Cunningham)
* FEATURE: Better Readme (Michael Rasmussen)
* FEATURE: Smart Sharpen Option -EXPERIMENTAL- (Jonas Rhodin)
* BUGFIX: Issue with google imap fixed (Jim Hodgson)
* BUGFIX: Fixed espacing issue in subjects (Paul Clip)
* BUGFIX: Typo in Div fixed (phil)

=  0.9.9.3.2 =
* BUGFIX: Typo
=  0.9.9.3.1 =
* BUGFIX: Removed debugging code

=  0.9.9.3 =
* BUGFIX: If your email address matches an existing user - then it will post as that user - even if you allow anyone to post.
* BUGFIX: Replaced get_settings('home') with get_settings('siteurl')
* BUGFIX: Better handling for Japanese charactersets - Thanks to http://www.souzouzone.jp/blog/archives/009531.html
* BUGFIX: Better thumbnail window opening code - thanks to Gabi & Duntello!
* FEATURE: Added an option to set the MAX Height of an image - idea from Duntello
* BUGFIX: Modified the FilterNewLines for better flowed text handling - You now HAVE TO PUT TWO NEW LINES to end a paragraph.
* FEATURE: Added new CSS tags to support positioning images/attachments/3gp videos
* BUGFIX: Tries to use the date in the message (Thanks Ravan) I tried this once before and it never worked - hopefully this time it will.
* BUGFIX: Added a workaround to fix the problem with Subscribe2 - it will now notify on posts that are not set to show up in the future.



=  0.9.9.2 =
* BUGFIX: Looks for the NOOP error and disgards it
* FEATURE: Postie now detects the version of WordPress being used 
* FEATURE: Smarter Parsing of VodaPhone 
* FEATURE: Easy place to add new code to handle other brain-dead mail clients
* BUGFIX: Handles insertion of single quotes properly
* BUGFIX: Thumbnails should now link properly

=  0.9.9.1 =
* BUGFIX: Needed a strtolower in places to catch all iso-8859 - thx to Gitte Wange for the catch
* BUGFIX: Fixed issue with the category not being posted properly

=  0.9.9 =
* UPDATE TO WP 2.0
* BUGFIX: Config Page now works
* FEATURES: Supports role based posting
* BUGFIX: Posting updates the category counts.

=  0.9.8.6 =
* BUGFIX: Fixed problems with config page <%php became <?php
* 
=  0.9.8.5 =
* BUGFIX: onClick changed to onclick
* BUGFIX: strolower added to test for iso - thanks daniele
* BUGFIX: Added a class to the 3gp video tags
* FEATURE: Added the option to put the images before the article
* BUGFIX: Added in selection for charsets - thanks Psykotik - this may cause problems for other encodings
* FEATURE: Added option to turn of quoted printable decoding
* FEATURE: :start tag - now postie looks for this tag before looking for you message - handy if your service provider prepends a message 
* FEATURE: Template for translation now included
=  0.9.8.4 =
* BUGFIX: Fixed problem with config_form.php - select had "NULL" instead of ""
* BUGFIX: 3g2 now supported
* BUGFIX: More line break issues addressed
* BUGFIX: QuickTime controls are now visible even if the movie is done playing
* BUGFIX: Email addresses in the format <some@domain.com> (Full Name) supported
* BUGFIX: Some images that were not being resized - are now
* BUGFIX: HTML problems - if you posted plain text with HTML on it ignored all images
* BUGFIX: The test system blew up on the thumbnails 
* BUGFIX: Selected HTML for preferred text is now shown in the config form properly
* BUGFIX: Postie now complains if it is not in its own directory
* BUGFIX: Postie doesn't include PEAR if it is already available
* BUGFIX: In Test mode rejected emails are simply dropped
* BUGFIX: Markdown messes up Postie - it will warn you if you turn it on.
* 
=  0.9.8.3 =
* BUGFIX: Fixed issue with the line feed replacement
* BUGFIX: Added Banned File Config back in
* FEATURE: Added in a link around 3gp video embedded via QT
* BUGFIX: Email that has both Plain and HTML content will show the HTML content and not the plain if html is preferred

=  0.9.8.2 =
* BUGFIX: Fixed an extra new line after attachin non-image files.
* BUGFIX: The Test system now displays any missing gd functions
* BUGFIX: The test system was only using ImageMagick

=  0.9.8.1 =
* BUGFIX: The test images are now included in the zip 

=  0.9.8 =
* BUGFIX: New Lines detected and handled properly in cases where the mail client doesn't put a space before the new line (Miss Distance)
* BUGFIX: 3gp mime type added (Paco Cotera)
* BUGFIX: Authorized Email Addresses are not case-insensitive
* FEATURE: The larger image now does a proper pop up 
* BUGFIX: Fixed Timeing Issue - turns out it wasn't reading the db at all
* FEATURE: New Test Screen - to help track down problems

=  0.9.7 =
* BUGFIX: removed all short tags
* BUGFIX: There were spacing issues in the way I wrote the QT embed statements 
* FEATURE: Added calls to WP-Cron - should work with that properly now if you activate Cronless Postie
* FEATURE: ImageMagick version works without any calls to GD
* BUGFIX: Postie now correctly handles cases wjere tjere are multiple blogs in one db
* BUGFIX: Turned off warnings when using without GD
* FEATURE: add the rotate:X  to your message to rotate all images
* FEATURE: new filter_postie_thumbnail_with_full which makes it easy to show a thumbnail on the front page but full image on the single page - see FAQ

=  0.9.6 =
* BUGFIX: handles email addresses that are no name and just <email@email.com> (Steve Cooley Reported)
* FEATURE: Basic support for embedding flash files
* BUGFIX: Postie now handles creating the correct URL on non Unix platforms
* BUGFIX: Fixed problem with file attachments not being put in the right place.
* FEATURE: You can now choose to use imagemagick convert to handle making thumbnails
* BUGFIX: Rewrote Cronless Postie to use direct sockets
* BUGFIX: Time offset is now settable just for Postie - hopefully this will fix problems for cases where the normal time offset doesn't work properly.
* FEATURE: First draft of frame for a 3GP video
* FEATURE: Option to embed 3GP in QuickTime Controller.

=  0.9.5.2 =
* BUGFIX: gmt varialble not being set correctly
* BUGFIX: Changed the name of the Check Mail button to fix an issue with mod_security
* BUGFIX: Fixed issue with Cronless-Postie
* BUGFIX: There was an argument passed by reference incorrectly
* FEATURE: Added in Cronless Postie Readme
* FEATURE: Added in Postie Readme

=  0.9.5.1 =
* BUGFIX: Confirmed POP3-SSL on debian-3.0
* BUGFIX: Updated the plugin version
* BUGFIX: Stopped displaying the email account
* 
=  0.9.5 =
* BUGFIX: Postie handles cases where you do not have GD
* FEATURE: You can now set the access level for posting - so other people can use the gate way
* BUGFIX: Fixed issue when admininstrator email is not tied to a user account.
* FEATURE: Can now reset all Postie configurations back to defaults
* BUGFIX: HTML Emails with embedded images are now handled properly.
* BUGFIX: The time difference should work correctly now
* BUGFIX: Postie's configs are completely seperate from Writing-By-Mail
* FEATURE: Warning if you use Gmail to make sure you turn on POP support
* BUGFIX: Manual Check Mail Button in interface
* BUGFIX: fixed issue of compatability with cjd-notepad
* BUGFIX: Windows Works Now


=  0.9.4 =
* BUGFIX: Cronless Postie - fixed the include statement
* BUGFIX: Authorized Addresses now supports a single address
* FEATURE: All configuration in Postie done in a single screen
* FEATURE: AUTHORIZATION can be completely overridden
* BUGFIX: line 1159 - didn't handle cases where the table didn't exist already very well
* FEATURE: Detects if you can do IMAP
* FEATURE: Added IMAP Support
* FEATURE: Added IMAP-SSL Support
* FEATURE: Added POP3-SSL Support

=  0.9.3 =
* Bug fixes for IIS
=  0.9.2 =
* Moved to more of a DIRECTORY_SEPARATOR structure 
=  0.9.1 =
* Added a define to fix a problem with over including
=  0.9 =
* Converted to an honest to god plugin
* BUGFIX: If you put a single category:subject it now works
* BUGFIX: ? Special characters may be supported?  The test post now shows a lot of umlats and accents?
* BUGFIX: The last ] in a subject with categories is now filtered out
* FEATURE: -1- subject - will put the post in category 1
=  0.312.13 =
* Code clean up - The main loop is finally readable by even non programmers
* FEATURE - You can now post to multiple categories at one time by using the [#],[Category Name], [Cat] in the subject
* FEATURE - You can now select a category by just including the begining characters [G] will select General 
* if you don't have any other categories that start with g
* FEATURE - Jay Talbot - added a new feature so you can have multiple email addresses be allowed in
* Make multi category posting more obvious
* BUG FIX: Timezones of GMT+? should now work properly
* BUG FIX: Able to handle mis-mime typed images as long as they are named with .jpg/.gif/.png

=  0.312.12 =
* Code clean up - slowing shrinking the main to make it easiery to fix things
* FEATURE: Be able to turn on/off allowing comments in an email
* BUG FIX: AppleDouble now mostly supported 
* BUG FIX: MIME handling improved.
* BUG FIX: Fix issue with timing delay
=  0.312.11 =
* FEATURE: Patterns to define where a sig starts are user configurable
* FEATURE: Add filter options for banned file names
* BUG FIX: Made it possible to turn off posting to the db for testing purposes
=  0.312.10 =
* FEATURE: Added in code to diplay the mime type of the file being linked to
* BUG FIX: It now tests for the existance of the directories and makes sure
* that the web server can write to them
=  0.312.9 =
* FEATURE:Should handle jpg as well as jpeg as the file type
* BUG FIX: Now correctly handles the subject in the message
* BUG FIX: Should handle Text preferences correctly 
=  0.312.8 =
* Some general code tidying. 
* FEATURE: Can now have email from invalid email addresses automatically forwared
* to the admin's email account. This forward includes all attachments. 
* Props to David Luden for getting this started.
* Minor change: The system will continue if it runs into a message that doesn't have 
* any content - it will also continue to process if it gets an email from 
* someone not in the system. In the past this could result in deleted mail
* if your cron job didn't run often enough.
=  0.312.7 =
* Confirm the handling of 3gp video for cell phones o
* Added in new directive SUPPORTED_FILE_TYPES -if the mime type is listed here then the system will try to make a link to it without making a thumb nail.
=  0.312.6 =
* Bug Fix: Ok the last bug I fixed - actually caused another bug - man I should set up some unit tests. Now it handles mail from the nokia mail client correctly.
=  0.312.5 =
* Bug Fix : The system was accepting all test/* types. Now you can set a preference (defaults to text/plain)
* to use as the main text for the post.
=  0.312.4  =
* Added in sanitize_title call suggested by Jemima
* Added in ability to provide a subject in an mms - by using #Subject#
* Fixed an issue with the time stamp system so it now automatically uses the gmt_offset from WordPress
* Fixed issue with the delay:1d1h tag that prevented it from being removed from the body.
* Fixed issue with the delay tag that caused problems if it was the last thing before an image.

=  0.312.3-HEY  (2005-05) =
* > Some changes and Bugfixes by Adrian Heydecker
* > Not (yet) in main development branch.
* Fixed bug: JPEG-thumbnails had a bigger filesize than full images caused by bad hardcoded compression value.
* Fixed bug: If images and signatures were present but no placeholder tags, the images were deleted together with the signature.
* Fixed bug: Generates valid postnames for users of mod_rewrite. Permalinks to posts should now work even when whitespaces are present in the subject line.
* Added support for Quoted Printable encoded mail.
* Added ability to encode Wordpress-posts in charset ISO-8859-1 instead of UTF-8.
* Added ability to choose JPEG-compression value for thumbnails.
* Added ability to add class="" and style="" to images.
* Added ability to use a different mailadress (eg. mobile) without setting up a new Wordpress-account.

=  0.312.2 =
* BUGFIX: It now removes the delay tag from the message
=  0.312.1 =
* Added modification for placeholder support for images (David Luden)
* Added in support to automatically scale down big images (Dirk Elmendorf)
* Fixed bug with multiple emails all getting the contents of the first image tag (Dirk Elmendorf)
* Added option to allow HTML in the body and subject of the email (Dirk Elmendorf)
* Switch config options to defines to reduce the number of global variables (Dirk Elmendorf)
* Added tests to make sure there is a trailing slash on the DIR definitions (Dirk Elmendorf)
* Add tests to see if they have gd installed (Dirk Elmendorf)
* Seperate the scaling out to a function for easier usage (Dirk Elmendorf)
* Add delay feature for future posting. (Dirk Elmendorf)
* Added in ability to use strtotime if it is available (Dirk ELmendorf)

* Todo
* Have option to have the email that is rejected forwarded on to another address.
* Fix bug that id still diplays the delay tag in the body 
=  0.312 - 2005-03 =
*  CHANGE FOR DEFAULT E-mail Categories, instead of [General] Subject you can now use General: Subject in the subject line.  Less typing, and there must be a space after the colon. 
*  Fixed bugs with no default posting for categories and user 
=  0.311 - 2005-01 =
*  eep, major bug for pop3 server. Next time I test my code more before I released, fixed so that pop3 now works.`
=  0.31 - 2004-12 & 2005-01 =
* (Has it been this long, best get back into the swing of things... did most of this coding on my holiday as I didn't have a machine to play WoW on :)
*  moved the deletion of pop3 emails into a check so that e-mails aren't deleted without proper checking.
*  added HTML 'decoding' (basic support for Thunderbird & Outlook) 
*  updated the Category search so that it matches words as well as numbers (i.e. [General] Subjectname will work instead of just [1] Subjectname)
*  Changed time function from time to strtotime (as per Senior Pez's suggestion), but found out that strtotime isn't in default php distro so removed...

= 0.3 - 2004-09 =
*  Added UBB decoding support
*  Added default title (when there is no subject assigned)
*  Started doing a little code cleanup, been reading Advanced PHP Book :)
* 
=  0.2 - 2004-08 =
*  Stopped using pear body decoding in favour of own decoding (may be slower but more modifiable) because of enriched text decoding
*  Added base64_decode checking (may help mobile phone users)
*  Fixed Subject line for non-english users (htmlentities instead of just trim)
*  Fixed error in some pop hanging -> more graceful exit on event on no emails in inbox ($pop3->quit)
*  Added work around for email addresses with exta <> in field (ie: <blade@lansmash.com> instead of blade@lasmash.com
*  Added some ===basic=== enriched text support
*  Updated readme file for easier install
*  Easy modify of globals (such as PHOTOSDIR and FILESDIR)
*  Cleaned up some pear stuff in install
* 
=  0.1 - 2004-06 =
* First release

== Upgrade Notice ==

= 1.4.4 =
Fixed possible XSS attack vulnerability