<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

function betpress_add_bet_option() {

    //codex says i MUST do this if
    if (is_admin() === true) {

        $db_errors = false;

        $bet_option_ID = (int) betpress_sanitize($_POST['bet_option_id']);

        $bet_option_errors = array();

        $user_ID = get_current_user_id();
        
        if (0 === $user_ID) {
            
            $bet_option_errors [] = __('Please login or register.', 'BetPress');
            
        }

		 if ( ! empty($bet_option_errors) ) {

            foreach ($bet_option_errors as $err) {

                $pass['error_message'] = $err;
                betpress_get_view('error-message', '', $pass);
            }
            
        } else {
		
		
			 $data = array(
				 "bet_option_id" => $_POST['bet_option_id'],
				 "user_id" => get_current_user_id(),
				 "bet_event_name" => $_POST['event'],
				 "bet_event_cat"  => $_POST['match'],
				 "bet_option_name" => $_POST['bet'],
				 "bet_option_odd" => $_POST['price'],
				 "bet_event_id"	  => $_POST['event_id']	
			 );
// 			 betpress_insert("slips_test",$data);
// 			 print_r($_POST);
		
		?>
		
		   <div class="bet-option-slip-wrapper">

        <div class="slip-bet-event-name">

            <?php echo $_POST['event']; ?>

        </div>

        <div class="slip-delete-option">

          

        </div>

        <div class="clear"></div>

        <div class="slip-bet-cat-name">

           <?php echo $_POST['match']; ?>

        </div>



        <div class="slip-bet-odd" id="">

            <?php esc_attr_e('Odd: ', 'betpress'); ?> <?php echo $_POST['price']; ?>
           

        </div>
        <div class="slip-bet-name">

           <?php echo $_POST['bet']; ?>

        </div>

        <div class="clear"></div>


    </div>
	<?php 
		
		
		
		}
		
		
    }

    //codex says i MUST use wp_die
    wp_die();
}

add_action('wp_ajax_add_bet_option', 'betpress_add_bet_option');
add_action('wp_ajax_nopriv_add_bet_option', 'betpress_add_bet_option');