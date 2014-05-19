<div class="wrap">
	<h2><?php _e('Reservation Online Settings'); ?></h2>
	<?php if ( isset($_GET['settings-updated'])) : ?>
   		<div id="message" class="updated"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
    <?php endif; // If the form has just been submitted, this shows the notification ?>
	<form method="post" action="options.php" autocomplete="off">
		<?php 
			settings_fields('ca_rsv_online_settings');
			do_settings_sections('ca-rsv-online/admin/setting.php'); 
			submit_button(); 
		?>
	</form>
</div>