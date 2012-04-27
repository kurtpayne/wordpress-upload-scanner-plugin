<?php

if ( !defined( 'ABSPATH') ) {
    exit();
}

?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br/></div>
	<h2><?php _e( 'Upload Scan Log', 'upload-scanner' ); ?></h2>
	<textarea id="upload-scanner-view-log" name="upload_scanner_view_log" style="font-family: monospace; width: 90%; height: 500px; margin: 25px auto auto 25px;"><?php
		if ( $this->does_log_file_exist() ) {
			$fp = fopen( get_option( 'upload-scanner_onfail_log_file' ) , 'r' );
			while ( ( $line = fgetss( $fp ) )!== false ) {
				echo htmlentities( $line );
			}
			fclose( $fp );
		} else {
			_e( 'Log file does not exist or is not readable', 'upload-scanner' );
		}
	?></textarea>
	<p>
	<a href="<?php echo remove_query_arg( '__action' ); ?>" class="button-secondary"><?php _e( 'Back', 'upload-scanner' ); ?></a>
	</p>
</div>
