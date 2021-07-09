<?php
global $wpdb;


if ( ! defined('ABSPATH') ) {
    exit();
}


	$curl = curl_init();
	$token = "87112-p1UgHT3S0929xu";
	if(!isset($_GET['event']))
	{
		echo json_encode(array("status" => 0 , "msg" => "event id not present"));
		return;
	}
	
	$event_id = $_GET['event'];
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<script type = "text/javascript">
	var event_id = "<?php echo $event_id ?>";
	var leagueName = "";
	const eventInfo = {};
	
	jQuery(document).ready(function() { 
		getLiveEventData(event_id);
// 		const interval = setInterval(function() {
// 		   getLiveEventData(event_id)
// 		 }, 5000);
	});
	function getLiveEventData(event_id)
	{
		jQuery.post(ajaxurl,
					{
						action :  'get_match_live_odds',
						event_id : event_id
						},function(response){
							response = JSON.parse(response);
							if(response['success'] === 1)
							{
								setInfo(response['event']);
								betOdds[event_id] = response.results;
								setLiveOddsData(event_id);
							}
						console.log(response);});
				}
	
	function setInfo(data)
	{
		eventInfo['league'] = data['CT'];
		eventInfo['match'] = data['NA'];
		
		jQuery("#leagueName").html(data['CT']);
		jQuery("#match").html(data['NA']);
		jQuery("#matchScore").html(data['SS']);
	}
	
	

</script>

<div class="jumbotron" id="jumbotronData" style="margin-bottom:0;">
  <h1 id="leagueName">-</h1>
  <p id="match">-</p>
  <p id="matchScore">-</p>
</div>

<div id="div_data_<?php  echo $event_id?>">
					
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

