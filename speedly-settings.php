<?php
add_action( 'admin_menu', 'Speedly_add_admin_menu' );
add_action( 'admin_init', 'Speedly_settings_init' );


function Speedly_add_admin_menu() { 

	add_options_page( 'Speedly', 'Speedly', 'manage_options', 'speedly', 'speedly_options_page' );

}


function Speedly_settings_init() { 

	register_setting( 'pluginPage', 'Speedly_settings' );

	add_settings_section(
		'Speedly_pluginPage_section', 
		__( '<br>Settings', 'wordpress' ), 
		'Speedly_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'Speedly_token', 
		__( 'Speedly Token', 'wordpress' ), 
		'Speedly_token_render', 
		'pluginPage', 
		'Speedly_pluginPage_section' 
	);

	add_settings_field( 
		'Speedly_speedkit', 
		__( 'Include SpeedKit?', 'wordpress' ), 
		'Speedly_speedkit_render', 
		'pluginPage', 
		'Speedly_pluginPage_section' 
	);

}


function Speedly_token_render() { 

	$options = get_option( 'Speedly_settings' );
	?>
	<input type='text' name='Speedly_settings[Speedly_token]' value='<?php echo $options['Speedly_token']; ?>'>
	<?php

}


function Speedly_speedkit_render() { 

	$options = get_option( 'Speedly_settings' );
	?>
	<input type='radio' name='Speedly_settings[Speedly_speedkit]' <?php checked( $options['Speedly_speedkit'], 1 ); ?> value='1'> Yes
	&nbsp;&nbsp;&nbsp;
	<input type='radio' name='Speedly_settings[Speedly_speedkit]' <?php checked( $options['Speedly_speedkit'], 0 ); ?> value='0'> No
	<?php

}


function Speedly_settings_section_callback() { 

	echo __( '<strong style="color:red">WARNING:</strong> Changing these settings can cause problems. Only make changes when you are instructed to do so. Please <a href="http://speedly.io/" target="_blank">contact our support team</a> if you need help!<br><br>If you are looking to clear the Speedly cache, please <a href="'.SPEEDLY_CLEAR_CACHE_URL.'" target="_blank">click here</a>.', 'wordpress' );

}


function Speedly_options_page() { 

	?>
	<form action='options.php' method='post'>
		
		<h2>Speedly</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>