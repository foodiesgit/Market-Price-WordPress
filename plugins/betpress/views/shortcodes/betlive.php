<?php

global $wpdb;

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">




<?php
global $wpdb;
$user_ID = get_current_user_id();
$timezone = get_user_meta($user_ID, 'gmt_offset', true);
?>

<style>
span.leagueItem {
   width: 100% !important;
   background-color: #666666;
   padding: 5px;
   padding-left: 9px;
   display: flex;
   font-weight:700;	
}
.div_home, .div_away {
   padding-left: 9px;
}	
.nav {
  flex-wrap: nowrap !important;
  overflow-x: scroll;
}
.container1 {
  display: flex;
  flex-direction: column;
  margin: auto;
}
.loader1 {
  width: 300px;
  height: 100px;
  padding-top: 40vh;
  margin: auto;
  display: flex;
}
.circle {
  width: 20px;
  height: 20px;
  background: green;
  border-radius: 50%;
  animation: jump 1s linear infinite;
  margin: 0 15px;
}
.caption {
  margin: auto;
  font-family: arial;
  font-size: 20px;
  color: black;
}
#b {
  animation-delay: 0.2s;
}
#c {
  animation-delay: 0.4s;
}
@keyframes jump {
  0% {
    margin-top: 0;
  }
  35% {
    margin-top: -75px;
  }
  70% {
    margin-top: 0px;
  }
}
</style>

<script type="text/javascript">
	
	var openedLeague = {};
	var sportId = "1";
	jQuery(document).ready(function(){
		
		getLiveEvents(1);
		jQuery(".changeSport").on("click",function(e){
			sportId = jQuery(this).attr("sport_id");
			getLiveEvents(sportId,true);
			jQuery(".changeSport").removeClass("active");
			jQuery(this).addClass("active");
		});
		
		jQuery('body').on("click",'h4[id^=league-name]',function(){
			let name = jQuery(this).attr("leagueName");
			if(openedLeague.hasOwnProperty(name))
			{
				delete openedLeague[name];		
			}else{
				openedLeague[name] = 1;
			}
			
// 			console.log(openedLeague);
		});
		
//		const interval = setInterval(function() {
		   getLiveEvents(sportId,false);
//		 }, 10000);
	});
	
	function getLiveEvents(sportId,showLoader)
	{
		if(showLoader)
		{
			jQuery('#data_container').html('');
			jQuery('.container1').show();
		}
		
		jQuery.post(ajaxurl,{
						action :  'get_inplay_events',
						sport_id: sportId
						
						},function(response){
			jQuery(".container1").hide();
						response = JSON.parse(response);
						processLiveEventsData(response)
						
						if(response['success'] == 1)
						{
							
						}
// 						console.log(response);
					});
	}
	
	function processLiveEventsData(data1)
	{
// 		console.log(data1['sport'][sportId]);
		var  str = ``;
		for(const item1 in data1['sport'][sportId]['league'])
		{
			
			let data = data1['sport'][sportId]['league'][item1];
// 			console.log(data);
			str += `<div class="main-div-league"  style="padding:0px;background-color:#585858; color: #fff;width:100%;border-bottom:0px solid green;">
						<span class="leagueItem" data-toggle="collapse" data-target="#div_${item1}" id="league-name-${item1}" leagueName="${item1}" style="cursor:pointer">${item1}</span>`;
			if(openedLeague.hasOwnProperty(item1))
			{
				str += `<div id="div_${item1}" class="collapse show"> `;		
			}else{
				str += `<div id="div_${item1}" class="collapse show"> `;
			}
			
			for(const item in data)
			{
				console.log(item);
				if(!data[item].hasOwnProperty("odds")){
					continue;
				}
				let teams = data[item]['match'].split(/ v | vs | V | @ | VS /);
				let itemOdds = data[item]['odds'];
				let totalItemOdds = itemOdds.length;
				let i = 0;	
				str += `
				<div class="ovm-CompetitionList" style="width: 100%;" event_id = "${data[item]['ID']}" event_name="${data[item]['league']}" match = "${data[item]['match']}">
					<div class="card-body" event_name="${data[item]['league']}" match = "${data[item]['match']}">
						<div>
						<div style="display:flex; justify-content:space-between">
							<div class="div get_odd" isLive="true" event_id = "${data[item]['ID']}" >
<a style="text-decoration:none !important;" href="https://passport.betcurry.com/inPlay/?event=${data[item]['ID']}" target="_blank" >
                                <div class="div_home" style="color:#fff;width:100%;font-size:15px;">${teams[0]}</div>
								<div class="div_away" style="color:#fff;width:100%;font-size:15px;">${teams[1]} </div>
</a>							
							</div>
   <div style="margin-right: 15px !important;color:#fff;font-size:15px;font-weight:700;"> ${data[item]['score']} </div>
						</div>`;

				while(i < totalItemOdds)
				{
					if(itemOdds[i]['type'] === "MA" && itemOdds[i].hasOwnProperty("CN"))
					{
						let oddsCount = itemOdds[i]['CN'];
						str += `
						<div class="cat-containerR" style="border-top: 1px solid #367a65; padding: 1px"> 
							<span style="padding:15px;font-weight:700;font-size: 16px;color:gray"> ${itemOdds[i]['NA']} </span>
							<div class="cat-wrapperR" style="width:100%" role="group" aria-label="Basic example">
								<div class="cat-container" style="width:100%">`;
						let  j = i+1;
						let betName = itemOdds[i]['NA'];
						while(j < totalItemOdds)
						{
							if(itemOdds[j]['type'] === "PA" && itemOdds[j].hasOwnProperty("OD"))
							{
								let na = "";
								let ha = "";
								if(itemOdds[j].hasOwnProperty("HA"))
								{
									ha = itemOdds[j]["HA"];		
								}
								
								if(itemOdds[j].hasOwnProperty("NA"))
								{
									na = itemOdds[j]['NA'];
								}else{
									switch(itemOdds[j]['OR'])
									{
										case "0":
											na = teams[0];
											break;
										case "1":
											if(oddsCount === "2")
											{
												na = teams[1];
											}else{
												na = "Draw";
											}
											break;
										case "2":
											na = teams[1];
											break;
										default:
											na = itemOdds[j]['OR'];
									}
								}
								let price = 1;
								if(itemOdds[j].hasOwnProperty("OD") && !isNaN(eval(itemOdds[j]['OD'])) )
								{
									price = eval(itemOdds[j]['OD'] + " + 1").toFixed(2);
									
								}else{
									j += 1;
									continue;
								}
							
								str += `
								<div class="j-option-wrapper" style="width:33.33%;">
									<span class="btn btn-third bet-option-btn col" style="width:100%;" event_id = "${data[item]['ID']}" id="bet-option-btn-${itemOdds[j]['ID']}" odd_id = "${itemOdds[j]['ID']}" price="${price}" bet="${na + " " + ha}" bet_name = "${betName}" >`;

								str += `<span style="display:block;float:left;font-size:14px;">${na} </span> <span style="color:yellow;float:right!important;font-size:14px"><span style="color:white">${ha} </span> ${price}</span>
						</span>	</div>`;
								i = j;
								j += 1;
								continue;
							}else if(itemOdds[j]['type'] === "MA"){
								break;
							}

							j += 1;
						}

						str += `	</div>
								</div>
							</div>`;
					}

					i += 1;
				}

				str += `</div></div></div>`;
			
			}
			
			str += `</div>
	</div>`;
		
		}
		
		jQuery(`#data_container`).html(str);
	}

</script>


<div style="width:100%; background-color:#518e79; padding:5px; margin-bottom:5px">
	<ul class="nav nav-tabs">
	  <li class="nav-item">
		  <a class="nav-link changeSport active" href="#"  sport_id="1" >Soccer</a>
	  </li>
	  <li class="nav-item " >
		<a class="nav-link changeSport" href="#"  sport_id="18">Basketball</a>
	  </li>
	  <li class="nav-item ">
		<a class="nav-link changeSport" href="#" sport_id="13">Tennis</a>
	  </li>
	  <li class="nav-item ">
		<a class="nav-link changeSport" href="#"  sport_id="16">Baseball</a>
	  </li>
	  <li class="nav-item ">
		<a class="nav-link changeSport" href="#" sport_id="151">Esports</a>
	  </li>		
	  <li class="nav-item ">
		<a class="nav-link changeSport" href="#"  sport_id="17">Ice Hockey</a>
	  </li>	
	  <li class="nav-item " >
		<a class="nav-link changeSport" href="#" sport_id="12">American Football</a>
	  </li>		
	  <li class="nav-item " >
		<a class="nav-link changeSport" href="#" sport_id="78">Handball</a>
	  </li>
	  <li class="nav-item " >
		<a class="nav-link changeSport" href="#" sport_id="83">Futsal</a>
	  </li>
	  <li class="nav-item " >
		<a class="nav-link changeSport" href="#" sport_id="91">Volleyball</a>
	  </li>	
	</ul>
</div>
<div class="container1">
  <div class="loader1" >
    <div class="circle" id="a"></div>
    <div class="circle" id="b"></div>
    <div class="circle" id="c"></div>
  </div>
</div>
<div id="data_container">
	
</div>



<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>















    
