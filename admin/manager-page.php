<?php
defined( 'ABSPATH' ) OR exit;

/**
 * Options Manager Settings Page
 *
 * This class declares & creates the settings page that runs the manager. All
 * PHP work including AJAX callbacks, page display, and various functionality
 * are currently housed in this class.
 *
 * @category   WordPress
 * @author     Mike Selander
 * @since      Class available since Release 1.0
 */
class OptionsManagerSettingsPage {

	/**
	 * dir
	 * Directory path that this file is in
	 *
	 * @var string
	 * @access private
	 */
    private $dir;

    /**
	 * file
	 * Parent file that calls this class.
	 *
	 * @var string
	 * @access private
	 */
	private $file;

	/**
	 * assets_dir
	 * Directory path housing the plugin assets.
	 *
	 * @var string
	 * @access private
	 */
	private $assets_dir;

	/**
	 * assets_url
	 * Directory URL housing the plugin assets.
	 *
	 * @var string
	 * @access private
	 */
	private $assets_url;

	/**
	 * settings_base
	 * Base ID slug for any options created.
	 *
	 * @var string
	 * @access private
	 */
	private $settings_base;

	/**
	 * settings
	 * Settings base if created.
	 *
	 * @var string
	 * @access private
	 */
	private $settings;

	/**
	 * wp_vital_options
	 * Array of options that are vital to WP working and cannot be deleted.
	 *
	 * @var array
	 * @access private
	 */
	private $wp_vital_options;

	/**
	 * wp_default_options
	 * Array of options that are created by Wp core, but not vital.
	 *
	 * @var array
	 * @access private
	 */
	private $wp_default_options;

	/**
	 * Constructor functon.
	 *
	 * @param string $file File path of declaring parent file.
	 */
	public function __construct( $file ) {
		$this->file				= $file;
		$this->dir				= dirname( $this->file );
		$this->assets_dir		= trailingslashit( $this->dir ) . 'assets';
		$this->assets_url		= esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base	= 'cn_';
		$this->wp_vital_options = array(
			'siteurl',
			'blogname',
			'blogdescription',
			'users_can_register',
			'admin_email',
			'start_of_week',
			'use_balanceTags',
			'use_smilies',
			'require_name_email',
			'comments_notify',
			'posts_per_rss',
			'rss_excerpt_length',
			'rss_use_excerpt',
			'mailserver_url',
			'mailserver_login',
			'mailserver_pass',
			'mailserver_port',
			'default_category',
			'default_comment_status',
			'default_ping_status',
			'default_pingback_flag',
			'default_post_edit_rows',
			'posts_per_page',
			'what_to_show',
			'date_format',
			'time_format',
			'links_updated_date_format',
			'links_recently_updated_prepend',
			'links_recently_updated_append',
			'links_recently_updated_time',
			'comment_moderation',
			'moderation_notify',
			'permalink_structure',
			'gzipcompression',
			'hack_file',
			'blog_charset',
			'moderation_keys',
			'active_plugins',
			'home',
			'category_base',
			'ping_sites',
			'advanced_edit',
			'comment_max_links',
			'gmt_offset',
			'default_email_category',
			'recently_edited',
			'use_linksupdate',
			'template',
			'stylesheet',
			'comment_whitelist',
			'blacklist_keys',
			'comment_registration',
			'open_proxy_check',
			'rss_language',
			'html_type',
			'use_trackback',
			'default_role',
			'db_version',
			'wp_user_roles',
			'uploads_use_yearmonth_folders',
			'upload_path',
			'secret',
			'blog_public',
			'default_link_category',
			'show_on_front',
			'default_link_category',
			'cron',
			'doing_cron',
			'sidebars_widgets',
			'widget_pages',
			'widget_calendar',
			'widget_archives',
			'widget_meta',
			'widget_categories',
			'widget_recent_entries',
			'widget_text',
			'widget_rss',
			'widget_recent_comments',
			'widget_wholinked',
			'widget_polls',
			'tag_base',
			'page_on_front',
			'page_for_posts',
			'page_uris',
			'page_attachment_uris',
			'show_avatars',
			'avatar_rating',
			'upload_url_path',
			'thumbnail_size_w',
			'thumbnail_size_h',
			'thumbnail_crop',
			'medium_size_w',
			'medium_size_h',
			'dashboard_widget_options',
			'current_theme',
			'auth_salt',
			'avatar_default',
			'enable_app',
			'enable_xmlrpc',
			'logged_in_salt',
			'recently_activated',
			'random_seed',
			'large_size_w',
			'large_size_h',
			'image_default_link_type',
			'image_default_size',
			'image_default_align',
			'close_comments_for_old_posts',
			'close_comments_days_old',
			'thread_comments',
			'thread_comments_depth',
			'page_comments',
			'comments_per_page',
			'default_comments_page',
			'comment_order',
			'use_ssl',
			'sticky_posts',
			'dismissed_update_core',
			'update_themes',
			'nonce_salt',
			'update_core',
			'uninstall_plugins',
			'wporg_popular_tags',
			'stats_options',
			'stats_cache',
			'rewrite_rules',
			'update_plugins',
			'category_children',
			'timezone_string',
			'can_compress_scripts',
			'db_upgraded',
			'widget_search',
			'default_post_format',
			'link_manager_enabled',
			'initial_db_version',
			'theme_switched'
		);
		$this->wp_default_options = array(
			'_site_transient_update_core',
			'_site_transient_timeout_theme_roots',
			'_site_transient_theme_roots',
			'_site_transient_update_themes',
			'_site_transient_update_plugins',
			'_transient_doing_cron',
			'_transient_plugins_delete_result_1',
			'_transient_plugin_slugs',
			'_transient_random_seed',
			'_transient_rewrite_rules',
			'_transient_update_core',
			'_transient_update_plugins',
			'_transient_update_themes',
			'widget_recent-posts',
			'widget_recent-comments'
		);

		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wp_ajax_manager_ajax_update_option', array( $this, 'manager_ajax_update_option_callback' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );

	}


	/**
	 * Add settings page to admin menu.
	 *
	 * @see $this->options_assets
	 */
	public function add_menu_item() {

		$page = add_submenu_page(
			'tools.php',
			__( 'Manage WP Options Table', 'options_editor' ),
			__( 'Manage wp_options', 'options_editor' ),
			'manage_options',
			'options_editor',
			array( $this, 'settings_page' )
		);

		add_action( 'admin_print_styles-' . $page, array( $this, 'options_assets' ) );
		add_action('load-'.$page, array( $this, 'manager_delete_options' ) );
		add_action('load-'.$page, array( $this, 'manager_add_option' ) );

	}

	/**
	 * Load the textdomain for this plugin if translation is available
	 *
	 * @see load_plugin_textdomain
	 */
	public function load_textdomain() {
	    load_plugin_textdomain( 'options_editor', FALSE, basename( dirname( $this->file ) ) . '/languages/' );
	}


	/**
	 * Load settings JS & CSS on our specific admin page
	 *
	 * @see wp_register_script, wp_localize_script, wp_enqueue_style, wp_enqueue_script
	 */
	public function options_assets() {

        // We're including the farbtastic script & styles here because they're needed for the colour picker
        wp_register_script( 'comment-notifier-js', $this->assets_url . 'js/manager-list.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script(
        	'comment-notifier-js',
        	'ajax_object',
        	array(
        		'ajax_url'	=> admin_url( 'admin-ajax.php' )
        	)
        );

        wp_register_style( 'manager-css', $this->assets_url . 'css/manager-css.css', array(), '1', 'all' );

		wp_enqueue_style( 'manager-css' );
        wp_enqueue_script( 'comment-notifier-js' );

	}


	/**
	 * Add settings link to plugin list table.
	 *
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {

		$settings_link = '<a href="options-general.php?page=options_editor">' . __( 'Edit Options', 'options_editor' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;

	}


	/**
	 * Parse for certain sources of options rows and return a specialized icon
	 * if applicable.
	 *
	 * This function is a basic case evaluator that swaps out an icon for easy
	 * identification of the author of a particular option. The benefit here is
	 * that you can easily see who's adding a bunch of nonsense to your options
	 * table.
	 *
	 * @param  string $name	Name of the option
	 * @return string 	 	String with icon
	 */
	public function wp_options_source( $name ) {
		$html = '';

		// WP Core
		if ( in_array( $name, $this->wp_default_options ) || in_array( $name, $this->wp_vital_options ) || preg_match( '/_site_transient_timeout_poptags\w{3,}/', $name ) || preg_match( '/_site_transient_poptags\w{3,}/', $name ) ){
			$html .= "<a class='dashicons dashicons-wordpress source-dashicon' title='".__( 'WordPress Core option', 'options_editor' )."'></a>";

		// Themes
		} else if ( preg_match( '/theme_mods\w{3,}/', $name )  ){
			$html .= "<a class='dashicons dashicons-admin-appearance source-dashicon' title='".__( 'Theme option', 'options_editor' )."'></a>";

		// Jetpack
		} else if ( preg_match( '/jetpack\w{3,}/', $name )  ){
			$html .= "<a class='dashicons source-dashicon' style='font-family: jetpack!important; font-size: 1.3em!important;' title='".__( 'Jetpack option', 'options_editor' )."'>&#61698;</a>";

		// Woocommerce
		} else if ( preg_match( '/woocommerce\w{3,}/', $name ) || preg_match( '/shop_\w{3,}_image_size/', $name ) ){
			$html .= "<a class='dashicons source-dashicon' style='font-family: WooCommerce!important; font-size: 1.3em!important;' title='".__( 'WooCommerce option', 'options_editor' )."'>&#57405;</a>";

		// Gravity Forms
		} else if ( preg_match( '/gform\w{3,}/', $name ) || preg_match( '/gravityform\w{3,}/', $name ) || preg_match( '/rg_form\w{3,}/', $name ) ){
			$html .= "<a class='dashicons source-dashicon' style='background-image: url(\"data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSItMTUgNzcgNTgxIDY0MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAtMTUgNzcgNTgxIDY0MCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGcgaWQ9IkxheWVyXzIiPjxwYXRoIGZpbGw9IiM5OTkiIGQ9Ik00ODkuNSwyMjdMNDg5LjUsMjI3TDMxNS45LDEyNi44Yy0yMi4xLTEyLjgtNTguNC0xMi44LTgwLjUsMEw2MS44LDIyN2MtMjIuMSwxMi44LTQwLjMsNDQuMi00MC4zLDY5Ljd2MjAwLjVjMCwyNS42LDE4LjEsNTYuOSw0MC4zLDY5LjdsMTczLjYsMTAwLjJjMjIuMSwxMi44LDU4LjQsMTIuOCw4MC41LDBMNDg5LjUsNTY3YzIyLjItMTIuOCw0MC4zLTQ0LjIsNDAuMy02OS43VjI5Ni44QzUyOS44LDI3MS4yLDUxMS43LDIzOS44LDQ4OS41LDIyN3ogTTQwMSwzMDAuNHY1OS4zSDI0MXYtNTkuM0g0MDF6IE0xNjMuMyw0OTAuOWMtMTYuNCwwLTI5LjYtMTMuMy0yOS42LTI5LjZjMC0xNi40LDEzLjMtMjkuNiwyOS42LTI5LjZzMjkuNiwxMy4zLDI5LjYsMjkuNkMxOTIuOSw0NzcuNiwxNzkuNiw0OTAuOSwxNjMuMyw0OTAuOXogTTE2My4zLDM1OS43Yy0xNi40LDAtMjkuNi0xMy4zLTI5LjYtMjkuNnMxMy4zLTI5LjYsMjkuNi0yOS42czI5LjYsMTMuMywyOS42LDI5LjZTMTc5LjYsMzU5LjcsMTYzLjMsMzU5Ljd6IE0yNDEsNDkwLjl2LTU5LjNoMTYwdjU5LjNIMjQxeiIvPjwvZz48L3N2Zz4=\"); background-repeat: no-repeat;' title='".__( 'Gravity Forms option', 'options_editor' )."'></a>";

		// iThemes Security
		} else if ( preg_match( '/itsec\w{3,}/', $name )  ){
			$html .= "<a class='dashicons source-dashicon' style='font-family: ithemes-icons!important; font-size: 1.3em!important;' title='".__( 'iThemes Security option', 'options_editor' )."'>&#61701;</a>";

		// Yoast/WP SEO
		} else if ( preg_match( '/wpseo\w{3,}/', $name )  ){
			$html .= "<a class='dashicons source-dashicon' style='background-image: url(\"data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbDpzcGFjZT0icHJlc2VydmUiIGZpbGw9IiM5OTkiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48Zz48Zz48Zz48Zz48cGF0aCBzdHlsZT0iZmlsbDojOTk5IiBkPSJNMjAzLjYsMzk1YzYuOC0xNy40LDYuOC0zNi42LDAtNTRsLTc5LjQtMjA0aDcwLjlsNDcuNywxNDkuNGw3NC44LTIwNy42SDExNi40Yy00MS44LDAtNzYsMzQuMi03Niw3NlYzNTdjMCw0MS44LDM0LjIsNzYsNzYsNzZIMTczQzE4OSw0MjQuMSwxOTcuNiw0MTAuMywyMDMuNiwzOTV6Ii8+PC9nPjxnPjxwYXRoIHN0eWxlPSJmaWxsOiM5OTkiIGQ9Ik00NzEuNiwxNTQuOGMwLTQxLjgtMzQuMi03Ni03Ni03NmgtM0wyODUuNywzNjVjLTkuNiwyNi43LTE5LjQsNDkuMy0zMC4zLDY4aDIxNi4yVjE1NC44eiIvPjwvZz48L2c+PHBhdGggc3R5bGU9ImZpbGw6Izk5OSIgc3Ryb2tlLXdpZHRoPSIyLjk3NCIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBkPSJNMzM4LDEuM2wtOTMuMywyNTkuMWwtNDIuMS0xMzEuOWgtODkuMWw4My44LDIxNS4yYzYsMTUuNSw2LDMyLjUsMCw0OGMtNy40LDE5LTE5LDM3LjMtNTMsNDEuOWwtNy4yLDF2NzZoOC4zYzgxLjcsMCwxMTguOS01Ny4yLDE0OS42LTE0Mi45TDQzMS42LDEuM0gzMzh6IE0yNzkuNCwzNjJjLTMyLjksOTItNjcuNiwxMjguNy0xMjUuNywxMzEuOHYtNDVjMzcuNS03LjUsNTEuMy0zMSw1OS4xLTUxLjFjNy41LTE5LjMsNy41LTQwLjcsMC02MGwtNzUtMTkyLjdoNTIuOGw1My4zLDE2Ni44bDEwNS45LTI5NGg1OC4xTDI3OS40LDM2MnoiLz48L2c+PC9nPjwvc3ZnPg==\"); background-repeat: no-repeat' title='".__( 'Yoast/WP SEO option', 'options_editor' )."'></a>";

		// All others from plugins
		} else {
			$html .= "<a class='dashicons dashicons-admin-plugins' title='".__( 'Plugin option', 'options_editor' )."'></a>";
		}

		return $html;

	}


	/**
	 * Return a delete button only for non-core options.
	 *
	 * @param  string $name	Name of the option
	 * @return string 		Deletion button
	 */
	public function get_options_delete_button( $name ) {

		// Check that the user is logged in & has proper permissions
		if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ){
			return;
		}

		if ( !in_array( $name, $this->wp_vital_options ) ){
			return "<a href='javascript:void(0);' onclick=\"verify_option_deletion( '$name', '".admin_url()."tools.php?page=options_editor&delete_option=$name&nonce=".wp_create_nonce( 'wp_options_delete_'.$name )."' );\" class='button-primary' />".__( 'Delete', 'options_editor' )."</a>";
		}

	}


	/**
	 * Handle deletion of options.
	 *
	 * @global object $wpdb WP database object access
	 */
	public function manager_delete_options(){
		global $wpdb;

		if ( isset( $_GET['delete_option'] ) ){
			$screen = get_current_screen();

		    // Check if current screen is My Admin Page
		    if ( $screen->id != 'tools_page_options_editor' ){
		        return;
		    }

		    // Check that the user is logged in & has proper permissions
			if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ){
				return;
			}

			// Verify the nonce
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'wp_options_delete_'.$_GET['delete_option'] ) ){

				$wpdb->delete( $wpdb->options , array( 'option_name' => $_GET['delete_option'] ), array( '%s' ) );

			} else {
				return;
			}
		}

	}


	/**
	 * Handle addition of options.
	 *
	 * @global object $wpdb WP database object access
	 */
	public function manager_add_option(){
		global $wpdb;

		$screen = get_current_screen();

	    // Check if current screen is My Admin Page
	    if ( $screen->id != 'tools_page_options_editor' ){
	        return;
	    }

	    // Check that the user is logged in & has proper permissions
		if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ){
			return;
		}

		if ( isset( $_POST['add_option_nonce'] ) && wp_verify_nonce( $_POST['add_option_nonce'], 'add_option_nonce' ) ){

			$wpdb->insert(
				$wpdb->options,
				array(
					'option_name' => $_POST['add_option_name'],
					'option_value' => $_POST['add_option_value']
				),
				array(
					'%s',
					'%s'
				)
			);

		}
	}


	/**
	 * Quick count of all options in the wp_options table.
	 *
	 * @global object $wpdb WP database object access
	 *
	 * @return string 		H3 with count of options
	 */
	public function manager_count_options(){
		global $wpdb;

		// Check that the user is logged in & has proper permissions
		if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ){
			return;
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->options" );

		if ( $count ){
			return "<h3>".sprintf( __( '%d total options in the %s table', 'options_editor' ), $count, $wpdb->prefix.'options' )."</h3>";
		}

	}


	/**
	 * AJAX function for updating rows.
	 *
	 * @global object $wpdb WP database object access
	 *
	 * @return string Modified value
	 */
	public function manager_ajax_update_option_callback() {
		global $wpdb;

		// Check that the user is logged in & has proper permissions
		if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ){
			return;
		}

		$name	= $_REQUEST['id'];
		$value	= $_REQUEST['value'];

		$wpdb->update(
			$wpdb->options,
			array( 'option_value' => $value ),
			array( 'option_name' => $name ),
			array( '%s' ),
			array( '%s' )
		);

		echo $value;

		die();

	}


	/**
	 * A cacheless function for getting all the options.
	 *
	 * @global object $wpdb WP database object access
	 *
	 * @return array All options from the wp_options table
	 */
	public function get_all_options_cacheless(){
		global $wpdb;

		$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options" );

		foreach ( (array) $alloptions_db as $o ) {
            $alloptions[$o->option_name] = $o->option_value;
        }

        return $alloptions;

	}


	/**
	 * Load settings page content.
	 */
	public function settings_page() {
		$html = '';

		// Check that the user is logged in & has proper permissions
		if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ){
			return;
		}

		// Build page HTML
		$html .= '<div class="wrap" id="options_editor">';
			$html .= '<h2>' . __( 'Manage Options' , 'options_editor' ) . '</h2>';

				$all_options = $this->get_all_options_cacheless();

				$html .= $this->manager_count_options();

				$html .= "<div id='wp-options-manager' width='100%;'>";

				$html .= "<fieldset class='search-options'>";
					$html .= "<label>".__( 'Live Search', 'options_editor' )." </label>";
					$html .= "<input type='text' class='search' placeholder='".__( 'Search', 'options_editor' )."' />";
				$html .= "</fieldset>";

				$html .= "<a href='javascript:void(0);' class='button-primary add-option'>".__( 'Add Option', 'options_editor' )."</a>";

				$html .= "<form method='POST' action='".admin_url()."tools.php?page=options_editor' class='add-option-form'>";

					$html .= "<h2>".__( 'Add Option', 'options_editor' )."</h2>";

					$html .= "<table class='form-table'>";

						$html .= "<tbody>";

							$html .= "<tr>";

								$html .= "<th><label>".__( 'Option Name:', 'options_editor' )."</label></th>";
								$html .= "<td><input type='text' name='add_option_name'></td>";

							$html .= "</tr>";

							$html .= "<tr>";

								$html .= "<th><label>".__( 'Option Value:', 'options_editor' )."</label></th>";
								$html .= "<td><input type='text' name='add_option_value'></td>";

							$html .= "</tr>";

							$html .= wp_nonce_field( 'add_option_nonce', 'add_option_nonce', '', false );

						$html .= "</tbody>";

					$html .= "</table>";

					$html .= "<button type='submit' class='submit button-primary'>".__( 'Submit', 'options_editor' )."</button>";

				$html .= "</form>";

				$html .= "<table class='wp-list-table widefat'>";

					$html .= "<thead>";

						$html .= "<th scope='col' class='manage-column column-source' style='width:7%;'>".__( 'Author', 'options_editor' )."</th>";
						$html .= "<th scope='col' class='sort manage-column column-name' data-sort='option-name'>".__( 'Option Name', 'options_editor' )."</th>";
						$html .= "<th scope='col' class='sort manage-column column-information' data-sort='option-value'>".__( 'Option Data', 'options_editor' )."</th>";
						$html .= "<th scope='col' class='manage-column column-date' style='width:14%;'>".__( 'Actions', 'options_editor' )."</th>";

					$html .= "</thead>";

					$html .= "<tbody class='list'>";

						$i = 0;

						foreach( $all_options as $name => $value ) {

							$class = '';
							if( $i&1 ) {
								$class = ' class="alternate"';
							}

							$html .= "<tr$class>";

								$html .= "<td>".$this->wp_options_source( $name )."</td>";
								$html .= "<td class='option-name'>$name</td>";
								$html .= "<td class='option-value'><div class='edit' id='$name'>$value</div></td>";
								$html .= "<td>".$this->get_options_delete_button( $name )."</td>";

							$html .= "</tr>";

							$i++;
						}

					$html .= "</tbody>";

				$html .= "</table>";

				$html .= "</div>";

		$html .= "</div>";

		echo $html;

	}

}