<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

class BetPress_odd_type_switcher extends WP_Widget {
    
    public function __construct() {
        
        $options = array(
            'name' => __('BetPress Odd Type Switcher', 'BetPress'),
        );
        
        parent::__construct('betpress_odd_type_switcher', 'Odd Type Switcher', $options);
    }
    
    public function form ($data) {
        
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title');?>"><?php esc_attr_e('Title', 'BetPress') ?></label>
            <input 
                class="widefat"
                type="text"
                id="<?php echo $this->get_field_id('title');?>"
                name="<?php echo $this->get_field_name('title'); ?>"
                value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>"
            />
        </p>
    
       <?php 
    }
    
    
    
    public function widget ($arguments, $data) {
        
        extract($arguments);
        extract($data);
        
        $title = apply_filters('widget_title', $title);
        
        echo $before_widget;
        echo $before_title . $title . $after_title;
        
        $pass['types'] = array(
            BETPRESS_DECIMAL => __('Decimal', 'BetPress'),
            BETPRESS_FRACTION => __('Fraction', 'BetPress'),
            BETPRESS_AMERICAN => __('Moneyline', 'BetPress'),
        );
        $pass['current_odd'] = betpress_get_desired_odd();
        betpress_get_view('odd-switcher', 'widgets', $pass);

        echo $after_widget;
    }

}

add_action('widgets_init', 'betpress_register_odd_type_switcher_widget');

function betpress_register_odd_type_switcher_widget () {
    
    register_widget('BetPress_odd_type_switcher');
}

function betpress_odd_type_switching_handler() {

    //just in case
    if ( ! isset($_GET['odd_type_changing']) ) {
        return;
    }

    $allowed_types = array(
        BETPRESS_DECIMAL,
        BETPRESS_AMERICAN,
        BETPRESS_FRACTION,
    );

    $type = betpress_sanitize($_GET['odd_type_changing']);

    if (in_array($type, $allowed_types, true)) {

        if (is_user_logged_in()) {

            $user_ID = get_current_user_id();

            update_user_meta($user_ID, 'bp_odd_type', $type);
            
        } else {

            setcookie('betpress_odd_type', $type, time()+60*60*24*30*12*10, '/');
            
        }
    }
        
    wp_redirect(betpress_get_url(array('odd_type_changing')));
    exit;
}

if (isset($_GET['odd_type_changing'])) {
    
    add_action('template_redirect', 'betpress_odd_type_switching_handler');
}