Upload Scanner Plugin for WordPress
----------------------------------------------------------------------------
This plugin will scan any uploaded files (detected via $_FILES) with ClamAV.  It
can also be configured to work with an admin-configured system command so third
party scanners can be used.

When a positive result is found, the remediation actions can be configured to
e-mail the site admin, quarantine the file, or send a 406 status and stop
Wordpress execution.

Since the $_FILES superglobal is used, and no WordPress hooks, this should work
for other plugins that accept file uploads, too, including front-end plugins,
and flash-based uploader.


How to use
----------------------------------------------------------------------------
Copy the upload-scan folder to your site's wp-content/plugins folder, then
activate the plugin, and visit your "Upload Scan" settings page to configure it.

![Options screen](http://github.com/kurtpayne/wordpress-upload-scanner-plugin/raw/master/upload-scanner/screenshot-1.png)

![Log viewer](http://github.com/kurtpayne/wordpress-upload-scanner-plugin/raw/master/upload-scanner/screenshot-2.png)

![Email report](http://github.com/kurtpayne/wordpress-upload-scanner-plugin/raw/master/upload-scanner/screenshot-3.png)

It says ClamAV isn't installed?
----------------------------------------------------------------------------
You'll need to install ClamAV and php-clamav.

http://php-clamav.sourceforge.net

You don't actually need ClamAV to use this plugin, though.  You can use the
system command to run another scanner that can accept input on the command line.

For example, you could scan with avira like this:

    avscan $UPLOAD_SCANNER_ORIG_TEMPNAME


It says exec is disabled
----------------------------------------------------------------------------
Your server admin has probably disabled the "exec" function.  You'll want to
talk to your server admin before moving forward with this plugin.  They may
be able to help, or they may have other security measures in place that mean
you don't need this plugin.


Why doesn't it offer a "delete file" option?
----------------------------------------------------------------------------
PHP automatically deletes the files for you if they're not handled.

"The file will be deleted from the temporary directory at the end of the request
if it has not been moved away or renamed."

http://www.php.net/manual/en/features.file-upload.post-method.php


Are there any security issues running shell commands?
----------------------------------------------------------------------------
The only user input that is passed to the shell command is the original file
name chosen by the user, and this is passed through escapeshellarg() to sanitize
it.  Otherwise, the only command that's run is chosen by you.  It's left to your
server admin (or you) to determine that it's safe for you to issue commands.
This plugin *should* prevent attacker input from making it into the command.
Let me know if you see any problems.


Is this compatible with Multisite?
----------------------------------------------------------------------------
I haven't tested this with Multisite yet.  If you have input, let me know.
