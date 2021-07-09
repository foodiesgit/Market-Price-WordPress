<?php
/**
 * Opt
 */


class Setting_Sections {

    function __construct() {
        add_action( 'edit_form_top', array($this,'mycred_fortunewheel_step_meta'),0 );
        add_action('admin_menu', array($this,'mycred_fortunewheel_remove_auther_meta_box') );
        add_action('admin_footer', array($this,'mycred_fortunewheel_action_sidebar') );
    }

    function mycred_fortunewheel_action_sidebar() {

        if( get_post_type() != 'mycred_fortunewheels' )
            return;

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

        $html = '<div class="opt-action-sidebar">
                    <div class="opt-sidebar-cross"><span class="dashicons dashicons-no-alt"></span></div>
                    <div class="opt-post-details">
                    <div class="opt-act-btn publish"><input type="button" id="opt-publish" value="'. ( ( ( isset( $_GET['post'] ) && get_post_status( $_GET['post'] ) == 'draft' ) || !isset( $_GET['post'] ) ) ? 'PUBLISH' : 'UPDATE' ) .'"></div>
                    <div class="opt-act-btn draft"><input type="button" id="opt-draft" value="DRAFT"></div>';
                    if( isset( $_GET['post'] ) )
                        $html .= '<div class="opt-act-btn preview"><a href="'.get_home_url().'?fortunewheel_preview='.get_the_ID().'" target="_black" id="fortunewheel_preview_btn" class="button mycred_fortunewheel_preview_btn"">PREVIEW</a></div>';
                    else
                        $html .= '<div class="opt-act-btn draft-preview"><input type="button" id="opt-draft-preview" value="PREVIEW"></div>';

        if( isset($_GET['post']) && !empty($_GET['post']) ){

            $html .= '<div class="opt-shortcode-msg"><span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly"
                value="[fortunewheel wheel_id='.$_GET['post'].']"
                class="large-text code"></span>';

            $html .= '<p>1. Only single shortcode can be used on a page</p>';
            $html .= '<p>2. Slider will not work on pages where shortcode is being used</p>';
            $html .= '</div>';
            $html .= '</div>';
            // $html .= '<div class="fortunewheel-stats-wrapper">
            //             <div class="view-stats">View Stats</div>
            //         <div class="os-fortunewheel-count cl-orange">
            //             <div class="c100 p100 small">
            //                 <span>'.( $count_click == '' ? '0' : $count_click ).'</span>
            //                 <div class="slice">
            //                     <div class="bar"></div>
            //                     <div class="fill"></div>
            //                 </div>
            //             </div>
            //             <div class="os-num-label">NUMBER OF CLICKS</div>
            //         </div>
                    
            //         <div class="os-fortunewheel-count cl-blue">
            //             <div class="c100 p'.floor($avg_play).' small">
            //                 <span>'.( $count_play == '' ? '0' : $count_play ).'</span>
            //                 <div class="slice">
            //                     <div class="bar"></div>
            //                     <div class="fill"></div>
            //                 </div>
            //             </div>
            //             <div class="os-num-label">NUMBER OF fortunewheelS</div>
            //         </div>
                    
            //         <div class="os-fortunewheel-count cl-yellow">
            //             <div class="c100 p'.floor($avg_play).' small">
            //                 <span>'.( $avg_play == '' ? '0' : number_format((float)$avg_play , 1, '.', '')).'%</span>
            //                 <div class="slice">
            //                     <div class="bar"></div>
            //                     <div class="fill"></div>
            //                 </div>
            //             </div>                        
            //             <div class="os-num-label">fortunewheel RATE</div>
            //         </div>
                    
            //         <div class="os-fortunewheel-count cl-green">
            //             <div class="c100 p'.floor($win_percent).' small">
            //                 <span>'.$count_wins.'</span>
            //                 <div class="slice">
            //                     <div class="bar"></div>
            //                     <div class="fill"></div>
            //                 </div>
            //             </div>
            //             <!-- <div class="os-num-wins os-num-counter"></div> -->
            //             <div class="os-num-label">NUMBER OF WINS</div>
            //         </div>
                    
            //         <div class="os-fortunewheel-count cl-red">
            //             <div class="c100 p'.floor($loss_percent).' small">
            //                 <span>'.( $count_loss == '' ? '0' : $count_loss ).'</span>
            //                 <div class="slice">
            //                     <div class="bar"></div>
            //                     <div class="fill"></div>
            //                 </div>
            //             </div>
            //             <div class="os-num-label">NUMBER OF LOSS</div>
            //         </div>

            //    ';


        } else {

            $html .= '<div class="opt-shortcode-msg">';
            $html .= __( '<p class="sidebar-msg">Save wheel to get shortcode</p>','wpcs');
            $html .= '</div>';
        }

                $html .= '</div>';

        $html .= '<div class="opt-pull-settings"><span class="dashicons dashicons-admin-generic"></span></div>';

        echo $html;
    }

    function mycred_fortunewheel_remove_auther_meta_box() {
        remove_meta_box( 'authordiv','mycred_fortunewheels','normal' );
    }

    function mycred_fortunewheel_step_meta( $post ) {

        if( get_post_type() != 'mycred_fortunewheels' )
            return;

        echo '<div class="opt-steps">
            <div class="opt-step title active"><span>1</span> <div class="opt-text">Title</div></div>
            <div class="opt-step wheel-slices"><span>2</span> <div class="opt-text">Wheel Slices</div></div>
            <div class="opt-step display"><span>3</span> <div class="opt-text">Display Settings</div></div>
            <div class="opt-step ready"><span>4</span> <div class="opt-text">Ready <i class="fas fa-check-circle"></i></div></div>
            <div class="opt-step adv"><span>5</span> <div class="opt-text">Advanced Settings <i class="fas fa-cog"></i></div></div>';

            if( isset( $_GET['post'] ) ) {
                //echo '<div class="opt-step stats"><span>7</span> <div class="opt-text">View Stats <i class="fas fa-chart-pie"></i></div></div>';
            }

            echo '</div>';
    }

    function mycred_fortunewheel_setting_sections() {
        add_menu_page(
            __( 'fortunewheel Wheel', 'textdomain' ),
            __( 'Unsub Emails','textdomain' ),
            'manage_options',
            'wpdocs-unsub-email-list',
            'wpdocs_unsub_page_callback',
            ''
        );
    }

    function wpdocs_unsub_page_callback() {
        echo 'Unsubscribe Email List';
    }
}