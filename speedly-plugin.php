<?php
/*
Plugin Name: Speedly
Plugin URI: http://Speedly.io
Description: The Speedly plugin helps you purge Speedly's cache after you update your Wordpress content
Version: 1.1.0
Author: Jerre Baumeister
Author URI: http://speedly.io
*/

/*  Copyright 2015  Speedly

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define( 'SPEEDLY_VERSION', '1.0.0' );
define( 'SPEEDLY_RELEASE_DATE', date_i18n( 'F j, Y', '1397937230' ) );
define( 'SPEEDLY_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPEEDLY_URL', plugin_dir_url( __FILE__ ) );

$options = get_option( 'Speedly_settings' );
define( 'SPEEDLY_TOKEN', $options['Speedly_token']);
define( 'SPEEDLY_SPEEDKIT', $options['Speedly_speedkit']);
define( 'SPEEDLY_CLEAR_CACHE_URL', 'https://api.speedly.io/purge/'.SPEEDLY_TOKEN);

if (is_admin()) {
	require(SPEEDLY_DIR . 'speedly-settings.php');	
}

// Add Toolbar Menus
function speedly_toolbar() {
	global $wp_admin_bar;

	## Main menu

	$args = array(
		'id'     => 'speedly',
		'title'  => __( 'Speedly purge cache', 'text_domain' ),
		'href'   => SPEEDLY_CLEAR_CACHE_URL,
	);
	$wp_admin_bar->add_menu($args);

}

// Register style sheet.
add_action( 'admin_enqueue_scripts', 'register_speedly_admin_scripts' );

function register_speedly_admin_scripts() {
	wp_register_style( 'speedly-toolbar', plugins_url().'/speedly/toolbar.css');
	wp_enqueue_style( 'speedly-toolbar' );

	//wp_register_script('backend-jquery', 'https://code.jquery.com/jquery-2.1.3.min.js', null,null,true);
	//wp_enqueue_script('backend-jquery');
	wp_enqueue_script('jquery');

	wp_register_script('purger', plugins_url().'/speedly/purger.js', null, null, true);
	wp_enqueue_script('purger');
}

// Register style sheet.
add_action( 'wp_enqueue_scripts', 'register_speedkit' );

function register_speedkit() {
	if(SPEEDLY_SPEEDKIT) {
		wp_register_script( 'speedly-speedkit', plugins_url().'/speedly/SpeedKit.min.js', null, null, true);
		wp_enqueue_script( 'speedly-speedkit' );
	}
}

// Hook into the 'wp_before_admin_bar_render' action
add_action( 'wp_before_admin_bar_render', 'speedly_toolbar', 999 );	


// Little function to return a custom field value
function speedly_get_custom_field( $value ) {
	global $post;

    $custom_field = get_post_meta( $post->ID, $value, true );
    if ( !empty( $custom_field ) )
	    return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );

    return false;
}

// Register the Metabox
function speedly_meta_box() {
	add_meta_box( 'wpshed-meta-box', __( 'SpeedKit', 'textdomain' ), 'speedly_meta_box_output', 'page', 'side', 'high' );
}
//add_action( 'add_meta_boxes', 'speedly_meta_box' );

// Output the Metabox
function speedly_meta_box_output( $post ) {
	// create a nonce field
	wp_nonce_field( 'my_speedly_meta_box_nonce', 'speedly_meta_box_nonce' ); ?>
	
	<p>
		<label for="speedly_textfield"><?php _e( 'Activate SpeedKit on this page?', 'textdomain' ); ?></label>

    </p>
    
	<?php
}

// Save the Metabox values
function speedly_meta_box_save( $post_id ) {
	// Stop the script when doing autosave
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// Verify the nonce. If insn't there, stop the script
	if( !isset( $_POST['speedly_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['speedly_meta_box_nonce'], 'my_speedly_meta_box_nonce' ) ) return;

	// Stop the script if the user does not have edit permissions
	if( !current_user_can( 'edit_post' ) ) return;

    // Save the textfield
	if( isset( $_POST['speedly_textfield'] ) )
		update_post_meta( $post_id, 'speedly_textfield', esc_attr( $_POST['speedly_textfield'] ) );

    // Save the textarea
	if( isset( $_POST['speedly_textarea'] ) )
		update_post_meta( $post_id, 'speedly_textarea', esc_attr( $_POST['speedly_textarea'] ) );
}
add_action( 'save_post', 'speedly_meta_box_save' );
?>