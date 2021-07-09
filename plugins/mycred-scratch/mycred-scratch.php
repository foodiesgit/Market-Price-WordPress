<?php
/**
 * Plugin Name: myCRED Scratch Cards
 * Description: Lets your users buy scratch cards using points.
 * Version: 1.3
 * Tags: mycred, games, scratch, card
 * Author: myCRED
 * Author URI: https://mycred.me
 * Author Email: support@mycred.me
 * Requires at least: WP 4.8
 * Tested up to: WP 5.0.2
 * Text Domain: mycred
 * Domain Path: /lang
 * License: Copyrighted
 *
 * Copyright © 2015 - 2017 myCRED
 * 
 * Permission is hereby granted, to the licensed domain to install and run this
 * software and associated documentation files (the "Software") for an unlimited
 * time with the followning restrictions:
 *
 * - This software is only used under the domain name registered with the purchased
 *   license though the myCRED website (mycred.me). Exception is given for localhost
 *   installations or test enviroments.
 *
 * - This software can not be copied and installed on a website not licensed.
 *
 * - This software is supported only if no changes are made to the software files
 *   or documentation. All support is voided as soon as any changes are made.
 *
 * - This software is not copied and re-sold under the current brand or any other
 *   branding in any medium or format.
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
if ( ! class_exists( 'myCRED_Scratch_Core' ) ) :
	final class myCRED_Scratch_Core {

		// Plugin Version
		public $version             = '1.3';

		// Plugin Slug
		public $slug                = 'mycred-scratch';

		// Textdomain
		public $textdomain          = 'mycred';

		// Plugin ID
		public $id                  = 400;

		// Plugin file
		public $plugin              = '';

		// Instnace
		protected static $_instance = NULL;

		// Current session
		public $session             = NULL;

		/**
		 * Setup Instance
		 * @since 1.1
		 * @version 1.0
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;

		}

		/**
		 * Not allowed
		 * @since 1.1
		 * @version 1.0
		 */
		public function __clone() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.7' ); }

		/**
		 * Not allowed
		 * @since 1.1
		 * @version 1.0
		 */
		public function __wakeup() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.7' ); }

		/**
		 * Define
		 * @since 1.1
		 * @version 1.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) )
				define( $name, $value );
		}

		/**
		 * Require File
		 * @since 1.1
		 * @version 1.0
		 */
		public function file( $required_file ) {
			if ( file_exists( $required_file ) )
				require_once $required_file;
		}

		/**
		 * Construct
		 * @since 1.1
		 * @version 1.0
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();
			$this->wordpress();

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ), $this->id );
			add_filter( 'plugins_api',                           array( $this, 'plugin_api_call' ), $this->id, 3 );
			add_filter( 'plugin_row_meta',                       array( $this, 'plugin_view_info' ), 10, 3 );

			$this->plugin = $this->slug . '/' . $this->slug . '.php';

		}

		/**
		 * Define Constants
		 * First, we start with defining all requires constants if they are not defined already.
		 * @since 1.1
		 * @version 1.0
		 */
		private function define_constants() {

			$this->define( 'MYCRED_SCRATCH_VERSION',       $this->version );
			$this->define( 'MYCRED_SCRATCH_SLUG',          $this->slug );

			$this->define( 'MYCRED_SLUG',                 'mycred' );
			$this->define( 'MYCRED_DEFAULT_TYPE_KEY',     'mycred_default' );
			$this->define( 'MYCRED_MAX_CARD_CREATION',    500 );

			$this->define( 'MYCRED_SCRATCH',               __FILE__ );
			$this->define( 'MYCRED_SCRATCH_ROOT',          plugin_dir_path( MYCRED_SCRATCH ) );
			$this->define( 'MYCRED_SCRATCH_CLASSES_DIR',   MYCRED_SCRATCH_ROOT . 'classes/' );
			$this->define( 'MYCRED_SCRATCH_INC_DIR',       MYCRED_SCRATCH_ROOT . 'includes/' );

		}

		/**
		 * Include Plugin Files
		 * @since 1.1
		 * @version 1.1
		 */
		public function includes() {

			$this->file( MYCRED_SCRATCH_CLASSES_DIR . 'class.scratch-card-set.php' );

			$this->file( MYCRED_SCRATCH_INC_DIR . 'functions-plugin.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'functions-cards.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'functions-play-api.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'functions-card-templates.php' );

			$this->file( MYCRED_SCRATCH_INC_DIR . 'functions-ajax.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'functions-shortcodes.php' );

			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/edit-cards.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/edit-cards-ajax.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/manage-cards.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/manage-give-cards.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/manage-purchase-log.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/manage-payout-log.php' );
			$this->file( MYCRED_SCRATCH_INC_DIR . 'admin/manage-settings.php' );

		}

		/**
		 * WordPress
		 * Next we hook into WordPress
		 * @since 1.1
		 * @version 1.0
		 */
		public function wordpress() {

			register_activation_hook( MYCRED_SCRATCH,            array( __CLASS__, 'activate_plugin' ) );
			register_uninstall_hook( MYCRED_SCRATCH,             array( __CLASS__, 'uninstall_plugin' ) );

			add_action( 'mycred_pre_init',                       array( $this, 'mycred_pre_init' ) );
			add_action( 'mycred_init',                           array( $this, 'mycred_init' ), 999 );
			add_action( 'mycred_admin_init',                     array( $this, 'mycred_admin_init' ) );
			add_filter( 'mycred_all_references',                 array( $this, 'add_new_references' ) );

			add_action( 'wp_footer',                             array( $this, 'enqueue_front' ) );
			add_action( 'admin_enqueue_scripts',                 array( $this, 'enqueue_admin' ) );

		}

		/**
		 * Pre Init
		 * @since 1.0
		 * @version 1.0.2
		 */
		public function mycred_pre_init() {

			global $wpdb, $scratch_card_set_db, $mycred_load_playfield, $my_scratch_cards, $current_scratch_card_set;

			$scratch_card_set_db      = $wpdb->prefix . 'scratch_cards';
			$mycred_load_playfield    = false;
			$my_scratch_cards         = false;
			$current_scratch_card_set = false;

		}

		/**
		 * Init
		 * @since 1.0
		 * @version 1.0.1
		 */
		public function mycred_init() {

			$this->register_textdomain();

			mycred_add_cards_post_type();

			$this->populate_globals();

			$this->register_assets();

			$this->register_shortcodes();

			// We have nothing to offer visitors
			if ( ! is_user_logged_in() ) return;

			$this->register_ajax_calls();

			add_action( 'admin_menu', 'mycred_admin_menu_scratch_giveout', 20 );
			add_action( 'admin_menu', 'mycred_admin_menu_scratch_purchases', 30 );
			add_action( 'admin_menu', 'mycred_admin_menu_scratch_payouts', 40 );
			add_action( 'admin_menu', 'mycred_admin_menu_scratch_settings', 90 );

		}

		/**
		 * Admin Init
		 * @since 1.0
		 * @version 1.0
		 */
		public function mycred_admin_init() {

			mycred_scratch_card_admin_actions();

			add_filter( 'manage_scratch_card_set_posts_columns',       'mycred_scratch_card_admin_column_headers' );
			add_action( 'manage_scratch_card_set_posts_custom_column', 'mycred_scratch_card_admin_column_content', 10, 2 );

			add_filter( 'post_row_actions',                            'mycred_scratch_card_admin_row_actions', 10, 2 );
			add_action( 'admin_notices',                               'mycred_scratch_card_admin_update_notices' );

			add_filter( 'enter_title_here',                            'mycred_scratch_card_title_text' );
			add_action( 'save_post_scratch_card_set',                  'mycred_save_scratch_card_set', 10, 2 );
			add_filter( 'admin_post_thumbnail_html',                   'mycred_scratch_card_logo_options', 10, 2 );
			add_filter( 'post_updated_messages',                       'mycred_card_set_update_messages' );

			register_setting( 'mycred-scratch-card', 'mycred_scratch_card_prefs', 'mycred_sanitize_scratch_settings' );

		}

		/**
		 * Register Textdomain
		 * @since 1.1.2
		 * @version 1.0
		 */
		public function register_textdomain() {

			// Load Translation
			$locale = apply_filters( 'plugin_locale', get_locale(), $this->textdomain );

			load_textdomain( $this->textdomain, WP_LANG_DIR . '/' . $this->slug . '/' . $this->textdomain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $this->textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		}

		/**
		 * Populate Globals
		 * @since 1.1.3
		 * @version 1.0
		 */
		public function populate_globals() {

			global $my_scratch_cards;

			$my_scratch_cards = mycred_get_available_card_sets( get_current_user_id() );

		}

		/**
		 * Add New References
		 * @since 1.0
		 * @version 1.0
		 */
		public function add_new_references( $references ) {

			$references['buy_scratch_card'] = __( 'Buying a Scratch Card', 'mycred' );
			$references['win_scratch_card'] = __( 'Winning on a Scratch Card', 'mycred' );

			return $references;

		}

		/**
		 * Register Assets
		 * @since 1.1
		 * @version 1.0
		 */
		public function register_assets() {

			// Styles
			wp_register_style( 'mycred-scratch-bootstrap', plugins_url( 'assets/css/bootstrap.min.css', MYCRED_SCRATCH ), array(), MYCRED_SCRATCH_VERSION, 'all' );
			wp_register_style( 'manage-scratch-cards',     plugins_url( 'assets/css/manage-cards.css', MYCRED_SCRATCH ),  array(), MYCRED_SCRATCH_VERSION, 'all' );
			wp_register_style( 'edit-scratch-cards',       plugins_url( 'assets/css/edit-cards.css', MYCRED_SCRATCH ),    array(), MYCRED_SCRATCH_VERSION, 'all' );

			// Scripts
			wp_register_script( 'mycred-scratch-card',     plugins_url( 'assets/js/scratch.min.js', MYCRED_SCRATCH ),     array(), MYCRED_SCRATCH_VERSION, true );
			wp_register_script( 'edit-scratch-cards',      plugins_url( 'assets/js/edit-cards.js', MYCRED_SCRATCH ),      array( 'jquery' ), MYCRED_SCRATCH_VERSION . '1' );
			wp_register_script( 'manage-scratch-cards',    plugins_url( 'assets/js/manage-cards.js', MYCRED_SCRATCH ),    array( 'jquery' ), MYCRED_SCRATCH_VERSION . '1' );

		}

		/**
		 * Register Shortcodes
		 * @since 1.1
		 * @version 1.0
		 */
		public function register_shortcodes() {

			add_shortcode( 'mycred_scratch_cards',           'mycred_scratch_cards_shortcode' );
			add_shortcode( 'mycred_all_scratch_cards',       'mycred_all_scratch_cards_shortcode' );
			add_shortcode( 'mycred_scratch_history',         'mycred_scratch_history_shortcode' );
			add_shortcode( 'mycred_scratch_cards_remaining', 'mycred_scratch_cards_remaining_shortcode' );

		}

		/**
		 * Register AJAX Call Handlers
		 * @since 1.1.3
		 * @version 1.0
		 */
		public function register_ajax_calls() {

			// There is nothing for us to do if we are handling a visitor
			if ( ! is_user_logged_in() ) return;

			add_action( 'wp_ajax_generate_scratch_cards',         'mycred_ajax_generate_scratch_cards' );
			add_action( 'wp_ajax_toggle_scratch_logo_bg',         'mycred_ajax_toggle_logo_as_bg' );

			add_action( 'mycred_scratch_ajax_load-scratch-card',  'mycred_scratch_ajax_load_field' );
			add_action( 'mycred_scratch_ajax_load-scratch-cards', 'mycred_scratch_ajax_load_fields' );
			add_action( 'mycred_scratch_ajax_buy-scratch-card',   'mycred_scratch_ajax_buy_a_card' );
			add_action( 'mycred_scratch_ajax_new-scratch-card',   'mycred_scratch_ajax_play_a_card' );
			add_action( 'mycred_scratch_ajax_claim-scratch-card', 'mycred_scratch_ajax_payout' );

			// In case our own scripts are calling via the Play.api
			if ( isset( $_POST['action'] ) && isset( $_POST['token'] ) && wp_verify_nonce( $_POST['token'], 'mycred-scratch-' . $_POST['action'] ) ) {

				$action = sanitize_text_field( $_POST['action'] );

				do_action( 'mycred_scratch_ajax_' . $action );

				die;

			}

		}

		/**
		 * Front Enqueue
		 * If a shortcode has been used during page load, we enqueue the
		 * required scripts and styles. This way we do not need to load stuff on every single page load.
		 * @since 1.0
		 * @version 1.1
		 */
		public function enqueue_front() {

			global $mycred_load_playfield;

			$args     = array();
			$settings = mycred_scratch_settings();
			if ( ! array_key_exists( 'load_delay', $settings ) ) $settings['load_delay'] = 0;

			// Delay load option (needs to be in miliseconds
			if ( $settings['load_delay'] > 0 )
				$args['delay'] = absint( $settings['load_delay'] );

			mycred_play_enqueue_all( apply_filters( 'mycred_scratch_play_js', $args, $settings ) );

			wp_enqueue_script( 'mycred-scratch-card' );

		}

		/**
		 * Admin Enqueue
		 * Enqueue scripts and styles on the appropriate admin screens only. There is nothing more
		 * irritating then scripts forced on every single page load.
		 * @since 1.0
		 * @version 1.0.1
		 */
		public function enqueue_admin() {

			$screen = get_current_screen();
			if ( ! isset( $screen->id ) ) return;

			if ( 'scratch_card_set' == $screen->id )
				mycred_edit_cards_admin_styles();

			elseif ( 'edit-scratch_card_set' == $screen->id )
				mycred_manage_cards_admin_styles();

		}

		/**
		 * Activate Plugin
		 * @since 1.0
		 * @version 1.0
		 */
		public static function activate_plugin() {

			global $wpdb;

			$message = array();

			// WordPress check
			$wp_version = $GLOBALS['wp_version'];
			if ( version_compare( $wp_version, '4.0', '<' ) )
				$message[] = __( 'This myCRED Add-on requires WordPress 4.0 or higher. Version detected:', 'mycred' ) . ' ' . $wp_version;

			// PHP check
			$php_version = phpversion();
			if ( version_compare( $php_version, '5.3', '<' ) )
				$message[] = __( 'This myCRED Add-on requires PHP 5.3 or higher. Version detected: ', 'mycred' ) . ' ' . $php_version;

			// SQL check
			$sql_version = $wpdb->db_version();
			if ( version_compare( $sql_version, '5.0', '<' ) )
				$message[] = __( 'This myCRED Add-on requires SQL 5.0 or higher. Version detected: ', 'mycred' ) . ' ' . $sql_version;

			// myCRED Check
			if ( defined( 'myCRED_VERSION' ) && version_compare( myCRED_VERSION, '1.6', '<' ) )
				$message[] = __( 'This add-on requires myCRED 1.6 or higher. Version detected:', 'mycred' ) . ' ' . myCRED_VERSION;

			// Not empty $message means there are issues
			if ( ! empty( $message ) ) {

				$error_message = implode( "\n", $message );
				die( __( 'Sorry but your WordPress installation does not reach the minimum requirements for running this add-on. The following errors were given:', 'mycred' ) . "\n" . $error_message );

			}

			mycred_scratch_install_db();

		}

		/**
		 * Uninstall Plugin
		 * Deletes:
		 * - All card set post types
		 * - All corresponding custom post meta
		 * - The custom scratch card table
		 * @since 1.0
		 * @version 1.0
		 */
		public static function uninstall_plugin() {

			global $wpdb;

			// Delete post types (if any are still around)
			$posts = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'scratch_card_set';" );
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post_id ) {

					// wp_delete_post() will also delete all corresponding meta
					wp_delete_post( $post_id, true );

				}
			}

			// Delete scratch card table
			$scratch_card_set_db = $wpdb->prefix . 'scratch_cards';
			$wpdb->query( "DROP TABLE IF EXISTS {$scratch_card_set_db};" );

			// Finally delete settings
			delete_option( 'mycred_scratch_card_prefs' );
			delete_option( 'mycred_scratch_card_db' );

			// Good bye!

		}

		/**
		 * Plugin Update Check
		 * @since 1.0
		 * @version 1.0
		 */
		public function check_for_plugin_update( $checked_data ) {

			global $wp_version;

			if ( empty( $checked_data->checked ) )
				return $checked_data;

			$args = array(
				'slug'    => $this->slug,
				'version' => $this->version,
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'     => 'version', 
					'request'    => serialize( $args ),
					'api-key'    => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			// Start checking for an update
			$response = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

			if ( ! is_wp_error( $response ) ) {

				$result = maybe_unserialize( $response['body'] );

				if ( is_object( $result ) && ! empty( $result ) )
					$checked_data->response[ $this->slug . '/' . $this->slug . '.php' ] = $result;

			}

			return $checked_data;

		}

		/**
		 * Plugin View Info
		 * @since 1.0.1
		 * @version 1.0.1
		 */
		public function plugin_view_info( $plugin_meta, $file, $plugin_data ) {

			if ( $file != plugin_basename( MYCRED_SCRATCH ) ) return $plugin_meta;

			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $this->slug .
				'&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( __( 'More information about this plugin', 'mycred' ) ),
				esc_attr( 'myCRED Scratch Cards' ),
				__( 'View details', 'mycred' )
			);

			$url     = str_replace( array( 'https://', 'http://' ), '', get_bloginfo( 'url' ) );
			$expires = get_option( 'mycred-premium-' . $this->slug . '-expires', '' );
			if ( $expires != '' ) {

				if ( $expires == 'never' )
					$plugin_meta[] = 'Unlimited License';

				elseif ( absint( $expires ) > 0 ) {

					$days = ceil( ( $expires - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
					if ( $days > 0 )
						$plugin_meta[] = sprintf(
							'License Expires in <strong%s>%s</strong>',
							( ( $days < 30 ) ? ' style="color:red;"' : '' ),
							sprintf( _n( '1 day', '%d days', $days ), $days )
						);

					$renew = get_option( 'mycred-premium-' . $this->slug . '-renew', '' );
					if ( $days < 30 && $renew != '' )
						$plugin_meta[] = '<a href="' . esc_url( $renew ) . '" target="_blank" class="delete">Renew License</a>';

				}

			}

			else $plugin_meta[] = '<a href="http://mycred.me/about/terms/#product-licenses" target="_blank">No license found for - ' . $url . '</a>';

			return $plugin_meta;

		}

		/**
		 * Plugin API Information
		 * @since 1.0
		 * @version 1.0
		 */
		public function plugin_api_call( $result, $action, $args ) {

			global $wp_version;

			if ( ! isset( $args->slug ) || ( $args->slug != $this->slug ) )
				return $result;

			// Get the current version
			$args = array(
				'slug'    => $this->slug,
				'version' => $this->version,
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'     => 'info', 
					'request'    => serialize( $args ),
					'api-key'    => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			$request = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

			if ( ! is_wp_error( $request ) )
				$result = maybe_unserialize( $request['body'] );

			if ( $result->license_expires != '' )
				update_option( 'mycred-premium-' . $this->slug . '-expires', $result->license_expires );

			if ( $result->license_renew != '' )
				update_option( 'mycred-premium-' . $this->slug . '-renew',   $result->license_renew );

			return $result;

		}

	}

endif;

function mycred_scratch_cards_core() {
	return myCRED_Scratch_Core::instance();
}
mycred_scratch_cards_core();
