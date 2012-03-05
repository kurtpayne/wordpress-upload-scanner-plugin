<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br/></div>
	<h2>Upload Scanner Options</h2>

	<form id="upload-scanner-settings-form" name="upload-scanner-settings-form" method="post" action="<?php echo add_query_arg( '__action', 'save' ); ?>">
		
		<?php wp_nonce_field( 'upload-scanner-save-settings' ); ?>

		<h3>How to scan files</h3>
		<p>Uploaded files can be scanned using ClamAV (if installed) and a system command can be invoked.  You
			can use both of these actions together, too</p>
		
		<h3>ClamAV Integration</h3>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_use_clamav" name="upload_scanner_use_clamav" <?php disabled( !extension_loaded( 'clamav' ) ); ?> <?php checked( extension_loaded( 'clamav' ) && get_option( 'upload-scanner_use_clamav' ) ); ?> />
			Scan uploaded files with ClamAV
		</label>
		</p>
		<p>
		<strong>Tip:</strong> Test this with the <a href="http://www.eicar.org/86-0-Intended-use.html" target="_blank">EICAR test file</a>
		</p>

		<h3>System Command</h3>
		<p>If you've chosen to invoke a system command, enter it here.  You can use this section to do things
			like log file names to syslog or send files to another malware scanner</p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_use_command" name="upload_scanner_use_command" <?php disabled( !$this->is_exec_enabled() ); ?> <?php checked( $this->is_exec_enabled() && get_option( 'upload-scanner_use_command' ) ); ?> />
			Issue a system command for every uploaded file
		</label>
		</p>

		<p>
		Use this command:<br />
		<textarea id="upload-scanner_command" style="width: 80%; height: 75px; font-family: monospace;" name="upload_scanner_command"><?php echo htmlentities( get_option( 'upload-scanner_command' ) ); ?></textarea>
		</p>
		
		<p>
		<strong>Command variables</strong>
		<br />
		The following shell variables will be available:
		<ul>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_FILENAME</code></strong> - The original filename</li>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_TEMPNAME</code></strong> - The temporary upload location</li>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_FILESIZE</code></strong> - The file size</li>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_FILETYPE</code></strong> - The mimetype of the file</li>
		</ul>
		</p>
		
		<h3>Remediation Actions</h3>
		<p>Tell the system what to do if ClamAV has detected a virus or if the shell command has returned exit status <code>0</code>.</p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_email_admin" name="upload_scanner_onfail_email_admin" <?php checked( get_option( 'upload-scanner_onfail_email_admin' ) ); ?> />
			Send an e-mail
		</label>
		</p>

		<p>
			<strong>Email address</strong><br />
			<input type="text" id="upload-scanner_onfail_email" name="upload_scanner_onfail_email" value="<?php echo htmlentities( get_option( 'upload-scanner_onfail_email' ) ); ?>" />
		</p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_quarantine_file" name="upload_scanner_onfail_quarantine_file" <?php checked( get_option( 'upload-scanner_onfail_quarantine_file' ) ); ?> />
			Move the file to a quarantine folder
		</label>
		</p>

		<p>
			<strong>Quarantine location</strong><br />
			<input type="text" id="upload-scanner_quarantine_folder" name="upload_scanner_quarantine_folder" value="<?php echo htmlentities( get_option( 'upload-scanner_quarantine_folder' ) ); ?>" />
			<br />
			<em>User "<?php echo get_current_user(); ?>" must have access to write to this folder</em>
		</p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_log_message" name="upload_scanner_onfail_log_message" <?php checked( get_option( 'upload-scanner_onfail_log_message' ) ); ?> />
			Log a message
		</label>
		</p>

		<p>
			<strong>Log file location</strong><br />
			<input type="text" id="upload-scanner_log_file" name="upload_scanner_onfail_log_file" value="<?php echo htmlentities( get_option( 'upload-scanner_onfail_log_file' ) ); ?>" />
			<a href="<?php echo add_query_arg( '__action', 'view-log' ); ?>" class="button-secondary">View log</a>
			<br />
			<em>User "<?php echo get_current_user(); ?>" must have access to write to this file</em>
		</p>
		
		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_send_406" name="upload_scanner_onfail_send_406" <?php checked( get_option( 'upload-scanner_onfail_send_406' ) ); ?> />
			Send a "406 - Not Acceptable" status and stop processing the request
		</label>
		</p>

		<br />
		<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" name="upload_scanner_submit1" id="upload-scanner-submit1" />
	</form>
</div>
