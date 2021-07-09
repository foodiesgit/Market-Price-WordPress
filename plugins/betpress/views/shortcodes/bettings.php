<?php

//don't allow direct access via url
if (!defined('ABSPATH')) {
    exit();
}
?>

<?php if ($active_bettings): ?>
<?php
global $wpdb;
$user_ID = get_current_user_id();
$check = $wpdb->get_results('SELECT bet_options_ids FROM ' . $wpdb->prefix . 'bp_slips where status = "unsubmitted" AND user_id = ' . $user_ID);
$str_val = $check[0]->bet_options_ids;
$timezone = get_user_meta($user_ID, 'gmt_offset', true);
?>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
#loading-image {
	background-color: #404040!important;
    z-index: 999;
    position: fixed;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
	display: none;
	border-radius: 5px;
}

.content1234 {
	display:none;
	overflow: hidden;
}

.w3-container, .w3-panel {
    padding: 0.01em 2px !important;
	margin-top: 10px !important;
}

@media screen and (max-width: 481px ) {
.w3-container, .w3-panel {
margin-top: -2px !important;
}
}
</style>
<script>

   //this will start from the current element and get all of the next siblings test123

function getNextSiblings(elem, filter) {
    var sibs = [];
    while (elem = elem.nextSibling) {
        if (elem.nodeType === 3) continue; // text node
        if (!filter || filter(elem)) sibs.push(elem);
    }
    return sibs;
}

	function ttgloe(gk, bet_event_id){
    jQuery('#loading-image').show();
    jQuery.post(
      ajaxurl,
      {
        action: 'get_betevent',
        bet_event_id: bet_event_id,
        gk: gk
      },
      function(response) {
        jQuery('#ajax' + gk).html(response);
        // console.log(response);
        if (document.getElementById('model'+gk).style.display === "block") {
          document.getElementById('model'+gk).style.display = "none";
        } else {
          document.getElementById('model'+gk).style.display = "block";
        }
        
        jQuery('#loading-image').hide();
		}
    );

  }
</script>
<div id="loading-image">
<img src="https://res.cloudinary.com/bethillary/image/upload/v1610242790/generic-spinner-48px_oftgss.gif" />
</div>
<div class="bettings-wrapper">	
  <?php 
	$i = 0;
	foreach ($sports as $k=>$sport): 
	if($i < $limit) {
		
		if ($sports[$k]["same_sports"]==1) {
			$sport = $sports[$k][$sports[$k]["sport_id"]];
		}
	?>
<?php if ($sport['count_active_events'] > 0): ?>
<div class="sport-wrapper">
<div class="sport-container">
      <?php foreach ($sport['events'] as $event): ?>
      <?php if ($event['count_active_bet_events'] > 0): ?>

	  <?php $i = $i + 1;
        if ($i <= $limit and $i > $start) { ?>
	
<div class="event-wrapper">
     <span class="event-title-bar"> <?php echo apply_filters('wpml_translate_single_string', $event['event_name'], 'betpress', 'event-' . $event['event_name']); ?> </span>
  
<div class="event-container">
          <?php foreach ($event ['bet_events'] as $bet_event): ?>
          <?php if ( ($bet_event['is_active']) && (count($bet_event['categories']) > 0) ): ?>
<div class="bet-event-wrapper">
<div class="bethomeaway">
<span class="bet-event-home"><?php echo mb_strimwidth($bet_event['team_home'], 0, 25, "..."); ?></span>	
<span class="bet-event-away"><?php echo mb_strimwidth($bet_event['team_away'], 0, 25, "..."); ?></span>
<span class="title-deadline"> <?php echo betpress_local_tz_time($bet_event['deadline']); ?></span>	
</div>
<span class="bet-event-title-bar"><?php echo ($bet_event['bet_event_name']); ?> <span class="title-deadline"> <?php echo betpress_local_tz_time($bet_event['deadline']); ?>  <?php echo ($bet_event['tv']); ?>
 <img style="display: inline!important;margin-op:-2px!important;" src="https://res.cloudinary.com/bethillary/image/upload/v1610745851/exv8_Feature_StreamingV2_pena83.png" width="20" height="20" /> </span>
</span>
<div class="bet-event-container" style="background-color: <?php echo $bet_event_container_bg; ?>"> <?php $counter=1; $countcategories=count($bet_event['categories']); foreach ($bet_event ['categories'] as $kys=>$category): $rtg1234 = apply_filters('wpml_translate_single_string', $category['bet_event_cat_name'], 'betpress', 'cat-' . $category['bet_event_cat_name']); $zxsp=array_keys($bet_event ['categories'])[0]; ?>

	<?php if ($counter == 2) {?>
    <div id="model<?php echo $zxsp; ?>" class="w3-modal">
   <?php
    echo $str;
            } else {

                $str = '<div class="cat-wrapper" >';
                $str .= '<div class="cat-title-bar" style="background-color: ' . $cat_title_bg . '; color: ' . $cat_title_text . '">';
                $str .= $rtg1234;

                $str .= '</div>';
                $str .= '<div class="cat-container" style="background-color: ' . $cat_container_bg . '">';

                $color_new = $button_bg;
                foreach ($category['bet_options'] as $bet_option):

                    $color_new = '';
                    $active_ = '';
                    if (stripos($str_val, 'i:' . $bet_option['bet_option_id'] . ';')) {
                        $color_new = '#7c98a6 !important';
                        $active_ = 'active';
                    } else {
                        $color_new = $button_bg;
                        $active_ = '';
                    }

                    $str .= '<div class="bet-option-wrapper bet-option-btn-' . $bet_option['bet_option_id'] . '" id="bet-option-btn-' . $bet_option['bet_option_id'] . '" style="width: ' . $category['css-width'] . '%; margin-left: ' . $category['css-margin_left'] . '%; background-color: ' . $color_new . '; color: ' . $button_text . '">';
                    $str .= '<div class="bet-option-title" style="color:#f2f2f2 !important;"> ' . apply_filters('wpml_translate_single_string', $bet_option['bet_option_name'], 'betpress', 'bet-option-' . $bet_option['bet_option_name']) . '</div>';
                    $str .= '<div class="bet-option-odd">' . $bet_option['bet_option_odd'] . '</div>';
                    $str .= '</div>';
                endforeach;
                $str .= '<div class="clear"></div>';
                $str .= '</div></div>';
            }
            ?>

<div class="cat-wrapper" >
<div class="cat-title-bar" style="background-color: <?php echo $cat_title_bg; ?>; color: <?php echo $cat_title_text; ?>">
	<?php
    echo $rtg1234;
            ?>
</div>
<div id="feat" class="cat-container" style="background-color: <?php echo $cat_container_bg; ?>">
     <?php $color_new = $button_bg;foreach ($category['bet_options'] as $bet_option): ?>
	 <?php
    $color_new = '';
            $active_ = '';
            if (stripos($str_val, 'i:' . $bet_option['bet_option_id'] . ';')) {
                $color_new = '#7c98a6 !important';
                $active_ = 'active';
            } else {
                $color_new = $button_bg;
                $active_ = '';
            }
            ?>
<div class="bet-option-wrapper bet-option-btn-<?php echo $bet_option['bet_option_id'] ?>" id="bet-option-btn-<?php echo $bet_option['bet_option_id'] ?>" style="width: <?php echo $category['css-width']; ?>%; margin-left: <?php echo $category['css-margin_left']; ?>%; background-color: <?php echo $color_new; ?>; color: <?php echo $button_text; ?>">

<?php if($bet_option['market'] == 'home'): ?>	
<div class="bet-option-title"> <?php echo "Casa" ?> </div>	
<?php endif; ?>
	
<?php if($bet_option['market'] == 'draw'): ?>	
<div class="bet-option-title"> <?php echo "Empate" ?> </div>	
<?php endif; ?>
	
<?php if($bet_option['market'] == 'away'): ?>	
<div class="bet-option-title"> <?php echo "Fora" ?> </div>	
<?php endif; ?>
	
<?php if($bet_option['market'] == '1market'): ?>	
<div class="bet-option-title"> <?php echo "Time da Casa" ?> </div>	
<?php endif; ?>
	
<?php if($bet_option['market'] == '2market'): ?>	
<div class="bet-option-title"> <?php echo "Time Visitante" ?> </div>	
<?php endif; ?>	
	
<?php if($bet_option['market'] == 'double1'): ?>	
<div class="bet-option-title"> <?php echo "1X" ?> </div>	
<?php endif; ?>
	
<?php if($bet_option['market'] == 'double2'): ?>	
<div class="bet-option-title"> <?php echo "X2" ?> </div>	
<?php endif; ?>
	
<?php if($bet_option['market'] == 'double3'): ?>	
<div class="bet-option-title"> <?php echo "12" ?> </div>	
<?php endif; ?>	
	
<?php if(($bet_option['market'] != 'home') && ($bet_option['market'] != 'draw') && ($bet_option['market'] != 'away') && ($bet_option['market'] != '1market') && ($bet_option['market'] != '2market') && ($bet_option['market'] != 'double1') && ($bet_option['market'] != 'double2') && ($bet_option['market'] != 'double3')) : ?>	
<div class="bet-option-title"> <?php echo apply_filters('wpml_translate_single_string', $bet_option['bet_option_name'], 'betpress', 'bet-option-' . $bet_option['bet_option_name']); ?> </div>	
<?php endif; ?>	
	
<div class="bet-option-odd"><?php echo apply_filters('betpress_odd', $bet_option['bet_option_odd']); ?></div>
			</div>

            <?php endforeach;?>
                  <div class="clear"></div>
                  <div id="ajax<?php echo $zxsp; ?>"></div>
                  <?php
if ($kys == $zxsp) {
            ?>
             <button class="collapsible1234" onclick="ttgloe(<?php echo $zxsp; ?>, <?php echo $bet_event['bet_event_id']; ?>);" style="height:22px!important;width:22px!important; float:right;padding:0px;background:#404040;margin-right:10px;border-radius: 4px;text-align:center;">+</button>
                  <?php
}
        ?>
                </div>
              </div>
               <?php if (($counter == $countcategories) && ($counter > 1)) {echo '</div></div></div></div>';}?>
              <?php $counter++;endforeach;?>
            </div>
          </div>
          <?php endif;?>
          <?php endforeach;?>
        </div>
      </div>
      <?php }
    endif;?>
      <?php endforeach;?>
    </div>
  </div>
  <?php endif;?>
  <?php }
endforeach;?>
</div>
<div class="paginator" style="margin-top: 8px !important;">
<div class="page-button" style="padding: 6px !important;">
     Pág. <?php echo $current_page ?>
    </div>
<?php if ($current_page > 1): ?>	
<div class="page-button1">	
<a <?php if ($previous_page > 0): ?> href="<?php echo $page_url . $symbol ?>sp_page=<?php echo $previous_page ?>" <?php endif; ?>>
   <?php esc_attr_e('Retornar', 'betpress') ?>
   </a>
</div>
<?php endif; ?>	
<div class="page-button"></div>
<?php if ($current_page < $last_page): ?>	
<div class="page-button2">
<a <?php if ($next_page <= $last_page): ?>href="<?php echo $page_url . $symbol ?>sp_page=<?php echo $next_page ?>" <?php endif; ?>>
            <?php esc_attr_e('Ver Mais', 'betpress') ?>
        </a>
    </div>
<?php endif; ?>	
</div>		  
<?php else: ?>
<div class="noevsfy">
  <?php esc_attr_e('Nenhuma partida disponível para data de hoje', 'betpress'); ?>
</div>
<?php endif; ?>