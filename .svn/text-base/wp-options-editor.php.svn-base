<?php
/*
Plugin Name: WP Options Editor
Plugin URI: http://wordpress.org/plugins/wp-options-editor/
Description: More easily view, edit, add, and delete all of your WP Options from the dashboard
Author: Mike Selander
Version: 1.0
Author URI: http://www.mikeselander.com/
*/

// include the settings page
require_once( 'admin/manager-page.php' );

// Load the settings page if we're in the admin section
if ( is_admin() ){
	$settings = new OptionsManagerSettingsPage( __FILE__ );
}

// define the plugin url
if (! defined('WPOE_URL') ){
	define('WPOE_URL', plugins_url( '' ,  __FILE__ ) );
}
?>