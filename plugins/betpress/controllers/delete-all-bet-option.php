<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

function betpress_delete_all_bet_option() {

global $wpdb;

$table_prefix = $wpdb->prefix;

$table_name = $table_prefix.'_bp_slips';
    //codex says i MUST do this if
    if (is_admin() === true) {

        $user_ID = get_current_user_id();

        $del_all_bt_options = $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'bp_slips WHERE user_id = '.$user_ID.' AND status = "unsubmitted"');
        echo $del_all_bt_options;		
}

    //codex says i MUST use wp_die
    wp_die();
}

add_action( 'wp_ajax_delete_all_bet_option', 'betpress_delete_all_bet_option');
add_action( 'wp_ajax_nopriv_delete_all_bet_option', 'betpress_delete_all_bet_option');