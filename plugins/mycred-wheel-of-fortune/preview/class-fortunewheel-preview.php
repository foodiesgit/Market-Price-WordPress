<?php

add_action('init','fortunewheel_preview');

function mycred_fortunewheel_preview() {
    if( isset( $_GET['fortunewheel_preview'] ) ) {
        echo do_shortcode('[fortunewheel wheeL_id='.$_GET['fortunewheel_preview'].' slide=1]');
    }
}

//add_action( 'post_submitbox_misc_actions', 'fortunewheel_preview_btn' );
function mycred_fortunewheel_preview_btn() {
    if( isset( $_GET['post'] ) ) {
        $post_id = $_GET['post'];
        echo '<a href="'.get_home_url().'?fortunewheel_preview='.$post_id.'" style="float: right;margin: 10px;" id="fortunewheel_preview_btn" class="button mycred_fortunewheel_preview_btn"">Preview</a>';
    }
}

//add_action('admin_head','fortunewheel_hide_default_preview');

function mycred_fortunewheel_hide_default_preview() {
    ?>
    <style>
        a#post-preview {
            display: none;
        }
    </style>
    <?php
}

add_action('wp_head','fortunewheel_trigger_wheel_preview');

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