<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! defined( 'BOOMBOX_CHILD_THEME_PATH' ) ) {
	define( 'BOOMBOX_CHILD_THEME_PATH', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'BOOMBOX_CHILD_THEME_URL' ) ) {
	define( 'BOOMBOX_CHILD_THEME_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

add_action( 'wp_enqueue_scripts', 'boombox_child_enqueue_styles', 100 );
function boombox_child_enqueue_styles() {
	wp_enqueue_style( 'boombox-child-style', get_stylesheet_uri(), array(), boombox_get_assets_version() );
}

//Custom CSS Widget
add_action('admin_menu', 'custom_css_hooks');
add_action('save_post', 'save_custom_css');
add_action('wp_head','insert_custom_css');
function custom_css_hooks() {
	add_meta_box('custom_css', 'Custom CSS', 'custom_css_input', 'post', 'normal', 'high');
	add_meta_box('custom_css', 'Custom CSS', 'custom_css_input', 'page', 'normal', 'high');
}
function custom_css_input() {
	global $post;
	echo '<input type="hidden" name="custom_css_noncename" id="custom_css_noncename" value="'.wp_create_nonce('custom-css').'" />';
	echo '<textarea name="custom_css" id="custom_css" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,'_custom_css',true).'</textarea>';
}
function save_custom_css($post_id) {
	if (!wp_verify_nonce($_POST['custom_css_noncename'], 'custom-css')) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	$custom_css = $_POST['custom_css'];
	update_post_meta($post_id, '_custom_css', $custom_css);
}
function insert_custom_css() {
	if (is_page() || is_single()) {
		if (have_posts()) : while (have_posts()) : the_post();
			echo '<style type="text/css">'.get_post_meta(get_the_ID(), '_custom_css', true).'</style>';
		endwhile; endif;
		rewind_posts();
	}
}

// Close comments on the front-end
function df_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
	$comments = array();
	return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu() {
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}
add_action('init', 'df_disable_comments_admin_bar');

//Footer Admin Change
function shapeSpace_custom_admin_footer() {
	echo 'BCurry <a href="https://betcurry.com/">Plataform</a>';
}
add_filter('admin_footer_text', 'shapeSpace_custom_admin_footer');

// remove toolbar items (examples)
// https://digwp.com/2016/06/remove-toolbar-items/
function shapeSpace_remove_toolbar_nodes($wp_admin_bar) {

	$wp_admin_bar->remove_node('wp-logo');
	$wp_admin_bar->remove_node('comments');
	$wp_admin_bar->remove_node('customize-background');
	$wp_admin_bar->remove_node('customize-header');

}
add_action('admin_bar_menu', 'shapeSpace_remove_toolbar_nodes', 999);

//Redirecionar o link autor para home page
add_filter( 'author_link', 'my_author_link' );
 
function my_author_link() {
 
return home_url( 'ho' );
 
}


// TRADUÇÃO DE TODO O SITE E ALGUNS REPLACES //

function start_modify_html() {
ob_start();
}

function end_modify_html() {
   $html = ob_get_clean();
   $user_ID = get_current_user_id();	
   $country = get_user_meta($user_ID, 'country', true);
   $currency = get_user_meta($user_ID, 'currency', true);	
   $wl = get_user_meta($user_ID, 'cipbet_lang', true);
   $wl0 = isset($_COOKIE['cipbet_lang_type']) ? cipbet_sanitize($_COOKIE['cipbet_lang_type']) : get_option('bp_default_lang_type');

//*** LOGO DAS EQUIPES //
// New York Red Bulls //
$html = str_replace( 'class="div_home" style="color:#444;width:100%;float:left;font-size:15px;">New York Red Bulls', 'class="div_home" style="color:#444;width:100%;float:left;font-size:15px;"><img style="display: inline!important;margin-op:-2px!important;" src="https://api.sofascore.com/api/v1/team/1957/image" width="20" height="18" /> New York Red Bulls', $html );
$html = str_replace( 'class="div_away" style="color:#444;width:100%;float:left;font-size:15px;">New York Red Bulls', 'class="div_away" style="color:#444;width:100%;float:left;font-size:15px;"><img style="display: inline!important;margin-op:-2px!important;" src="https://api.sofascore.com/api/v1/team/1957/image" width="20" height="18" /> New York Red Bulls', $html );	
	
	
// TRADUZIR SÍMBOLO DAS MOEDAS //
if ( in_array($currency, array('EUR'))) {
$html = str_replace( 'R$', '€', $html );
}
	
if ( in_array($currency, array('USD'))) {
$html = str_replace( 'R$', '$', $html );
}
	
if ( in_array($currency, array('RUB'))) {
$html = str_replace( 'R$', '₽', $html );
}	
	   
if ( in_array($currency, array('GBP'))) {
$html = str_replace( 'R$', '£', $html );
}
	
	echo $html;
}
add_action( 'wp_head', 'start_modify_html' );
add_action( 'wp_footer', 'end_modify_html' );	
	   
	
