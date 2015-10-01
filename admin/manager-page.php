<?php
defined( 'ABSPATH' ) OR exit;

class OptionsManagerSettingsPage {
    private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;
	private $wp_vital_options;
	private $wp_default_options;

	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = 'cn_';
		$this->wp_vital_options = array( 'siteurl', 'blogname', 'blogdescription', 'users_can_register', 'admin_email', 'start_of_week', 'use_balanceTags', 'use_smilies', 'require_name_email', 'comments_notify', 'posts_per_rss', 'rss_excerpt_length', 'rss_use_excerpt', 'mailserver_url', 'mailserver_login', 'mailserver_pass', 'mailserver_port', 'default_category', 'default_comment_status', 'default_ping_status', 'default_pingback_flag', 'default_post_edit_rows', 'posts_per_page', 'what_to_show', 'date_format', 'time_format', 'links_updated_date_format', 'links_recently_updated_prepend', 'links_recently_updated_append', 'links_recently_updated_time', 'comment_moderation', 'moderation_notify', 'permalink_structure', 'gzipcompression', 'hack_file', 'blog_charset', 'moderation_keys', 'active_plugins', 'home', 'category_base', 'ping_sites', 'advanced_edit', 'comment_max_links', 'gmt_offset', 'default_email_category', 'recently_edited', 'use_linksupdate', 'template', 'stylesheet', 'comment_whitelist', 'blacklist_keys', 'comment_registration', 'open_proxy_check', 'rss_language', 'html_type', 'use_trackback', 'default_role', 'db_version', 'wp_user_roles', 'uploads_use_yearmonth_folders', 'upload_path', 'secret', 'blog_public', 'default_link_category', 'show_on_front', 'default_link_category', 'cron', 'doing_cron', 'sidebars_widgets', 'widget_pages', 'widget_calendar', 'widget_archives', 'widget_meta', 'widget_categories', 'widget_recent_entries', 'widget_text', 'widget_rss', 'widget_recent_comments', 'widget_wholinked', 'widget_polls', 'tag_base', 'page_on_front', 'page_for_posts', 'page_uris', 'page_attachment_uris', 'show_avatars', 'avatar_rating', 'upload_url_path', 'thumbnail_size_w', 'thumbnail_size_h', 'thumbnail_crop', 'medium_size_w', 'medium_size_h', 'dashboard_widget_options', 'current_theme', 'auth_salt', 'avatar_default', 'enable_app', 'enable_xmlrpc', 'logged_in_salt', 'recently_activated', 'random_seed', 'large_size_w', 'large_size_h', 'image_default_link_type', 'image_default_size', 'image_default_align', 'close_comments_for_old_posts', 'close_comments_days_old', 'thread_comments', 'thread_comments_depth', 'page_comments', 'comments_per_page', 'default_comments_page', 'comment_order', 'use_ssl', 'sticky_posts', 'dismissed_update_core', 'update_themes', 'nonce_salt', 'update_core', 'uninstall_plugins', 'wporg_popular_tags', 'stats_options', 'stats_cache', 'rewrite_rules', 'update_plugins', 'category_children', 'timezone_string', 'can_compress_scripts', 'db_upgraded',  'widget_search', 'default_post_format', 'link_manager_enabled', 'initial_db_version',  'theme_switched' );
		$this->wp_default_options = array( '_site_transient_update_core', '_site_transient_timeout_theme_roots', '_site_transient_theme_roots', '_site_transient_update_themes', '_site_transient_update_plugins', '_transient_doing_cron', '_transient_plugins_delete_result_1', '_transient_plugin_slugs', '_transient_random_seed', '_transient_rewrite_rules', '_transient_update_core', '_transient_update_plugins', '_transient_update_themes', 'widget_recent-posts', 'widget_recent-comments' );

		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		add_action( 'wp_ajax_manager_ajax_update_option', array( $this, 'manager_ajax_update_option_callback' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add settings page to admin menu
	 * @return void
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
	 * Load settings JS & CSS
	 * @return void
	 */
	public function options_assets() {

        // We're including the farbtastic script & styles here because they're needed for the colour picker
        wp_register_script( 'comment-notifier-js', $this->assets_url . 'js/manager-list.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'comment-notifier-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        wp_register_style( 'manager-css', $this->assets_url . 'css/manager-css.css', array(), '1', 'all' );

		wp_enqueue_style( 'manager-css' );
        wp_enqueue_script( 'comment-notifier-js' );

	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=options_editor">' . __( 'Settings', 'options_editor' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Parse for certain sources of options rows and return a specialized icon if applicable
	 * @param  string $name	Name of the option
	 * @return string $html String with icon
	 */
	public function wp_options_source( $name ) {

		// WP Core
		if ( in_array( $name, $this->wp_default_options ) || in_array( $name, $this->wp_vital_options ) || preg_match( '/_site_transient_timeout_poptags\w{3,}/', $name ) || preg_match( '/_site_transient_poptags\w{3,}/', $name ) ){
			$html .= "<a class='dashicons dashicons-wordpress source-dashicon' title='WordPress Core option'></a>";

		// Themes
		} else if ( preg_match( '/theme_mods\w{3,}/', $name )  ){
			$html .= "<a class='dashicons dashicons-admin-appearance source-dashicon' title='Theme option'></a>";

		// Jetpack
		} else if ( preg_match( '/jetpack\w{3,}/', $name )  ){
			$html .= "<a class='dashicons source-dashicon' style='font-family: jetpack!important; font-size: 1.3em!important;' title='Jetpack option'>&#61698;</a>";

		// Woocommerce
		} else if ( preg_match( '/woocommerce\w{3,}/', $name ) || preg_match( '/shop_\w{3,}_image_size/', $name ) ){
			$html .= "<a class='dashicons source-dashicon' style='font-family: WooCommerce!important; font-size: 1.3em!important;' title='Woocommerce option'>&#57405;</a>";

		// Gravity Forms
		} else if ( preg_match( '/gform\w{3,}/', $name ) || preg_match( '/gravityform\w{3,}/', $name ) || preg_match( '/rg_form\w{3,}/', $name ) ){
			$html .= "<a class='source-dashicon' title='Gravity Forms option'><img src='".WP_PLUGIN_URL . "/gravityforms/images/gravity-admin-icon.png'></a>";

		// iThemes Security
		} else if ( preg_match( '/itsec\w{3,}/', $name )  ){
			$html .= "<a class='dashicons source-dashicon' style='font-family: ithemes-icons!important; font-size: 1.3em!important;' title='iThemes Security option'>&#61701;</a>";

		// All others from plugins
		} else {
			$html .= "<a class='dashicons dashicons-admin-plugins' title='Plugin option'></a>";
		}

		return $html;

	}

	/**
	 * Return a delete button only for non-core options
	 * @param  string $name	Name of the option
	 * @return string $html String with icon
	 */
	public function get_options_delete_button( $name ) {

		if ( !in_array( $name, $this->wp_vital_options ) ){
			return "<a href='javascript:void(0);' onclick=\"verify_option_deletion( '$name', '".admin_url()."tools.php?page=options_editor&delete_option=$name&nonce=".wp_create_nonce( 'wp_options_delete_'.$name )."' );\" class='button-primary' />".__( 'Delete Option', 'options_editor' )."</a>";
		}

	}

	/**
	 * Handle deletion of options
	 * @return void
	 */
	public function manager_delete_options(){
		global $wpdb;

		if ( $_GET['delete_option'] ){
			$screen = get_current_screen();

		    // Check if current screen is My Admin Page
		    if ( $screen->id != 'tools_page_'.'options_editor' ){
		        return;
		    }

			// Verify the nonce
			if ( wp_verify_nonce( $_GET['nonce'], 'wp_options_delete_'.$_GET['delete_option'] ) ){

				//$where = $wpdb->prepare( "WHERE option_name=%s", $_GET['delete_option'] );
				$wpdb->delete( $wpdb->options , array( 'option_name' => $_GET['delete_option'] ), array( '%s' ) );

			} else {
				return;
			}
		}
	}

	/**
	 * Handle addition of options
	 * @return void
	 */
	public function manager_add_option(){
		global $wpdb;

		$screen = get_current_screen();

	    // Check if current screen is My Admin Page
	    if ( $screen->id != 'tools_page_'.'options_editor' ){
	        return;
	    }

		if ( wp_verify_nonce( $_POST['add_option_nonce'], 'add_option_nonce' ) ){

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
	 * Quick count of all options in the wp_options table
	 * @return string 		H3 with count of options
	 */
	public function manager_count_options(){
		global $wpdb;

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->options" );

		if ( $count ){
			return "<h3>$count total options in the ".$wpdb->prefix."options table</h3>";
		}

	}

	/**
	 * AJAX function for updating rows
	 * @return string 		Modified value
	 */
	function manager_ajax_update_option_callback() {
		global $wpdb; // this is how you get access to the database

		$name = $_REQUEST['id'];
		$value = $_REQUEST['value'];

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
	 * A cacheless function for getting all the options
	 * @return array 		All options from the wp_options table
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
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="options_editor">' . "\n";
			$html .= '<h2>' . __( 'Manage Options' , 'options_editor' ) . '</h2>' . "\n";

				$all_options = $this->get_all_options_cacheless();

				$html .= $this->manager_count_options();

				$html .= "<div id='wp-options-manager' width='100%;'>";

				$html .= "<fieldset class='search-options'>";
					$html .= "<label>Search Options: </label>";
					$html .= "<input type='text' class='search' placeholder='Search' />";
				$html .= "</fieldset>";

				$html .= "<a href='javascript:void(0);' class='button-primary add-option'>".__( 'Add Options', 'options_editor' )."</a>";

				$html .= "<form method='POST' action='".admin_url()."tools.php?page=options_editor' class='add-option-form'>";

					$html .= "<h2>Add Option</h2>";

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

						$html .= "<th scope='col' class='manage-column column-source' style='width:7%;'>".__( 'Source', 'options_editor' )."</th>";
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

		$html .= '</div>' . "\n";

		echo $html;

	}

}