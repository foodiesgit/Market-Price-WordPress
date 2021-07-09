<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

class betpress_lang_type_switcher extends WP_Widget {
    
    public function __construct() {
        
        $options = array(
            'name' => __('Lang Type Switcher', 'betpress'),
        );
        
        parent::__construct('betpress_lang_type_switcher', 'Lang Type Switcher', $options);
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
        BETPRESS_BR => __('Português', 'betpress'),
        BETPRESS_EN => __('English', 'betpress'),
        BETPRESS_ES => __('Español', 'betpress'),
        );
		
        $pass['current_odd'] = betpress_get_desired_lang();
        betpress_get_view('lang', 'widgets', $pass);

        echo $after_widget;
    }
	
}

add_action('widgets_init', 'betpress_register_lang_type_switcher_widget');

function betpress_register_lang_type_switcher_widget () {
    
    register_widget('betpress_lang_type_switcher');
}

function betpress_lang_type_switching_handler() {

    //just in case
    if ( ! isset($_GET['lang_type_changing']) ) {
        return;
    }

    $allowed_types = array(
        BETPRESS_BR,
        BETPRESS_EN,
        BETPRESS_ES,
	);

    $type = betpress_sanitize($_GET['lang_type_changing']);

    if (in_array($type, $allowed_types, true)) {

        if (is_user_logged_in()) {

            $user_ID = get_current_user_id();

            update_user_meta($user_ID, 'betpress_lang', $type);
            
        } else {

            setcookie('bcurry_lang_type', $type, time()+60*60*24*30*12*10, '/');
            
        }
    }
        
    wp_redirect(betpress_get_url(array('lang_type_changing')));
    exit;
}

if (isset($_GET['lang_type_changing'])) {
    
    add_action('template_redirect', 'betpress_lang_type_switching_handler');
}