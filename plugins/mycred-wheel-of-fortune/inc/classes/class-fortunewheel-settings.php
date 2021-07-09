<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

if( ( isset( $_GET['post'] ) && get_post_status( $_GET['post'] ) == 'draft' ) || !isset( $_GET['post'] ) )
    $ready_msg = '<div class="opt-note"><strong>NOTE:</strong> Your wheel is not visible to your users, until you publish it!</div>';

Class mycred_fortunewheel_Settings {

    public $wheel_slices_labels = array('slices' => 'Slices','slice' => 'Slice');

    function __construct() {
        add_action( 'carbon_fields_register_fields', array($this,'mycred_fortunewheel_add_settings_page') );
        add_action( 'after_setup_theme', array($this,'mycred_fortunewheel_crb_load') );
    }

    function mycred_fortunewheel_add_settings_page() {
        global $wheel_slices_labels,$ready_msg;
        $fortunewheel_mailchimp_get_list = get_option( 'fortunewheel_mailchimp_get_list');
        if(!empty($fortunewheel_mailchimp_get_list)){
            $fortunewheel_mailchimp_get_list = json_decode($fortunewheel_mailchimp_get_list);
            if(!empty($fortunewheel_mailchimp_get_list->data)){
                $arraysfor_fortunewheel_mailchimp_get_list[''] = 'Select Email List';
                foreach($fortunewheel_mailchimp_get_list->data as $data){
                    $arraysfor_fortunewheel_mailchimp_get_list[$data->id] =  $data->name;
                }
            } else {
                $arraysfor_fortunewheel_mailchimp_get_list[''] = 'Email List not found!';
            }
        }else{
            $arraysfor_fortunewheel_mailchimp_get_list[''] = 'Email List not found!';
        }

        $fortunewheel_active_campaign_get_list = get_option( 'fortunewheel_active_campaign_get_list');
        if(!empty($fortunewheel_active_campaign_get_list)){
            if(!empty($fortunewheel_active_campaign_get_list)){
                $arraysfor_fortunewheel_active_campaign_get_list[''] = 'Select Email List';
                foreach($fortunewheel_active_campaign_get_list as $data){
                    if(!empty($data['id']) and !empty($data['name'])){
                        $arraysfor_fortunewheel_active_campaign_get_list[$data['id']] =  $data['name'];
                    }
                }

            } else {
                $arraysfor_fortunewheel_active_campaign_get_list[''] = 'Email List not found!';
            }
        }else{
            $arraysfor_fortunewheel_active_campaign_get_list[''] = 'Email List not found!';
        }
        if(function_exists( 'mailster' )){
            $lists = mailster( 'lists' )->get();
            $mailsteractive = 1;
            update_option('_fortunewheel_mailsteractive',1);
            if(!empty($lists)){
                $arraysfor_fortunewheel_mailster_get_list[''] = 'Select Email List';
                foreach($lists as $list){
                    $arraysfor_fortunewheel_mailster_get_list[$list->ID] = $list->name;
                }
            } else {
                $arraysfor_fortunewheel_mailster_get_list[''] = 'Email List not found!';
            }
        } else {
            update_option('_fortunewheel_mailsteractive',0);
            $arraysfor_fortunewheel_mailster_get_list = array();
            $mailsteractive = 0;
        }

        $form_fileds_labels = array(
            'plural_name' => 'Fields',
            'singular_name' => 'New Field',
        );

        Container::make( 'post_meta', 'Wheel Settings' )
            ->where( 'post_type', '=', 'mycred_fortunewheels' )
            ->set_priority( 'low' )
            // ->set_page_file( 'fortunewheel-settings' )
            ->add_tab( __('Title'), array(
                Field::make( 'text', 'fortunewheel_title'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Set Your Wheel Title' ),
            ) )

            ->add_tab( __('Slices'), array(

                Field::make( 'complex', 'crb_section'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Slices' )
                    ->set_collapsed( true )
                    ->setup_labels( array(
                        'plural_name' => 'Slices',
                        'singular_name' => 'Slice',
                    ) )
                    ->add_fields( 'no_prize', array(

                        Field::make( 'select', 'fortunewheel_section_type', 'Section Type' )
                            ->add_options( $this->mycred_fortunewheel_section_types() )
                            ->set_help_text('Choose Coupon for this section')
                            ->set_classes( 'fortunewheel_coupon_list' ),

                        Field::make( 'text', 'fortunewheel_section_label', 'Label' )
                            ->set_help_text('Label of wheel section (section label doesn\'t appear on front-end if your section type is `image`)')
                            ->set_classes( 'fortunewheel_section_label' ),

                        Field::make( 'select', 'fortunewheel_mycred_types', 'Point Type' )
                            ->add_options( $this->mycred_get_types_settings() )
                            ->set_help_text('Select Your Point Type'),

                        Field::make( 'text', 'fortunewheel_mycred_points', 'Points' )
                            ->set_help_text('Set Lossing Points'),

                        Field::make( 'text', 'fortunewheel_mycred_log_template', 'Loss Log Tempalte' )
                            ->set_help_text('Set Lossing Points')
                            ->set_default_value('Deduct %plural% for lossing Spin from fortunewheel')
                            ->set_conditional_logic( array(
                                array(
                                    'field' => 'fortunewheel_mycred_points_check',
                                    'value' => true,
                                )
                            ) ),

                        Field::make( 'image', 'fortunewheel_section_image'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Section Image' )
                            ->set_value_type( 'url' )
                            ->set_help_text( 'Add Your Section Image' )
                            ->set_conditional_logic( array(
                                array(
                                    'field' => 'fortunewheel_section_type',
                                    'value' => 'image',
                                )
                            ) ),

                        Field::make( 'color', 'segment_color', 'Section color' )
                            ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                            ->set_help_text( 'Set the color of the respective section/segment' ),
                        Field::make( 'textarea', 'fortunewheel_win_loss_text', 'Lossing text' ),
                        Field::make( 'text', 'fortunewheel_probability', 'Probability' )
                            ->set_attribute('type','number')
                            ->set_required( true )
                            ->set_help_text( 'How much chances to stop at this segment ( 0 - 100 )' ),



                    ))->set_collapsed( true )
                    ->set_header_template( '
                                    <% if (fortunewheel_section_label) { %>
                                        LOSS PRIZE - <%- fortunewheel_section_label %>
                                    <% } %>
                                ' )
                    ->add_fields( 'win_prize', array(

                        Field::make( 'select', 'fortunewheel_section_type', 'Section Type' )
                            ->add_options( $this->mycred_fortunewheel_section_types() )
                            ->set_help_text('Choose Coupon for this section')
                            ->set_classes( 'fortunewheel_coupon_list' ),

                        Field::make( 'text', 'fortunewheel_section_label', 'Label' )
                            ->set_help_text('Label of wheel section')
                            ->set_classes( 'fortunewheel_section_label' ),

                        Field::make( 'image', 'fortunewheel_section_image'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Section Image' )
                            ->set_value_type( 'url' )
                            ->set_help_text( 'Add Your Section Image' )
                            ->set_conditional_logic( array(
                                array(
                                    'field' => 'fortunewheel_section_type',
                                    'value' => 'image',
                                )
                            ) ),

                        Field::make( 'select', 'fortunewheel_mycred_types', 'Point Type' )
                            ->add_options( $this->mycred_get_types_settings() )
                            ->set_help_text('Select Your Point Type'),

                        Field::make( 'checkbox', 'mycred_lottery_enable'.mycred_fortunewheel_crb_get_i18n_suffix(), 'myCred Lottery Enabled' )
                            ->set_option_value( 'myCred Lottery Enabled' ),

                        Field::make( 'text', 'fortunewheel_mycred_points', 'Points' )
                            ->set_help_text('Set Winning Points')
                            ->set_conditional_logic( array(
                                array(
                                    'field' => 'mycred_lottery_enable',
                                    'value' => false,
                                )
                            ) ),

                        Field::make( 'select', 'mycred_lottery_id', 'Select myCred Lottery' )
                            ->add_options( $this->mycred_get_all_lotteries() )
                            ->set_help_text('myCred Lottery')
                            ->set_conditional_logic( array(
                                array(
                                    'field' => 'mycred_lottery_enable',
                                    'value' => true,
                                )
                            ) ),

                        Field::make( 'text', 'fortunewheel_mycred_no_of_lottery_entries', 'No of Free Entries' )
                            ->set_help_text('No of entries')
                            ->set_conditional_logic( array(
                                array(
                                    'field' => 'mycred_lottery_enable',
                                    'value' => true,
                                )
                            ) ),

                        Field::make( 'text', 'fortunewheel_mycred_log_template', 'Win Log Tempalte' )
                            ->set_help_text('Set Lossing Points')
                            ->set_default_value('Reward %plural% for winning Spin from fortunewheel'),

                        Field::make( 'text', 'fortunewheel_max_availability', 'Max Availability' )
                            ->set_attribute('type','number'),

                        Field::make( 'text', 'fortunewheel_unique_section_id', 'unique id' )
                            ->set_attribute( 'type', 'hidden' )
                            ->set_classes( 'fortunewheel_section_class' ),

                        Field::make( 'text', 'fortunewheel_flush_availability', 'Clear number of wins' )
                            ->set_attribute( 'type', 'button' )
                            ->set_classes( 'fortunewheel_flush_availability' )
                            ->set_default_value('Clear number of wins'),

                        Field::make( 'text', 'fortunewheel_probability', 'Probability' )
                            ->set_attribute('type','number')
                            ->set_required( true ),
                        Field::make( 'color', 'segment_color', 'Section color' )
                            ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                            ->set_help_text( 'Set the color of the respective section/segment' ),
                        Field::make( 'textarea', 'fortunewheel_win_loss_text', 'Winning text' ),
                    ))->set_collapsed( true )
                    ->set_header_template( '
                                    <% if (fortunewheel_section_label) { %>
                                        WIN PRIZE - <%- fortunewheel_section_label %>
                                    <% } %>
                                ' ),
            ) )

            ->add_tab( __('Display Settings'), array(

                Field::make( 'checkbox', 'fortunewheel_display_slider_wheel'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Display fortunewheel via Slider' )
                    ->set_option_value( 'Display fortunewheel Slider' ),

                Field::make( 'checkbox', 'fortunewheel_display_all_pages'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Display fortunewheel on All Pages' )
                    ->set_option_value( 'Display fortunewheel on all pages' )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => true,
                        )
                    ) ),


                Field::make( 'checkbox', 'fortunewheel_enable_pages_display'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable fortunewheel on specific pages' )
                    ->set_option_value( 'Enable fortunewheel on specific pages' )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => true,
                        )
                    ) ),

                Field::make( 'association', 'fortunewheel_selected_pages', 'Page' )
                    ->set_types( array(
                        array(
                            'type' => 'post',
                            'post_type' => 'page',
                        ),
                    ) )

                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_enable_pages_display',
                            'value' => true,
                        )
                    ) ),


                Field::make( 'checkbox', 'fortunewheel_enable_posts_display'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable fortunewheel on specific posts' )
                    ->set_option_value( 'Enable fortunewheel on specific posts' )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => true,
                        )
                    ) ),

                Field::make( 'complex', 'fortunewheel_show_posts_complex'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Display on Specific Post' )
                    ->setup_labels( array(
                        'plural_name' => 'Posts',
                        'singular_name' => 'Another Posts',
                    ) )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_enable_posts_display',
                            'value' => true,
                        )
                    ) )
                    ->add_fields( array(
                        Field::make( 'select', 'fortunewheel_show_posts', 'Select Post' )
                            ->add_options( $this->mycred_fortunewheel_lists_of_posts() )
                            ->set_help_text('Select post where you would like to display fortunewheel'),
                    ) )
            ) )

        

            ->add_tab( __('Ready'), array(
                Field::make( 'html', 'crb_information_text' )
                    ->set_html( '<div class="opt-wheel-ready-wrapper">
                                    <h1>GREAT! YOUR WHEEL IS READY <i class="fas fa-thumbs-up"></i></h1> 
                                    <p class="opt-ready-text">Your wheel is ready to spin. You can configured triggers, form fields, email templates, change slider colors, labels and other interesting feature from advanced settings.</p>
                                    <p><i class="fas fa-cog"></i></p>
                                    <div class="opt-adv-setting-btn">Go to Advanced Settings</div><br>'.$ready_msg.'
                                </div>' )
            ))


            ->add_tab( __('Form Fields'), array(

                Field::make( 'html', 'fortunewheelpsin_sub_tabs' )
                    ->set_html( '<div class="opt-wheel-sub-tabs">
                                    <ul class="fortunewheel-sub-tabs">
                                        <li><span class="dashicons dashicons-admin-generic"></span> General Settings</li>
                                        <li><span class="dashicons dashicons-editor-expand"></span> Triggers</li>
                                        <li><span class="dashicons dashicons-share-alt"></span> Privacy</li>                                                                                
                                        <li><span class="dashicons dashicons-share-alt"></span> Wining Message</li>                                                                                
                                        <li><span class="dashicons dashicons-art"></span> Additional CSS</li>
                                        <li><span class="dashicons dashicons-image-filter"></span> Snow Fall</li>
                                        <li><span class="dashicons dashicons-lock"></span></span> Password Protect</li>
                                    </ul>
                                </div>' ),

                Field::make( 'separator', 'fortunewheel_general', 'General Settings' )
                    ->set_classes( 'fortunewheel_separator' ),

                Field::make( 'text', 'fortunewheel_spin_speed'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Wheel Spin Speed' )
                    ->set_attribute('type','number')
                    ->set_default_value('0.5')
                    ->set_help_text('Control the speed of the wheel'),

                Field::make( 'separator', 'wheel_access_settings', 'Wheel Access Settings' ),

                // Field::make( 'checkbox', 'fortunewheel_restricted_by_cooike'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Restrict by Cookie' )
                //     ->set_option_value( 'Restricted by Cookie' ),

                // Field::make( 'checkbox', 'fortunewheel_restricted_by_ip'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Restrict by IP (NOT RECOMMENDED SINCE 2 PERSON SHOULD BE ABLE TO PLAY WITH SAME IP)' )
                //     ->set_option_value( 'Restricted by IP' ),

                // Field::make( 'select', 'fortunewheel_duration_type', 'Duration Type' )
                //     ->add_options( array(
                //         'day' =>'Day',
                //         'hour' =>'Hour',
                //     ) ),
                Field::make( 'text', 'fortunewheel_cookie_expiry'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Cookie Expiry Time' )
                    ->set_attribute('type','number')
                    ->set_default_value('2')
                    ->set_help_text('Expire Cookie Time (please enter 1 or greater number)'),

                // Field::make( 'text', 'fortunewheel_number_chances_play'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Number Of Chances To Play' )
                //     ->set_default_value('1')
                //     ->set_help_text('Numbner of chances per user in specified duration'),

                Field::make( 'text', 'fortunewheel_shortcode_message'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Shortcode Message' )
                    ->set_default_value('Try Your Luck again tomorrow')
                    ->set_help_text('Shortcode mesage after user have played fortunewheel'),

                // Field::make( 'checkbox', 'fortunewheel_enable_cart_redirect'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Cart Redirect' )
                //     ->set_help_text( 'Enable Cart Redirect after successfuly added coupon to cart' )
                //     ->set_option_value( 'Enable Cart Redirect after successfuly added coupon to cart' ),

                Field::make( 'separator', 'fortunewheel_wheel_style', 'Style' ),

                Field::make( 'image', 'fortunewheel_background_image'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Background Image' )
                    ->set_value_type( 'url' ),

                Field::make( 'text', 'fortunewheel_text_size'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Wheel Text Size' )
                    ->set_attribute('type','number')
                    ->set_default_value('2.3')
                    ->set_help_text('Adjust Text Size of Segments'),

                Field::make( 'text', 'fortunewheel_border_width'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Border Width' )
                    ->set_attribute('type','number')
                    ->set_default_value('18')
                    ->set_help_text('Set Border Width'),

                Field::make( 'image', 'fortunewheel_wheel_center_logo'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Wheel Center Logo' )
                    ->set_value_type( 'url' )
                    ->set_help_text( 'Set Logo in the above the form' ),

                Field::make( 'image', 'fortunewheel_logo'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Logo' )
                    ->set_value_type( 'url' )
                    ->set_help_text( 'Set Logo in the above the form' ),

                Field::make( 'checkbox', 'fortunewheel_enable_sound'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Sound' )
                    ->set_option_value( 'Enable Sound' ),

                Field::make( 'checkbox', 'fortunewheel_enable_sparkle'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Sparkel' )
                    ->set_option_value( 'Enable Sparkel after win' ),

                // Field::make( 'checkbox', 'fortunewheel_allow_same_email'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Allow multiple times with same email' )
                //     ->set_help_text( 'if you check this users will be albe to use their same email to play again once wheel appears.' )
                //     ->set_option_value( 'Allow Same Email' ),

                Field::make( 'text', 'fortunewheel_free_attempts'.mycred_fortunewheel_crb_get_i18n_suffix(), 'How many free spin in 1 day' )
                ->set_attribute('type','number')
                ->set_help_text( 'Free spin in 1 day' )
                ->set_default_value(2),

                Field::make( 'text', 'fortunewheel_another_spin_with_points'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Another Spin with points' )
                ->set_attribute('type','number')
                ->set_help_text( 'User can spin another wheel by giving some' )
                ->set_default_value(50),

                Field::make( 'text', 'fortunewheel_no_points_msg'.mycred_fortunewheel_crb_get_i18n_suffix(), 'No Points Message' )
                ->set_help_text( 'Message when user doesn\'t have enough balance for spin' )
                ->set_default_value('You do not have enough points for spin. <a href="#">Buy Points</a>'),
                
                Field::make( 'select', 'fortunewheel_pay_for_spin_mycred_types', 'Point Type for Pay Spin' )
                    ->add_options( $this->mycred_get_types_settings() )
                    ->set_help_text('Select Your Point Type for another Spin'),

                Field::make( 'separator', 'fortunewheel_wheel_setting', 'Colors' ),

                Field::make( 'color', 'fortunewheel_background_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Background' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set background Color of Wheel' ),

                Field::make( 'color', 'fortunewheel_border_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Outer Border' )
                    ->set_palette( array( '#FF0000', '#00FF00'.mycred_fortunewheel_crb_get_i18n_suffix(), '#0000FF' ) )
                    ->set_help_text( 'Set outer border Color of wheel' ),

                Field::make( 'color', 'fortunewheel_inner_border_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Inner Border' )
                    ->set_palette( array( '#FF0000', '#00FF00'.mycred_fortunewheel_crb_get_i18n_suffix(), '#0000FF' ) )
                    ->set_help_text( 'Set inner border Color of wheel' ),

                Field::make( 'color', 'fortunewheel_text_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Text Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Text Color Of Segment' ),

                Field::make( 'color', 'fortunewheel_buttons_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Button Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Color of the Button' ),

                Field::make( 'color', 'fortunewheel_buttons_text_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Button Text Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Color of the Button' ),

                Field::make( 'color', 'fortunewheel_buttons_hover_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Button Hover Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Color of the Button Hover' ),

                Field::make( 'separator', 'fortunewheel_wheel_formating', 'Labels / Text' ),

                Field::make( 'text', 'fortunewheel_spinner_label'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Spinner Label' )
                    ->set_help_text('Set Text of Spinner Label')
                    ->set_default_value('Try Spin to Win!'),

                Field::make( 'text', 'fortunewheel_cross_label'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Cross Label' )
                    ->set_help_text('Cross Label'),

                Field::make( 'text', 'fortunewheel_email_label'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Email Label' )
                    ->set_help_text( 'Set label of the Email Field' )
                    ->set_default_value('Your Email'),

                Field::make( 'text', 'fortunewheel_button_label'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Button Label' )
                    ->set_help_text( 'Set Label of the button' )
                    ->set_default_value('Spin Now!'),

                Field::make( 'textarea', 'fortunewheel_intro_text'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Intro Text' )
                    ->set_default_value('<div id="fortunewheel-content" style="text-align: left;padding: 0px 0px 0px 10px;"><div class="wlo_title" style="font-family: sans-serif  !important;font-size: 25px;line-height: 1.3em;    font-weight: bold;color: white;margin-bottom: 20px;" >myCRED Fortune Wheel <b style="color: #f1c40f;focnt-size: 25px;line-height: 1.3em;font-family: sans-serif;font-weight:bold;">special offer</b> unlocked!</div><div class="wlo_text" style="color: white;font-size: 14px;text-shadow: none;">You have a chance to win free tokens. Are you feeling lucky? Give it a spin.</div>						<div class="wlo_small_text wlo_disclaimer_text"style="    font-size: 12px;color: #b1b1b1;" >* You can spin the wheel only once in a month.<br>* You can spin again any time by paying 500 points <br>* Once you pay for spin you are not able to refund it. </div></div>'),

                Field::make( 'separator', 'fortunewheel_triggers', 'Triggers' )
                    ->set_classes( 'fortunewheel_separator' ),

                Field::make( 'separator', 'fortunewheel_trigger_message', 'You must select "fortunewheel Slider" in "Display Settings" tab to view trigger settings' )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => false,
                        )
                    ) ),

                Field::make( 'separator', 'fortunewheel_wheel_clickable_tab', 'Open Spin By Clickable Tab' ),

                Field::make( 'checkbox', 'fortunewheel_enable_clickable_tab_desktop'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Show Clickable Tab on Desktop' )
                    ->set_option_value( 'Enable Time Delay Popup on Deskto' )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => true,
                        )
                    ) ),

                Field::make( 'checkbox', 'fortunewheel_enable_clickable_tab_mobile'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Show Clickable Tab on Mobile' )
                    ->set_option_value( 'Enable Intent Exit Popup on Mobile' )
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => true,
                        )
                    ) ),

                Field::make( 'separator', 'fortunewheel_wheel_time_interval_separator', 'Open Spin By Time' ),

                Field::make( 'select', 'fortunewheel_wheel_open_at'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Open Wheel at' )
                    ->add_options( array(
                        'none' => 'none',
                        'once' => 'once',
                        'every' => 'every',
                    ) )
                    ->set_help_text('Time/Interval')
                    ->set_conditional_logic( array(
                        array(
                            'field' => 'fortunewheel_display_slider_wheel',
                            'value' => true,
                        )
                    ) ),

                Field::make( 'text', 'fortunewheel_open_wheel_after'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Open Wheel After' )
                    ->set_help_text( 'Open Wheel after X seconds' )
                    ->set_default_value( '5' ),

                Field::make( 'checkbox', 'fortunewheel_enable_time_delay_desktop'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Time Delay Popup on Desktop' )
                    ->set_option_value( 'Enable Time Delay Popup on Deskto' ),

                Field::make( 'checkbox', 'fortunewheel_enable_time_delay_mobile'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Time Delay Popup on Mobile' )
                    ->set_option_value( 'Enable Intent Exit Popup on Mobile' ),

                Field::make( 'separator', 'fortunewheel_wheel_intent_exit', 'Open Spin By Intent Exit' ),

                Field::make( 'checkbox', 'fortunewheel_enable_intent_exit_popup_desktop'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Intent Exit Popup Desktop' )
                    ->set_option_value( 'Enable Intent Exit Popup on Desktop' ),

                Field::make( 'checkbox', 'fortunewheel_enable_intent_exit_popup_mobile'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable Intent Exit Popup Mobile' )
                    ->set_option_value( 'Enable Intent Exit Popup on Mobile' ),

                Field::make( 'separator', 'fortunewheel_privacy', 'Privacy' )
                    ->set_classes( 'fortunewheel_separator' ),

                Field::make( 'text', 'fortunewheel_privacy_label'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Label' )
                    ->set_help_text('Enter Privacy Label')
                    ->set_default_value('Privacy'),

                Field::make( 'select', 'fortunewheel_privacy_page'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Privacy Page' )
                    ->add_options( $this->mycred_fortunewheel_lists_of_pages() )
                    ->set_help_text('Select Privacy Page'),

                Field::make( 'separator', 'fortunewheel_result', 'Configure Winning Message' )
                    ->set_classes( 'fortunewheel_separator' ),

                Field::make( 'color', 'fortunewheel_win_background_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Background Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Background Color after winning or lossing' ),

                Field::make( 'color', 'fortunewheel_win_border_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Border Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Border Color after winning or lossing' ),

                Field::make( 'color', 'fortunewheel_win_text_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Text Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Text Color after winning or lossing' ),

                // Field::make( 'color', 'fortunewheel_add_cart_link_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Link Color' )
                //     ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                //     ->set_help_text( 'Set Link Color after winning or lossing' ),

                Field::make( 'color', 'fortunewheel_skip_link_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Link Color' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Link Color after winning or lossing' ),

                // Field::make( 'color', 'fortunewheel_coupon_msg_bg'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Coupon Message Background Color' )
                //     ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                //     ->set_help_text( 'Set Link Color after winning or lossing' ),

                // Field::make( 'color', 'fortunewheel_coupon_msg_text_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Coupon Message Text Color' )
                //     ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                //     ->set_help_text( 'Set Link Color after winning or lossing' ),

                // Field::make( 'color', 'fortunewheel_add_cart_bg_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Add to Cart Background Color' )
                //     ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                //     ->set_help_text( 'Add to Cart Button Background Color' ),

                Field::make( 'textarea', 'fortunewheel_coupon_message'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Winning Message Text' )
                    ->set_help_text( 'Message after spin' )
                    ->set_default_value('You have won free points'),

                // Field::make( 'text', 'fortunewheel_add_to_cart_btn'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Add To Cart Label' )
                //     ->set_help_text( 'Add To Cart Label' )
                //     ->set_default_value('Continue and Apply To Cart'),

                // Field::make( 'text', 'fortunewheel_skip_btn'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Skip Slide' )
                //     ->set_help_text( 'Skip Slide' )
                //     ->set_default_value(''),

                Field::make( 'separator', 'fortunewheel_additionalcss', 'Additional CSS' )
                    ->set_classes( 'fortunewheel_separator' ),

                Field::make( 'textarea', 'fortunewheel_custom_css'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Custom CSS' )
                    ->set_help_text( 'Apply Custom CSS' )
                    ->set_default_value('')
                    ->set_rows( 10 ),

                Field::make( 'separator', 'fortunewheel_snowfall', 'Snow Fall' )
                    ->set_classes( 'fortunewheel_separator' ),

                Field::make( 'checkbox', 'fortunewheel_snowflak_enable'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable / Disable Snowflake' )
                    ->set_help_text( 'Enable Snow showering in fortunewheel Wheel Page!' ),


                Field::make( 'text', 'fortunewheel_snow_numfla'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Number of Snowflake ' )
                    ->set_attribute('type','number')
                    ->set_default_value('40')
                    ->set_help_text('Control the number snowflake ( keep the flakes count less than 50 to keep the functionality smooth!  )'),

                Field::make( 'image', 'fortunewheel_image_snowflake'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Image of snowflake' )
                    ->set_value_type( 'url' ),


                Field::make( 'text', 'fortunewheel_snowflake_width'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Width of Snowflake Image' )
                    ->set_attribute('type','number')
                    ->set_default_value('25')
                    ->set_help_text('Control the width of Snowflake image.'),

                Field::make( 'text', 'speed_of_flake'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Speed of Snowflake ' )
                    ->set_attribute('type','number')
                    ->set_default_value('40')
                    ->set_help_text('Control the Speed Snow Falling.'),

                Field::make( 'separator', 'fortunewheel_password_protect_sep', 'Password Protect' )
                    ->set_classes( 'fortunewheel_separator' ),

                // Field::make( 'checkbox', 'fortunewheel_enabled_logged_in_only'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable For Logged In Users' )
                //     ->set_help_text( 'Enable For Logged In Users' ),

                Field::make( 'checkbox', 'fortunewheel_enabled_password_protect'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Enable password Protect' )
                    ->set_help_text( 'Enable Password Protect' ),

                Field::make( 'text', 'fortunewheel_password_protect'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Password' )
                    ->set_help_text('Set Your Password to protect spin'),

                Field::make( 'text', 'fortunewheel_protect_label_text'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Password Label' )
                    ->set_default_value('Enter Password')
                    ->set_help_text('Set Your Password Label'),

                Field::make( 'text', 'fortunewheel_button_text'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Password Button Text' )
                    ->set_default_value('Enter')
                    ->set_help_text('Set Your Password Button Label'),

                Field::make( 'color', 'fortunewheel_password_button_background_color'.mycred_fortunewheel_crb_get_i18n_suffix(), 'Background' )
                    ->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) )
                    ->set_help_text( 'Set Password Button Background color' ),

            ));

    }

    function mycred_get_types_settings() {
        $mycred_types = array();
        if( function_exists( 'mycred_get_types' ) ) {
            return mycred_get_types();
        }

        return $mycred_types;
    }

    function mycred_fortunewheel_crb_load() {
        require_once( mycred_fortunewheel_PLUGIN_PATH . '/inc/settings/carbon-fields/vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot();
    }


    function mycred_fortunewheel_lists_of_pages() {
        $page_list = array();
        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => 'page',
            'post_status'      => 'publish',
        );

        $pages = get_posts( $args );
        $page_list['none'] = 'none';
        foreach( $pages as $page ) {
            $page_list[get_permalink($page->ID)] = $page->post_title;
        }

        $data = Field::make( 'checkbox', 'fortunewheel_disable_coupon_bar', 'Disable Coupon Bar' )
            ->set_option_value( 'Disable Coupon Bar' );

        return $page_list;
    }

    function mycred_get_all_lotteries() {
        $mycred_lottries = array();
        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => 'mycred_lottery',
            'post_status'      => 'publish',
        );

        $mycred_lottery_data = get_posts( $args );
        $mycred_lottries['none'] = 'none';
        foreach( $mycred_lottery_data as $mycred_lottery ) {
            $mycred_lottries[$mycred_lottery->ID] = $mycred_lottery->post_title;
        }

        return $mycred_lottries;
    }

    function mycred_fortunewheel_section_types() {

        $sections['text'] = 'Text';
        $sections['image'] = 'Image';

        return $sections;
    }

    function mycred_fortunewheel_coupon_options() {
        global $mycred_fortunewheel;

        $coupon_options = array();

        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
            $coupon_options['woocommere_coupon'] = 'WooCommerce Coupon';

        if( in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
            $coupon_options['edd_coupon'] = 'Easy Digital Downloads Coupon';

        $coupon_values = array(
            'coupon_text' => 'Coupon Text',
            'coupon_link' => 'Coupon Link',
        );

        if( $mycred_fortunewheel->mycred_fortunewheel_is_mycred_emabled() == true )
            $coupon_values['fortunewheel_mycred'] = 'MyCred Points';

        $coupon_values = array_merge($coupon_options,$coupon_values);

        return $coupon_values;
    }

    function mycred_fortunewheel_lists_of_posts() {

        $default_value = array();
        $default_value['page'] = 'page';
        $default_value['post'] = 'post';
        $available_posts = get_option('fortunewheel_available_posts',false);

        if($available_posts) {
            $available_posts = array_merge($default_value, $available_posts);
            return $available_posts;
        } else {
            return $default_value;
        }
    }
}