<?php
/*
Plugin Name: WP Options Editor
Plugin URI: http://wordpress.org/plugins/wp-options-editor/
Description: Easily view, edit, add, and delete all of your WP Options rows from the dashboard
Author: Mike Selander
Version: 1.1
Author URI: http://www.mikeselander.com/
Text Domain: wp-options-editor
Domain Path: /languages
*/

// include the settings page
require_once( 'admin/manager-page.php' );

// Load the settings page if we're in the admin section
if ( is_admin() ){
	$settings = new OptionsManagerSettingsPage( __FILE__ );
}
