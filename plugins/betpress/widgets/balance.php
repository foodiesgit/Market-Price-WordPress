<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

class betpress_balance_type_switcher extends WP_Widget {
    
    public function __construct() {
        
        $options = array(
            'name' => __('Balance Type Switcher', 'betpress'),
        );
        
        parent::__construct('betpress_balance_type_switcher', 'Balance Type Switcher', $options);
    }
    
    public function form ($data) {
        
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title');?>"><?php esc_attr_e('Title', 'betpress') ?></label>
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
            BETPRESS_SD => __('Apostar com Saldo Disponível', 'betpress'),			
            BETPRESS_SB => __('Apostar com Saldo de Bônus', 'betpress'),
        );
        $pass['current_balance'] = betpress_get_desired_balance();
        betpress_get_view('balance', 'widgets', $pass);

        echo $after_widget;
    }

}

add_action('widgets_init', 'betpress_register_balance_type_switcher_widget');

function betpress_register_balance_type_switcher_widget () {
    
    register_widget('betpress_balance_type_switcher');
}

function betpress_balance_type_switching_handler() {

    //just in case
    if ( ! isset($_GET['balance_type_changing']) ) {
        return;
    }

    $allowed_types = array(
        BETPRESS_SD,
        BETPRESS_SB,
    );

    $type = betpress_sanitize($_GET['balance_type_changing']);

    if (in_array($type, $allowed_types, true)) {

        if (is_user_logged_in()) {

            $user_ID = get_current_user_id();

            update_user_meta($user_ID, 'balance', $type);
            
            } else {

            setcookie('bcurry_balance_type', $type, time()+60*60*24*30*12*10, '/');
            
        }
    }
        
    wp_redirect(betpress_get_url(array('balance_type_changing')));
    exit;
}

if (isset($_GET['balance_type_changing'])) {
    
    add_action('template_redirect', 'betpress_balance_type_switching_handler');
}