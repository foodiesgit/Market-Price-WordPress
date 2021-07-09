<?php

//don't allow direct access via url
if (!defined('ABSPATH')) {
    exit();
}

function betpress_get_betevent() {
    $bet_event_id = isset($_POST['bet_event_id']) ? $_POST['bet_event_id'] : "N/A";
    $gk = isset($_POST['gk']) ? $_POST['gk'] : "N/A";
    $bet_event = betpress_get_bet_event($bet_event_id);

    $html = '<div id="model'.$gk.'" class="w3-modal">
<div id="newDiv" class="w3-modal-content w3-animate-zoom">
  <div class="w3-container">
    <header class="w3-container" style="background-color:#494949; color:white;padding-bottom:6px!important;">
    <span onclick="document.getElementById(\'model'.$gk.'\').style.display=\'none\'" class="w3-button w3-display-topright">Voltar</span> <h2 style="width:90%!important;color:#ddd !important; font-size: 14px !important; font-weight: 700; padding-left: 5px;margin-bottom: 5px !important;font-family: Verdana !important;"> <span class="pophome">'.mb_strimwidth($bet_event['team_home'], 0, 45, "...").'</span> <span style="display:block!important;padding-top:10px!important;">'.mb_strimwidth($bet_event['team_away'], 0, 45, "...").'</span> </h2> <span style="margin-left: 5px !important;font-size: 11px !important;color: #bbb !important;">'.betpress_local_tz_time($bet_event['deadline']).' </span>
    </header>
<div class="cmspop" style="height:500px; overflow-y:scroll;overflow-x:hidden!important;">';
	// aqui deverá ser adicionado _all //
    $bet_cats = betpress_get_categories($bet_event_id);
	//print_r($bet_cats);
	$temp_cat_names=array();
    foreach ($bet_cats as $cat_name) {
		$custom_cat_name = $cat_name['bet_event_cat_name'];
		 
        $bet_options = betpress_get_bet_options($cat_name['bet_event_cat_id']);
		
		$user_ID = get_current_user_id();
		$wl = get_user_meta($user_ID, 'betpress_lang', true);
        $wl0 = isset($_COOKIE['betpress_lang_type']) ? betpress_sanitize($_COOKIE['betpress_lang_type']) : get_option('bp_default_lang_type');
		
        $html .= '<div class="cat-wrapper">';
		
        if ((0 === $user_ID) && ( in_array($wl0, array('en'))) OR ( in_array($wl, array('en')))) {
        $cat_name = str_replace( 'Resultado Final', 'Fulltime Result', $cat_name );
        $cat_name = str_replace( '1° Tempo - Dupla Hipótese', 'Half Time Double Chance', $cat_name );			
        $cat_name = str_replace( 'Dupla Hipótese', 'Double Chance', $cat_name );	
        $cat_name = str_replace( 'Gols Mais/Menos', 'Goals Over/Under', $cat_name );	
        $cat_name = str_replace( 'Total de Escanteios', 'Match Corners', $cat_name );	
        $cat_name = str_replace( '1° Tempo - Escanteios', '1st Half Corners', $cat_name );	
        $cat_name = str_replace( 'Intervalo - Resultado', 'Half Time Result', $cat_name );	
        $cat_name = str_replace( '1°Tempo - Resultado Correto', 'Half Time Correct Score', $cat_name );	
        $cat_name = str_replace( 'Resultado Correto', 'Correct Score', $cat_name );	
       $cat_name = str_replace( '1º Tempo - Para Ambos os Times Marcarem', 'Both Teams to Score in 1st Half', $cat_name );
        $cat_name = str_replace( 'Para Ambos os Times Marcarem', 'Both Teams to Score', $cat_name );	
        $cat_name = str_replace( 'Finalizações no gol', 'Shots On Target', $cat_name );	
        $cat_name = str_replace( 'Resultados de Handicap Alternativos', 'Handicap Result', $cat_name );	
        $cat_name = str_replace( 'Empate Anula Aposta', 'Draw No Bet', $cat_name );	
        $cat_name = str_replace( 'Primeiro Time a Marcar', 'First Team to Score', $cat_name );	
        $cat_name = str_replace( '1º Tempo - Total de Gols', 'First Half Goals', $cat_name );	
        $cat_name = str_replace( '2º Tempo - Total de Gols', '2nd Half Goals', $cat_name );	
        $cat_name = str_replace( 'Gols Ímpar/Par', 'Goals Odd/Even', $cat_name );	
        $cat_name = str_replace( 'Número de Gols', 'Number of Goals in Match', $cat_name );	
        $cat_name = str_replace( 'Para Marcar a Qualquer Momento', 'Goalscorers (Anytime)', $cat_name );	
        $cat_name = str_replace( 'Total de Impedimentos', 'Offsides', $cat_name );	
        $cat_name = str_replace( 'Total de Faltas', 'Total Fouls', $cat_name );	
        $cat_name = str_replace( '1° Tempo - Faltas', '1st Half Fouls', $cat_name );	
        $cat_name = str_replace( 'Lançamento Lateral', 'Throw-ins', $cat_name );	
        $cat_name = str_replace( 'Tiro de meta', 'Goal kicks', $cat_name );	
        $cat_name = str_replace( 'Resultado do 2° Tempo', '2nd Half Result', $cat_name );	
        $cat_name = str_replace( 'Momento do Primeiro Gol', 'First Goal Moment', $cat_name );	
        }		

		if($cat_name['bet_event_cat_name'] == '1º Tempo - Total de Gols (1.5)'){
	    $html .= '<div class="cat-title-bar" style="background-color: #3b4d4c; color: #fff">' . '1º Tempo - Gols +/-' . '</div>';
		}		
		
		if($cat_name['bet_event_cat_name'] == 'Gols Mais/Menos (0.5)'){
	    $html .= '<div class="cat-title-bar" style="background-color: #3b4d4c; color: #fff">' . 'Total de Gols 0.5' . '</div>';
		}
		if($cat_name['bet_event_cat_name'] == 'Gols Mais/Menos (1.5)'){
	    $html .= '<div class="cat-title-bar" style="background-color: #3b4d4c; color: #fff">' . 'Gols Mais/Menos' . '</div>';
		}
		if($cat_name['bet_event_cat_name'] == 'Handicap') {
			if(!in_array($custom_cat_name, $temp_cat_names))
		   {
			  
			  $html .= '<div class="cat-title-bar" style="background-color: #3b4d4c; color: #fff">' . 'Handicap' . '</div>';
			   array_push($temp_cat_names,$custom_cat_name);
		   }
	   // $html .= '<div class="cat-title-bar" style="background-color: #3b4d4c; color: #fff">' . 'Handicap' . '</div>';
		}		
		if (($cat_name['bet_event_cat_name'] != 'Gols Mais/Menos (1.5)') && ($cat_name['bet_event_cat_name'] != 'Gols Mais/Menos (2.5)') && ($cat_name['bet_event_cat_name'] != 'Gols Mais/Menos (3.5)') && ($cat_name['bet_event_cat_name'] != 'Gols Mais/Menos (4.5)') && ($cat_name['bet_event_cat_name'] != 'Gols Mais/Menos (5.5)') && ($cat_name['bet_event_cat_name'] != '1º Tempo - Total de Gols (0.5)') && ($cat_name['bet_event_cat_name'] != '1º Tempo - Total de Gols (1.5)') && ($cat_name['bet_event_cat_name'] != '1º Tempo - Total de Gols (2.5)') && ($cat_name['bet_event_cat_name'] != 'Handicap')) 
		{
			if(!in_array($custom_cat_name, $temp_cat_names))
		   {
			  
			  $html .= '<div class="cat-title-bar" style="background-color: #3b4d4c; color: #fff">'.$cat_name['bet_event_cat_name'].'</div>';
			   array_push($temp_cat_names,$custom_cat_name);
		   }
			
		}
		$html .= '<div class="cat-container" style="background-color: #666">';
		
		global $wpdb;
        $user_ID = get_current_user_id();
        $check = $wpdb->get_results('SELECT DISTINCT bet_options_ids FROM ' . $wpdb->prefix . 'bp_slips where status = "unsubmitted" AND user_id = ' . $user_ID);
		$str_val = $check[0]->bet_options_ids;

		$color_new = $button_bg;
        foreach($bet_options as $bet_option) {
			
                    $color_new = '';
                    $active_ = '';
                    if (stripos($str_val, 'i:' . $bet_option['bet_option_id'] . ';')) {
                        $color_new = '#7c98a6 !important';
                        $active_ = 'active';
                    } else {
                        $color_new = $button_bg;
                        $active_ = '';
                    }
			
       $html .= '<div class="bet-option-wrapper bet-option-btn-' . $bet_option['bet_option_id'] . ' opt' . $bet_option['market'] . ' " id="bet-option-btn-' . $bet_option['bet_option_id'] . '" style="width: 50%; margin-left:0px; background-color: ' . $color_new . '; color: ' . $button_text . '">';

if(($cat_name['bet_event_cat_name'] == 'Jogador - Pontos') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Rebotes e Assistências') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Triplos Conseguidos') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Rebotes') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Assistências')) {			
//$bet_option = str_replace( '- Mais de ', '+', $bet_option );
//$bet_option = str_replace( '- Menos de ', '-', $bet_option );
$bet_option = str_replace( '.5 Pontos', '.5', $bet_option );
$bet_option = str_replace( '.5 Cestas de 3', '.5', $bet_option );
$bet_option = str_replace( '.5 Assistências', '.5', $bet_option );
$bet_option = str_replace( '.5 Rebotes', '.5', $bet_option );
$bet_option = str_replace( 'Kentavious Caldwell-Pope', 'K. Caldwell-Pope', $bet_option );	
$bet_option = str_replace( 'Giannis Antetokounmpo', 'Antetokounmpo', $bet_option );	
//$bet_option = str_replace( 'Khris Middleton', 'Middleton', $bet_option );	
//$bet_option = str_replace( 'Dennis Schroder', 'D. Schroder', $bet_option );	
//$bet_option = str_replace( 'Dennis Schroder', 'D. Schroder', $bet_option );	
}
		
if ((0 === $user_ID) && ( in_array($wl0, array('en'))) OR ( in_array($wl, array('en')))) {			
$bet_option = str_replace( 'Sim', 'Yes', $bet_option );
$bet_option = str_replace( 'Não', 'No', $bet_option );
$bet_option = str_replace( 'Mais de', 'Over', $bet_option );
$bet_option = str_replace( 'Menos de', 'Under', $bet_option );
$bet_option = str_replace( 'Empate', 'Draw', $bet_option );
$bet_option = str_replace( 'Sem 1° Gol', 'No 1st Goal', $bet_option );
$bet_option = str_replace( 'minutos', 'minutes', $bet_option );
$bet_option = str_replace( 'Nenhum Gol', 'No Goal', $bet_option );
$bet_option = str_replace( 'ou mais gols', 'or more goals', $bet_option );
$bet_option = str_replace( 'gols', 'goals', $bet_option );
$bet_option = str_replace( 'gol', 'goal', $bet_option );
$bet_option = str_replace( 'outro', 'other', $bet_option );
$bet_option = str_replace( 'Ímpar', 'Odd', $bet_option );
$bet_option = str_replace( 'Par', 'Even', $bet_option );
}
			
if(($bet_option['market'] != 'home') && ($bet_option['market'] != 'away') && ($bet_option['market'] != 'home1sc') && ($bet_option['market'] != 'away1sc') && ($bet_option['market'] != 'double1') && ($bet_option['market'] != 'double2') && ($bet_option['market'] != 'double3')) {			
$html .= '<div class="bet-option-title1" style="color:#f2f2f2 !important;"> ' . apply_filters('wpml_translate_single_string', $bet_option['bet_option_name'], 'betpress', 'bet-option-' . $bet_option['bet_option_name']) . '</div>';
}
if(($cat_name['bet_event_cat_name'] == 'Jogador - Pontos') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Rebotes e Assistências') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Triplos Conseguidos') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Rebotes') OR ($cat_name['bet_event_cat_name'] == 'Jogador - Assistências')) {
$html .= '<div class="b_team" style="color:#c3c3c3!important;"> ' . apply_filters('wpml_translate_single_string', $bet_option['b_team'], 'betpress', 'bet-option-' . $bet_option['b_team']) . '</div>';
}
	
if ((0 === $user_ID) && ( in_array($wl0, array('en'))) OR ( in_array($wl, array('en')))) {
if($bet_option['market'] == 'home'){
	   $html .= '<div class="bet-option-title">' . 'Home' . '</div>';
		}
if($bet_option['market'] == 'away'){
	   $html .= '<div class="bet-option-title">' . 'Away' . '</div>';
		}
if($bet_option['market'] == 'home1sc'){
	   $html .= '<div class="bet-option-title">' . 'Home' . '</div>';
		}			
if($bet_option['market'] == 'away1sc'){
	   $html .= '<div class="bet-option-title">' . 'Away' . '</div>';
		}
if($bet_option['market'] == 'double1'){
	   $html .= '<div class="bet-option-title">' . 'Home or Draw' . '</div>';
		}			
if($bet_option['market'] == 'double2'){
	   $html .= '<div class="bet-option-title">' . 'Draw or Away' . '</div>';
		}			
if($bet_option['market'] == 'double3'){
	   $html .= '<div class="bet-option-title">' . 'Home or Away' . '</div>';
		}
}
			
if (($wl0 != 'en') && ($wl != 'en')) {
if($bet_option['market'] == 'home'){
	   $html .= '<div class="bet-option-title">' . 'Casa' . '</div>';
		}
if($bet_option['market'] == 'away'){
	   $html .= '<div class="bet-option-title">' . 'Fora' . '</div>';
		}
if($bet_option['market'] == 'home1sc'){
	   $html .= '<div class="bet-option-title">' . 'Casa' . '</div>';
		}			
if($bet_option['market'] == 'away1sc'){
	   $html .= '<div class="bet-option-title">' . 'Fora' . '</div>';
		}
if($bet_option['market'] == 'double1'){
	   $html .= '<div class="bet-option-title">' . 'Casa ou Empate' . '</div>';
		}			
if($bet_option['market'] == 'double2'){
	   $html .= '<div class="bet-option-title">' . 'Empate ou Fora' . '</div>';
		}			
if($bet_option['market'] == 'double3'){
	   $html .= '<div class="bet-option-title">' . 'Casa ou Fora' . '</div>';
		}
}			
			
	   $html .= '<div class="bet-option-odd1">'.apply_filters('betpress_odd',$bet_option['bet_option_odd']) . '</div>';
       $html .= '</div>';
        }
        $html .= '      <div class="clear"></div>
                    </div>
                </div>';
    }

    $html .= '      </div>
                </div>
            </div>
        </div>';

    print_r($html);
    wp_die();
}

add_action('wp_ajax_get_betevent', 'betpress_get_betevent');
add_action('wp_ajax_nopriv_get_betevent', 'betpress_get_betevent');