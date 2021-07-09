<?php

/*
* Plugin Name: BetPress
* Plugin URI: http://www.web-able.com/betpress/
* Description: A game where users predict sports games (and not only) by placing betting slips.
* Author: WebAble
* Author URI: http://www.web-able.com
* Version: 1.0.2
*/


//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}


global $betpress_version, $betpress_db_version;

$betpress_version = '1.0.2';
$betpress_db_version = '1.0.2';


//add some constants
define('BETPRESS_DIR_PATH', plugin_dir_path(__FILE__));
define('BETPRESS_MAIN_FILE_DIR', __FILE__);
define('BETPRESS_IMAGE_FOLDER', plugin_dir_url(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR);
define('BETPRESS_VIEWS_DIR', BETPRESS_DIR_PATH . 'views' . DIRECTORY_SEPARATOR);
define('BETPRESS_TIME', 'd-m-Y H:i O');
define('BETPRESS_TIME_NO_ZONE', 'd-m-Y H:i');
define('BETPRESS_TIME_HUMAN_READABLE', 'l jS \of F Y h:i:s A');
define('BETPRESS_DECIMAL', 'decimal');
define('BETPRESS_FRACTION', 'fraction');
define('BETPRESS_AMERICAN', 'american');
define('BETPRESS_SPORTS_TO_ADD_DURING_ACTIVATION', 2);
define('BETPRESS_EVENTS_TO_ADD_DURING_ACTIVATION', 2);
define('BETPRESS_BET_EVENTS_TO_ADD_DURING_ACTIVATION', 2);
define('BETPRESS_BET_EVENTS_CATS_TO_ADD_DURING_ACTIVATION', 2);
define('BETPRESS_STATUS_UNSUBMITTED', 'unsubmitted');
define('BETPRESS_STATUS_AWAITING', 'awaiting');
define('BETPRESS_STATUS_WINNING', 'winning');
define('BETPRESS_STATUS_LOSING', 'losing');
define('BETPRESS_STATUS_CANCELED', 'canceled');
define('BETPRESS_STATUS_TIMED_OUT', 'timed_out');
define('BETPRESS_STATUS_ACTIVE', 'active');
define('BETPRESS_STATUS_PAST', 'past');
define('BETPRESS_STATUS_FAIL', 'fail');
define('BETPRESS_STATUS_PAID', 'paid');
define('BETPRESS_VALUE_YES', 'yes');
define('BETPRESS_VALUE_ON', 'on');
define('BETPRESS_VALUE_ALL', 'all');
define('BETPRESS_IMPORT_NEW', 'new_data');
define('BETPRESS_IMPORT_UPDATE', 'update_data');
define('BETPRESS_POINTS', 'points');
define('BETPRESS_BOUGHT_POINTS', 'bought_points');

// DEFINIR FUSO HORÁRIO //
define('BETPRESS_12N', 'GMT-12');
define('BETPRESS_1130N', 'GMT-11:30');
define('BETPRESS_11N', 'GMT-11');
define('BETPRESS_1030N', 'GMT-10:30');
define('BETPRESS_10N', 'GMT-10:30');
define('BETPRESS_930N', 'GMT-9:30');
define('BETPRESS_9N', 'GMT-9');
define('BETPRESS_830N', 'GMT-8:30');
define('BETPRESS_8N', 'GMT-8');
define('BETPRESS_730N', 'GMT-7:30');
define('BETPRESS_7N', 'GMT-7');
define('BETPRESS_630N', 'GMT-6:30');
define('BETPRESS_6N', 'GMT-6');
define('BETPRESS_530N', 'GMT-5:30');
define('BETPRESS_5N', 'GMT-5');
define('BETPRESS_430N', 'GMT-4:30');
define('BETPRESS_4N', 'GMT-4');
define('BETPRESS_5N', 'GMT-5');
define('BETPRESS_430N', 'GMT-4:30');
define('BETPRESS_4N', 'GMT-4');
define('BETPRESS_330N', 'GMT-3:30');
define('BETPRESS_3N', 'GMT-3');
define('BETPRESS_230N', 'GMT-2:30');
define('BETPRESS_2N', 'GMT-2');
define('BETPRESS_130N', 'GMT-1:30');
define('BETPRESS_1N', 'GMT-1');
define('BETPRESS_0000P', 'GMT-0000');
define('BETPRESS_0030P', 'GMT-0030');
define('BETPRESS_0100P', 'GMT-0100');
define('BETPRESS_0130P', 'GMT-0130');
define('BETPRESS_0200P', 'GMT-0200');
define('BETPRESS_0230P', 'GMT-0230');
define('BETPRESS_0300P', 'GMT-0300');
define('BETPRESS_0330P', 'GMT-0330');
define('BETPRESS_0400P', 'GMT-0400');
define('BETPRESS_0430P', 'GMT-0430');
define('BETPRESS_0500P', 'GMT-0500');
define('BETPRESS_0530P', 'GMT-0530');
define('BETPRESS_0545P', 'GMT-0545');
define('BETPRESS_0600P', 'GMT-0600');
define('BETPRESS_0630P', 'GMT-0630');
define('BETPRESS_0700P', 'GMT-0700');
define('BETPRESS_0730P', 'GMT-0730');
define('BETPRESS_0800P', 'GMT-0800');
define('BETPRESS_0830P', 'GMT-0830');
define('BETPRESS_0845P', 'GMT-0845');
define('BETPRESS_0900P', 'GMT-0900');
define('BETPRESS_0930P', 'GMT-0930');
define('BETPRESS_1000P', 'GMT-1000');
define('BETPRESS_1030P', 'GMT-1030');
define('BETPRESS_1100P', 'GMT-1100');
define('BETPRESS_1130P', 'GMT-1130');
define('BETPRESS_1200P', 'GMT-1200');
define('BETPRESS_1245P', 'GMT-1245');
define('BETPRESS_1300P', 'GMT-1300');
define('BETPRESS_1345P', 'GMT-1345');
define('BETPRESS_1400P', 'GMT-1400');

// DEFINIR IDIOMAS //
define('BETPRESS_BR', 'br');
define('BETPRESS_EN', 'en');
define('BETPRESS_ES', 'es');

//define('BETPRESS_XML_URL', 'http://xml.cdn.betclic.com/odds_br.xml');


//include custom functions and db queries
if (file_exists(BETPRESS_DIR_PATH . 'functions.php')) {
    
    require_once 'functions.php';
}


//include wp ajax library
function betpress_add_ajax_library() {
    
    if (file_exists(BETPRESS_DIR_PATH . 'includes' . DIRECTORY_SEPARATOR . 'ajaxurl.php')) {
    
        require_once 'includes' . DIRECTORY_SEPARATOR . 'ajaxurl.php';
    } 
}
add_action('wp_head', 'betpress_add_ajax_library');


//load js & css
function betpress_register_scripts() {
    
    wp_register_script('js_front', plugins_url('/includes/js/front.js', __FILE__), array('jquery', 'wp-ajax-response'), false, true);
	wp_register_script('js_front_live', plugins_url('/includes/js/liveevent.js', __FILE__), array('jquery', 'wp-ajax-response'), false, true);
    wp_register_script('js_timepicker', plugins_url('/includes/js/timepicker.js', __FILE__), array('jquery'), false, true);
    wp_register_style('css_style', plugins_url('/includes/css/style.css', __FILE__));
}
add_action('init', 'betpress_register_scripts');


//use js & css
function betpress_use_scripts() {
    
    wp_enqueue_style('css_style', plugins_url('/includes/css/style.css', __FILE__));
    wp_enqueue_style('jquery-style', 'https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/themes/blitzer/jquery-ui.css');
    
    if (is_admin()) {
        
        wp_enqueue_style('wp-color-picker');
        
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('js_timepicker', plugins_url('/includes/js/timepicker.js', __FILE__), array('jquery'), false, true);
        wp_enqueue_script('js_admin', plugins_url('/includes/js/admin.js', __FILE__), array('jquery', 'js_timepicker', 'wp-color-picker'), false, true);
		
        
        wp_localize_script(
                'js_admin',
                'i18n_admin',
                array(
                    'sport_delete_confirm_message' => __('You are about to delete the sport and all the sport associated data. Are you sure?', 'BetPress'),
                    'event_delete_confirm_message' => __('You are about to delete the event and all the event associated data. Are you sure?', 'BetPress'),
                    'bet_event_delete_confirm_message' => __('You are about to delete the bet event and all the bet event associated data. Are you sure?', 'BetPress'),
                    'cat_delete_confirm_message' => __('You are about to delete the category and all the category associated data. Are you sure?', 'BetPress'),
                    'bet_option_delete_confirm_message' => __('You are about to delete the bet option and all the bet option associated data. Are you sure?', 'BetPress'),
                )
        );
        
    } else {
        
        wp_enqueue_script('js_front', plugins_url('/includes/js/front.js', __FILE__), array('jquery'), false, true);     
        
        wp_localize_script(
                'js_front',
                'i18n_front',
                array(
                    'show' => __('Show', 'BetPress'),
                    'hide' => __('Hide', 'BetPress'),
                    'toggle_symbol_minus' => __('-', 'BetPress'),
                    'toggle_symbol_plus' => __('+', 'BetPress'),
                    'loading' => __('Loading...', 'BetPress'),
                )
        );
    }
}
add_action('wp_enqueue_scripts', 'betpress_use_scripts');
add_action('admin_enqueue_scripts', 'betpress_use_scripts');


//load translations
function betpress_load_translations() {
    
    load_plugin_textdomain('BetPress', FALSE, dirname(plugin_basename(__FILE__)) . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR);
}
add_action('plugins_loaded', 'betpress_load_translations');




function betpress_display_odd($decimal_odd_string) {
    
    $decimal_odd = number_format($decimal_odd_string, 2, '.', ',')."<br>";
    
    $desired_odd = betpress_get_desired_odd();
    
    switch ($desired_odd) {
        
        case BETPRESS_AMERICAN:

            if (2 > $decimal_odd) {
                
                $plus_minus = '-';
                $result = 100 / ($decimal_odd - 1);
                
            } else {
                
                $plus_minus = '+';
                $result = ($decimal_odd - 1) * 100;
            }
                
            return ($plus_minus . betpress_floordec($result, 2));
            
        case BETPRESS_FRACTION:
            
            if (2 == $decimal_odd) {
                return '1/1';
            }
            
            $dividend = intval(strval((($decimal_odd - 1) * 100)));
            $divisor = 100;
            
            $smaller = ($dividend > $divisor) ? $divisor : $dividend;
            
            //worst case: 100 iterations
            for ($common_denominator = $smaller; $common_denominator > 0; $common_denominator --) {
                
                if ( (0 === ($dividend % $common_denominator)) && (0 === ($divisor % $common_denominator)) ) {
                    
                    $dividend /= $common_denominator;
                    $divisor /= $common_denominator;
                    
                    return ($dividend . '/' . $divisor);
                }
            }
            
            return ($dividend . '/' . $divisor);
            
        //no filtering need for BETPRESS_DECIMAL, thats how we store the odd in db
        default:
            return $decimal_odd;
    }
}
add_filter('betpress_odd', 'betpress_display_odd');


//register the settings the wp way
function betpress_register_settings() {

    register_setting('bp_settings_group', 'bp_starting_points', 'betpress_sanitize_positive_number');
    register_setting('bp_settings_group', 'bp_close_bets', 'betpress_sanitize_positive_number');
    register_setting('bp_settings_group', 'bp_min_stake', 'betpress_sanitize_positive_number');
    register_setting('bp_settings_group', 'bp_max_stake', 'betpress_sanitize_positive_number');
    register_setting('bp_settings_group', 'bp_one_win_per_cat', 'betpress_sanitize_checkbox');
    register_setting('bp_settings_group', 'bp_only_int_stakes', 'betpress_sanitize_checkbox');
    register_setting('bp_settings_group', 'bp_default_odd_type', 'betpress_sanitize_odd_select');
    register_setting('bp_settings_group', 'bp_max_points_to_buy', 'betpress_sanitize_positive_number');
    register_setting('bp_settings_group', 'bp_max_allowed_points', 'betpress_sanitize_positive_number');
    register_setting('bp_settings_group', 'bp_paypal_mail', 'betpress_sanitize_email');
    register_setting('bp_settings_group', 'bp_paypal_url_fail', 'betpress_sanitize_url');
    register_setting('bp_settings_group', 'bp_paypal_token', 'betpress_sanitize');
    register_setting('bp_settings_group', 'bp_paypal_sandbox', 'betpress_sanitize_checkbox');
    register_setting('bp_settings_group', 'bp_paypal_success_message', 'betpress_sanitize_pp_success');
    register_setting('bp_settings_group', 'bp_paypal_error_message', 'betpress_sanitize_pp_error');
    register_setting('bp_settings_group', 'bp_sport_title_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_sport_title_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_sport_container_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_event_title_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_event_title_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_event_container_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_bet_event_title_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_bet_event_title_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_cat_title_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_cat_title_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_cat_container_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_button_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_button_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_featured_heading_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_featured_heading_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_featured_name_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_featured_name_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_featured_button_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_featured_button_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_lb_table_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_lb_heading_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_lb_odd_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_lb_even_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_slip_heading_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_slip_heading_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_slip_row_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_slip_row_text_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_slip_subrow_bg_color', 'betpress_sanitize_color');
    register_setting('bp_settings_group', 'bp_slip_subrow_text_color', 'betpress_sanitize_color');
    
    // as of version 1.0.2
    register_setting('bp_settings_group', 'bp_points_per_approved_comment', 'betpress_sanitize_positive_number_or_zero');
    add_option('bp_points_per_approved_comment', 0);
}
add_action('admin_init', 'betpress_register_settings');


//register admin menu
function betpress_register_admin_menu_page() {
    
    add_menu_page(
            __('BetPress settings', 'BetPress'),    //page title
            __('BetPress', 'BetPress'),             //menu title
            'manage_options',                       //capability
            'betpress-settings',                    //menu slug
            'betpress_settings_controller'          //callback
    );

    add_submenu_page(
            'betpress-settings',                    //parent slug
            __('Bettings', 'BetPress'),             //page title
            __('Bettings', 'BetPress'),             //menu title
            'manage_options',                       //capability
            'betpress-sports-and-events',           //menu slug
            'betpress_bettings_controller'          //callback
    );
    
    add_submenu_page(
            'betpress-settings',
            __('Leaderboards', 'BetPress'),
            __('Leaderboards', 'BetPress'),
            'manage_options',
            'bp-leaderboards',
            'betpress_leaderboards_controller'
    );
    
    add_submenu_page(
            'betpress-settings',
            __('PayPal log', 'BetPress'),
            __('PayPal log', 'BetPress'),
            'manage_options',
            'bp-paypal',
            'betpress_paypal_controller'
    );
    
    add_submenu_page(
            'betpress-settings',
            __('Points log', 'BetPress'),
            __('Points log', 'BetPress'),
            'manage_options',
            'bp-points-log',
            'betpress_points_log_controller'
    );
    
    add_submenu_page(
            'betpress-settings',
            __('Auto insert data', 'BetPress'),
            __('Auto insert data', 'BetPress'),
            'manage_options',
            'bp-auto-insert',
            'betpress_auto_insert_controller'
    );
    
    add_submenu_page(
            'betpress-settings',
            __('Import/Export', 'BetPress'),
            __('Import/Export', 'BetPress'),
            'manage_options',
            'bp-import-export',
            'betpress_import_export_controller'
    );

}
add_action('admin_menu', 'betpress_register_admin_menu_page');


function betpress_modify_admin_users_table($column) {
    
    $column['betpress_points'] = __('BetPress Points', 'BetPress');
    $column['betpress_buyed_points'] = __('BetPress Bought Points', 'BetPress');
    
    return $column;
}
add_filter('manage_users_columns', 'betpress_modify_admin_users_table');


function betpress_modify_admin_users_table_data($val, $column_name, $user_ID) {
    
    switch ($column_name) {
        
        case 'betpress_points' :
            $user_points_db = get_user_meta($user_ID, 'bp_points', true);
            $user_points = ('' === $user_points_db) ? get_option('bp_starting_points') : (float) $user_points_db;
            return $user_points;
            
        case 'betpress_buyed_points' :
            return ( (float) get_user_meta($user_ID, 'bp_points_buyed', true) );
            
        default:
            return;
    }
}
add_filter('manage_users_custom_column', 'betpress_modify_admin_users_table_data', 10, 3);


function betpress_admin_user_custom_profile($user) {
    
    $user_points_db = esc_attr(get_user_meta($user->ID, 'bp_points', true));
    $user_points_buyed_db = esc_attr(get_user_meta($user->ID, 'bp_points_buyed', true));

    $pass['user_points'] = ('' === $user_points_db) ? get_option('bp_starting_points') : (float) $user_points_db;
    $pass['user_buyed_points'] = (float) $user_points_buyed_db;
    $pass['admin_url'] = get_admin_url(null, 'admin.php?page=bp-points-log&user_id=' . $user->ID);
    betpress_get_view('user-edit-extra-fields', 'admin', $pass);
}
add_action('edit_user_profile', 'betpress_admin_user_custom_profile');
add_action('show_user_profile', 'betpress_admin_user_custom_profile');


function betpress_admin_user_custom_profile_save($user_ID) {
	
	if ( ! current_user_can('manage_options') ) {
            return false;
        }
        
        $new_user_points = betpress_sanitize($_POST['bp_points']);
        $new_user_bought_points = betpress_sanitize($_POST['bp_points_buyed']);
        
        if ( ( ! is_numeric($new_user_points) ) || ( ! is_numeric($new_user_bought_points) ) ) {
            wp_die(__('BetPress points and bought points must be numbers.', 'BetPress'));
        }
        
        $old_user_points = get_user_meta($user_ID, 'bp_points', true);
        $old_user_bought_points = get_user_meta($user_ID, 'bp_points_buyed', true);
	
	update_user_meta($user_ID, 'bp_points', $new_user_points);
	update_user_meta($user_ID, 'bp_points_buyed', $new_user_bought_points);
        
        if (strcmp(get_user_meta($user_ID, 'bp_points', true), $new_user_points) === 0) {
            
            if (strcmp($new_user_points, $old_user_points) !== 0) {
            
                betpress_insert(
                    'points_log',
                    array(
                        'user_id' => $user_ID,
                        'comment_id' => 0,
                        'admin_id' => get_current_user_id(),
                        'points_amount' => $new_user_points - $old_user_points,
                        'date' => time(),
                        'type' => BETPRESS_POINTS,
                    )
                );
            
            }
            
        } else {
            
            $db_error = true;
            
        }
        
        if (strcmp(get_user_meta($user_ID, 'bp_points_buyed', true), $new_user_bought_points) === 0) {
            
            if (strcmp($new_user_bought_points, $old_user_bought_points) !== 0) {
            
                betpress_insert(
                    'points_log',
                    array(
                        'user_id' => $user_ID,
                        'comment_id' => 0,
                        'admin_id' => get_current_user_id(),
                        'points_amount' => $new_user_bought_points - $old_user_bought_points,
                        'date' => time(),
                        'type' => BETPRESS_BOUGHT_POINTS,
                    )
                );
            
            }
            
        } else {
            
            $db_error = true;
            
        }
        
        if (isset($db_error) && $db_error === true) {
            
            wp_die(__('Database error.', 'BetPress'));
            
        }
}
add_action('edit_user_profile_update', 'betpress_admin_user_custom_profile_save');
add_action('personal_options_update', 'betpress_admin_user_custom_profile_save');

function betpress_add_dashboard_widgets() {
    
    if (current_user_can('manage_options')) {
        
        wp_add_dashboard_widget('betpress_dashboard', __('BetPress', 'BetPress'), 'betpress_render_admin_dashboard_widget');
    }
}
add_action('wp_dashboard_setup', 'betpress_add_dashboard_widgets' );

function betpress_comment_approved($comment_object) {
    
    $comment = $comment_object->to_array();
    
    betpress_award_user_for_approved_comment($comment['user_id'], $comment['comment_ID']);
}
add_action('comment_unapproved_to_approved', 'betpress_comment_approved');


function betpress_new_comment($comment_ID, $is_approved) {
    
    if (1 === $is_approved) {
        
        $comment = get_comment($comment_ID, ARRAY_A);
        
        betpress_award_user_for_approved_comment($comment['user_id'], $comment_ID);
    }
}
add_action('comment_post', 'betpress_new_comment', 10, 2);

//for odds

function get_match_odds_callback()
{
	
	
	$curl = curl_init();
	$token = "87521-RYCHtUfNcS5XQM";
	if(!isset($_POST['event_id']))
	{
		echo json_encode(array("status" => 0 , "msg" => "event id not present"));
		wp_die();
		return;
	}
	$event_id = $_POST['event_id'];
	$url = "https://api.b365api.com/v3/bet365/prematch?token=87521-RYCHtUfNcS5XQM&FI=".$event_id;
	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
// 		$response = json_decode($response,true);
		echo $response;

	}
	wp_die();
	
}
add_action( 'wp_ajax_get_match_odds', 'get_match_odds_callback' );
add_action( 'wp_ajax_nopriv_get_match_odds', 'get_match_odds_callback' );


//get match live odds
//

function get_match_live_odds()
{
	$curl = curl_init();
	$token = "87521-RYCHtUfNcS5XQM";
	if(!isset($_POST['event_id']))
	{
		echo json_encode(array("status" => 0 , "msg" => "event id not present"));
		wp_die();
		return;
	}
	
	$event_id = $_POST['event_id'];
	$url = "https://api.b365api.com/v1/bet365/event?token=87521-RYCHtUfNcS5XQM&FI=".$event_id;
	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		$response = json_decode($response,true);
// 		todo : process the response here.
		echo processLiveData($response);

	}
	wp_die();
	
}


function processLiveData($data)
{
	if($data['success'] != 1)
	{
		return json_encode(array("status" => 0 , "msg" => "odds not found","res" => $data));
	}
	
	$response = [];
	
	$len = count($data['results'][0]);
    // echo $len;
	$i = 0;
	$data = $data['results'][0];
	$response['event'] = $data[0];
	while($i < $len)
	{
        // echo $data[$i]['type'];
		if($data[$i]['type'] == "MG")
		{
            // echo $data[$i]['NA'];
            $oddName = $data[$i]['NA'];
			$response['results'][$oddName] = $data[$i]; 
			$j = $i+1;
			while($j < $len)
			{
				if($j >= $len)
				{
					$i = $j-1;
					break;
				}
				if($data[$j]['type'] == "MG" || $data[$j]['type'] == "ST" || $data[$j]['type'] == "SG" )
				{
					$i = $j-1;
					break;
				}else{
                    // echo "heelo";
                    // print_r($data[$j]);
					$response['results'][$oddName]['odds'][] = $data[$j];
				}
				
				$j += 1;
			}
		
		}
		
		$i += 1;
	}
	if(count($response['results']) > 0)
	{
		$response['success'] = 1;
	}else{
		$response['success'] = 0;
	}
	
	echo json_encode($response);
}

add_action( 'wp_ajax_get_match_live_odds', 'get_match_live_odds' );
add_action( 'wp_ajax_nopriv_get_match_live_odds', 'get_match_live_odds' );


function get_inplay_events()
{
	if(isset($_POST['sport_id'])){
		$sport_id = $_POST['sport_id'];
	}else{
		$sport_id = 1;
	}
	$curl = curl_init();
	$url = "https://api.b365api.com/v1/bet365/inplay?token=87521-RYCHtUfNcS5XQM&sport_id=".$sport_id;
	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		$response = json_decode($response,true);
// 		todo : process the response here.
		echo processLiveEventData($response);

	}
	wp_die();

	
}

function processLiveEventData($data)
{
	if($data['success'] != 1)
	{
		return json_encode(array("status" => 0 , "msg" => "odds not found"));
	}
	
	$response = [];
	
	$len = count($data['results'][0]);
    // echo $len;
	$i = 0;
	$data = $data['results'][0];
	while($i < $len)
	{
        // echo $data[$i]['type'];
		if($data[$i]['type'] == "CL")
		{
			$sportID = $data[$i]['ID'];
			$response['sport'][$sportID] = array(
				
				"sportId" => $sportID
			);

			$j = $i + 1;
			while($j < $len)
			{
				if($data[$j]['type'] == "CT")
				{
					$league = $data[$j]['NA'];
					
					
					$k = $j+ 1;
					while($k < $len)
					{
						
						if($data[$k]['type'] == "EV")
						{
							$oddName = $data[$k]['NA'];
							$matchData = array(
								"ID" => $data[$k]['ID'],
								"league" => $data[$k]['CT'],
								"match"  => $data[$k]['NA'],
								"score"  => $data[$k]['SS'],
							);

							$response['sport'][$sportID]['league'][$league][$oddName] = $matchData;
							$l = $k + 1;
							while($l < $len)
							{
								if($l >= $len)
								{
									$k = $l-1;
									break;
								}
								if($data[$l]['type'] == "EV" || $data[$l]['type'] == "CT")
								{
									$k = $l-1;
									break;
								}else{
									$response['sport'][$sportID]['league'][$league][$oddName]['odds'][] = $data[$l];	
								}
								$l += 1;
							}
						}elseif ($data[$k]['type'] == "CT" || $data[$k]['type'] == "CL") {
							$k = $l-1;
							break;
						}

						$k += 1;
					}
				}elseif($data[$j]['type'] == "CL")
				{
					$i = $j-1;
					break;
				}

				$j += 1;
			}

		}
		
		$i += 1;
	}
	
	return json_encode($response);
}



add_action( 'wp_ajax_get_inplay_events', 'get_inplay_events' );
add_action( 'wp_ajax_nopriv_get_inplay_events', 'get_inplay_events' );

//function to get awaiting slips 

function get_awaiting_slips_ajax()
{
	echo json_encode(betpress_getunSubmittedSlips());
	wp_die();
}
add_action( 'wp_ajax_get_awaiting_slips', 'get_awaiting_slips_ajax' );
add_action( 'wp_ajax_nopriv_get_awaiting_slips', 'get_awaiting_slips_ajax' );

//function to set result for bets

function set_bet_result()
{
	if(isset($_POST['id']))
	{
		$id = $_POST['id'];
	}else{
		echo json_encode(array("status" => 0 , "error" => "no id present") ); 
		
	}
	
	$slip = get_bet_slip($id);
	
	
	if( strcasecmp($slip[0]->bet_option_name,"Fulltime Result")  == 0)
	{
		//get result data
		$results = get_event_result($slip[0]->event_id);
		$status = get_full_time_result($slip,$results);
		
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"Match Goals")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_match_goals_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"Goals Over/Under")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_match_goals_result($slip,$results);
		echo json_encode($status);		
	}elseif(strcasecmp($slip[0]->bet_option_name,"Alternative Match Goals")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_match_goals_result($slip,$results);
		echo json_encode($status);		
	}elseif(strcasecmp($slip[0]->bet_option_name,"Game Lines - Total")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_match_goals_result($slip,$results);
		echo json_encode($status);			
	}elseif(strcasecmp($slip[0]->bet_option_name,"Both Teams to Score")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_both_teams_to_score_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"Match Corners")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_match_corners_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"Alternative Corners")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_match_corners_result($slip,$results);
		echo json_encode($status);		
	}elseif(strcasecmp($slip[0]->bet_option_name,"Double Chance")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_double_chance_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"To Win 2nd Half")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_to_win_2nd_half_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"Half Time Result")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_half_time_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"1st Half Corners")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_first_half_corners_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"First Half Goals")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_first_half_goals_result($slip,$results);
		echo json_encode($status);
	}elseif(strcasecmp($slip[0]->bet_option_name,"Game Lines - Spread")  == 0)
	{
		$results = get_event_result($slip[0]->event_id);
		$status = get_game_lines_spread_result($slip,$results);
		echo json_encode($status);
	}		
	wp_die();
}

add_action( 'wp_ajax_set_bet_result', 'set_bet_result' );
add_action( 'wp_ajax_nopriv_set_bet_result', 'set_bet_result' );

//cron jjob for automation of results

function automate_results () {
    // code to execute on cron run
    error_log("ayush here");
	
	betpress_update("slips_test",array("won" => 1), array("won" => -1));
	
} add_action('automate_results', 'automate_results');

//include folders
betpress_require('controllers');
betpress_require('widgets');