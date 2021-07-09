<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

class betpress_gmt_type_switcher extends WP_Widget {
    
    public function __construct() {
        
        $options = array(
            'name' => __('Timezone Type Switcher', 'betpresst'),
        );
        
        parent::__construct('betpress_gmt_type_switcher', 'Timezone Type Switcher', $options);
    }
    
    public function form ($data) {
        
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title');?>"><?php esc_attr_e('Title', 'betpresst') ?></label>
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
            BETPRESS_12N => __('GMT-12', 'betpresst'),
            BETPRESS_1130N => __('GMT-11:30', 'betpresst'),
            BETPRESS_11N => __('GMT-11', 'betpresst'),
            BETPRESS_1030N => __('GMT-10:30', 'betpresst'),
            BETPRESS_10N => __('GMT-10', 'betpresst'),
            BETPRESS_930N => __('GMT-9:30', 'betpresst'),
            BETPRESS_9N => __('GMT-9', 'betpresst'),
            BETPRESS_830N => __('GMT-8:30', 'betpresst'),
            BETPRESS_8N => __('GMT-8', 'betpresst'),
            BETPRESS_730N => __('GMT-7:30', 'betpresst'),
            BETPRESS_7N => __('GMT-7', 'betpresst'),
            BETPRESS_630N => __('GMT-6:30', 'betpresst'),
            BETPRESS_6N => __('GMT-6', 'betpresst'),
            BETPRESS_530N => __('GMT-5:30', 'betpresst'),
            BETPRESS_5N => __('GMT-5', 'betpresst'),
            BETPRESS_430N => __('GMT-4:30', 'betpresst'),
            BETPRESS_4N => __('GMT-4', 'betpresst'),
            BETPRESS_330N => __('GMT-3:30', 'betpresst'),
            BETPRESS_3N => __('GMT-3', 'betpresst'),
            BETPRESS_230N => __('GMT-2:30', 'betpresst'),
            BETPRESS_2N => __('GMT-2', 'betpresst'),
            BETPRESS_130N => __('GMT-1:30', 'betpresst'),
            BETPRESS_1N => __('GMT-1', 'betpresst'),
			BETPRESS_0000P => __('GMT+0', 'betpresst'),
			BETPRESS_0030P => __('GMT+0:30', 'betpresst'),
			BETPRESS_0100P => __('GMT+1', 'betpresst'),
			BETPRESS_0130P => __('GMT+1:30', 'betpresst'),
			BETPRESS_0200P => __('GMT+2', 'betpresst'),
			BETPRESS_0230P => __('GMT+2:30', 'betpresst'),
			BETPRESS_0300P => __('GMT+3', 'betpresst'),
            BETPRESS_0330P => __('GMT+3:30', 'betpresst'),
            BETPRESS_0400P => __('GMT+4', 'betpresst'),
            BETPRESS_0430P => __('GMT+4:30', 'betpresst'),
            BETPRESS_0500P => __('GMT+5', 'betpresst'),
            BETPRESS_0530P => __('GMT+5:30', 'betpresst'),
            BETPRESS_0545P => __('GMT+5:45', 'betpresst'),
            BETPRESS_0600P => __('GMT+6', 'betpresst'),
            BETPRESS_0630P => __('GMT+6:30', 'betpresst'),
            BETPRESS_0700P => __('GMT+7', 'betpresst'),
            BETPRESS_0730P => __('GMT+7:30', 'betpresst'),
            BETPRESS_0800P => __('GMT+8', 'betpresst'),
            BETPRESS_0830P => __('GMT+8:30', 'betpresst'),
            BETPRESS_0845P => __('GMT+8:45', 'betpresst'),
            BETPRESS_0900P => __('GMT+9', 'betpresst'),
            BETPRESS_0930P => __('GMT+9:30', 'betpresst'),
            BETPRESS_1000P => __('GMT+10', 'betpresst'),
            BETPRESS_1030P => __('GMT+10:30', 'betpresst'),
            BETPRESS_1100P => __('GMT+11', 'betpresst'),
            BETPRESS_1130P => __('GMT+11:30', 'betpresst'),
            BETPRESS_1200P => __('GMT+12', 'betpresst'),
            BETPRESS_1245P => __('GMT+12:45', 'betpresst'),
            BETPRESS_1300P => __('GMT+13', 'betpresst'),
            BETPRESS_1345P => __('GMT+13:45', 'betpresst'),
            BETPRESS_1400P => __('GMT+14', 'betpresst'),
        );
        $pass['current_odd'] = betpress_get_desired_gmt();
        betpress_get_view('gmt', 'widgets', $pass);

        echo $after_widget;
    }

}

add_action('widgets_init', 'betpress_register_gmt_type_switcher_widget');

function betpress_register_gmt_type_switcher_widget () {
    
    register_widget('betpress_gmt_type_switcher');
}

function betpress_gmt_type_switching_handler() {

    //just in case
    if ( ! isset($_GET['gmt_type_changing']) ) {
        return;
    }

    $allowed_types = array(
        BETPRESS_12N,
        BETPRESS_1130N,
        BETPRESS_11N,
        BETPRESS_1030N,
        BETPRESS_10N,
        BETPRESS_930N,
        BETPRESS_9N,
        BETPRESS_830N,
        BETPRESS_8N,
        BETPRESS_730N,
        BETPRESS_7N,
        BETPRESS_630N,
        BETPRESS_6N,
        BETPRESS_530N,
        BETPRESS_5N,
        BETPRESS_430N,
        BETPRESS_4N,
        BETPRESS_330N,
        BETPRESS_3N,
        BETPRESS_230N,
        BETPRESS_2N,
        BETPRESS_130N,
        BETPRESS_1N,
		BETPRESS_0000P,
		BETPRESS_0030P,
		BETPRESS_0100P,
		BETPRESS_0130P,
		BETPRESS_0200P,
		BETPRESS_0230P,
		BETPRESS_0300P,
		BETPRESS_0330P,
		BETPRESS_0400P,
		BETPRESS_0430P,
		BETPRESS_0500P,
		BETPRESS_0530P,
        BETPRESS_0545P,
        BETPRESS_0600P,
        BETPRESS_0630P,
        BETPRESS_0700P,
        BETPRESS_0730P,
        BETPRESS_0800P,
        BETPRESS_0830P,
        BETPRESS_0845P,
        BETPRESS_0900P,
        BETPRESS_0930P,
        BETPRESS_1000P,		
        BETPRESS_1030P,		
        BETPRESS_1100P,		
        BETPRESS_1130P,		
        BETPRESS_1200P,
        BETPRESS_1245P,		
        BETPRESS_1300P,		
        BETPRESS_1345P,
        BETPRESS_1400P,		
    );

    $type = betpress_sanitize($_GET['gmt_type_changing']);

    if (in_array($type, $allowed_types, true)) {

        if (is_user_logged_in()) {

            $user_ID = get_current_user_id();

            update_user_meta($user_ID, 'gmt_offset', $type);
            
        } else {

            setcookie('bcurry_gmt_type', $type, time()+60*60*24*30*12*10, '/');
            
        }
    }
        
    wp_redirect(betpress_get_url(array('gmt_type_changing')));
    exit;
}

if (isset($_GET['gmt_type_changing'])) {
    
    add_action('template_redirect', 'betpress_gmt_type_switching_handler');
}