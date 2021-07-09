<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

function betpress_json_controller() {
    wp_enqueue_script('js_admin', plugins_url('/includes/js/admin.js', __FILE__), array('jquery', 'js_timepicker', 'wp-color-picker'), true, true);
	
    betpress_get_view('betjson', 'shortcodes', $pass);
    
}

add_shortcode('betpress_json', 'betpress_json_controller');

function betpress_live_controller() {
    wp_enqueue_script('js_admin', plugins_url('/includes/js/admin.js', __FILE__), array('jquery', 'js_timepicker', 'wp-color-picker'), true, true);
	
    betpress_get_view('betlive', 'shortcodes', $pass);
    
}



add_shortcode('betpress_live', 'betpress_live_controller');

function betpress_inPlay_controller() {
    wp_enqueue_script('js_admin', plugins_url('/includes/js/admin.js', __FILE__), array('jquery', 'js_timepicker', 'wp-color-picker'), true, true);
	
    betpress_get_view('betinPlayEvent', 'shortcodes', $pass);
    
}



add_shortcode('betpress_inPlay', 'betpress_inPlay_controller');

function betpress_testing_controller() {
    wp_enqueue_script('js_admin', plugins_url('/includes/js/admin.js', __FILE__), array('jquery', 'js_timepicker', 'wp-color-picker'), true, true);
	
    betpress_get_view('testing', 'shortcodes', $pass);
    
}



add_shortcode('betpress_testing', 'betpress_testing_controller');