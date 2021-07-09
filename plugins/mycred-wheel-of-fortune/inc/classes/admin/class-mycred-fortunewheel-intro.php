<?php

Class myCred_fortunewheel_Intro {

    public function __construct() {
        add_action( 'admin_menu', array($this,'mycred_fortunewheelspon_intro') );
    }

    public function mycred_fortunewheelspon_intro(){
        add_menu_page( 
            __( 'Intro', 'textdomain' ),
            'Intro',
            'manage_options',
            'mycred_fortunewheel_into',
            array($this,'mycred_fortunewheelspon_intro_callback'),
            '',
            6
        ); 
    }

    public function mycred_fortunewheelspon_intro_callback() {
        $html = '<div class="mycred-fortunewheel-wrapper">
                    <div class="mycred-fortunewheel-head">myCred <span>WHEEL OF FORTUNE</span></div>
                    
                    <div class="mycred-fortunewheel-box-wrapper">

                        <div class="mycred-boxes">
                            <div class="mycred-fortunewheel-box">
                                <div class="mycred-fortunewheel-icon"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/feature-image-sell-content.png" /></div>
                                    <div class="mycred-fortunewheel-title">Set Your Own Slices</div>
                                    <div class="mycred-fortunewheel-short-desc">
                                    There are two types of wheel slices (1. No Prize 2. Win Prize) . You have the power to set  unlimited slices of your wheel with the priority based which mean you can easily control which slice you would like to stop most.
                                    </div>
                                                        
                            </div>

                            <div class="mycred-fortunewheel-box">
                                <div class="mycred-fortunewheel-icon"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/mycred-vote-1544x500.png" /></div>
                                    <div class="mycred-fortunewheel-title">Full Control Over Layout</div>
                                    <div class="mycred-fortunewheel-short-desc">
                                    You have full control over the layout of myCred Fortune Wheel. You can easily customize the colors labels and everything which you like.
                                    </div>
                                                        
                            </div>

                            <div class="mycred-fortunewheel-box">
                                <div class="mycred-fortunewheel-icon"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/transfer-plus-1544x772.png" /></div>
                                    <div class="mycred-fortunewheel-title">Offer Free Spin</div>
                                    <div class="mycred-fortunewheel-short-desc">
                                    myCred fortune wheel has the ability to offer free spins for your customers. You can set the free spin limit from the wheel settings.
                                    </div>
                                                        
                            </div>

                            <div class="mycred-fortunewheel-box">
                                <div class="mycred-fortunewheel-icon"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/feature-image-sell-content.png" /></div>
                                    <div class="mycred-fortunewheel-title">Buy Spin With Points</div>
                                    <div class="mycred-fortunewheel-short-desc">
                                    Buy Spin with Points is an amazing feature for your customers. Once the user has reached their free spin limit and if they still wants to play fortune wheel so they have option to pay some points in order to play it again.
                                    </div>
                                                        
                            </div>

                            <div class="mycred-fortunewheel-box">
                                <div class="mycred-fortunewheel-icon"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/mycred-vote-1544x500.png" /></div>
                                    <div class="mycred-fortunewheel-title">Shortcode Support</div>
                                    <div class="mycred-fortunewheel-short-desc">
                                        You can place a fortunewheel shortcode in a page to display wheel on any place of the page.
                                    </div>
                                                        
                            </div>

                            <div class="mycred-fortunewheel-box">
                                <div class="mycred-fortunewheel-icon"><img src="'.mycred_fortunewheel_PLUGIN_URL.'assets/img/transfer-plus-1544x772.png" /></div>
                                    <div class="mycred-fortunewheel-title">Limit To Access Wheel</div>
                                    <div class="mycred-fortunewheel-short-desc">
                                    myCred Fortune wheel has the option to set on which page you would like to appear on your site. You can set all pages or a specific page.
                                    </div>
                                                        
                            </div>
                            
                        </div>
                    </div>

                    <div class="mycred-fortunewheel-explain">
                        <div class="mycred-fortunewheel-sample">
                            <img src="https://mycred.me/wp-content/uploads/2013/11/mycred-member-ranks.png"/>
                        </div>
                        <div class="mycred-fortunewheel-sample-para">
                        Gamify your WordPress website by offering lotteries for points!
                        </div>
                    </div>

                    <div class="mycred-forunewheel-btn-wrapper">
                        <a href="'.get_admin_url().'post-new.php?post_type=mycred_fortunewheels" class="mycred-forunewheel-btn">Start Creating Your First Wheel</a>
                    </div>
                </div>';

        echo $html;
    }
}