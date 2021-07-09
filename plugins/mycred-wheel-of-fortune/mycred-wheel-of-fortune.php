<?php
/**
 * Plugin Name: myCred Wheel of Fortune
 * Version: 2.1
 * Description: myCred Fortune Wheel is a gamification Add-on which attracts visitor of your site and gives them a chance to spin the fortune wheel where they will be awarded by points. It is a different way to offer points to your customers.
 * Plugin URI:  https://wpexperts.io/
 * Author:      myCred
 * Author URI:  https://mycred.me/store/mycred-wheel-of-fortune/
 * Text Domain: fortunewheel
 */

define('mycred_fortunewheel_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('mycred_fortunewheel_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define( 'mycred_fortunewheel', __FILE__ );

function mycred_fortunewheel_crb_get_i18n_suffix() {
    $suffix = '';
    if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
        return $suffix;
    }
    $suffix = '_' . ICL_LANGUAGE_CODE;
    return $suffix;
}

function mycred_fortunewheel_get_post_meta($post_id,$key) {

    if( !empty( carbon_get_post_meta($post_id,$key) ) ) {
        return carbon_get_post_meta($post_id,$key);
    } else {
        return carbon_get_post_meta($post_id,$key.mycred_fortunewheel_crb_get_i18n_suffix());
    }

}

// ADD CARBON FIELD LIBRARY
if( is_admin() && get_post_type() == 'mycred_fortunewheel_wheels' ) {
    include 'inc/settings/carbon-fields/carbon-fields-plugin.php';
}

// Email Subscribers
include 'inc/classes/admin/class-fortunewheel-subscribers.php';
$fortunewheel_email_subscriber = new mycred_fortunewheel_Subscriber();

// MyCred
include 'inc/classes/mycred/class-mycred-settings.php';
$mycred_fortunewheel = new mycred_fortunewheel_MyCred_Settings();

// Woo The Wheel
include 'inc/classes/class-fortunewheel-wheel.php';
$fortunewheel_woo_the_wheel = new mycred_fortunewheel_Wheel();

// fortunewheelo Protect
include 'inc/classes/class-fortunewheel-protect.php';
$fortunewheel_protect_post = new mycred_fortunewheel_Protect();

// Woo The Wheel Admin Settings
include 'inc/classes/class-fortunewheel-settings.php';
$fortunewheel_settigns = new mycred_fortunewheel_Settings();

// Woo The Wheel
include 'inc/classes/class-fortunewheel-coupon-request.php';
$fortunewheel_coupon_request = new mycred_fortunewheel_Coupon_Request();

// Woo Stats
include 'inc/classes/admin/class-fortunewheel-statistics.php';
$fortunewheel_statistics = new mycred_fortunewheel_Statistics();

// Woo Stats
include 'inc/classes/admin/class-fortunewheel-setting-steps.php';
$setting_sections = new Setting_Sections();

// fortunewheel MultiWheel PostType
include 'inc/classes/class-fortunewheel-post-type.php';
$fortunewheel_wheels = new fortunewheel_Wheels();

// myCred fortunewheel Intro
include 'inc/classes/admin/class-mycred-fortunewheel-intro.php';
$myCred_fortunewheel_Intro = new myCred_fortunewheel_Intro();

// Add License System
include 'Licensing/license.php';

function myplugin_init() {
    foreach( $_POST as $key => $value ) {
        if( is_array($value) )
            echo '$key ' . $key . ' - ' . print_r($value ). '<br>';
        else
            echo '$key ' . $key . ' - ' . $value . '<br>';
    }
}

//add_action( 'init', 'myplugin_init' );

add_filter( 'views_edit-fortunewheel-stats', 'so_13813805_add_button_to_views' );
function so_13813805_add_button_to_views( $views ) {
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $views['export-fortunewheel-list'] = '<a href="'.$actual_link.'&export=1" id="export-fortunewheel-list" type="button" class="button button-primary button-large" title="Export fortunewheel List" style="margin: 0px;height: 20px;line-height: 16px;font-size: 12px;">Export fortunewheel List</a>';
    return $views;
}

function mycred_fortunewheel_crb_get_i18n_theme_option( $option_name ) {
    $suffix = mycred_fortunewheel_crb_get_i18n_suffix();
    if(!empty(carbon_get_theme_option( $option_name . $suffix ))){
        return carbon_get_theme_option( $option_name . $suffix );
    } else {
        return carbon_get_theme_option( $option_name );
    }
}

function mycred_fortunewheel_wheel_script_style() {
	wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'fortunewheel-style-css', mycred_fortunewheel_PLUGIN_URL . 'assets/css/style-css.css' );
	wp_enqueue_script( 'fortunewheel-script-js', mycred_fortunewheel_PLUGIN_URL . 'assets/js/script.js' );
}
add_action( 'wp_enqueue_scripts', 'mycred_fortunewheel_wheel_script_style',99 );

function mycred_fortunewheel_admin_wheel_script() {
    if( get_post_type() == 'mycred_fortunewheels' ) {
        wp_enqueue_style( 'fortunewheel-font-awesome-style', 'https://use.fontawesome.com/releases/v5.0.10/css/all.css' );
        wp_enqueue_style( 'fortunewheel-admin-setting-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/setting-style.css' );
        wp_enqueue_script( 'fortunewheel-admin-setting-script', mycred_fortunewheel_PLUGIN_URL . 'assets/js/setting-script.js' );

        wp_enqueue_style( 'fortunewheel-admin-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/admin-style.css' );
        wp_enqueue_script( 'fortunewheel-admin-script-backend', mycred_fortunewheel_PLUGIN_URL . 'assets/js/admin-script.js' );
    }
        $ajaxurl = array(
            'ajaxurl' => admin_url('admin-ajax.php')
        );
		wp_localize_script( 'fortunewheel-admin-script', 'php_data', $ajaxurl );
		
	wp_enqueue_style( 'mycred-fortunewheel-intro', mycred_fortunewheel_PLUGIN_URL . 'assets/css/mycred-fortunewheel-intro.css' );

}
add_action( 'admin_enqueue_scripts', 'mycred_fortunewheel_admin_wheel_script' );

function mycred_fortunewheel_update_posts() {

	$args = array(
			'public'   => true,
			'_builtin' => false
	);

	$output = 'names'; // names or objects, note names is the default
	$operator = 'or'; // 'and' or 'or'

	$post_types = get_post_types(  $args, $output, $operator );
	update_option('fortunewheel_available_posts',$post_types);
}
register_activation_hook( __FILE__, 'fortunewheel_update_posts' );

function mycred_fortunewheel_admin_notice() {

	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', carbon_get_post_meta($_SESSION['wheel_id'], 'active_plugins' ) ) ) ) {
		$class = 'notice notice-error';
		$message = __("Error! <a href='https://wordpress.org/plugins/woocommerce/' target='_blank'>WooCommerce</a> Plugin is required to activate fortunewheel", 'fortunewheel');

		printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}

//add_action( 'admin_notices', 'fortunewheel_admin_notice' );

add_action('admin_print_scripts', 'ure_remove_admin_notices');
function ure_remove_admin_notices() {
    global $wp_filter;
    if (is_user_admin()) {
        if (isset($wp_filter['user_admin_notices'])) {
            unset($wp_filter['user_admin_notices']);
        }
    } elseif (isset($wp_filter['admin_notices'])) {
        unset($wp_filter['admin_notices']);
    }
    if (isset($wp_filter['all_admin_notices'])) {
        unset($wp_filter['all_admin_notices']);
    }
}

function mycred_fortunewheel_activation( $plugin, $network_activation ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=mycred_fortunewheel_into' ) ) );
    }
}
add_action( 'activated_plugin', 'mycred_fortunewheel_activation', 10, 2 );