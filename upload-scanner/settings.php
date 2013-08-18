<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br/></div>
	<h2><?php _e( 'Upload Scanner Options', 'upload-scanner' ); ?></h2>

	<form id="upload-scanner-settings-form" name="upload-scanner-settings-form" method="post" action="<?php echo add_query_arg( '__action', 'save' ); ?>">
		
		<?php wp_nonce_field( 'upload-scanner-save-settings' ); ?>

		<h3><?php _e( 'How to scan files', 'upload-scanner' ); ?></h3>
		<p><?php _e( 'Uploaded files can be scanned using ClamAV (if installed) and a system command can be invoked.  You can use both of these actions together, too', 'upload-scanner' ); ?></p>
		
		<h3><?php _e( 'ClamAV Integration', 'upload-scanner' ); ?></h3>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_use_clamav" name="upload_scanner_use_clamav" <?php disabled( !extension_loaded( 'clamav' ) ); ?> <?php checked( extension_loaded( 'clamav' ) && get_site_option( 'upload-scanner_use_clamav' ) ); ?> />
			<?php _e( 'Scan uploaded files with ClamAV', 'upload-scanner' ); ?>
		</label>
		</p>
		<p>
		<?php printf( __( '<strong>Tip:</strong> Test this with the <a href="%s" target="_blank">EICAR test file</a>', 'upload-scanner' ), 'http://www.eicar.org/86-0-Intended-use.html' ); ?>
		</p>

		<h3><?php _e( 'System Command', 'upload-scanner' ); ?></h3>
		<p><?php _e( "If you've chosen to invoke a system command, enter it here.  You can use this section to do things like log file names to syslog or send files to another malware scanner", 'upload-scanner' ); ?></p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_use_command" name="upload_scanner_use_command" <?php disabled( !$this->is_exec_enabled() ); ?> <?php checked( $this->is_exec_enabled() && get_site_option( 'upload-scanner_use_command' ) ); ?> />
			<?php _e( 'Issue a system command for every uploaded file', 'upload-scanner' ); ?>
		</label>
		</p>

		<p>
		<?php _e( 'Use this command:', 'upload-scanner' ); ?><br />
		<textarea id="upload-scanner_command" style="width: 80%; height: 75px; font-family: monospace;" name="upload_scanner_command"><?php echo htmlentities( get_site_option( 'upload-scanner_command' ) ); ?></textarea>
		</p>
		
		<p>
		<strong><?php _e( 'Command variables', 'upload-scanner' ); ?></strong>
		<br />
		<?php _e( 'The following shell variables will be available:', 'upload-scanner' ); ?>
		<ul>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_FILENAME</code></strong> - <?php _e( 'The original filename', 'upload-scanner' ); ?></li>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_TEMPNAME</code></strong> - <?php _e( 'The temporary upload location<', 'upload-scanner' ); ?>/li>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_FILESIZE</code></strong> - <?php _e( 'The file size', 'upload-scanner' ); ?></li>
			<li><strong><code>$UPLOAD_SCANNER_ORIG_FILETYPE</code></strong> - <?php _e( 'The mimetype of the file', 'upload-scanner' ); ?></li>
		</ul>
		</p>
		
		<h3><?php _e( 'Remediation Actions', 'upload-scanner' ); ?></h3>
		<p><?php _e( 'Tell the system what to do if ClamAV has detected a virus or if the shell command has returned exit status <code>1</code>.', 'upload-scanner' ); ?></p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_email_admin" name="upload_scanner_onfail_email_admin" <?php checked( get_site_option( 'upload-scanner_onfail_email_admin' ) ); ?> />
			<?php _e( 'Send an e-mail', 'upload-scanner' ); ?>
		</label>
		</p>

		<p>
			<strong><?php _e( 'Email address' ); ?></strong><br />
			<input type="text" id="upload-scanner_onfail_email" name="upload_scanner_onfail_email" value="<?php echo esc_attr( get_site_option( 'upload-scanner_onfail_email' ) ); ?>" />
		</p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_quarantine_file" name="upload_scanner_onfail_quarantine_file" <?php checked( get_site_option( 'upload-scanner_onfail_quarantine_file' ) ); ?> />
			<?php _e( 'Move the file to a quarantine folder', 'upload-scanner' ); ?>
		</label>
		</p>

		<p>
			<strong><?php _e( 'Quarantine location', 'upload-scanner' ); ?></strong><br />
			<input type="text" id="upload-scanner_quarantine_folder" name="upload_scanner_quarantine_folder" value="<?php echo esc_attr( get_site_option( 'upload-scanner_quarantine_folder' ) ); ?>" />
			<br />
			<em><?php printf( __( 'User %s must have access to write to this folder', 'upload-scanner'), get_current_user() ); ?></em>
		</p>

		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_log_message" name="upload_scanner_onfail_log_message" <?php checked( get_site_option( 'upload-scanner_onfail_log_message' ) ); ?> />
			<?php _e( 'Log a message', 'upload-scanner' ); ?>
		</label>
		</p>

		<p>
			<strong><?php _e( 'Log file location', 'upload-scanner' ); ?></strong><br />
			<input type="text" id="upload-scanner_log_file" name="upload_scanner_onfail_log_file" value="<?php echo esc_attr( get_site_option( 'upload-scanner_onfail_log_file' ) ); ?>" />
			<a href="<?php echo add_query_arg( '__action', 'view-log' ); ?>" class="button-secondary"><?php _e( 'View log', 'upload-scanner' ); ?></a>
			<br />
			<em><?php printf( __( 'User %s must have access to write to this file', 'upload-scanner'), get_current_user() ) ; ?></em>
		</p>
		
		<p>
		<label>
			<input type="checkbox" id="upload-scanner_onfail_send_406" name="upload_scanner_onfail_send_406" <?php checked( get_site_option( 'upload-scanner_onfail_send_406' ) ); ?> />
			<?php _e( 'Send a "406 - Not Acceptable" status and stop processing the request', 'upload-scanner' ); ?>
		</label>
		</p>

		<br />
		<input type="submit" class="button-primary" value="<?php _e( 'Save', 'upload-scanner' ); ?>" name="upload_scanner_submit1" id="upload-scanner-submit1" />
	</form>
</div>
