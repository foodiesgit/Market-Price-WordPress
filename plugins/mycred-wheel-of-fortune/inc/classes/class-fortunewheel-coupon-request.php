<?php

class mycred_fortunewheel_Coupon_Request extends mycred_fortunewheel_Subscriber {

    function __construct() {
        add_action( 'wp_footer', array($this,'mycred_fortunewheel_coupon_request_javascript') );
        add_action( 'wp_footer', array($this,'mycred_fortunewheel_coupon_request_style') );
        add_action( 'wp_ajax_fortunewheel_coupon_request', array($this,'mycred_fortunewheel_coupon_request_callback') );
        add_action( 'wp_ajax_nopriv_fortunewheel_coupon_request', array($this,'mycred_fortunewheel_coupon_request_callback') );
    }
	
	function mycred_fortunewheel_coupon_request_style() {
		?>
		<style>
		.fortunewheel-fortunewheel-bar {
			background-color: red;
			width: 100%;
			color: white;
			font-weight: bold;
			padding: 8px;
			text-align: center;
			position: fixed;
			z-index: 999999;
			bottom: 0px;
			font-size: 14px;
			box-shadow: 0px 0px 4px #ababab;
		}
		</style>
		<?php
	}

    function mycred_fortunewheel_coupon_request_javascript() {

        // global $fortunewheel_Chatchamp;
        // $chatchamp_subscriber = json_decode( $fortunewheel_Chatchamp->mycred_fortunewheel_get_chatchamp_subscriber_name()['body'] );
        // $u_name = 'fortunewheel';
        // if( is_object( $chatchamp_subscriber ) && $chatchamp_subscriber->subscriber->firstName != '' )
        //     $u_name = $chatchamp_subscriber->subscriber->firstName;

        $this->mycred_fortunewheel_win_loss_style(); ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {
                var click_test = 0;
                jQuery('.fortunewheel-name, .fortunewheel-email').keyup(function (e) {
                    if (e.keyCode === 13 && click_test == 0) {
                        click_test = 1;
                        jQuery('.fortunewheel-form-btn').trigger('click');
                    }
                });

                var is_required = 0;
                var form_data = '';
                var form_save_data = [];
                var error_msg = '<?php echo __('Please Fill Up all the Required Fields','fortunewheel') ?>';

                jQuery('.fortunewheel-form-btn').click(function(e) {

                    var counter = 0;

                    jQuery('.fortunewheel-notify-field').remove();
                    jQuery('.fortunewheel-error').hide();
                    jQuery('.lds-css.ng-scope').fadeIn();

                    is_required = 0;
                    jQuery('.fortunewheel-from input,select').css('border','solid 0px');

                    form_data = '{'; var comma = ',';
                    var total_fields = jQuery('.fortunewheel-from input,select').length;
                    jQuery('.fortunewheel-from input,select').each(function() {
                        counter++;
                        if( counter == total_fields )
                            comma = '';

                        form_data += '"'+jQuery(this).attr('name')+ '":"'+jQuery(this).val()+'"'+comma;

                            var required_field = jQuery(this).attr('required');
                            var field_name = jQuery(this).attr('name');
                            var field_type = jQuery(this).attr('type');

                            if( field_type == 'checkbox' ) {
                                if (typeof required_field !== typeof undefined && required_field !== false) {
                                    if ( !jQuery(this).is(':checked') ) {
                                        jQuery('.fortunewheel-error').html('<?php echo __('Please Check ','fortunewheel') ?>' + field_name );
                                        jQuery('.fortunewheel-error').show();
                                        is_required = 1;
                                        error_msg = '<?php echo __('Please Check ','fortunewheel') ?>' + field_name;
                                    }
                                }
                            }

                            if (typeof required_field !== typeof undefined && required_field !== false) {
                                if( jQuery(this).val() == '' ) {
                                    is_required = 1;
                                    error_msg = '<?php echo __('Please Fill Up all the Required Fields','fortunewheel') ?>';
                                    jQuery(this).css('border','solid 2px #d83c3c');
                                    e.preventDefault();
                                }
                            }

                    });

                    form_data += '}';

                    var cookie_expiry = 1;
                    <?php if( isset( $_SESSION['wheel_id'] ) ) { ?>
                        cookie_expiry = <?php echo '1'?>;
                    <?php } ?>

                    setCookie('form_data'+current_wheel_id,form_data,cookie_expiry);

                    if( is_required == 1 ) {
                        jQuery('.fortunewheel-error').html(error_msg);
                        jQuery('.fortunewheel-error').show();
                        return;
                    }

                    mycred_fortunewheel_start_spin();

                    var spin_width = jQuery(window).width();
                    setCookie('fortunewheel_spin_start'+current_wheel_id,1,1);
                    setCookie('fortunewheel_spin_width'+current_wheel_id,spin_width,1);
                });
            });

            function mycred_fortunewheel_isValidEmailAddress(emailAddress) {
                var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
                return pattern.test(emailAddress);
            }

            function mycred_fortunewheel_start_spin() {
                jQuery('.fortunewheel-from').fadeOut();
                jQuery('.fortunewheel-error').hide();
                var width = jQuery(window).width();
                
                setTimeout(function() {
                    jQuery('.lds-css.ng-scope').hide();
                    jQuery('.fortunewheel-from').css('opacity','1');
                    jQuery('.fortunewheel-intro').hide();
                    if( width > 480 ) {
                        jQuery('.wheelContainer').animate({
                            'marginLeft': "30%"
                        }, 300, function () {
                            jQuery('.fortunewheel-error').hide();
                            jQuery('.fortunewheel-cross-wrapper').hide();
                            jQuery('.spinBtn').trigger('click');
                            click_test = 0;
                        });
                    } else if( width <= 480 ) {
                        jQuery('.wheelContainer').animate({
                            'marginLeft': "0%"
                        }, 300, function () {
                            jQuery('.fortunewheel-error').hide();
                            jQuery('.fortunewheel-cross-wrapper').hide();
                            jQuery('.spinBtn').trigger('click');
                            if (getCookie('optinspin_slide' + current_wheel_id) == 1) {
                                spin_480_start();
                            } else {
                                jQuery( ".fortunewheel-right" ).animate({
                                    height: "100px"
                                }, 2000);
                            }
                            click_test = 0;
                        });
                    }
                },1000);
                
            }
        </script> <?php
    }

    function mycred_fortunewheel_coupon_request_callback() {

        $wheel_id = $_POST['current_wheel_id'];

        if( $_POST['request_to'] == 'coupon_request' ) {
            $name = sanitize_text_field( $_POST['name'] );
            $email = sanitize_text_field( $_POST['email'] );
            $subscribe = $this->mycred_fortunewheel_add_new_subscriber( $name, $email );
            echo $subscribe;
            die();
        } else if( $_POST['request_to'] == 'count_section_win' ) {
            $section_id = $_POST['section_id'];
            $count_section_win = (int) get_option('complex_'.fortunewheel_crb_get_i18n_suffix().'_'.$section_id);
            if( !empty($section_id) ) {
                $count_section_win++;
                update_option('complex_'.fortunewheel_crb_get_i18n_suffix().'_'.$section_id,$count_section_win);
            } else {
                update_option('complex_'.fortunewheel_crb_get_i18n_suffix().'_'.$section_id,1);
            }
        } else if( $_POST['request_to'] == 'flush_availablity' ) {
            $section_id = $_POST['section_id'];
            delete_option('complex_'.fortunewheel_crb_get_i18n_suffix().'_'.$section_id);
        } else if( $_POST['request_to'] == 'mycred_points' ) {
            $wheel_id = str_replace( '_', '', $_POST['wheel_id'] );
            if( isset( $_POST['token'] ) && wp_verify_nonce( $_POST['token'], 'run-mycred-fortunewheel' . $wheel_id ) ) {
                if( $_POST['win'] == 'true' ) {
                    $log_template = $_POST['mycred_log_template'];
                    $mycred_point_type = $_POST['mycred_point_type'];
                    mycred_add( 'mycred_fortune_wheel', get_current_user_id(), $_POST['mycred_points'], $log_template,0,'',$mycred_point_type );
                } else if( $_POST['win'] == 'false' ) {
                    $points = -1 * abs($_POST['mycred_points']);
                    $log_template = $_POST['mycred_log_template'];
                    $mycred_point_type = $_POST['mycred_point_type'];
                    mycred_add( 'mycred_fortune_wheel', get_current_user_id(), $points, $log_template,0,'',$mycred_point_type );
                }
            } else {
                echo $wheel_id . 'TOKEN INVALID';
                $token = $_POST['token'];
                var_dump( $token );
            }
//            echo get_current_user_id();
        } else if( $_POST['request_to'] == 'mycred_spin_with_points' ) {
            $points = -1 * abs($_POST['points']);
            $mycred_point_type = $_POST['point_type'];

            $user_id = get_current_user_id();
            $balance = mycred_get_users_balance( $user_id, $mycred_point_type );

            if( $balance >= $_POST['points'] ) {
                $mycred_spin = mycred_add( 'deduct_points_for_play_spin', $user_id, $points, 'Deduct Points for play another spin',0,'',$mycred_point_type );
                echo $mycred_spin;
            } else {
                echo '0';
            }
        } else if( $_POST['request_to'] == 'mycred_lottery_win' ) {
            $mycred_log_template = $_POST['mycred_log_template'];
            $lottery_id = $_POST['lottery_id'];
            $lottery_enable = $_POST['lottery_enabled'];

            $lottery_data = array();

            $lottery_data['lottery_enabled'] = $lottery_enable;
            $lottery_data['lottery_id'] = $lottery_id;

            update_user_meta( get_current_user_id(), 'mc_free_lottery', $lottery_data );
        }
        die(); // this is required to terminate immediately and return a proper response
    }

    function mycred_fortunewheel_win_loss_style() {
        ?>
        <canvas id="world"></canvas>
        <style>
            .winning_lossing {
                background-color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_win_background_color') ?>;
                color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_win_text_color') ?>;
                border: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_win_border_color') ?>;
                font-size: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_font_size') ?>;
            }
            .winning_lossing a {
                color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_add_cart_link_color') ?> !important;
                text-decoration: none;
            }
            .fortunewheel-decline-coupon a {
                color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_skip_link_color') ?>  !important;
            }
            .fortunewheel-win-info {
                color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_coupon_msg_text_color') ?>  !important;
                background-color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_coupon_msg_bg') ?>  !important;
            }
            .fortunewheel-win-info a {
                text-decoration: underline;
            }
            .fortunewheel-fortunewheel-bar {
                color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_coupon_bar_color') ?>  !important;
                background-color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_coupon_bar_bg') ?>  !important;
            }
            span.exp-time {
                background-color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_coupon_bar_timer_color') ?>  !important;
                color: <?php echo mycred_fortunewheel_get_post_meta($_SESSION['wheel_id'],'fortunewheel_coupon_bar_timer_text_color') ?>  !important;
            }
			#world {
				display:none;
			}
        </style>
        <?php
    }

    function mycred_fortunewheel_unique_coupon() {
        $str = 'abcdefghijklmnopqrstuvwxyz01234567891011121314151617181920212223242526';
        $shuffled = str_shuffle($str);
        $shuffled = substr($shuffled,1,5);
        return $shuffled;
    }
}