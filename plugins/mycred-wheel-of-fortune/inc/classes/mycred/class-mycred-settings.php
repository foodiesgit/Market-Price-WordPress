<?php

Class mycred_fortunewheel_MyCred_Settings {

    function __construct() {
        add_filter('fortunewheel_wheel_segments',array($this,'mycred_fortunewheel_wheel_segments'),99,3);
        add_action('wp_footer',array($this,'mycred_fortunewheel_custom_event'));
        add_filter( 'mycred_run_this', array($this,'mycred_giving_free_lottery'),10,2 );
        add_filter( 'mycred_lottery_insolvent', array($this,'mycred_lottery_insolvent_func'),99,5 );
    }

    function mycred_lottery_insolvent_func($true, $user_id, $entries, $repeat, $object ) {

        // Check if user has reward by free lottery
        $free_lottery = get_user_meta( $user_id, 'mc_free_lottery', true );

        if( $free_lottery && !empty( $free_lottery ) ) {
            // if this is winning lottery from Spin
            if( $free_lottery['lottery_id'] == $object->id && $free_lottery['lottery_enabled'] == true ) {
                return false; // allow for spin
            }
        }

        $prefs = $object->get_participation_prefs();
        $cost = $prefs['price'];
        if ( $cost > 0 ) {

            // Make sure user has enough points
            $users_balance = $object->mycred->get_users_cred( $user_id );
            if ( $users_balance < $cost ) return true;

            // Make sure user can buy the requested amount of entries
            $max_entries_per_balance = mycred_lotto_calculate_max( $users_balance, $user_id, $cost );
            if ( $entries > $max_entries_per_balance ) return true;

            // Make sure user can buy the requested amount of repeats
            $max_repeat_per_balance = mycred_lotto_calculate_max( $users_balance, $user_id, $cost );
            if ( $repeat > $max_repeat_per_balance ) return true;

        }
        
        return false;
    }

    /**
     * Do not deduct points when user play a winning lottery
     */
    function mycred_giving_free_lottery($data,$object) {
    
        $user_id = get_current_user_id();
    
        if( empty( $user_id ) )
            return $data;
    
        $free_lottery = get_user_meta( $user_id, 'mc_free_lottery', true );
    
        if( $free_lottery && !empty( $free_lottery ) ) {
            
            if( $free_lottery['lottery_id'] == $data['ref_id'] && $free_lottery['lottery_enabled'] == true ) {
                $data['amount'] = 0;
                // Clear data for not adding free lottery again
                update_user_meta( $user_id, 'mc_free_lottery', '' ); 
            }
        }

        return $data;
    }
    
    /**
     * After Spin
     */
    function mycred_fortunewheel_custom_event() {
        ?>
        <script>
            jQuery(document).ready(function() {
                jQuery(window).on('fortunewheel_wheel_result', function (result) {

                    if( result.win ) {

                        console.log( result );

                        if( result.lottery_enable ) {
                             // When use MyCred Lotter
                             
                            // var ajaxurl = fortunewheel_wheel_spin.ajax_url;

                            // var data = {
                            //     'action': 'mycred-lotto-play',
                            //     'no-entries': 8,
                            //     'token':  '',
                            //     'lotto-id': result.lottery_id
                            // };

                            // // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                            // jQuery.post(ajaxurl, data, function(response) {
                            //     alert('Got this from the server: ' + response);
                            // });

                            var ajaxurl = fortunewheel_wheel_spin.ajax_url;
                            var data = {
                                'action': 'fortunewheel_coupon_request',
                                'request_to': 'mycred_lottery_win',
                                'mycred_log_template': result.mycred_log_template,
                                'lottery_enabled': result.lottery_enable,
                                'lottery_id': result.lottery_id,
                                'token': result.token,
                                'wheel_id': result.current_wheel_id,
                            };

                            jQuery.post(ajaxurl, data, function (response) {
                            
                                jQuery(function($) {
                                    var lotto_token = '<?php echo wp_create_nonce( 'mycred-lotto-play' )?>';
                                    $.ajax({
                                        type       : 'POST',
                                        data       : 'no-entries='+result.no_of_entries+'&action=mycred-lotto-play&token='+lotto_token+'&lotto-id='+result.lottery_id,
                                        dataType   : 'json',
                                        url        : fortunewheel_wheel_spin.ajax_url,                                
                                        success    : function( data ) {
                                            // Debug - uncomment to use
                                            console.log( data );
                                            alert( "You have won Free Lottery" + data.message );
                                        },
                                        error      : function( jqXHR, textStatus, errorThrown ) {
                                            //$('form#play-3508').slideDown( 600 ).show();
                                            // Debug - uncomment to use
                                            console.log( jqXHR + ' : ' + textStatus + ' : ' + errorThrown );
                                            alert('Error: '+textStatus)
                                        }
                                    });
                                });

                             });
                         
                        } else {
                            // When use MyCred Points
                            var ajaxurl = fortunewheel_wheel_spin.ajax_url;
                            var data = {
                                'action': 'fortunewheel_coupon_request',
                                'request_to': 'mycred_points',
                                'mycred_log_template': result.mycred_log_template,
                                'mycred_points': result.mycred_loss_points,
                                'mycred_point_type': result.point_type,
                                'win': 'true',
                                'token': result.token,
                                'wheel_id': result.current_wheel_id,
                            };

                            jQuery.post(ajaxurl, data, function (response) {
                            });
                        }
                        
                    } else {
                        
                        var ajaxurl = fortunewheel_wheel_spin.ajax_url;
                        var data = {
                            'action': 'fortunewheel_coupon_request',
                            'request_to': 'mycred_points',
                            'mycred_points': result.mycred_loss_points,
                            'mycred_log_template': result.mycred_log_template,
                            'mycred_point_type': result.point_type,
                            'win': 'false',
                            'token': result.token,
                            'wheel_id': result.current_wheel_id,
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                        });
                        
                    }
                });
            });
        </script>
        <?php
    }

    function mycred_fortunewheel_wheel_segments( $segments_each, $sections, $section ) {

        $mycred_points = $section['fortunewheel_mycred_points'];
        $mycred_log_template = $section['fortunewheel_mycred_log_template'];
        $mycred_point_types = $section['fortunewheel_mycred_points'];

        $segments_mycred = array();
        $segments_mycred['mycred_loss_points'] = $mycred_points;
        $segments_mycred['mycred_loss_point_types'] = $mycred_point_types;
        $segments_mycred['mycred_log_template'] = $mycred_log_template;

        $segments_each = array_merge($segments_each,$segments_mycred);

        return $segments_each;

        $segments_each = array_merge($segments_each,$segments_mycred);

        return $segments_each;

    }

    // Check mycred Enabled or not
    function mycred_fortunewheel_is_mycred_emabled() {

        if( class_exists ( 'myCRED_Core' ) ) {
            return true;
        }

        return false;
    }
}