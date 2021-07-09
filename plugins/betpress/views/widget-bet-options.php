<?php

    //don't allow direct access via url
    if ( ! defined('ABSPATH') ) {
        exit();
    }

    // make ids & class
    $cat_name = $bet_option_info['bet_event_cat_name'];

    $cat_name = str_replace(' ', '-', $cat_name); // Replaces all spaces with hyphens.
   
    $cat_name = preg_replace('/[^A-Za-z0-9\-]/', '', $cat_name); // Removes special chars.


    $ids = $bet_option_info['bet_option_name'];

    $ids = str_replace(' ', '-', $ids); // Replaces all spaces with hyphens.

    $ids = preg_replace('/[^A-Za-z0-9\-]/', '', $ids); // Removes special chars.


    $event_name = $bet_option_info['bet_event_name'];

    $event_name = str_replace(' ', '-', $event_name); // Replaces all spaces with hyphens.

    $event_name = preg_replace('/[^A-Za-z0-9\-]/', '', $event_name); // Removes special chars.


    $bet_option_odd = $bet_option_info['bet_option_odd'];

    $bet_option_odd = str_replace(' ', '-', $bet_option_odd); // Replaces all spaces with hyphens.

    $bet_option_odd = preg_replace('/[^A-Za-z0-9\-]/', '', $bet_option_odd); // Removes special chars.

	$min_stake = get_option('bp_min_stake');
        
    $max_stake = get_option('bp_max_stake');
    $user_ID = get_current_user_id();
   // $currency = get_user_meta($user_ID, 'currency', true);
   // $wl = get_user_meta($user_ID, 'betpress_lang', true);
    //$wl0 = isset($_COOKIE['betpress_lang_type']) ? betpress_sanitize($_COOKIE['betpress_lang_type']) : get_option('bp_default_lang_type');    
    
        $users_db_points = get_user_meta($user_ID, 'bp_points', true);
        //$users_db_points = get_user_meta($user_ID, 'mycred_eur', true);	
        
        $users_points = $users_db_points === '' ? get_option('bp_starting_points') : $users_db_points;
    ?>

    <div class="bet-option-slip-wrapper">

        <div class="slip-bet-event-name">

            <?php echo apply_filters('wpml_translate_single_string', $bet_option_info['bet_event_name'], 'betpress', 'bet-event-' . $bet_option_info['bet_event_name']); ?>
			

        </div>

        <div class="slip-delete-option">

            <a href="#" id="delete-<?php echo $bet_option_info['bet_option_id']; ?>" class="delete-bet-option" data="<?php echo 'odd_' . $event_name .'_'. $cat_name .'_'. $ids;?>">

                <img src="<?php echo betpress_IMAGE_FOLDER . 'delete-16.png'; ?>" alt="<?php esc_attr_e('Delete', 'betpress'); ?>" />

            </a>

        </div>

        <div class="clear"></div>

        <div class="slip-bet-cat-name">

            <?php echo apply_filters('wpml_translate_single_string', $bet_option_info['bet_event_cat_name'], 'betpress', 'cat-' . $bet_option_info['bet_event_cat_name']); ?>

        </div>



        <div class="slip-bet-odd" id="<?php echo 'odd_' . $event_name .'_'. $cat_name .'_'. $ids;?>">

            <?php esc_attr_e('Odd: ', 'betpress'); ?><?php echo $bet_option_info['bet_option_odd']; ?>
           

        </div>
        <div class="slip-bet-name">

            <?php echo apply_filters('wpml_translate_single_string', $bet_option_info['bet_option_name'], 'betpress', 'bet-option-' . $bet_option_info['bet_option_name']); ?>

        </div>

        <div class="clear"></div>


    </div>

    <script>

		function dublicate_odds(){
			//don't dublicate odds
			if(odds.indexOf("<?php echo 'odd_' . $event_name .'_'. $cat_name .'_'. $ids;?>") == -1){
			  //  add once
				odds.push("<?php echo 'odd_' . $event_name .'_'. $cat_name .'_'. $ids;?>");

			}else{
				//do nothing
			}
		}
        dublicate_odds();

    setTimeout(function(){

    // fire this on load
    do_calculation();

    // fire this on keyup (from keyboard)
        jQuery('#stake-input').bind('keyup',function(){

            // do all calulaltion
            do_calculation();


        });

    // fire this on change(clicking up dowm arrow from mouse)
        jQuery('#stake-input').change(function(){

            // do all calulaltion
            do_calculation();


        });


    },0.1);

    // Multiply all odds

    function do_calculation(){ 
        if(odds.length != 0 ){ 
            var final_odds = [];

            jQuery('#pw').html('');

            for(var i=0;i<odds.length;i++){
               
                var get_odds_value = jQuery('#'+odds[i]).html();
                if (typeof get_odds_value != "undefined") {
					var split_odds = get_odds_value.split(':');
					final_odds.push(split_odds[1]);
				}

            }
            //fire this after once to prevent undefined errors
            if(final_odds.length >0){
             setTimeout(function(){
                const reducer = (accumulator, currentValue) => accumulator * currentValue;
                var odd_value = final_odds.reduce(reducer);
                var stake_val = jQuery('#stake-input').val();
                put_value(odd_value * stake_val);

             },0.1);
			}
        }
    }

    // show possible winnings
    function put_value(result){

        setTimeout(function(){
            jQuery('#pw').html(result.toFixed(2));
        },0.1);

    }

       // on delete event
    jQuery('#delete-<?php echo $bet_option_info["bet_option_id"]; ?>').click(function(){
    
       var del_id = jQuery('#delete-<?php echo $bet_option_info["bet_option_id"]; ?>').attr('data');
       var del_index = odds.indexOf(del_id);
       if( del_index > -1 ){ 
            //remove deleted from odds array
            odds.splice(del_index,1);

            

            if(odds.length == 0){
               

                jQuery('#pw').html('0.00');
              
               
            }


            //caculate again 
            do_calculation();
    
       }
    
    
       
    });

jQuery('#submit-slip-button').click(function(){

		var stake = jQuery('#stake-input').val();
		var user_point = <?php echo $users_points ?>;

        if(stake >= <?php echo $min_stake?> && stake <= <?php echo $max_stake?> && user_point >= stake){
		   jQuery('.bet-option-wrapper').removeClass('active');
		}
		
		jQuery('#pw').html('0.00');

        odds = [];
        do_zero();


    });

    //made possible winning zero after submitting the slip 
    function do_zero(){
        jQuery('#pw').html('');
        setTimeout(function(){ 
            jQuery('#pw').html('0.00');
        },0.2);
    }
    var prevent_int = 0;
    // delete all bet options after some min
    setTimeout(function(){
        //var to prevent errors 
        
        //delete all bet option
        jQuery('.clear_all_bet_option').on('click',function (event) {
            if(prevent_int == 0){
                   // alert('hie');
                    event.preventDefault();
                    jQuery('.bet-option-wrapper').removeClass('active');
                    jQuery('.bets-holder').html(i18n_front.loading);
                    odds = [];
                    jQuery('#pw').html('0.00');
                    jQuery.post(ajaxurl, {
                        
                        action: 'delete_all_bet_option',
                        del_all: 1
                        
                        
                    }, function (response) {
                        odds = [];
                        jQuery('#pw').html('0.00');
                        jQuery('.bets-holder').html("Clique em um preço para adicionar uma seleção");
                       
                    });
               
            }  prevent_int = 1;
        });

    },0.3);


    </script>