<?php

class mycred_fortunewheel_Wheel extends mycred_fortunewheel_Subscriber {

    function __construct() {
        add_shortcode('fortunewheel',array($this,'mycred_fortunewheel_woo_the_wheel'));
        add_action('wp_footer',array($this,'mycred_fortunewheel_wheel_slide'),0);
        add_action( 'admin_menu', array($this,'mycred_fortunewheel_settings_menu'),5 );
        add_action('init',array($this,'mycred_fortunewheel_preview'));
        add_action( 'wp_ajax_fortunewheel_get_wheel_attributes', array($this,'mycred_fortunewheel_get_wheel_attributes_callback') );
        add_action( 'wp_ajax_nopriv_fortunewheel_get_wheel_attributes', array($this,'mycred_fortunewheel_get_wheel_attributes_callback') );
    }

    function mycred_fortunewheel_trigger_wheel_preview() {
        if( isset( $_GET['fortunewheel_preview'] ) ) {
            ?>
            <script>
                jQuery(document).ready(function () {
                    setTimeout(function() {
                        open_wheel_slide();
                    },2000);
                });
            </script>
            <?php
        }
    }

    function mycred_fortunewheel_preview() {
        if( isset( $_GET['fortunewheel_preview'] ) ) {
            echo do_shortcode('[fortunewheel wheeL_id='.$_GET['fortunewheel_preview'].' slide=1]');
        }
    }

    function mycred_fortunewheel_wheel_slide() {
        
        global $wp_query,$post;
    
        if( wp_doing_ajax() )
            return;

        $page_id = $wp_query->get_queried_object_id();

        if( empty($page_id) )
            $page_id = get_the_ID();
                        
            
        $wheel_id = $this->get_fortunewheel_wheel( $page_id );

		if( !empty( $wheel_id ) ) {
			$content_post = get_post($page_id);
			$content = $content_post->post_content;

			if( isset( $_GET['fortunewheel_preview']) ) {
				$this->mycred_fortunewheel_trigger_wheel_preview();
			} else if( !has_shortcode( $content, 'fortunewheel' ) && !isset( $_GET['fortunewheel_preview']) ) {
				echo do_shortcode('[fortunewheel wheel_id="'.$wheel_id.'" slide="1"]');
			}
		}
    }

    function mycred_fortunewheel_clickable_tab( $wheel_id ) {
        $clickable_desktop = 0; $clickable_mobile = 0;
//        echo 'total'.print_r( $this->mycred_fortunewheel_get_segments() );
        ?>
        <script>

            jQuery(document).ready(function() {
                var clickable_desktop = 0; var clickable_mobile = 0;
                <?php
                if( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_clickable_tab_desktop') ) ) {
                ?> clickable_desktop = 1; <?php
                }

                if( !empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_clickable_tab_mobile') ) ) {
                ?> clickable_mobile = 1; <?php
                }
                ?>

                var window_width = jQuery(window).width();

                if( window_width <= 768 && clickable_mobile == 1 && getCookie('fortunewheel_use') == '' ) {
                    jQuery('#bottom_spin_icon').removeClass('hide');
                } else if ( window_width > 768 && clickable_desktop == 1 && getCookie('fortunewheel_use') == '') {
                    jQuery('#bottom_spin_icon').removeClass('hide');
                }
            });

        </script>
        <?php
    }

    function mycred_fortunewheel_rotate_mobile_popup() {
        $html = '<div class="fortunewheel-rotate-mob">
                    <div class="fortunewheelsin-rotote-content">
                        <div class="fortunewheel-rotate-img"><img src="'. mycred_fortunewheel_PLUGIN_URL . 'assets/img/rotate-mobile.png"> </div>
                        <div class="fortunewheel-rotate-msg">Kindly get back to your previous orientation view... your wheel is rolling there...</div>
                    </div>
                </div>';
        echo $html;
    }

    function mycred_fortunewheel_exit_intent( $wheel_id ) {

        if( !empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_intent_exit_popup_desktop')) ) {
            ?>
            <script>
                jQuery(window).load(function(event) {
                    setTimeout(function() {
                        jQuery(window).mouseleave(function(event) {
                            var cookie_expiry = <?php echo '1'; ?>;
                            var window_width = jQuery(window).width();
                            if (event.toElement == null && getCookie('fortunewheel_use') == '' && window_width > 768 && getCookie('desktopIntent') == '') {
                                open_wheel_slide();

                                setCookie('desktopIntent',1,cookie_expiry);
                            }
                        });
                    },2000);
                });
            </script>
            <?php
        }

        if( !empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_intent_exit_popup_mobile')) ) {
            ?>
            <script>
                jQuery('document').ready(function() {
                    var window_width = jQuery(window).width();
                    var cookie_expiry = <?php echo '1'; ?>;
                    if( getCookie('fortunewheel_use') == '' && window_width <= 768 && getCookie('mobileIntent') == '') {
                        var lastScrollTop = 0;
                        jQuery(window).scroll(function(event){
                            var st = jQuery(this).scrollTop();
                            if (st > lastScrollTop){
                            }
                            else {
                                setTimeout(function() {
                                    open_wheel_slide();
                                },1000);
                            }
                            lastScrollTop = st;
                        });

                        setCookie('mobileIntent',1,cookie_expiry);
                    }
                });
            </script>
            <?php
        }
    }

    function mycred_fortunewheel_interval( $wheel_id ) {
        if( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_wheel_open_at') != 'none' && empty($_COOKIE['fortunewheel_use'])) {

            $wheel_open_after = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_open_wheel_after');
            if( !empty($wheel_open_after) ) {

                if( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_wheel_open_at') == 'once'  && ( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_desktop')) || !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_mobile')) ) ) {
                    $enable_desktop = 0; $enable_mob = 0;

                    ?>
                    <script>
                        jQuery(window).load(function() {
                            var enable_desktop = 0;
                            var enable_mobile = 0;
                            <?php
                                if( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_desktop')) ) {
                                ?>enable_desktop = 1; <?php
                                }
                                if( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_mobile')) ) {
                                ?>enable_mobile = 1; <?php
                            }
                            ?>
                            var window_width = jQuery(window).width();
                            if( getCookie('fortunewheel_use') == '' && ( (window_width <= 768 && enable_mobile == 1) || (window_width > 768 && enable_desktop == 1) ) ) {

                                if( getCookie('fortunewheel_wheel_open_intetval') != 1) {
                                    setTimeout( function() {
                                        open_wheel_slide();
                                    }, <?php echo $wheel_open_after?>000);
                                }
                                setCookie('fortunewheel_wheel_open_intetval',1);
                            }
                        });
                    </script>
                    <?php
                } else if( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_wheel_open_at') == 'every'  && ( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_desktop')) || !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_mobile')) ) ) {
                    ?>
                    <script>
                        jQuery(window).load(function() {
                            var enable_desktop = 0;
                            var enable_mobile = 0;

                            <?php
                                if( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_desktop')) ){
                                ?>enable_desktop = 1; <?php
                                }
                                if( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_time_delay_mobile')) ) {
                                ?>enable_mobile = 1; <?php
                            }
                            ?>
                            var window_width = jQuery(window).width();

                            if( getCookie('fortunewheel_use') == '' && ( (window_width <= 768 && enable_mobile == 1) || (window_width > 768 && enable_desktop == 1))) {
                                setInterval( function(){
                                    open_wheel_slide();
                                }, <?php echo $wheel_open_after?>000);
                            }
                        });
                    </script>
                    <?php
                }
            }
        }

    }

    function mycred_fortunewheel_woo_the_wheel( $atts ) {
        global $wheel_ID;

        static $counter = 0;

        $atts = shortcode_atts(
            array(
                'wheel_id' => 0,
                'slide' => 0,
            ), $atts );

        $_SESSION['wheel_id'] = $atts['wheel_id'];

        $counter++;

        if( count( $this->mycred_fortunewheel_get_segments( $atts['wheel_id'] ) ) == 0 ) {
            return '<div style="color:red;">Your wheel doesn\'t have any section. please add some section to appear the wheel</div>';
        }

        if( count( $this->mycred_fortunewheel_get_segments( $atts['wheel_id'] ) ) > 0 && $counter == 1 && $this->mycred_fortunewheel_is_user_allowed_to_play( $atts['wheel_id'] ) == true ) {
		

            $this->mycred_fortunewheel_wheel_attributes($atts['wheel_id']);
            $this->mycred_fortunewheel_wheel_script( $atts['wheel_id'], $atts['slide'] );

            $cart_url = '';
            if( function_exists( 'wc_get_cart_url' ) )
                $cart_url = wc_get_cart_url();
            $disable_fortunewheelbar = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_disable_coupon_bar');
            if( !empty($disable_fortunewheelbar) )
                $disable_fortunewheelbar = 'off';

            $coupon_expire_label = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_coupon_bar_expire_label');
            if( empty($coupon_expire_label) )
                $coupon_expire_label = 'Coupon Time Left';

            $sparkle_enable = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_enable_sparkle');
            if( empty( $sparkle_enable ) )
                $sparkle_enable = 0;
            else
                $sparkle_enable = 1;

            $cookie_expiry = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_cookie_expiry');
            if( empty($cookie_expiry) )
                $cookie_expiry = 0;

            $coupon_msg = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_coupon_bar_msg');

            if( empty($coupon_msg) )
                $coupon_msg = 'Check your email to get your winning coupon!';

            $fortunewheel_enable_cart_redirect = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_enable_cart_redirect');
            if( empty( $fortunewheel_enable_cart_redirect ) )
                $fortunewheel_enable_cart_redirect = 0;
            else
                $fortunewheel_enable_cart_redirect = 1;

            $enable_snow_feature = mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_snowflak_enable');
            if( empty( $enable_snow_feature ) )
                $enable_snow_feature = 0;
            else
                $enable_snow_feature = 1;

            wp_enqueue_style( 'fortunewheel-wheel-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/wheel-style.css' );
            wp_enqueue_style( 'fortunewheel-google-font', mycred_fortunewheel_PLUGIN_URL . 'assets/css/google-font.css' );
            wp_enqueue_style( 'fortunewheel-wheel-main-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/style.css' );
            wp_enqueue_style( 'fortunewheel-phone-number-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/intlTelInput.css' );

            wp_enqueue_script( 'jquery' );
            wp_register_script( 'fortunewheel-grunt-scripts', mycred_fortunewheel_PLUGIN_URL . 'assets/js/fortunewheel-merge.js' );
            wp_enqueue_script( 'fortunewheel-phone-number', mycred_fortunewheel_PLUGIN_URL . 'assets/js/intlTelInput.js' );
			wp_enqueue_script( 'tp-tools',null,'',true );
			wp_enqueue_script( 'revmin',null,'',true );

            if($enable_snow_feature == 1){
                $snowparam = array(
                    'no_of_flake' => mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_snow_numfla'),
                    'speed_of_flake' => mycred_fortunewheel_get_post_meta($atts['wheel_id'],'speed_of_flake'),

                );
                wp_register_script( 'fortunewheel-snow-scripts', mycred_fortunewheel_PLUGIN_URL . 'assets/js/fallingsnow_v6.js', null, '', true );
                wp_enqueue_script( 	'fortunewheel-snow-scripts' );
                wp_localize_script( 'fortunewheel-snow-scripts', 'fortunewheel_snowparam', $snowparam );

            }

            $param = array(
                'plugin_url' => mycred_fortunewheel_PLUGIN_URL,
                'ajax_url' => admin_url('admin-ajax.php'),
                'coupon_msg' => $coupon_msg,
                'token' => wp_create_nonce( 'run-mycred-fortunewheel' . $atts['wheel_id'] ),
                'cart_url' => $cart_url,
                'disable_fortunewheelbar' => $disable_fortunewheelbar,
                'coupon_expire_label' => $coupon_expire_label,
                'wheel_data' => mycred_fortunewheel_PLUGIN_URL .'inc/wheel_data.php',
                'snow_fall' => $enable_snow_feature,
                'sparkle_enable' => $sparkle_enable,
                'cookie_expiry' => $cookie_expiry,
                'ajaxurl' => admin_url('admin-ajax.php'),
                'enable_cart_redirect' => $fortunewheel_enable_cart_redirect
            );
            wp_localize_script( 'fortunewheel-grunt-scripts', 'fortunewheel_wheel_spin', $param );
            wp_enqueue_script( 'fortunewheel-grunt-scripts' );

            $_SESSION['wheel_id_'.$atts['wheel_id']] = $atts['wheel_id'];
            $_SESSION['wheel_id'] = $atts['wheel_id'];

            $wheel_data_var =  $this->mycred_fortunewheel_wheel_canvas( $atts['wheel_id'], $atts['slide'] );
            $wheel_data_var .=  '<div class="fortunewheel_wheel_id" style="display:none">'.$atts['wheel_id'].'</div>';
            if( $atts['slide'] == 0 ) {
                ?>
                <script>
                    jQuery(document).ready(function() {
                        setCookie('fortunewheel_slide_<?php echo $atts['wheel_id']?>',<?php echo $atts['slide']?>);
                        var actual_height = jQuery('.fortunewheel-right').height() + 60;
                        jQuery( ".fortunewheel-right" ).animate({
                            opacity: 1,
                            height: actual_height +"px"
                        }, 1000, function() {
                            jQuery( ".fortunewheel-right").show();
                        });
                    });
                </script>
                <?php
            }
            return $wheel_data_var;
        } else if ( $counter > 1 ) {
            if( !isset( $_GET['fortunewheel_preview'] ) )
                return '<div style="color:red;">You can use only one shortcode on single page</div>';
        } else if( isset( $_COOKIE['fortunewheel_use_'.$atts['wheel_id'] ] ) && !empty( $_COOKIE['fortunewheel_use_'.$atts['wheel_id'] ] ) ) {

            if( isset( $_COOKIE['fortunewheel_coupon_code_'.$atts['wheel_id'] ] ) && !empty( $_COOKIE['fortunewheel_coupon_code_'.$atts['wheel_id'] ] ) ) {
                return '<div class="fortunewheel-played win">'.mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_shortcode_message').'</div>
                    <div class="fortunewheel_wheel_id" style="display:none">'.$atts['wheel_id'].'</div>';
                ?> <script> jQuery(document).ready(function() { show_fortunewheel_bar('<?php echo $_COOKIE['fortunewheel_coupon_code_'.$atts['wheel_id'] ] ?>'); }); </script> <?php
            } else
                return '<div class="fortunewheel-played loss">'.mycred_fortunewheel_get_post_meta($atts['wheel_id'],'fortunewheel_shortcode_message').'</div>
                    <div class="fortunewheel_wheel_id" style="display:none">'.$atts['wheel_id'].'</div>';
        } else {
            return '<div class="fortunewheel_wheel_id" style="display:none">'.$atts['wheel_id'].'</div>';
        }
    }

    function mycred_fortunewheel_scripts_load( $wheel_id, $slide ) {
        $this->mycred_fortunewheel_wheel_attributes($wheel_id);
        $this->mycred_fortunewheel_wheel_script( $wheel_id, $slide );

        $cart_url = '';
        if( function_exists( 'wc_get_cart_url' ) )
            $cart_url = wc_get_cart_url();
        $disable_fortunewheelbar = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_disable_coupon_bar');
        if( !empty($disable_fortunewheelbar) )
            $disable_fortunewheelbar = 'off';

        $coupon_expire_label = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_coupon_bar_expire_label');
        if( empty($coupon_expire_label) )
            $coupon_expire_label = 'Coupon Time Left';

        $sparkle_enable = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_sparkle');
        if( empty( $sparkle_enable ) )
            $sparkle_enable = 0;
        else
            $sparkle_enable = 1;

        $cookie_expiry = '1';
        if( empty($cookie_expiry) )
            $cookie_expiry = 0;

        $coupon_msg = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_coupon_bar_msg');

        if( empty($coupon_msg) )
            $coupon_msg = 'Check your email to get your winning coupon!';

        $fortunewheel_enable_cart_redirect = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_cart_redirect');
        if( empty( $fortunewheel_enable_cart_redirect ) )
            $fortunewheel_enable_cart_redirect = 0;
        else
            $fortunewheel_enable_cart_redirect = 1;

        $enable_snow_feature = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_snowflak_enable');
        if( empty( $enable_snow_feature ) )
            $enable_snow_feature = 0;
        else
            $enable_snow_feature = 1;

        wp_enqueue_style( 'fortunewheel-wheel-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/wheel-style.css' );
        wp_enqueue_style( 'fortunewheel-google-font', mycred_fortunewheel_PLUGIN_URL . 'assets/css/google-font.css' );
        wp_enqueue_style( 'fortunewheel-wheel-main-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/style.css' );
        wp_enqueue_style( 'fortunewheel-phone-number-style', mycred_fortunewheel_PLUGIN_URL . 'assets/css/intlTelInput.css' );

        wp_enqueue_script( 'jquery' );
        wp_register_script( 'fortunewheel-grunt-scripts', mycred_fortunewheel_PLUGIN_URL . 'assets/js/fortunewheel-merge.js', null, '', true );
        wp_enqueue_script( 'fortunewheel-phone-number', mycred_fortunewheel_PLUGIN_URL . 'assets/js/intlTelInput.js', null, '', true );



        if($enable_snow_feature == 1){
            $snowparam = array(
                'no_of_flake' => mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_snow_numfla'),
                'speed_of_flake' => mycred_fortunewheel_get_post_meta($wheel_id,'speed_of_flake'),

            );
            wp_register_script( 'fortunewheel-snow-scripts', mycred_fortunewheel_PLUGIN_URL . 'assets/js/fallingsnow_v6.js', null, '', true );
            wp_enqueue_script( 	'fortunewheel-snow-scripts' );
            wp_localize_script( 'fortunewheel-snow-scripts', 'fortunewheel_snowparam', $snowparam );

        }



        $param = array(
            'plugin_url' => mycred_fortunewheel_PLUGIN_URL,
            'ajax_url' => admin_url('admin-ajax.php'),
            'coupon_msg' => $coupon_msg,
            'cart_url' => $cart_url,
            'disable_fortunewheelbar' => $disable_fortunewheelbar,
            'coupon_expire_label' => $coupon_expire_label,
            'wheel_data' => mycred_fortunewheel_PLUGIN_URL .'inc/wheel_data.php',
            'snow_fall' => $enable_snow_feature,
            'sparkle_enable' => $sparkle_enable,
            'cookie_expiry' => $cookie_expiry,
            'ajaxurl' => admin_url('admin-ajax.php'),
            'enable_cart_redirect' => $fortunewheel_enable_cart_redirect
        );
        wp_localize_script( 'fortunewheel-grunt-scripts', 'fortunewheel_wheel_spin', $param );
        wp_enqueue_script( 'fortunewheel-grunt-scripts' );
    }

    function mycred_fortunewheel_is_user_allowed_to_play( $wheel_id ) {
//        return true;
        // Restrict By IP
        $is_ip_restect = mycred_fortunewheel_get_post_meta( $wheel_id,'fortunewheel_restricted_by_ip' );

        if( !empty( $is_ip_restect ) ) {
            $user_ip = $this->mycred_fortunewheel_user_ip();

            $data_timeout = get_option('_transient_timeout_' . 'ip_restrict__'.$wheel_id.'_'.$user_ip);
            $data_timeout_exp = date("Y-m-d", $data_timeout);

            $current_date = date("Y-m-d");

            if ( $data_timeout_exp > $current_date && !empty( $data_timeout ) ) {
                return false;
            }
        }

        $play_chances = mycred_fortunewheel_get_post_meta( $wheel_id,'fortunewheel_number_chances_play' );
        $is_cookie_restricted = mycred_fortunewheel_get_post_meta( $wheel_id,'fortunewheel_restricted_by_cooike' );

        /*if( isset( $_COOKIE['fortunewheel_play_'.$wheel_id ] ) ) {
            return true;
        }*/
        $fortunewheel_play_cookie = 0;
        if( isset( $_COOKIE['fortunewheel_play_'.$wheel_id ] ) || !empty( $_COOKIE['fortunewheel_play_'.$wheel_id ] ) ) {
            $fortunewheel_play_cookie = $_COOKIE['fortunewheel_play_'.$wheel_id ];
        }

        if( !empty($is_cookie_restricted) && $fortunewheel_play_cookie < $play_chances ) {
            return true;
        } else if( !empty($is_cookie_restricted) && $fortunewheel_play_cookie >= $play_chances )
            return false;

        return true;
    }

    function mycred_fortunewheel_get_segment_colors( $wheel_id ) {

        $sections = mycred_fortunewheel_get_post_meta($wheel_id,'crb_section');
        $segments_colors = array();

        if( !empty( $sections ) ) {
            // Getting All Section colors in the loop and save them in array
            foreach( $sections as $section ) {

                $color = $section['segment_color'];

                if(empty($color)) // IF Don't have any coupon
                    $color = '#364c62';

                $segments_colors[] = $color;

            }

            // segment_color
            return $segments_colors;
        }
    }

    function mycred_fortunewheel_wheel_canvas( $wheel_id, $slide ) {

        if( empty($wheel_id) )
            return;

//        $html = $this->mycred_fortunewheel_wheel_attributes($wheel_id);

        $html = '<div class="woo-wheel-roll-bg"><canvas id="world" width="1536" height="511"></canvas></div>
                
                <div class="woo-wheel-roll" id="opinspin-wheel-roll">
                <div class="woo-wheel-bg-img"></div>';
        $html .= '<div class="fortunewheel-right">
                    <div class="fortunewheel-cross-wrapper"><div class="fortunewheel-cross-label">'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_cross_label').'</div><div class="fortunewheel-cross"></div></div>
                    <div class="toast">
                        <p/>
                    </div>';


        $html .= $this->mycred_fortunewheel_get_logo( $wheel_id );
        $html .= $this->winning_lossing_text( $wheel_id,$slide );
//                $html .= '<div class="fortunewheel-intro">'.$this->mycred_fortunewheel_get_general_settings()['fortunewheel_intro_text'].'</div>';
        $html .= $this->mycred_fortunewheel_form_fields( $wheel_id );
//                $html .= $this->mycred_fortunewheel_initially_try_luck_btn();
        $html .= $this->mycred_fortunewheel_error_notify( $wheel_id );
        $html .= $this->mycred_fortunewheel_privacy_link( $wheel_id );



        $html .= '</div>';
        $html .= '<div class="fortunewheel-left">

                    <div class="fortunewheel-canvas">
                    <div class="wheelContainer">
                    <svg class="wheelSVG" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" text-rendering="optimizeSpeed">
                        <defs>
                            <filter id="shadow" x="-100%" y="-100%" width="550%" height="550%">
                                <feOffset in="SourceAlpha" dx="0" dy="0" result="offsetOut"></feOffset>
                                <feGaussianBlur stdDeviation="9" in="offsetOut" result="drop" />
                                <feColorMatrix in="drop" result="color-out" type="matrix" values="0 0 0 0   0
                          0 0 0 0   0
                          0 0 0 0   0
                          0 0 0 .3 0" />
                                <feBlend in="SourceGraphic" in2="color-out" mode="normal" />
                            </filter>
                        </defs>
                        <g class="mainContainer">
                        <g class="wheel">
                            <!-- <image  xlink:href="http://example.com/images/wheel_graphic.png" x="0%" y="0%" height="100%" width="100%"></image> -->
                        </g>
                        </g>
                        <g class="centerCircle" />
                        <g class="wheelOutline" />
                        <g class="pegContainer" opacity="1">
                            <path class="peg" fill="#EEEEEE" d="M22.139,0C5.623,0-1.523,15.572,0.269,27.037c3.392,21.707,21.87,42.232,21.87,42.232 s18.478-20.525,21.87-42.232C45.801,15.572,38.623,0,22.139,0z" />
                        </g>
                        <g class="valueContainer" />';
                        $logo_url = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_wheel_center_logo');
                        $html .= '<image xlink:href="'.$logo_url.'" width="160" height="160" x="430" y="300" />';
                        $html .= '</svg>
                    </div>
                    </div>
                </div>';
        $enable_snow_feature = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_snowflak_enable');

        if($enable_snow_feature == '1'){
            $_fortunewheel_image_snowflake = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_image_snowflake');
            $fortunewheel_snowflake_width = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_snowflake_width');

            if(!empty($_fortunewheel_image_snowflake)){
                $img = "<img src=".$_fortunewheel_image_snowflake." width=".$fortunewheel_snowflake_width." />";
            } else {
                $img = "*";
            }
            $html .= '<div id="snowflakeContainer">
								<p class="snowflake" style="opacity: 0.87828; font-size: 54.7671px; transform: translate3d(223px, 462px, 0px);">'.$img.'</p>
								<p class="snowflake" style="opacity: 0.235512; font-size: 54.7058px; transform: translate3d(1244px, 404px, 0px);">'.$img.'</p>
								<p class="snowflake" style="opacity: 0.304743; font-size: 56.2731px; transform: translate3d(681px, 373px, 0px);">'.$img.'</p>
								<p class="snowflake" style="opacity: 0.301569; font-size: 26.2554px; transform: translate3d(1100px, 147px, 0px);">'.$img.'</p>
								<p class="snowflake" style="opacity: 0.930111; font-size: 44.3089px; transform: translate3d(162px, 346px, 0px);">'.$img.'</p>
							</div>';
        }
        $html .= '</div>';

        $html .= $this->mycred_fortunewheel_fortune_open($wheel_id);
        $html .= $this->mycred_fortunewheel_interval($wheel_id);
        $html .= $this->mycred_fortunewheel_exit_intent($wheel_id);
        $html .= $this->mycred_fortunewheel_clickable_tab($wheel_id);
        $html .= $this->mycred_fortunewheel_rotate_mobile_popup($wheel_id);


        //$html .= $this->mycred_fortunewheel_side_luck_btn();
        return $html;
    }

    function mycred_fortunewheel_error_notify() {
        $html = '<div class="fortunewheel-error"></div>';
        return $html;
    }

    function mycred_fortunewheel_get_logo( $wheel_id ) {
        $html = '<div class="fortunewheel-logo">
                    <img src="'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_logo').'" class="fortunewheel-wheel-logo" />
                </div>';

        return $html;
    }

    function mycred_fortunewheel_side_luck_btn() {
        $html = '<div class="woo-try_btn" id="fortunewheel-simple-btn">Try Your Luck</div>';
        return $html;
    }

    function mycred_fortunewheel_is_mycred_exist_in_wheel( $wheel_id ) {

        $sections = mycred_fortunewheel_get_post_meta($wheel_id,'crb_section');

        $mycred_exist = false;
        if( !empty($sections) ) {
            // Getting All Section in the loop
            foreach( $sections as $section ) {
                if( isset( $section['fortunewheel_woocommerce_type'] ) && $section['fortunewheel_woocommerce_type'] == 'fortunewheel_mycred' ) {
                    $mycred_exist = true;
                    break;
                }

                if( isset( $section['fortunewheel_mycred_points_check'] ) && !empty( $section['fortunewheel_mycred_points_check'] ) ) {
                    $mycred_exist = true;
                    break;
                }
            }
        }

        return $mycred_exist;
    }

    function mycred_fortunewheel_form_fields( $wheel_id ) {
        global $fortunewheel_protect_post,$mycred_fortunewheel;

        if( !is_user_logged_in() && $this->mycred_fortunewheel_is_mycred_exist_in_wheel( $wheel_id ) && $mycred_fortunewheel->fortunewheel_is_mycred_emabled() ) {
            $html = '<div class="fortunewheel-wrapper-notice">
                        <div class="fortunewheel-notice-msg">'. __('<i class="fas fa-sign-in-alt"></i> You must <a href="'.wp_login_url().'">login</a> to play fortunewheel','fortunewheel') .'
                        <div class="fortunewheel-notice-link"><a href="'.wp_login_url().'">'. __('Click here to get Login','fortunewheel') .'</a></div></div>
                    </div>';

            return $html;
        } else if( $this->mycred_fortunewheel_is_mycred_exist_in_wheel( $wheel_id ) && $mycred_fortunewheel->fortunewheel_is_mycred_emabled() == false ) {
            $html = '<div class="fortunewheel-wrapper-notice">
                        <div class="fortunewheel-notice-msg">'. __('<strong><i class="far fa-frown"></i> Warning</strong> : Seems like wheel was setup with <link to mycred>myCRED Plugin</link to mycred>, and later mycred was deactivated.','fortunewheel') .'</div>
                    </div>';

            return $html;
        }

        do_action('fortunewheel_before_form_fields');

        $name_field = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_name_label');
        $name_field = ($name_field != '') ? 'block' : 'none';
        $username = ''; $user_email = '';
        if( is_user_logged_in() ) {
            $user_info = get_userdata(get_current_user_id());
            $username = $user_info->user_login;
            $user_email = $user_info->user_email;
        }
        //$fortunewheel_form_fields = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_form_fields');
        $fortunewheel_form_fields = array();
        $html = '<div class="fortunewheel-intro">'.$this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_intro_text'].'</div>
                    <div class="fortunewheel-from">';

        if( $fortunewheel_protect_post->mycred_fortunewheel_protection_is_enabled( $wheel_id ) ) {
            $html .= $fortunewheel_protect_post->mycred_fortunewheel_password_protect_html( $wheel_id );
            $html .= $fortunewheel_protect_post->mycred_fortunewheel_hide_form();
        }

        if( !is_user_logged_in() ) {
            $html .= $fortunewheel_protect_post->mycred_fortunewheel_hide_complete_form();
            $html .= $fortunewheel_protect_post->mycred_fortunewheel_not_loggedin_html();
        } else if( $fortunewheel_protect_post->mycred_fortunewheel_free_played() >= mycred_fortunewheel_get_post_meta( $wheel_id,'fortunewheel_free_attempts') ) {
            $html .= $fortunewheel_protect_post->mycred_fortunewheel_hide_complete_form();
            $html .= $fortunewheel_protect_post->mycred_fortunewheel_limit_exceed_html( $wheel_id );
        } 

        $html .= '<form class="toggle-disabled">
                    <div class="fortunewheel-name field-'.$name_field.'" style="display: '. $name_field .'">
                        <input type="text" name="your name" placeholder="'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_name_label').'" autocomplete="off" class="fortunewheel-form-field fortunewheel-name fortunewheel-'.$name_field.'" value="'.$username.'" name="fortunewheel-name">
                    </div>
                    <div class="fortunewheel-email">
                        <input type="text" name="your email" style="display:none" placeholder="'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_email_label').'" autocomplete="off"  class="fortunewheel-form-field fortunewheel-email" value="'.$user_email.'" name="fortunewheel-email">
                    </div>';
        foreach( $fortunewheel_form_fields as $fortunewheel_form_field ) {
            $field_key = $fortunewheel_form_field['fortunewheel_key'];
            $field = str_replace('fortunewheel_','',$fortunewheel_form_field['_type']);
            $required = '';
            if( !empty( $fortunewheel_form_field['fortunewheel_required_field'] ) ) {
                $required = 'required';
            }
            if( $field == 'checkbox' ) {
                $html .= '<div class="fortunewheel-name checkbox-field" id="checkbox-field">';
                $checkbopx_label = str_replace('[a','<a',$fortunewheel_form_field['fortunewheel_label']);
                $checkbopx_label = str_replace('"]','">',$checkbopx_label);
                $checkbopx_label = str_replace('[/a]','</a>',$checkbopx_label);

                $text = $fortunewheel_form_field['fortunewheel_label'];
                $pos = strpos($text,'[a');
                $checkbox_name= substr($text,0,$pos);

                $html .= '<input type="'.$field.'" name="'.$field_key.'" '.$required.' value="yes" /><label>'.$checkbopx_label.'</label>';
                $html .= '</div>';
            } else if( $field == 'text' ) {
                $html .= '<div class="fortunewheel-name">
                                <input class="fortunewheel-form-field" name="'.$field_key.'" '.$required.' type="'.$field.'" placeholder="'.$fortunewheel_form_field['fortunewheel_placeholder'].'" />';
                $html .= '</div>';
            } else if( $field == 'select' ) {
                $html .= '<div class="fortunewheel-name">
                                        <select data-type="select" class="fortunewheel-form-field" name="'.$field_key.'">';
                $html .= '<option value="">'.$fortunewheel_form_field['fortunewheel_label'].'</option>';
                foreach( $fortunewheel_form_field['fortunewheel_dropdown_values'] as $fortunewheel_form_dropdown ) {
                    $html .= '<option value="'.$fortunewheel_form_dropdown['fortunewheel_dropdown_val'].'">'.$fortunewheel_form_dropdown['fortunewheel_dropdown_val'].'</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
            } else if( $field == 'phone' ) {
                $phone_id = str_replace(' ','',$fortunewheel_form_field['fortunewheel_label']);
                $html .= '<div class="fortunewheel-name">
                                <input id="'.$phone_id.'" class="fortunewheel-form-field" name="'.$field_key.'" '.$required.' type="'.$field.'" placeholder="'.$fortunewheel_form_field['fortunewheel_placeholder'].'" />';
                $html .= '</div>';
                ?>
                <script>
                    jQuery(document).ready(function() {
                        jQuery("#<?php echo $phone_id?>").intlTelInput({
                            // allowDropdown: false,
                            // autoHideDialCode: false,
                            // autoPlaceholder: "off",
                            // dropdownContainer: "body",
                            // excludeCountries: ["us"],
                            // formatOnDisplay: false,
                            // geoIpLookup: function(callback) {
                            //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                            //     var countryCode = (resp && resp.country) ? resp.country : "";
                            //     callback(countryCode);
                            //   });
                            // },
                            // hiddenInput: "full_number",
                            // initialCountry: "auto",
                            // nationalMode: false,
                            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                            // placeholderNumberType: "MOBILE",
                            // preferredCountries: ['cn', 'jp'],
                            // separateDialCode: true,
                            utilsScript: "<?php echo mycred_fortunewheel_PLUGIN_PATH.'/assets/js/utils.js' ?>"
                        });
                    });
                </script>
                <?php
            } else if( $field == 'name' ) {
                $html .= '<div class="fortunewheel-name">
                                <input class="fortunewheel-form-field name-field" name="'.$field_key.'" '.$required.' type="'.$field.'" placeholder="'.$fortunewheel_form_field['fortunewheel_placeholder'].'" />';
                $html .= '</div>';
            }
        }
        $html .= '<div class="fortunewheel-sub-btn">

                        <input type="button" class="fortunewheel-form-btn" id="fortunewheel-simple-btn" value="'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_button_label').'" name="fortunewheel-sub-btn">
                        <input type="button" class="spinBtn" style="display:none">
                    </div>
                    </form>

                    <div class="lds-css ng-scope">
                          <div style="width:100%;height:100%" class="lds-rolling">
                            <div></div>
                          </div>
                      </div>
                </div>';
        return $html;
    }

    function mycred_fortunewheel_get_segments( $wheel_id ) {

        $sections = mycred_fortunewheel_get_post_meta($wheel_id,'crb_section');

        $segments_each = array(); $segments_array = array();
        $counter = 0;

        if( !empty($sections) ) {
            // Getting All Section in the loop
            foreach( $sections as $section ) {
                $counter++;

                $label = $section['fortunewheel_section_label'];

                $probability = $section['fortunewheel_probability'];
                $winning_lossing_text = $section['fortunewheel_win_loss_text'];

                $win = true;

                if( $section['_type'] == 'no_prize' )
                    $win = false;

                $section_id = '';
                if( isset( $section['fortunewheel_unique_section_id'] ) ) {
                    $section_id = $section['fortunewheel_unique_section_id'];
                    $total_usage = $section['fortunewheel_max_availability'];

                    if( $this->optionspin_check_availability($section_id,$total_usage) == false )
                        $probability = '0';
                }

                if( empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_duration_type' ) ) )
                    $wheel_duration_type = 'day';
                else
                    $wheel_duration_type = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_duration_type' );

                // Set Section Type
                $section_type = 'string';
                if( $section['fortunewheel_section_type'] == 'image' ) {
                    $section_type = 'image';
                    $label = $section['fortunewheel_section_image'];
                }

                // Lottery
                $mycred_lootery_enabled = false;
                if( isset( $section['mycred_lottery_enable'] ) && !empty( $section['mycred_lottery_enable'] ) ) {
                    $mycred_lootery_enabled = true;
                }

                $mycred_lottery_id = 0; $no_of_entries = 0;
                if( isset( $section['mycred_lottery_id'] ) && !empty( $section['mycred_lottery_id'] ) ) {
                    $mycred_lottery_id = $section['mycred_lottery_id'];
                    $no_of_entries = $section['fortunewheel_mycred_no_of_lottery_entries'];
                }

                $section['fortunewheel_win_loss_text'];

                $segments_each['probability'] = $probability;
                $segments_each['type'] = $section_type;
                $segments_each['value'] = $label;
                $segments_each['win'] = $win;
                $segments_each['resultText'] = $winning_lossing_text;
                $segments_each['userData'] = array("score" => 10);
                $segments_each['section_id'] = $section_id;
                $segments_each['duration_type'] = $wheel_duration_type;
                $segments_each['user_ip'] = $this->mycred_fortunewheel_user_ip();
                $segments_each['lottery_enable'] = $mycred_lootery_enabled;
                $segments_each['lottery_id'] = $mycred_lottery_id;          
                $segments_each['no_of_entries'] = $no_of_entries;                

                $segments_array[] = apply_filters('fortunewheel_wheel_segments',$segments_each,$sections,$section);
            }

            return $segments_array;
        }
    }

    function mycred_fortunewheel_user_ip() {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    function optionspin_check_availability( $section_id,$total_usage_limit ) {
        $total_win = get_option('complex_'.mycred_fortunewheel_crb_get_i18n_suffix().'_'.$section_id);
        if( $total_usage_limit == '' )
            return true;
        else if( $total_win < $total_usage_limit )
            return true;
        else
            return false;
    }

    // General Settings of fortunewheel Spin
    function mycred_fortunewheel_get_general_settings( $wheel_id ) {
        $general = array();
        $general['fortunewheel_allowed_users'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_allowed_users');
        $general['fortunewheel_spin_speed'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_spin_speed');
        $general['fortunewheel_no_of_spin'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_no_of_spin');
        $general['fortunewheel_text_size'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_text_size');
        $general['fortunewheel_wheel_logo'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_wheel_logo');
        $general['fortunewheel_logo'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_logo');
        $general['fortunewheel_background_color'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_background_color');
        $general['fortunewheel_border_color'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_border_color');
        $general['fortunewheel_inner_border_color'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_inner_border_color');
        $general['fortunewheel_border_width'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_border_width');
        $general['fortunewheel_text_color'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_text_color');
        $general['fortunewheel_background_image'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_background_image');
        $general['fortunewheel_wheel_border_color'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_wheel_border_color');
        $general['fortunewheel_email_label'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_email_label');
        $general['fortunewheel_button_label'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_button_label');
        $general['fortunewheel_intro_text'] = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_intro_text');
        if( !empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_sound') )) // Check Sound is enable or not
            $general['fortunewheel_enable_sound'] = true;
        else
            $general['fortunewheel_enable_sound'] = false;

        return $general;
    }

    function mycred_fortunewheel_total_segments( $wheel_id ) {
        $total_sections = count( mycred_fortunewheel_get_post_meta($wheel_id,'crb_section') );
        return $total_sections; // Total Segments in the Wheel
    }

    function winning_lossing_text( $wheel_id, $slide ) {

        $coupn_msg_txt = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_coupon_message');
        $html = '<div class="winning_lossing"> 
                    <div class="fortunewheel-win-info">'.$coupn_msg_txt.'</div>
                    <div class="fortunewheel-btn">                    
<a class="try_again" href="https://betcurry.com/ho/watchapi-id54/sglacip-id7420/">Tentar novamente</a>';
                if( $slide == 1 ){
                    $html .= '<div class="fortunewheel-decline-coupon" style="display: none;"><a href="javascript:void(0)" class="fortunewheel-coupon-decline">'.mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_skip_btn').'</a></div>';
                }
        $html .= '</div>
                </div>';
        $html .= '<div class="win-coupon" style="display:none"></div>';
        return $html; // Text after winning or losing
    }

    function mycred_fortunewheel_spin_speed( $wheel_id ) {
        return (float) mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_spin_speed');
    }

    function mycred_fortunewheel_number_of_spin( $wheel_id ) {
        return (float) mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_no_of_spin');
    }

    function mycred_fortunewheel_form() {
        $html = '<div class="woo-form"><form>';
        $html .= '<label>Email</label>';
        $html .= '<input type="text" />';
        $html .= '<label>Name</label>';
        $html .= '<input type="text" />';
        $html .= '<input type="button" value="Try Your Luck" />';
        $html .= '</form></div>';

        return $html;
    }

    function mycred_fortunewheel_wheel_script( $wheel_id, $slide ) {
        ?>
        <style>
            .woo-wheel-roll {
                background-color: <?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_background_color') ?> !important;
                top: 0%;
            <?php
            if( $slide == 0 ) { ?>
                position: initial !important;
                width: 100% !important;
                min-height: 600px !important;
                height: auto !important;
                margin-left: 0% !important;
                visibility: visible !important;
            <?php } ?>
            }

            <?php if( $slide == 0 )  {?>
            #bottom_spin_icon {
                display: none !important;
            }

            .woo-wheel-roll-bg {
                display: none !important;
            }

            .wheelContainer {
                left: 46% !important;
                visibility: visible !important;
                opacity: 1 !important;
                top: 60px !important;
            }

            @media only screen and (max-width: 480px) {
                .wheelContainer {
                    left: 0% !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    top: 60px !important;
                }
            }

            .fortunewheel-cross-wrapper {
                display:none !important;
            }

            .woo-wheel-roll {
                background-image: url(<?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_background_image')?>);
            }
            <?php } ?>

            <?php if( $slide == 1 )  {?>
            .woo-wheel-bg-img:before {
                background-image: url(<?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_background_image')?>);
                width: 100% !important;
                height: 100% !important;
                bottom: 20px;
                opacity: 1;
            }
            <?php } ?>
            #fortunewheel-simple-btn {
                background-color: <?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_buttons_color') ?> !important;
                color: <?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_buttons_text_color') ?>  !important;;
            }
            #fortunewheel-simple-btn:hover {
                background-color: <?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_buttons_hover_color') ?>  !important;
            }
            .fortunewheel-add-to-cart {
                background-color: <?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_add_cart_bg_color') ?>  !important;
                margin: 10px;
            }
            <?php echo mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_custom_css')?>
        </style>
        <?php
    }

    function mycred_fortunewheel_initially_try_luck_btn() {
        $html = '<div class="fortunewheel-try-luck-btn" id="fortunewheel-simple-btn">Want To Try Your Luck!</div>';
        return $html;
    }

    function mycred_fortunewheel_privacy_link( $wheel_id ) {
        $label = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_privacy_label');
        $page = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_privacy_page');
        $html = '';
        if( $page != 'none' ){
            $html = '<div class="fortunewheel-privacy"><a href="'.$page.'">'.$label.'</a></div>';
        }

        return $html ;
    }

    function mycred_fortunewheel_fortune_open( $wheel_id ) {
        global $wp_query;
        $page_id = $wp_query->get_queried_object_id();

        if( !$this->get_fortunewheel_wheel( $page_id ) )
            return;

        $this->mycred_fortunewheel_get_segments($wheel_id);
        $click_popup = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_enable_clickable_tab_desktop');
        $btn_class = '';
        if (empty($click_popup)) {
            $btn_class = 'hide';
        }
        $html = '<div id="bottom_spin_icon" class="fortunewheel-click-btn ' . $btn_class . '">
                    <div class="spin_icon_text">
                        <span class="privy-floating-text">' . mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_spinner_label') . '  </span>
                    </div>
                    <div class="spin_icon_img">
                            <img src="' . mycred_fortunewheel_PLUGIN_URL . '/assets/img/fortune-icon.png" >
                    </div>

                </div>';
        echo $html;
    }

    function mycred_fortunewheel_settings_menu() {
        add_submenu_page( 'crb_carbon_fields_container_fortunewheel_spin.php', 'Settings', 'Settings',
            'manage_options', '?page=crb_carbon_fields_container_fortunewheel_spin.php');
    }

    function mycred_fortunewheel_wheel_attributes( $wheel_id ){

        $datas= array(

            "colorArray" => $this->mycred_fortunewheel_get_segment_colors( $wheel_id ),

            "segmentValuesArray" => $this->mycred_fortunewheel_get_segments( $wheel_id ),
            "svgWidth" => 1024,
            "svgHeight" => 768,
            "wheelStrokeColor" => $this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_border_color'],
            "wheelStrokeWidth" => $this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_border_width'],
            "wheelSize" => 800,
            "wheelTextOffsetY" => 110,
            "wheelTextColor" => $this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_text_color'],
            "wheelTextSize" => $this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_text_size']."em",
            "wheelImageOffsetY" => 40,
            "wheelImageSize" => 50,
            "centerCircleSize" => 100,
            "centerCircleStrokeColor" =>  $this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_inner_border_color'],
            "centerCircleStrokeWidth" => 12,
            "centerCircleFillColor" => "#EDEDED",
            //"segmentStrokeColor" => "#E2E2E2",
            "segmentStrokeColor" => "#000",
            "segmentStrokeWidth" => 2,
            "centerX" => 512,
            "centerY" => 384,
            "hasShadows" => false,
            "numSpins" => 2,
            "spinDestinationArray" => array(),
            "minSpinDuration" => $this->mycred_fortunewheel_spin_speed( $wheel_id ),
            "gameOverText" => "THANK YOU FOR PLAYING SPIN2WIN WHEEL. COME AND PLAY AGAIN SOON!",
            "invalidSpinText" =>"INVALID SPIN. PLEASE SPIN AGAIN.",
            "introText" => "YOU HAVE TO<br>SPIN IT <span style='color=>#F282A9;'>2</span> WIN IT!",
            "hasSound" => $this->mycred_fortunewheel_get_general_settings( $wheel_id )['fortunewheel_enable_sound'],
            "gameId" => "9a0232ec06bc431114e2a7f3aea03bbe2164f1aa",
            "clickToSpin" => true
        );

        $_SESSION['wheeldata_'.$wheel_id] = $datas;

        return $datas;
    }

    function mycred_fortunewheel_get_wheel_attributes_callback() {
        $wheel_id = $_POST['wheel_id'];
        $wheel_id = str_replace('_','',$wheel_id);
        $wheel_json = $this->mycred_fortunewheel_wheel_attributes( $wheel_id );
        echo  json_encode($wheel_json, true);
        die();
    }

    function get_fortunewheel_wheel( $current_page_id ) {
        
        // GET WHEEL BY CURRENT PAGE ID
        $args = array(
            'post_type'  => 'mycred_fortunewheels',
            'fields' => 'ids',
            //'post_status' => array('publish', 'pending', 'draft', 'auto-draft')
        );
        $query = new WP_Query( $args );
        $wheel_id = 0;

        // CHECK CURRENT PAGE AND THIER WHEEL
        if ( $query->have_posts() ) {
            // The 2nd Loop
            while ( $query->have_posts() ) {
                $query->the_post();
                $wheel_id = get_the_ID();

                if( !empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_display_slider_wheel') ) && !empty( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_display_all_pages') ) ) {
                    return $wheel_id;
                    break;
                }

                $fortunewheel_selected_pages = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_selected_pages');
//                print_r($fortunewheel_selected_pages);
                if( !empty( $current_page_id ) && !empty($fortunewheel_selected_pages) ) {
                    foreach($fortunewheel_selected_pages as $fortunewheel_selected_page) {
                        if( in_array($current_page_id,$fortunewheel_selected_page) ){
                            return $wheel_id;
                            break;
                        }
                    }
                }
                
                $posts_to_show = array();
                foreach( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_show_posts_complex') as $posts ) {
                    $posts_to_show[] = $posts['fortunewheel_show_posts'];
                }
                
                $post_type = get_post_type($current_page_id);
                
                if( in_array($post_type,$posts_to_show) ) {
                    return $wheel_id;
                    break;
                }

            }

            // Restore original Post Data
            wp_reset_postdata();
        }


        // APPLY WHEEL
        return 0;
    }
	
	function revslider_scripts_cleanup() {
		global $wp_scripts;
        if( !is_admin() ) {
            wp_deregister_script('tp-tools');
            wp_dequeue_script('tp-tools');
            wp_deregister_script('revmin');
            wp_dequeue_script('revmin');
        }
	}
}

function mycred_fortunewheel_is_displayed( $wheel_id ) {
    global $post;

    global $wp_query;
    $page_id = $wp_query->get_queried_object_id();

    $post_id = get_the_ID();
    $pages_to_show = array();  $posts_to_show =  array();

    if( !empty(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_display_all_pages') ) )
        return true;


    foreach( mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_show_posts_complex') as $posts ) {
        $posts_to_show[] = $posts['fortunewheel_show_posts'];
    }

    if( in_array(get_post_type( $post_id ), $posts_to_show) || $this->get_fortunewheel_wheel( $page_id ) ) {
        return true;
    }

    return false;
}