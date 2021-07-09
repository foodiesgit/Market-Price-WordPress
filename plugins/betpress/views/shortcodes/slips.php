<?php
//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<?php
$bets = betpress_getunSubmittedSlips();
// print_r($bets);
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Awaiting Slips</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Bet's Won</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Bet's Lost</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active container" id="home" role="tabpanel" aria-labelledby="home-tab">
	
	  <div  class="row" id="awaiting_slips_div">
		 
	  </div>
	</div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">bets won</div>
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">bets lost</div>
</div>





<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<script type="text/javascript">
		jQuery(document).ready(function() { 
		getAwaitingSlips();
			
		jQuery('body').on('click','button[id^=automate_result]', function (response) {
			let id = jQuery(this).attr("row_id");
			jQuery.post(ajaxurl,
			{
				action :  'set_bet_result',
				id: id
				},function(response)
			{
				response = JSON.parse(response);
// 			updateAwaitingSlipsDiv(response);
					
				console.log(response);
			;});
			
		});
		

	});
	
	
	function getAwaitingSlips()
	{
		jQuery.post(ajaxurl,
			{
				action :  'get_awaiting_slips',
				},function(response)
			{
				response = JSON.parse(response);
			updateAwaitingSlipsDiv(response);
					
				console.log(response)
			;});
	}
	
	function updateAwaitingSlipsDiv(data)
	{
		let str = ``;
		data.forEach(function(item){
			str += `
			<div class="card col-6"  >
			  <div class="card-header">
				<h5>League : <span>${item.bet_event_name}</span></h5>
				<h5>Event : <span>${item.bet_event_cat}</span></h5>
				</div>
			  <div class="card-body">
					<h6>Bet Name : <span>${item.bet_option_name}</span></h6>
					<h6>Bet : <span>${item.bet}</span></h6>
					<h6>Odds : <span>${item.bet_option_odd}</span></h6>
					<h6>Stake : <span>${item.stake}</span><span> Winnings : ${item.winnings}</span></h6>
				</div>
			  <div class="card-footer">
				<button type="button" class="btn btn-primary">View Results</button>
				<button type="button" class="btn btn-primary" id="automate_result_${item.id}" row_id="${item.id}">Automate Result</button>
			  </div>
			</div>`;
		
		})
		
		jQuery("#awaiting_slips_div").html(str);
			
		
	}

</script>