=== Upload Scanner ===
Contributors: kurtpayne 
Tags: clamav, scanner, antivirus, malware, syslog
Requires at least: 3.2
Tested up to: 3.6
Stable tag: 1.3
Network: true

Scan all uploaded files with ClamAV or your favorite malware scanner

== Description ==

Scan uploaded files with ClamAV or run system commands against uploaded files.  This allows you to integrate third party malware scanners.

This plugin __requires__ either ClamAV or another third-party scanner to be installed.  This plugin will pass uploaded files to the scanner and take appropriate actions based ont he results, but it is not, itself, a malware scanner.

If you have a dedicated server, you can install [php-clamav](http://php-clamav.sourceforge.net) for performance and convenience.

Banner image from [Eric Martinenz](http://www.flickr.com/photos/runlevel0/3647554681/in/photostream/)

== Screenshots ==

1. Options screen.
2. Log viewer.
3. Sample e-mail report.

== Installation ==

Automatic installation

1. Log into your WordPress admin
2. Click __Plugins__
3. Click __Add New__
4. Search for __Upload Scaner__
5. Click __Install Now__ under "Upload Scanner"
6. Activate the plugin

Manual installation:

1. Download the plugin
2. Extract the contents of the zip file
3. Upload the contents of the zip file to the wp-content/plugins/ folder of your WordPress installation
4. Then activate the Plugin from Plugins page.

== Changelog ==

= 1.3 =
 * Multisite support. props daggerhart
 * Marked compatibility with WordPress 3.6

= 1.2 =
 * Internationalized strings
 * Marked compatibility with WordPress 3.4

= 1.1 =
 * Added logging

= 1.0 =
 * Released

== Frequently Asked Questions ==

= It says ClamAV isn't installed? =

You'll need to install ClamAV and [php-clamav](http://php-clamav.sourceforge.net).  You don't actually need ClamAV to use this plugin, though.  You can use the system command to run another scanner that can accept input on the command line.

For example, you could scan with avira like this:

 `avscan $UPLOAD_SCANNER_ORIG_TEMPNAME`

= It says exec is disabled =

Your server admin has probably disabled the "exec" function.  You'll want to talk to your server admin before moving forward with this plugin.  They may be able to help, or they may have other security measures in place that mean you don't need this plugin.

= Why doesn't this plugin offer a "delete file" option? =

PHP automatically deletes the files for you if they're not handled.

 "The file will be deleted from the temporary directory at the end of the request if it has not been moved away or renamed."

http://www.php.net/manual/en/features.file-upload.post-method.php

= Are there any security issues running shell commands? =

The only user input that is passed to the shell command is the original file name chosen by the user, and this is passed through escapeshellarg() to sanitize it.  Otherwise, the only command that's run is chosen by you.  It's left to your server admin (or you) to determine that it's safe for you to issue commands. This plugin _should_ prevent attacker input from making it into the command.  Let me know if you see any problems.

= Is this compatible with Multisite? =

I haven't tested this with Multisite yet.  If you have input, let me know.

== Upgrade Notice ==

= 1.2 =

Internationalization.  Testing with WordPress 3.4.

= 1.1 =

First release on WordPress.org repository
