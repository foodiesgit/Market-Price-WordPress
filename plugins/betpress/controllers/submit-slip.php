<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

function betpress_submit_bet_slip() {

    //codex says i MUST do this if
    if (is_admin() === true) {
        
        $user_ID = get_current_user_id();
        
        $users_db_points = get_user_meta($user_ID, 'bp_points', true);
        
        $users_points = $users_db_points === '' ? get_option('bp_starting_points') : $users_db_points;
        
        $errors = array();
        
        $bet_stake = betpress_sanitize($_POST['bet_stake']);
        
        $min_stake = get_option('bp_min_stake');
        
        $max_stake = get_option('bp_max_stake');
        
            if (get_option('bp_only_int_stakes') === BETPRESS_VALUE_YES) {

                if ($bet_stake - intval($bet_stake) !== 0) {

                    $errors [] = __('Stake must be whole number.', 'BetPress');
                }
            }

            if ($users_points < $bet_stake) {

                $errors [] = __('You don\'t have enough points.', 'BetPress');
            }

            if ($bet_stake < $min_stake) {

                $errors [] = sprintf(__('Minimum allowed stake is %s', 'BetPress'), $min_stake);
                
            } else {

                $min_stake_ok = true;
            }

            if ($bet_stake > $max_stake) {

                $errors [] = sprintf(__('Maximum allowed stake is %s', 'BetPress'), $max_stake);
            }

            if ($bet_stake <= 0 && isset($min_stake_ok)) {

                $errors [] = __('The stake must be greater than zero.', 'BetPress');
            }
        }
        
        if ($errors) {
            
            //show errors
            foreach($errors as $error) {
                
                $pass['error_message'] = $error;
                betpress_get_view('error-message', '', $pass);
            }

            //take bet options and make them array
            $bet_option_ids = unserialize($slip['bet_options_ids']);

            //show the slip
            if ($slip) {
                betpress_render_bet_options($bet_option_ids);
            }
            
        } else {
            
            //calculate possible winnings
            $possible_winnings = $bet_stake;
            
            $bet_options_ids = unserialize($slip['bet_options_ids']);
            
            foreach ($bet_options_ids as $bet_option_ID => $bet_option_odd) {
                
                $possible_winnings *= $bet_option_odd;
            }
            
            $possible_winnings_rounded = betpress_floordec($possible_winnings);
                        
            //calculate new points
            $bet_stake_rounded = betpress_floordec($bet_stake);
            $updated_points = $users_points - $bet_stake_rounded;
            
            //update the points
            update_user_meta($user_ID, 'bp_points', (string)$updated_points);
            
            //make sure its updated
            if (strcmp(get_user_meta($user_ID, 'bp_points', true), (string)$updated_points) !== 0) {
                wp_die('DB error.1');
            }
            
            $active_leaderboard = betpress_get_active_leaderboard();
			
			
			//get the selected bets
			if(isset($_POST['bet_selected']))
			{
				//insert the selected bets
				//generate a slip_id for the bets
				$slip_id = uniqid();
				$bets = $_POST['bet_selected'];
				
				$inserted = 0;
				//calculate winnings
				$winnings = floor($_POST['bet_stake']) ;
				foreach($bets as $key => $value)
				{
					$winnings *= floatval($value['price']);
					$wins = number_format($winnings, 2, '.', ',');
				}
				
				foreach($bets as $key => $value)
				{
					$data = array(
						 	"bet_option_id" => $value['bet_option_id'],
						 	"slip_id" 	=> $slip_id,
							"stake" => $_POST['bet_stake'],
						 	"user_id" => get_current_user_id(),
						 	"bet_event_name" => $value['event'],
						 	"bet_event_cat"  => $value['match'],
						 	"bet_option_name" => $value['bet_name'],
							"bet"			=> $value['bet'],	
							"event_id"		=> $value['event_id'],	
						 	"bet_option_odd" => $value['price'],
							"winnings"		=> $wins			
					 );
// 					print_r($data);
					$inserted = betpress_insert("slips_test",$data);
				}
// 				print_r($_POST);
				
				if($inserted != 0)
				{
					 $pass['success_message'] = esc_attr__('Your bet slip has been submitted.', 'BetPress');
                	 betpress_get_view('success-message', '', $pass);
				}else{
					 wp_die('DB error.');
				}
			}
            

        }
    //codex says to use wp_die in the end
    wp_die();
}

add_action('wp_ajax_submit_bet_slip', 'betpress_submit_bet_slip');
add_action('wp_ajax_nopriv_submit_bet_slip', 'betpress_submit_bet_slip');