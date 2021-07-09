<?php

/**
 * fortunewheel Stats
 */
class mycred_fortunewheel_Statistics {

    function __construct() {
        add_action( 'init', array($this,'mycred_fortunewheel_register_statistics') );
        add_action( 'wp_ajax_fortunewheel_statistics', array($this,'mycred_fortunewheel_statistics_callback') );
        add_action( 'wp_ajax_nopriv_fortunewheel_statistics', array($this,'mycred_fortunewheel_statistics_callback') );
        add_action( 'wp_ajax_fortunewheel_stats', array($this,'mycred_fortunewheel_stats_callback') );
        add_action( 'wp_ajax_nopriv_fortunewheel_stats', array($this,'mycred_fortunewheel_stats_callback') );
        add_filter( 'manage_edit-fortunewheel-stats_columns', array($this,'mycred_fortunewheel_edit_columns') ) ;
        add_action( 'manage_fortunewheel-stats_posts_custom_column', array($this,'mycred_fortunewheel_manage_columns'), 999, 2 );
        add_action( 'admin_menu', array($this,'mycred_fortunewheel_stats_menu') );
        add_filter( 'bulk_actions-edit-fortunewheel-stats', array($this,'mycred_fortunewheel_bulk_edit') );
        add_action( 'init',array($this,'mycred_fortunewheel_write_csv') );
        add_action( 'pre_get_posts' , array($this, 'mycred_fortunewheel_filter_wheel_data' ) );
        add_action( 'restrict_manage_posts' , array($this, 'mycred_fortunewheel_wheel_filters' ),99 );
        add_action( 'pre_get_posts', array($this,'mycred_fortunewheel_wheel_search') );
        add_action( 'add_meta_boxes', array($this,'mycred_fortunewheel_stats_display'),1 );
    }

    function mycred_fortunewheel_stats_display() {
        add_meta_box(
            'fortunewheel',       // $id
            'Stats',                  // $title
            array($this,'mycred_fortunewheel_stats_data'),  // $callback
            'mycred_fortunewheels',                 // $page
            'normal',                  // $context
            'high'                     // $priority
        );
    }

    function mycred_fortunewheel_stats_data() {
        $wheel_id = get_the_ID();
        $count_click = get_post_meta( $wheel_id,'count_clicks',true);
        $count_wins = get_post_meta( $wheel_id,'count_wins',true);
        $count_loss = get_post_meta( $wheel_id,'count_loss',true);
        $count_play = get_post_meta( $wheel_id,'count_play',true);

        $win_percent = 0; $loss_percent = 0; $avg_play = 0;
        if( !empty( $count_wins ) && !empty( $count_play ) )
            $win_percent = ( $count_wins / $count_play ) * 100;

        if( !empty( $count_loss ) && !empty( $count_loss ) )
            $loss_percent = ( $count_loss / $count_play ) * 100;

        if( !empty( $count_play ) && !empty( $count_play ) )
            $avg_play = ( $count_play / $count_click ) * 100;

        $html = '<div class="fortunewheel-stats-wrapper">

                    <div class="os-fortunewheel-count cl-orange">
                        <div class="c100 p100 small">
                            <span>'.( $count_click == '' ? '0' : $count_click ).'</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                        </div>
                        <div class="os-num-label">NUMBER OF CLICKS</div>
                    </div>
                    
                    <div class="os-fortunewheel-count cl-blue">
                        <div class="c100 p'.floor($avg_play).' small">
                            <span>'.( $count_play == '' ? '0' : $count_play ).'</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                        </div>
                        <div class="os-num-label">NUMBER OF fortunewheelS</div>
                    </div>
                    
                    <div class="os-fortunewheel-count cl-yellow">
                        <div class="c100 p'.floor($avg_play).' small">
                            <span>'.( $avg_play == '' ? '0' : number_format((float)$avg_play , 1, '.', '')).'%</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                        </div>                        
                        <div class="os-num-label">fortunewheel RATE</div>
                    </div>
                    
                    <div class="os-fortunewheel-count cl-green">
                        <div class="c100 p'.floor($win_percent).' small">
                            <span>'.$count_wins.'</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                        </div>
                        <!-- <div class="os-num-wins os-num-counter"></div> -->
                        <div class="os-num-label">NUMBER OF WINS</div>
                    </div>
                    
                    <div class="os-fortunewheel-count cl-red">
                        <div class="c100 p'.floor($loss_percent).' small">
                            <span>'.( $count_loss == '' ? '0' : $count_loss ).'</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                        </div>
                        <div class="os-num-label">NUMBER OF LOSS</div>
                    </div>

                </div>';

        echo $html;
    }

    function mycred_fortunewheel_wheel_search( $query ) {

        // Extend search for document post type
        $post_type = 'fortunewheel-stats';
        // Custom fields to search for
        $custom_fields = array(
            "user_email",
            "username",
            "win_loss",
            "coupon",
        );

        if( ! is_admin() )
            return;

        if ( $query->query['post_type'] != $post_type )
            return;

        $search_term = $query->query_vars['s'];

        // Set to empty, otherwise it won't find anything
        $query->query_vars['s'] = '';

        if ( $search_term != '' ) {
            $meta_query = array( 'relation' => 'OR' );

            foreach( $custom_fields as $custom_field ) {
                array_push( $meta_query, array(
                    'key' => $custom_field,
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ));
            }

            $query->set( 'meta_query', $meta_query );
        };
    }

    function mycred_fortunewheel_stats_callback() {
        $request_to = $_POST['request_to'];
        $current_wheel_id = $_POST['current_wheel_id'];
        $count_number = get_post_meta( $current_wheel_id, $request_to, true );
        if( !empty($count_number) ) {
            $count_number = $count_number + 1;
            update_post_meta( $current_wheel_id,$request_to,$count_number );
        } else {
            update_post_meta( $current_wheel_id,$request_to,1 );
        }
    }

    function mycred_fortunewheel_filter_wheel_data( $query ) {
        global $post_type;

        if( isset( $_GET['wheel_id'] ) && $_GET['wheel_id'] == 'previous_data' ) {
            if($post_type == 'fortunewheel-stats' && $query->is_main_query() ) {

                $meta_query = array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'wheel_id',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'wheel_id',
                        'value' => '',
                        'compare' => '=',
                    ),

                );
                $query->set( 'meta_query', $meta_query );

                /* $query->query_vars[ 'meta_key' ] = 'wheel_id';
                 $query->query_vars[ 'meta_value' ] = $_GET['wheel_id'];*/
            }
        } else {

            $wheel_id = 0;
            if( isset($_GET['wheel_id']) && !empty($_GET['wheel_id']) )
                $wheel_id = $_GET['wheel_id'];

            if($post_type == 'fortunewheel-stats' && $query->is_main_query() ) {

                $meta_query = array(
                    array(
                        'key'     => 'wheel_id',
                        'value'   => '_'.$wheel_id,
                        'compare' => '=',
                    ),
                );
                $query->set( 'meta_query', $meta_query );

                /* $query->query_vars[ 'meta_key' ] = 'wheel_id';
                 $query->query_vars[ 'meta_value' ] = $_GET['wheel_id'];*/
            }
        }
    }

    function mycred_fortunewheel_wheel_filters() {
        // Only apply the filter to our specific post type
        global $typenow; global $post;


        $wheel_id = 0;
        if( isset($_GET['wheel_id']) && !empty($_GET['wheel_id']) )
            $wheel_id = $_GET['wheel_id'];

        $html = '';
        if( $typenow == 'fortunewheel-stats' ) {
            $args = array( 'post_type' => 'mycred_fortunewheels', 'posts_per_page' => -1 );
            $lastposts = get_posts( $args );
            $html .= "<select name='wheel_id'>";
            $html .= '<option value="">Select Wheel</option>';
            foreach ( $lastposts as $post ) :
                setup_postdata( $post );
                $selected = '';
                if( get_the_ID() == $wheel_id )
                    $selected = 'selected';

                $html .= '<option '.$selected.' value="'.get_the_ID().'">'.get_the_title().'</option>';
            endforeach;

            $selected = '';
            if( isset($_GET['wheel_id']) && $_GET['wheel_id'] == 'previous_data' )
                $selected = 'selected';

            $html .= '<option value="previous_data" '.$selected.'>Previous Wheel Data</option>';
            wp_reset_postdata();

            $html .= '</select>';
        }

        echo $html;
    }

    function mycred_fortunewheel_bulk_edit( $actions ){
        unset( $actions[ 'edit' ] );
        return $actions;
    }

    function mycred_fortunewheel_register_statistics() {

        if( isset( $_GET['wheel_id'] ) ) {
            $wheel_id = $_GET['wheel_id'];
            $data = get_post_meta($wheel_id,'more_fields',true);
        }

        $labels = array(
            'name'               => _x( 'fortunewheel List', 'post type general name', 'fortunewheel' ),
            'singular_name'      => _x( 'fortunewheel List', 'post type singular name', 'fortunewheel' ),
            'menu_name'          => _x( 'fortunewheel List', 'admin menu', 'fortunewheel' ),
            'name_admin_bar'     => _x( 'fortunewheel List', 'add new on admin bar', 'fortunewheel' ),
            'add_new'            => _x( 'Add New', 'fortunewheel List', 'fortunewheel' ),
            'add_new_item'       => __( 'Add New fortunewheel List', 'fortunewheel' ),
            'new_item'           => __( 'New fortunewheel List', 'fortunewheel' ),
            'edit_item'          => __( 'Edit fortunewheel List', 'fortunewheel' ),
            'view_item'          => __( 'View fortunewheel List', 'fortunewheel' ),
            'all_items'          => __( 'All fortunewheel List', 'fortunewheel' ),
            'search_items'       => __( 'Search fortunewheel List', 'fortunewheel' ),
            'parent_item_colon'  => __( 'Parent fortunewheel List:', 'fortunewheel' ),
            'not_found'          => __( 'No fortunewheel List found Please Select another wheel from dropdown to display the result.', 'fortunewheel' ),
            'not_found_in_trash' => __( 'No fortunewheel List found in Trash.', 'fortunewheel' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'fortunewheel' ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'fortunewheel-stats' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' )
        );

        register_post_type( 'fortunewheel-stats', $args );
    }

    function mycred_fortunewheel_statistics_callback() {

        $request_to = $_POST['request_to'];

        
        $wheel_id = $_POST['wheel_id'];

        $get_user_id = get_current_user_id();

        $user_info = get_userdata( $get_user_id );
       
        $email = $user_info->user_email;
        $username = $user_info->user_login;

    

        if(mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_cookie_expiry') == 0){
           
            unset($_COOKIE['fortunewheel_user_for_zero']);
            unset($_COOKIE['fortunewheel_email_for_zero']);
        }

        if( empty($username) )
            $username = 'GUEST USER';
        
        
        // Create post object
        $fortunewheel_stats = array(
            'post_title'    => wp_strip_all_tags( $username ),
            'post_type'    => 'fortunewheel-stats',
            'post_status'    => 'publish',
            'post_author'    => 0,
        );

        $stats_id = wp_insert_post( $fortunewheel_stats );
        
        $count = (int) get_post_meta($stats_id,$request_to,true);
        if(!empty($count))
            $count = $count + 1;
        else
            $count = 1;

        update_post_meta($stats_id,$request_to,$count);

        $spin_count = (int) get_post_meta($stats_id,'no_of_spins',true);
        if(!empty($count))
            $spin_count = $spin_count + 1;
        else
            $spin_count = 1;

        update_post_meta($stats_id,'no_of_spins',$spin_count);
        update_post_meta($stats_id,'user_email',$email);
        update_post_meta($stats_id,'username',$username);

        update_post_meta($stats_id,'win_loss',$request_to);

        update_post_meta($stats_id,'ip_address',$this->mycred_fortunewheel_get_user_details()->ip_address);
        update_post_meta($stats_id,'location',$this->mycred_fortunewheel_get_user_details()->city.', '.$this->mycred_fortunewheel_get_user_details()->country);
        update_post_meta($stats_id,'country_code',$this->mycred_fortunewheel_get_user_details()->country_code);

        update_post_meta($stats_id,'wheel_id',$wheel_id);

        $user_roles = implode(', ', $user_info->roles);
        update_post_meta($stats_id,'user_role',$user_roles);


        do_action('fortunewheel_save_email',$email,$username,$stats_id);
        do_action('mycred_fortunewheel_after_spin', $request_to);

        $restrict_ip_expire_time =  date('Y-m-d', strtotime('+1 day'));
        set_transient( 'ip_restrict_'.$wheel_id.'_'.$this->mycred_fortunewheel_getRealUserIp(), $restrict_ip_expire_time, 24 * HOUR_IN_SECONDS );
        
        echo 'DONE';
        die();
    }

    function mycred_fortunewheel_edit_columns( $columns ) {


        $columns = array(
            'cb' => '<input type="checkbox" />',
            'username' => __( 'Username' ),
            'email' => __( 'Email' ),
            'ip' => __( 'IP' ),
            'location' => __( 'Location' ),
            'win_loss' => __( 'Win / Loss' ),
            'user_role' => __( 'User Role' ),
            'date' => __( 'Date' )
        );

        return $columns;
    }

    function mycred_fortunewheel_manage_columns( $column, $post_id ) {

        // $more_fields_arr = array();
        // if( isset( $_GET['wheel_id'] ) || !empty( $_GET['wheel_id'] ) ) {
        //     $wheel_id = $_GET['wheel_id'];

        //     $fortunewheel_form_fields = mycred_fortunewheel_get_post_meta($wheel_id,'fortunewheel_form_fields');
        //     foreach( $fortunewheel_form_fields as $fortunewheel_form_field ) {
        //         $field_key = $fortunewheel_form_field['fortunewheel_key'];
        //         $form_field_name = $fortunewheel_form_field['fortunewheel_label'];
        //         $field_type = str_replace('fortunewheel_','',$fortunewheel_form_field['_type']);

        //         if( $column == $field_key && isset($field_key) ) {
        //             $more_fields_value = json_decode( get_post_meta($post_id,'more_fields',true),true  );
        //             if( isset(  $more_fields_value[ $field_key ] ) ) {
        //                 echo $more_fields_value[ $field_key  ];
        //             }
        //         }
        //     }
        // }

        if( $column == 'username') {
            $username = get_post_meta($post_id,'username',true);
            if( !isset($username) || $username == 'undefined' || empty($username) )
                echo 'GUEST';
            else
                echo $username;
        }

        if( $column == 'email') {
            echo get_post_meta($post_id,'user_email',true);
        }

        if( $column == 'ip') {
            $ip_address = get_post_meta($post_id,'ip_address',true);
            echo ( $ip_address != '' ) ? $ip_address : '-';
        }

        if( $column == 'location') {
            $location = get_post_meta($post_id,'location',true);
            echo ( $location != '' ) ? $location : '-';
        }

     
        if( $column == 'win_loss') {
            $win_loss = get_post_meta($post_id,'win_loss',true);
            echo ( $win_loss == 'no_of_wins' ) ? 'WIN' : 'LOSS';
        }

        if( $column == 'user_role') {
            echo get_post_meta($post_id,'user_role',true);
        }
    }

    function mycred_fortunewheel_get_user_details() {
        $json = file_get_contents("https://ipfind.co/?ip=".$this->mycred_fortunewheel_getRealUserIp());
        $data = json_decode($json);
        return $data;
    }

    function mycred_fortunewheel_stats_menu() {
        /*add_submenu_page( 'fortunewheel-settings', 'Settings', 'Settings',
            'manage_options', 'admin.php?page=fortunewheel-settings');*/
        /*add_submenu_page( 'fortunewheel-settings', 'fortunewheel List', 'fortunewheel List',
            'manage_options', 'edit.php?post_type=fortunewheel-stats');*/
        add_submenu_page( 'edit.php?post_type=mycred_fortunewheels', 'fortunewheel List', 'fortunewheel List',
            'manage_options', 'edit.php?post_type=fortunewheel-stats');
    }

    function mycred_fortunewheel_getRealUserIp(){
        /*if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }*/

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


    function mycred_fortunewheel_write_csv() {
        if( is_admin() && isset( $_GET['export'] ) && $_GET['wheel_id'] == 'previous_data' ) {
            $this->fortunewheel_write_previous_data();
        } else if( is_admin() && isset( $_GET['export'] ) ) {
            $csv_data = array();
            $columns_data = array();
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'fortunewheel-stats',
                'fields ' => 'ids',
                'meta_query' => array(
                    array(
                        'key'     => 'wheel_id',
                        'value'   => '_'.$_GET['wheel_id'],
                        'compare' => '=',
                    ),
                ),
            );
            $more_fields_arr = array();
            $fortunewheel_form_fields = mycred_fortunewheel_get_post_meta($_GET['wheel_id'],'fortunewheel_form_fields');
            foreach( $fortunewheel_form_fields as $fortunewheel_form_field ) {
                $field_type = str_replace('fortunewheel_','',$fortunewheel_form_field['_type']);
                $form_field_name = $fortunewheel_form_field['fortunewheel_label'];
                if($field_type == 'checkbox') {
                    if(strpos($fortunewheel_form_field['fortunewheel_label'],'[a') > 0 ) {
                        $checkbopx_label = str_replace('[a','<a',$fortunewheel_form_field['fortunewheel_label']);
                        $checkbopx_label = str_replace('"]','">',$checkbopx_label);
                        $checkbopx_label = str_replace('[/a]','</a>',$checkbopx_label);

                        $text = $fortunewheel_form_field['fortunewheel_label'];
                        $s_pos = strpos($text,']') + 1;
                        $e_pos = strpos($text,'[/') - $s_pos;
                        $checkbox_name= substr($text,$s_pos,$e_pos);
                        $more_fields_arr[$checkbox_name] = $checkbox_name;
                    } else {
                        $more_fields_arr[] = $fortunewheel_form_field['fortunewheel_label'];
                    }
                } else {
                    $more_fields_arr[] = $fortunewheel_form_field['fortunewheel_label'];
                }
            }

            $columns_data[] = 'Username';
            $columns_data[] = 'Email';
            $columns_data[] = 'IP';
            $columns_data[] = 'Location';
            $columns_data[] = 'No of Win / No of Loss';
            $columns_data[] = 'User Role';
            $columns_data[] = 'Coupon';

            $columns_data = array_merge($columns_data,$more_fields_arr);

            $csv_data[] = $columns_data;

            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                // The 2nd Loop
                while ( $query->have_posts() ) {
                    $query->the_post();

                    unset($columns_data);

                    $username = get_post_meta(get_the_ID(),'username',true);
                    if( !isset($username) || $username == 'undefined' )
                        $username = 'GUEST';

                    $columns_data[] = $username;

                    $columns_data[] = get_post_meta(get_the_ID(),'user_email',true);
                    $columns_data[] = get_post_meta(get_the_ID(),'ip_address',true);
                    $columns_data[] = get_post_meta(get_the_ID(),'location',true);


                    $win_loss = get_post_meta(get_the_ID(),'win_loss',true);
                    $columns_data[] = ( $win_loss == 'no_of_wins' ) ? 'WIN' : 'LOSS';

                    $columns_data[] = get_post_meta(get_the_ID(),'user_role',true);
                    $coupon = get_post_meta(get_the_ID(),'coupon',true);
                    if( !empty( $coupon ) ) {
                        if( !empty( get_the_title( $coupon ) && is_numeric($coupon) ) ) {
                            $columns_data[] = get_the_title( $coupon );
                        } else {
                            $columns_data[] = $coupon;
                        }

                    } else {
                        $columns_data[] = '-';
                    }

                    $fortunewheel_form_fields = mycred_fortunewheel_get_post_meta($_GET['wheel_id'],'fortunewheel_form_fields');
                    foreach( $fortunewheel_form_fields as $fortunewheel_form_field ) {
                        $field_key = $fortunewheel_form_field['fortunewheel_key'];
                        $field_type = str_replace('fortunewheel_','',$fortunewheel_form_field['_type']);
                        $form_field_name = $fortunewheel_form_field['fortunewheel_label'];
                        if($field_type == 'checkbox') {
                            if(strpos($fortunewheel_form_field['fortunewheel_label'],'[a') > 0 ) {
                                $checkbopx_label = str_replace('[a','<a',$fortunewheel_form_field['fortunewheel_label']);
                                $checkbopx_label = str_replace('"]','">',$checkbopx_label);
                                $checkbopx_label = str_replace('[/a]','</a>',$checkbopx_label);

                                $text = $fortunewheel_form_field['fortunewheel_label'];
                                $s_pos = strpos($text,']') + 1;
                                $e_pos = strpos($text,'[/') - $s_pos;
                                $checkbox_name= substr($text,$s_pos,$e_pos);
                                $form_field_name = $checkbox_name;
                            } else {
                                $form_field_name = $checkbox_name;
                            }
                        }

                        $more_fields_value = json_decode( get_post_meta(get_the_ID(),'more_fields',true),true  );
                        if( isset(  $more_fields_value[$field_key] ) ) {



                            $columns_data[] = $more_fields_value[$field_key];
                        }

                    }

                    $csv_data[] = $columns_data;

                }

                // Restore original Post Data
                wp_reset_postdata();
            }

            $fp = fopen(fortunewheel_PLUGIN_PATH.'/fortunewheel-list.csv', 'w');

            foreach ($csv_data as $fields) {
                fputcsv($fp, $fields);
            }

            fclose($fp);

            $fileurl = mycred_fortunewheel_PLUGIN_PATH.'/fortunewheel-list.csv';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($fileurl));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileurl));
            ob_clean();
            flush();
            readfile($fileurl);
            exit;
        }
    }

    function mycred_fortunewheel_write_previous_data() {
        if( is_admin() ) {
            $csv_data = array();
            $columns_data = array();
            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'fortunewheel-stats',
                'fields ' => 'ids',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'wheel_id',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'wheel_id',
                        'value' => '',
                        'compare' => '=',
                    ),

                ),
            );
            $columns_data[] = 'Username';
            $columns_data[] = 'Email';
            $columns_data[] = 'IP';
            $columns_data[] = 'Location';
            $columns_data[] = 'No of Win / No of Loss';
            $columns_data[] = 'User Role';
            $columns_data[] = 'Coupon';

            $csv_data[] = $columns_data;

            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                // The 2nd Loop
                while ( $query->have_posts() ) {
                    $query->the_post();

                    unset($columns_data);

                    $columns_data[] = get_post_meta($query->post->ID,'username',true);
                    $columns_data[] = get_post_meta($query->post->ID,'user_email',true);
                    $columns_data[] = get_post_meta($query->post->ID,'ip_address',true);
                    $columns_data[] = get_post_meta($query->post->ID,'location',true);
                    $columns_data[] = get_post_meta($query->post->ID,'win_loss',true);
                    $columns_data[] = get_post_meta($query->post->ID,'user_role',true);
                    $coupon = get_post_meta($query->post->ID,'coupon',true);
                    $columns_data[] = ( $coupon != '' ? get_the_title( $coupon ) : '');

                    $csv_data[] = $columns_data;
                    unset($columns_data);
                }

                // Restore original Post Data
                wp_reset_postdata();
            }

            $fp = fopen(fortunewheel_PLUGIN_PATH.'/fortunewheel-list.csv', 'w');

            foreach ($csv_data as $fields) {
                fputcsv($fp, $fields);
            }

            fclose($fp);

            $fileurl = mycred_fortunewheel_PLUGIN_PATH.'/fortunewheel-list.csv';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($fileurl));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileurl));
            ob_clean();
            flush();
            readfile($fileurl);
            exit;
        }
    }
}