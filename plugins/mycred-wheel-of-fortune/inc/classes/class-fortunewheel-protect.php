<?php

Class mycred_fortunewheel_Protect {

    function __construct() {
        add_action( 'wp_footer', array($this,'mycred_fortunewheel_password_javascript') );
        add_action( 'wp_ajax_fortunewheel_password', array($this,'mycred_fortunewheel_password_callback') );
        add_action( 'wp_ajax_nopriv_fortunewheel_password', array($this,'mycred_fortunewheel_password_callback') );
    }

    function mycred_fortunewheel_password_protect_html( $wheel_id ) {
        $html = '<div class="fortunewheel-password">';
        $html .= '<div class="fortunewheel-label">'.fortunewheel_get_post_meta($wheel_id,'fortunewheel_protect_label_text').'</div>';
        $html .= '<div class="fortunewheel-password-field"><input class="fortunewheel-pass" type="text"  /></div>';
        $html .= '<div class="fortunewheel-password-check"><input class="fortunewheel-pass-apply" type="button" value="Enter" /></div>';
        $html .= '</div>';

        return $html;
    }

    function mycred_fortunewheel_not_loggedin_html() {
        $html = '<div class="mc-loggedin-req-wrapper">';
        $html .= '<div>You must <a href="'.wp_login_url('https://mycred.me/mycred-fortunewheel/').'">login</a> to play mycred fortune wheel</div>';
        $html .= '</div>';

        return $html;
    }

    function mycred_fortunewheel_limit_exceed_html( $wheel_id ) {
        $html = '<div class="mc-limit-exceed-wrapper">';
        $html .= '<div style="color:#494949" class="mc-message-head">Rodada da sorte!</div>';
        $html .= '<div class="mc-img"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/mycred-sit.png" width="50"/></div>';
        $html .= '<div style="color:#585858" class="mc-message">Clique no botão abaixo e obtenha 1 giro por "'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_another_spin_with_points').'" reais. Atenção os giros não são reembolsavéis, por tanto ao clicar em "Apostar" certifique-se de Girar a roleta antes de sair da página</div>';
        $html .= '<div class="mc-pay-btn"><input type="button" id="mc-pay-point-for-spin" data-points="'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_another_spin_with_points').'" data-point-type="'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_pay_for_spin_mycred_types').'" value="Apostar '.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_another_spin_with_points').' reais">                </div>';
        $html .= '<div class="mc-no-points-msg">'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_no_points_msg').'</div>';
        $html .= '</div>';

        return $html;
    }

    function mycred_fortunewheel_protect_style() {
        ?>
        <style>
            .fortunewheel-pass-apply {
                background-color:<?php echo mycred_fortunewheel_get_post_meta('fortunewheel_password_button_background_color') ?> !important;
            }
            .fortunewheel-pass-apply:hover {
                background-color:<?php echo mycred_fortunewheel_get_post_meta('fortunewheel_password_button_background_color') ?> !important;
            }
        </style>
        <?php
    }
    
    function mycred_fortunewheel_free_played() {

        $time = apply_filters('mycred_fortunewheel_free_spin_timeframe', 'today');

        $args = array(
            'user_id' => get_current_user_id(),
            'time' => $time
        );
    
        // The Query
        $log = new myCRED_Query_Log( $args );
    
        $reference_count = 0;

        // The Loop
        if ( $log->have_entries() ) {
            
            foreach ( $log->results as $entry ) {
                if( $entry->ref == 'mycred_fortune_wheel' ) {
                    $reference_count++;
                }
            }
        }

        return $reference_count;
    }

    function mycred_fortunewheel_protection_is_enabled( $wheel_id ) {
        $is_enabled = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enabled_password_protect');
        if(!empty($is_enabled))
            return true;
        else
            return false;
    }

    function mycred_fortunewheel_hide_form() {
        ?>
        <script>
            jQuery(document).ready(function() {
                jQuery('.fortunewheel-from').hide();
                jQuery('.fortunewheel-intro').css('opacity',0);
                jQuery('.wlo_small_text').hide();
                jQuery('.fb-send-to-messenger').hide();
            });
        </script>
        <?php
    }

    function mycred_fortunewheel_hide_complete_form() {
        ?>
        <script>
            jQuery(document).ready(function() {
                jQuery('.fortunewheel-intro').css('opacity',0);
                jQuery('.wlo_small_text').hide();
                jQuery('.fb-send-to-messenger').hide();
                jQuery('.toggle-disabled').hide();
                jQuery('.fortunewheel-intro').height('50');
            });
        </script>
        <?php
    }

    function mycred_fortunewheel_password_javascript() { ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {
                jQuery('.fortunewheel-pass-apply').click(function() {
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var mycred_fortunewheel_pass = jQuery('.fortunewheel-pass').val();

                    jQuery('.ng-scope').show();
                    jQuery('.fortunewheel-error').hide();
                    jQuery('.fortunewheel-password').css('opacity','0.5');

                    var wheel_id = jQuery('.fortunewheel_wheel_id').text();

                    var data = {
                        'action': 'fortunewheel_password',
                        'password': mycred_fortunewheel_pass,
                        'wheel_id': wheel_id
                    };
                    $.post(ajaxurl, data, function(response) {
                        jQuery('.ng-scope').hide();
                        jQuery('.fortunewheel-password').css('opacity','1');
                        if(response == 'MATCHED') {
                            jQuery('.fortunewheel-password').fadeOut("slow",function() {
                                jQuery('.fortunewheel-from form').fadeIn();
                                jQuery('.wlo_small_text').show();
                                jQuery('.fortunewheel-intro').css('opacity',1);
                                jQuery('.fb-send-to-messenger').show();
                            });
                        } else {
                            jQuery('.fortunewheel-error').html('<?php echo __('Invalid Password','fortunewheel')?>');
                            jQuery('.fortunewheel-error').show();
                        }
                    });
                });

                jQuery('#mc-pay-point-for-spin').click(function() {
                    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    var points = jQuery(this).data('points');
                    var point_type = jQuery(this).data('point-type');

                    jQuery('.fortunewheel-error').hide();
                    jQuery('.lds-css.ng-scope').fadeIn();

                    var data = {
                        'action': 'fortunewheel_coupon_request',
                        'request_to': 'mycred_spin_with_points',
                        'points': points,
                        'point_type': point_type,
                    };

                    $.post(ajaxurl, data, function(response) {
                        if( response == 1 ) {
                            mycred_fortunewheel_show_complete_form();
                            jQuery('.lds-css.ng-scope').fadeOut();
                        } else {
                            jQuery('.lds-css.ng-scope').fadeOut();
                            jQuery('.fortunewheel-error').html( jQuery('.mc-no-points-msg').html() );
                            jQuery('.fortunewheel-error').show();
                        }
                    });
                });
            });

            function mycred_fortunewheel_show_complete_form() {
                jQuery('.fortunewheel-intro').css('opacity',1);
                jQuery('.wlo_small_text').show();
                jQuery('.fb-send-to-messenger').show();
                jQuery('.toggle-disabled').show();
                jQuery('.mc-limit-exceed-wrapper').hide();
                jQuery('.fortunewheel-intro').height('auto');
            }
        </script> <?php
    }

    
    function mycred_fortunewheel_password_callback() {
        $entered_password = $_POST['password'];
        $wheel_id = $_POST['wheel_id'];
        $saved_password = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_password_protect');
        if( $saved_password == $entered_password )
            echo 'MATCHED';
        else
            echo 'NOT MATCHED';
        die();
    }
}