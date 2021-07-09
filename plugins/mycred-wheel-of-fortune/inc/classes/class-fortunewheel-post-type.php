<?php

Class fortunewheel_Wheels {

    function __construct() {
        add_action( 'init', array($this,'mycred_fortunewheel_multiwheel_posttype') );
        add_action( 'add_meta_boxes', array($this,'mycred_fortunewheel_register_meta_boxes') );
        add_action( 'save_post', array($this,'mycred_fortunewheel_save_meta_box') );
    }

    function mycred_fortunewheel_register_meta_boxes() {
        add_meta_box( 'fortunewheel-shortcode', __( 'fortunewheel ShortCode', 'fortunewheel' ), array($this,'mycred_fortunewheel_my_display_callback'), 'mycred_fortunewheels','side' );
    }

    function mycred_fortunewheel_my_display_callback( $post ) {
        if( isset($_GET['post']) && !empty($_GET['post']) ){

            echo '<span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly"
                value="[myCred Wheel_id='.$_GET['post'].']"
                class="large-text code"></span>';

            echo '<p>1. Only single shortcode can be used on a page</p>';
            echo '<p>2. Slider will not work on pages where shortcode is being used';

        } else {

            echo __( '<p>Save wheel to get shortcode</p>','wpcs');

        }

        /*$html = '<span class="shortcode wp-ui-highlight"><input type="text" id="fortunewheel-shortcide" onfocus="this.select();" readonly="readonly" class="large-text code" value="[contact-form-7 id=&quot;158&quot; title=&quot;Contact form 1&quot;]"></span>';
        echo $html;*/
    }

    function mycred_fortunewheel_save_meta_box( $post_id ) {
        // Save logic goes here. Don't forget to include nonce checks!
    }

    function mycred_fortunewheel_multiwheel_posttype() {
        $labels = array(
            'name'               => _x( 'myCred Wheels', 'post type general name', 'fortunewheel' ),
            'singular_name'      => _x( 'myCred Wheel', 'post type singular name', 'fortunewheel' ),
            'menu_name'          => _x( 'myCred Wheels', 'admin menu', 'fortunewheel' ),
            'name_admin_bar'     => _x( 'myCred Wheel', 'add new on admin bar', 'fortunewheel' ),
            'add_new'            => _x( 'Add New Wheel', 'myCred Wheel', 'fortunewheel' ),
            'add_new_item'       => __( 'Add New myCred Wheel', 'fortunewheel' ),
            'new_item'           => __( 'New myCred Wheel', 'fortunewheel' ),
            'edit_item'          => __( 'Edit myCred Wheel', 'fortunewheel' ),
            'view_item'          => __( 'View myCred Wheel', 'fortunewheel' ),
            'all_items'          => __( 'All myCred Wheels', 'fortunewheel' ),
            'search_items'       => __( 'Search myCred Wheels', 'fortunewheel' ),
            'parent_item_colon'  => __( 'Parent myCred Wheels:', 'fortunewheel' ),
            'not_found'          => __( 'No myCred Wheels found.', 'fortunewheel' ),
            'not_found_in_trash' => __( 'No myCred Wheels found in Trash.', 'fortunewheel' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'fortunewheel' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'mycred_fortunewheels' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => 5,
            'menu_icon'           => 'dashicons-marker',
            'supports'           => array( 'title', 'author')
        );

        register_post_type( 'mycred_fortunewheels', $args );
    }
}