<?php

class mycred_fortunewheel_Wheel_Preview extends mycred_fortunewheel_Wheel{

    function __construct() {
        add_action('admin_footer',array($this,'mycred_fortunewheel_wheel_preview'));
        add_action( 'admin_enqueue_scripts', array($this,'load_custom_wp_admin_style') );
    }

    function load_custom_wp_admin_style() {
        wp_enqueue_style( 'fortunewheel-wheel-style', mycred_mycred_fortunewheel_PLUGIN_URL . 'assets/css/wheel-style.css' );
        wp_enqueue_style( 'fortunewheel-admin-style', mycred_mycred_fortunewheel_PLUGIN_URL . 'assets/css/admin-style.css' );
        wp_enqueue_script( 'fortunewheel-win-wheel-script', mycred_mycred_fortunewheel_PLUGIN_URL . 'assets/js/winwheel.js' );
        wp_enqueue_script( 'fortunewheel-tweenmax-script', 'http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js' );
    }

    function mycred_fortunewheel_wheel_preview() {
        $this->fortunewheel_wheel_canvas();
        $this->fortunewheel_wheel_script();
    }
}