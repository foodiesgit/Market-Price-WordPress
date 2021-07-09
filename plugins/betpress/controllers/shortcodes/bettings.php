<?php

// don't allow direct access via url
if (! defined('ABSPATH')) {
    exit();
}

function betpress_bettings_front_controller($atts)
{
    // set default attributes
    $attributes = shortcode_atts(array(
        'sport' => betpress_VALUE_ALL,
        'event' => 0,		
		'rows' => 20,
        'time'  => betpress_VALUE_ALL,
        'toggle' => 1
    ), $atts);
    ob_start();

    $active_events = false;
	$total_active_events=0;
    $data = array();
    if (betpress_VALUE_ALL == $attributes['sport']) {

		if(isset($attributes['event']) && $attributes['event']!="0"){
			$event_array = @explode(",", $attributes['event']);			
		}
        
		if(isset($attributes['event']) && sizeof($event_array)>0){ 
			$same_sports = 0;
			foreach ($event_array as $k=>$event_name) {
				$sport = betpress_get_event_by_name(trim($event_name));
				$same_sports = 1;
				$data1[$sport['sport_id']] = betpress_get_sport_data($sport, get_option('bp_close_bets'),true);
				if ($data1[$sport['sport_id']]['count_active_events'] > 0) {
					$active_events = true;
					$total_active_events = $total_active_events+$data1[$sport['sport_id']]['count_active_events'];
					
					$data[$k] = $data1;
					$data[$k]["sport_id"] = $sport['sport_id'];
					$data[$k]["same_sports"] = $same_sports;
				}				
			}
		} else { 
            $sports = betpress_get_sports();
            foreach ($sports as $sport) {
                $data[$sport['sport_id']] = betpress_get_sport_data($sport, get_option('bp_close_bets'));

                if ($data[$sport['sport_id']]['count_active_events'] > 0) {
                    $active_events = true;
					$total_active_events = $total_active_events+$data[$sport['sport_id']]['count_active_events'];
                }
            }
        }
    } else { 
        if($attributes['time'] == betpress_VALUE_ALL){ 
            $sport = betpress_get_sport_by_name($attributes['sport']);
            $data[$sport['sport_id']] = betpress_get_sport_data($sport, get_option('bp_close_bets'));

            if ($data[$sport['sport_id']]['count_active_events'] > 0) {
                $active_events = true;
				$total_active_events = $total_active_events+$data[$sport['sport_id']]['count_active_events'];
            }
        }else{
			
			if(isset($attributes['time']) && $attributes['time']!=betpress_VALUE_ALL){
				$time_array = @explode(",", $attributes['time']);
			}
			
			if(isset($attributes['time']) && sizeof($time_array)>0){
				$same_sports = 0;
				date_default_timezone_set('America/Sao_Paulo');
				//$more = 86400;
				foreach ($time_array as $k=>$time) {					
					switch ($time){
						case 'Today':
							$startDate = date(betpress_TIME_NO_ZONE,strtotime('today'));
							//echo $startDate;
						   // $startDate = date('d-m-Y',strtotime('today')). ' 00:00';
							$endDate = date('d-m-Y',strtotime('today')). ' 23:59';
							break;							
					}
					$sport = betpress_get_sport_by_name($attributes['sport']);
					$sport_id=$sport['sport_id'];
					$sport_name=$sport['sport_name'];
					$sport_sort_order=$sport['sport_sort_order'];
            		$data1 = betpress_get_sport_data_by_time($sport, get_option('bp_close_bets'), $startDate, $endDate);
					if ($data1['count_active_events'] > 0) {
						$active_events = true;
						$total_active_events = $total_active_events+$data1['count_active_events'];
						$data2[$k] = $data1;
					}
				}
				$count = count($data2);
				$result = array();
				foreach($data2 as $v=>$data3){
					$result = array_merge($result,$data2[$v]["events"]);
				} 				
				$data[$sport_id]["sport_id"] = $sport_id;
				$data[$sport_id]["sport_sort_order"] = $sport_sort_order;
				$data[$sport_id]["sport_name"] = $sport_name;
				$data[$sport_id]["events"] = $result;
				$data[$sport_id]["count_active_events"] = $total_active_events;				
			}            
        }
    }
    $pass['sports'] = $data;
	$pass['sport_title_bg'] = get_option('bp_sport_title_bg_color');
    $pass['sport_title_text'] = get_option('bp_sport_title_text_color');
    $pass['sport_container_bg'] = get_option('bp_sport_container_bg_color');
    $pass['event_title_bg'] = get_option('bp_event_title_bg_color');
    $pass['event_title_text'] = get_option('bp_event_title_text_color');
    $pass['event_container_bg'] = get_option('bp_event_container_bg_color');
    $pass['bet_event_title_bg'] = get_option('bp_bet_event_title_bg_color');
    $pass['bet_event_title_text'] = get_option('bp_bet_event_title_text_color');
    $pass['cat_title_bg'] = get_option('bp_cat_title_bg_color');
    $pass['cat_title_text'] = get_option('bp_cat_title_text_color');
    $pass['cat_container_bg'] = get_option('bp_cat_container_bg_color');
    $pass['button_bg'] = get_option('bp_button_bg_color');
    $pass['button_text'] = get_option('bp_button_text_color');
    $pass['min_children_to_show_toggle'] = (int) $attributes['toggle'];
    $pass['active_bettings'] = $active_events;
	$page = isset($_GET['sp_page']) ? intval(betpress_sanitize($_GET['sp_page'])) : 1;
    $limit = intval(betpress_sanitize($attributes['rows']));
    $query_start = ($page - 1) * $limit;

    //$total_rows = count(betpress_get_events()) ;
	$total_rows = $total_active_events;
	$last_page = intval($total_rows / $limit);
	
    if ( (count($_GET) === 0) || ( (count($_GET) === 1) && (isset($_GET['sp_page'])) ) ) {
        $pass['symbol'] = '?';
    } else {
        $pass['symbol'] = '&amp;';
    }
	
	$pass['page_url'] = betpress_get_url(array('sp_page'));
    $pass['current_page'] = $page;
    $pass['next_page'] = $page + 1;
    $pass['previous_page'] = $page - 1;
	$pass['totrow'] = $total_rows;
	$pass['limit'] = $limit * $page;
	$pass['start'] = $query_start;
    $pass['last_page'] = $total_rows % $limit === 0 ? $last_page : $last_page + 1;
	
    betpress_get_view('bettings', 'shortcodes', $pass);

    return ob_get_clean();
}

add_shortcode('betpress_bettings', 'betpress_bettings_front_controller');