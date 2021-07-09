<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

// paypal api logic
function betpress_paypal_tx_handler() {

    //just in case
    if ( ! isset($_GET['tx']) ) {
        return;
    }
    
    global $display_view_paypal;

    $tx = $_GET['tx'];

    $user_ID = get_current_user_id();
    
    $paypal_listener = strcmp(get_option('bp_paypal_sandbox'), BETPRESS_VALUE_YES) === 0 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

    $request = curl_init();

    curl_setopt_array(
        $request,
        array(
            CURLOPT_URL => $paypal_listener,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'cmd' => '_notify-synch',
                'tx' => $tx,
                'at' => get_option('bp_paypal_token'),
            )),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HEADER => FALSE,
        )
    );

    $response = curl_exec($request);
    $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($request);

    curl_close($request);
    
    $log_message = '';

    if ((200 == $status) && (strpos($response, 'SUCCESS') === 0)) {

        $resp = substr($response, 7);

        $response_decoded = urldecode($resp);

        preg_match_all('/^([^=\s]++)=(.*+)/m', $response_decoded, $m, PREG_PATTERN_ORDER);
        $response = array_combine($m[1], $m[2]);

        $errors = array();

        $points_should_be = isset($_COOKIE['betpress_points_quantity']) ? $_COOKIE['betpress_points_quantity'] : 'Points cookie not set';

        $price_should_be = isset($_COOKIE['betpress_price']) ? $_COOKIE['betpress_price'] : 'Price cookie not set';
        
        $currency_should_be = isset($_COOKIE['betpress_currency']) ? $_COOKIE['betpress_currency'] : 'Currency not set';

        $used_txn_ids = get_option('bp_paypal_txn_ids');

        if (in_array($response['txn_id'], $used_txn_ids)) {

            $errors [] = __('Txn ID already used.', 'BetPress');
        }

        if ($response['mc_gross'] != $price_should_be) {

            $errors [] = __('Price doesn\'t match.', 'BetPress');
        }

        if ($response['mc_currency'] != $currency_should_be) {

            $errors [] = __('Currency doesn\'t match.', 'BetPress');
        }

        if ((int) $response['item_name'] != $points_should_be) {

            $errors [] = __('Points amount doesn\'t match.', 'BetPress');
        }
        
        if (0 === $user_ID) {
            
            $errors [] = __('User is not logged in.', 'BetPress');
        }

        if ($errors) {

            foreach ($errors as $err) {

                $log_message .= $err . ' ';
            }

            betpress_insert(
                'paypal',
                array(
                    'transaction_message' => $log_message,
                    'transaction_time' => time(),
                    'transaction_status' => BETPRESS_STATUS_FAIL,
                    'user_id' => $user_ID,
                    'user_ip' => $_SERVER['REMOTE_ADDR'],
                    'points' => 0,
                )
            );

            $display_view_paypal['error'] = get_option('bp_paypal_error_message');
            
        } else {

            $user_points = get_user_meta($user_ID, 'bp_points', true);
            $points_buyed = (int) $points_should_be;
            $used_txn_ids [] = $response['txn_id'];
            $user_total_points_buyed = get_user_meta($user_ID, 'bp_points_buyed', true);

            update_option('bp_paypal_txn_ids', $used_txn_ids);
            
            $new_points_buyed = (string) ($user_total_points_buyed + $points_buyed);
            $new_points = (string) ($user_points + $points_buyed);
            
            update_user_meta($user_ID, 'bp_points_buyed', $new_points_buyed);
            update_user_meta($user_ID, 'bp_points', $new_points);

            if ( (strcmp(get_user_meta($user_ID, 'bp_points_buyed', true), $new_points_buyed === 0)) &&
                    (strcmp(get_user_meta($user_ID, 'bp_points_buyed', true), $new_points_buyed === 0)) ) {
            
                betpress_insert(
                    'paypal',
                    array(
                        'transaction_message' => '',
                        'transaction_time' => time(),
                        'transaction_status' => BETPRESS_STATUS_PAID,
                        'user_id' => $user_ID,
                        'user_ip' => $_SERVER['REMOTE_ADDR'],
                        'points' => $points_buyed,
                    )
                );
                
                betpress_insert(
                    'points_log',
                    array(
                        'user_id' => $user_ID,
                        'comment_id' => 0,
                        'admin_id' => 0,
                        'points_amount' => $points_buyed,
                        'date' => time(),
                        'type' => BETPRESS_POINTS,
                    )
                );
                
                betpress_insert(
                    'points_log',
                    array(
                        'user_id' => $user_ID,
                        'comment_id' => 0,
                        'admin_id' => 0,
                        'points_amount' => $points_buyed,
                        'date' => time(),
                        'type' => BETPRESS_BOUGHT_POINTS,
                    )
                );

                $display_view_paypal['success'] = get_option('bp_paypal_success_message');
                
            }
        }

    } elseif (false === $response) {
        
        if (strpos($curl_error, 'SSL certificate problem: unable to get local issuer certificate') === 0) {
            
            $contact_who = 'Contact your hosting provider and tell them that you need an up-to-date CA root certificate bundle.'
                    . '<br />They can download one from <a href="http://curl.haxx.se/docs/caextract.html" target="_blank">here</a>';
            
        } else {
            
            $contact_who = 'Contact the support of BetPress and tell them about this error.';
            
        }
        
        $log_message .= $curl_error . '.<br />' . $contact_who;
        
        $failed = true;
        
    } else {

        $log_message .= 'Most probably the user tried to cheat.';
        
        $failed = true;
    }
    
    if (isset($failed) && true === $failed) {

        betpress_insert(
            'paypal',
            array(
                'transaction_message' => $log_message,
                'transaction_time' => time(),
                'transaction_status' => BETPRESS_STATUS_FAIL,
                'user_id' => $user_ID,
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'points' => 0,
            )
        );

        $display_view_paypal['error'] = get_option('bp_paypal_error_message');
        
    }
    
    //unset cookies so they cant be used twice
    setcookie('betpress_points_quantity', '', time() + 1, '/');
    setcookie('betpress_price', '', time() + 1, '/');
    setcookie('betpress_currency', '', time() + 1, '/');
}

if (isset($_GET['tx'])) {
    
    add_action('template_redirect', 'betpress_paypal_tx_handler');
}

function betpress_paypal_front_controller ($atts) {
    
    if ( ! is_user_logged_in() ) {
        return;
    }
    
    global $is_paypal_displayed;
    
    //restrict the shortcode from showing more than once on same page
    if (NULL === $is_paypal_displayed) {
    
        ob_start();
        
        $is_paypal_displayed = true;
    
        global $display_view_paypal;
    
        if (is_array($display_view_paypal) && count($display_view_paypal) > 0) {
        
            foreach ($display_view_paypal as $type => $value) {
            
                betpress_get_view('paypal-' . $type . '-message', 'shortcodes', array('paypal_' . $type . '_message' => $value));

                //just in case
                break;         
            }   
        }

        $user_ID = get_current_user_id();
        
        $user_db_points = get_user_meta($user_ID, 'bp_points', true);
        
        $user_points = ('' === $user_db_points) ? get_option('bp_starting_points') : $user_db_points;
        
        $max_points_user_can_have = get_option('bp_max_allowed_points');
        
        if ($user_points >= $max_points_user_can_have) {
            
            $pass['error_message'] = sprintf(esc_attr__('Points can be bought only by users with less than %s points.', 'BetPress'), $max_points_user_can_have);
            betpress_get_view('error-message', '', $pass);
            
            return;
        }
        
        //set default attributes
        $attributes = shortcode_atts(
                array(
                    'default_quantity' => 10,
                    'ratio' => 10,
                    'min_points' => 1,
                    'max_points' => 50,
                    'currency' => 'USD',
                    'points_name' => 'points',
                    'code' => 'P01',
                ),
                $atts
        );

        $pass['quantity'] = $attributes['default_quantity'];
        $pass['ratio'] = $attributes['ratio'];
        $pass['min_points'] = $attributes['min_points'];
        $pass['points_name'] = $attributes['points_name'];
        $pass['name'] = $attributes['default_quantity'] . ' ' . $attributes['points_name'];
        $pass['price'] = betpress_floordec($attributes['default_quantity'] / $attributes['ratio']);
        $pass['currency'] = $attributes['currency'];
        $pass['code'] = $attributes['code'];
        $pass['paypal_mail'] = sanitize_email(get_option('bp_paypal_mail'));
        $pass['return_url_ok'] = esc_url(betpress_get_url());
        $pass['return_url_fail'] = esc_url(get_option('bp_paypal_url_fail'));
        $pass['paypal_listener'] = strcmp(get_option('bp_paypal_sandbox'), BETPRESS_VALUE_YES) === 0 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        
        $max_total_buy_allowed = get_option('bp_max_points_to_buy');
        $new_user_total_points_buyed = get_user_meta($user_ID, 'bp_points_buyed', true);
        $new_difference = $max_total_buy_allowed - $new_user_total_points_buyed;
        
        $pass['max_points'] = $attributes['max_points'] < $new_difference ? $attributes['max_points'] : $new_difference;
        $pass['user_max_points_to_buy'] = $new_difference;

        betpress_get_view('buy-points', 'shortcodes', $pass);
        
        return ob_get_clean();
    }
}

add_shortcode('betpress_buy_points', 'betpress_paypal_front_controller');