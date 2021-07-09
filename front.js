var bet_selected = [];	
var slipCount = 0;
var removeByAttr = function(arr, attr, value){
			var i = arr.length;
			while(i--){
			   if( arr[i] 
				   && arr[i].hasOwnProperty(attr) 
				   && (arguments.length > 2 && arr[i][attr] === value ) ){ 

				   arr.splice(i,1);

			   }
			}
			return arr;
	}

var betOdds = {};


jQuery(document).ready(function (response) { 
	
    //make stake input height equals the submit button height
    jQuery('#stake-input').css('min-height', jQuery('#submit-slip-button').css('height'));
	
		str = localStorage.getItem("slips_json");
		if(str != "" || str != null)
		{
			bet_selected = JSON.parse(str);
			updateBetDiv();

		}
	
	jQuery(window).focus(function(){
		
		str = localStorage.getItem("slips_json");
		if(str != "" || str != null)
		{
			bet_selected = JSON.parse(str);
			updateBetDiv();

		}
	});
	

    //fix height bugs in featured page
    jQuery('.table-col-featured-options').each(function(response) {
        var this_height = jQuery(this).css('height');
        var children = jQuery(this).children('.featured-bet-option-wrapper');

        jQuery(this).siblings('.table-col-featured-bet-event').css('height', this_height);

        jQuery.each(children, function (index, element) {
            jQuery(element).css('height', this_height);
        });

    });

	//lang changing redirection
    jQuery('#lang-type-switcher-dropdown').on('change', function (response) {

        var selected_option = jQuery(this).find('option:selected').val();
        var current_url = window.location.href;
        var query_params = window.location.search;
        var get_symbol = (query_params === '') ? '?' : '&';

        //change the desired lang
        window.location.replace(current_url + get_symbol + 'lang_type_changing=' + selected_option);
    });
	
	//gmt changing redirection
    jQuery('#gmt-type-switcher-dropdown').on('change', function (response) {

        var selected_option = jQuery(this).find('option:selected').val();
        var current_url = window.location.href;
        var query_params = window.location.search;
        var get_symbol = (query_params === '') ? '?' : '&';

        //change the desired lang
        window.location.replace(current_url + get_symbol + 'gmt_type_changing=' + selected_option);
    });	
	
	// balance changing redirection
    jQuery('#balance-type-switcher-dropdown').on('change', function (response) {

        var selected_option = jQuery(this).find('option:selected').val();
        var current_url = window.location.href;
        var query_params = window.location.search;
        var get_symbol = (query_params === '') ? '?' : '&';

        //change the desired balance
        window.location.replace(current_url + get_symbol + 'balance_type_changing=' + selected_option);
    });	
	
    //odd changing redirection
    jQuery('#odd-type-switcher-dropdown').on('change', function (response) {

        var selected_option = jQuery(this).find('option:selected').val();
        var current_url = window.location.href;
        var query_params = window.location.search;
        var get_symbol = (query_params === '') ? '?' : '&';

        //change the desired odd
        window.location.replace(current_url + get_symbol + 'odd_type_changing=' + selected_option);
    });

    //toggle bettings
    jQuery('span[class=toggle-btn]').on('click', function (response) {

        var selected_id = this.id;
        var selected = selected_id.split('--');
        var type = selected[0];
        var id = selected[1];

        var container = jQuery('#' + type + '-container-' + id);

        this.innerHTML = container.css('display') === 'none' ? i18n_front.toggle_symbol_minus : i18n_front.toggle_symbol_plus;

        container.slideToggle();

    });
	jQuery("#stake-input").on("keyup click",function(event){
  		
		let stakeVal = jQuery(this).val();
		if(stakeVal.length > 0)
		{
			var toReturnVal = parseFloat(stakeVal);
			bet_selected.forEach(function(arrayItem){
				toReturnVal *= parseFloat(arrayItem['price']);
			});
			jQuery("#pw").text(toReturnVal.toFixed(2));
		}else{
			jQuery("#pw").text("0.00");
		}
	});
	
	

	
    //add bet option
    jQuery('body').on('click','span[id^=bet-option-btn]', function (response) {
		
		
		var previousData = "";
		if(slipCount != 0)
		{
			previousData = jQuery('.bets-holder').html();
		}
	
        var id = jQuery(this).attr('odd_id');							//bet option odd id
		var event_id = jQuery(this).attr("event_id");
		var isLive = jQuery(this)[0].hasAttribute("isLive");
		if(isLive)
		{
			var event = eventInfo['league'];
			var match = eventInfo['match'];
		}else{
			var event = jQuery(this).parents().eq(6).attr('event_name');
			var match = jQuery(this).parents().eq(6).attr("match");
		}
		
		var bet = jQuery(this).attr('bet');
		var price = jQuery(this).attr('price');
		var bet_name = jQuery(this).attr('bet_name');
		var selectBet = true;
		
		for(var i = 0 ; i < bet_selected.length ; i++)
		{
				if(bet_selected[i]["event_id"] == event_id)
				{
					if(bet_selected[i]['bet_option_id'] != id)
					{
						removeByAttr(bet_selected,"event",bet_selected[i]["event"]);
						console.log(bet_selected);
					}else{
						removeByAttr(bet_selected,"event",bet_selected[i]["event"]);
						localStorage.setItem("slips_json", JSON.stringify(bet_selected));
						updateBetDiv();
						selectBet = false;
					}
				}
		}
		if(selectBet)
		{
			slipCount += 1;
		
		
			jQuery('.bets-holder').html(i18n_front.loading);

			jQuery('#pw').html('');


			var bet_data = {
				"bet_option_id": id,
				"match" : match,
				"event" : event,
				"bet" : bet,
				"bet_name" : bet_name,
				"price" : price,
				"event_id" : event_id
			}

			jQuery.post(ajaxurl, {

				action: 'add_bet_option',
				bet_option_id: id,
				match: match,
				event: event,
				bet: bet,
				price: price,
				event_id : event_id

			}, function (response) { //alert('hie');
				//alert(jQuery('#'+selected_option_id).attr('id');
				//jQuery(div[id^='#'+selected_option_id]).css('background-color',' #189970 !important');
				jQuery(".cat-container").next().toggle();
// 				jQuery('.'+selected_option_id).parent().children('.bet-option-wrapper').removeClass('active');
				if(response.indexOf('error-holder') == -1) {
// 					jQuery('.'+selected_option_id).parent().children('.bet-option-wrapper').removeClass('active');
// 					jQuery('.'+selected_option_id).addClass('active');
				}
				previousData += response;  
				bet_selected.push(bet_data);
// 				console.log(bet_selected);

// 				jQuery('.bets-holder').html(previousData);

				localStorage.setItem("slips_json", JSON.stringify(bet_selected));
				updateBetDiv();

				if(odds.length == 0)
					jQuery('#pw').html('0.00');
				else{
					jQuery('#pw').html('');;
				}
			});	
		}
		
    });

    //delete bet option
    jQuery('.bets-holder').on('click', '.delete-bet-option', function (event) {

        event.preventDefault();

        jQuery('.bets-holder').html(i18n_front.loading);

        var id = this.id.split('-')[1];

        jQuery.post(ajaxurl, {

            action: 'delete_bet_option',
            bet_option_id: id

        }, function (response) {
            var selected_option_id = eval('"bet-option-btn-'+id+'"');
			jQuery('.'+selected_option_id).removeClass('active');
			jQuery('.bets-holder').html(response);
        });
    });
	
	//delete slip 
	
		jQuery(".bet-option-slip-wrapper").on("click",function (event) {
			console.log("imhere");
// 			id = jQuery(this).attr("bet_option_id");
// 			removeByAttr(bet_selected,"bet_option_id",id);
// 			jQuery(this).closest(".bet-option-slip-wrapper").remove();
// 			localStorage.setItem("slips_json", JSON.stringify(bet_selected));
		});
					
	
	
	//clear all bets options
	jQuery('.clear_all_bet_option').click(function (event) {

        event.preventDefault();
		localStorage.setItem("slips_json", JSON.stringify([]));event
		

        jQuery('.bets-holder').html(`<div class="info-holder">
    		Your betting slip is empty.    
			</div>`);
		bet_selected = [];
		setSelectedBetsToBlue();
		
        
    });

    //submit bet slip
    jQuery('button[id=submit-slip-button]').on('click', function (event) {
        event.preventDefault();

        jQuery(this).blur();

        jQuery('.bets-holder').html(i18n_front.loading);

        var bet_stake = jQuery('#stake-input').val();
		if(bet_stake.length == 0)
		{
				alert("Please enter stake");
				return;
		}


        jQuery.post(ajaxurl, {

            action: 'submit_bet_slip',
            bet_stake: bet_stake,
			bet_selected: bet_selected

        }, function (response) {
	
            jQuery('.bets-holder').html(response);
				
            if(jQuery('.success-holder').length > 0){
				localStorage.setItem("slips_json", JSON.stringify([]));
				jQuery("#pw").text("0.00");
				jQuery("#stake-input").val('');
				setSelectedBetsToBlue();       	
            }
            jQuery('.points-holder').html(i18n_front.loading);

            jQuery.post(ajaxurl, {

                action: 'points_change'

            }, function (response) {

                jQuery('.points-holder').html(response);

            });
        });
    });

    //slips page, toggling a slip's bet options
    jQuery('.toggle-bet-options').on('click', function (response) {

        jQuery(this).next().toggle();

    });
	
	//functions created by ayush
	

	
		
	jQuery('.get_odds').on("click",function(e){
		
		if(!jQuery(this).hasClass("collapsed"))
		{
			let event_id = jQuery(this).attr("event_id");
			var home  = jQuery(this).attr("home");
			var away = jQuery(this).attr("away");
			let actionName = "get_match_odds";
			var isLive = jQuery(this)[0].hasAttribute("isLive");
			if(isLive){
				actionName = "get_match_live_odds";
			}
				if(event_id in betOdds)
				{
					makeTabs(event_id,home,away);
// 					setOddsData(event_id,home,away);
				}else{
					jQuery.post(ajaxurl,{
						action :  actionName,
						event_id : event_id
						},function(response){
						response = JSON.parse(response);
						if(response['success'] == 1)
						{
							if(isLive)
							{
								betOdds[event_id] = response.results;
								setLiveOddsData(event_id);
								return;
							}
							if(response['results'].length > 0)
							{
								let resCopy = response.results[0];
								for(let item in resCopy){
									if(item == 'FI' || item == 'event_id' || item == 'others'){
										continue;
									}
									for(let key in resCopy[item]["sp"]){
										if(resCopy[item]["sp"][key]["odds"] && resCopy[item]["sp"][key]["odds"].length == 0 && resCopy['others']){
											for(let j = 0; j < resCopy['others'].length; j++){
												for(let otherItem in resCopy['others'][j]["sp"]){
													if(otherItem == key){
														resCopy[item]["sp"][key]["odds"] = resCopy['others'][j]["sp"][otherItem]["odds"];
													}
												}
											}
										}
									}
								}
								betOdds[event_id] = resCopy;
								makeTabs(event_id,home,away);
							}
						}
								console.log(response);
					});
				}
		}
		
		

		
	});
	
	//click event for tab switching
	jQuery('body').on('click','p[id^=data-category]',function(response){
		let event_id 	= jQuery(this).attr("event_id");
		let category 	= jQuery(this).attr("category_name");
		let home 		= jQuery(this).attr("home");
		let away 		= jQuery(this).attr("away");
		
		jQuery(`.data_category_${event_id}`).removeClass("active");
		jQuery(`#data-category-${event_id}-${category}`).addClass("active");
		setOddsData(event_id,home,away,category);
	});
	
});



function setLiveOddsData(event_id)
{
	console.log("imhere");
	let str = ``;
	let oddData = betOdds[event_id];
	for(const item in oddData)
	{
		
		let itemOdds = oddData[item]['odds'];
		let totalItemOdds = itemOdds.length;
		let suspended =  parseInt(oddData[item]['SU']);
		let i = 0;
		while(i < totalItemOdds)
		{
			if(itemOdds[i]['type'] === "MA")
			{
				if(itemOdds[i]['SY'] === "_a")
				{
					console.log("_a :" + item + " :su = "+ suspended);
					if(suspended === 1)
					{
						str += `<div class="cat-containerR" style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"> 
					<span style="padding:15px;font-weight:700;font-size: 16px"> ${oddData[item]['NA']} <span> suspended</span></span>
					<div class="cat-wrapperR" style="width:100%" role="group" aria-label="Basic example">
						<div class="cat-container" style="width:100%">`;
					}else{
						str += `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"> 
					<span style="padding:15px;font-weight:700;font-size: 16px"> ${oddData[item]['NA']} </span>
					<div class="cat-wrapperR" style="width:100%" role="group" aria-label="Basic example">
						<div class="cat-container" style="width:100%">`;
					}
					
					let  j = i+1;
					while(j < totalItemOdds)
					{
						if(itemOdds[j]['type'] === "PA" && itemOdds[j].hasOwnProperty("OD"))
						{
							let na = itemOdds[j]['NA'];
							let hd = itemOdds[j]['HD'];
							if(!na)
							{
								na = "";	
							}

							if(!hd)
							{
								hd = "";	
							}
							if(suspended === 1)
							{
								str += `<div class="j-option-wrapper" style="width:33.33%;">
							<span class="btn btn-third bet-option-btn col" style="width:100%;cursor:not-allowed" event_id = "${event_id}"  odd_id = "${itemOdds[j]['ID']}" price="${eval(itemOdds[j]['OD']+ " + 1").toFixed(2)}" bet="${na +" " + hd}" bet_name = "${oddData[item]['NA'] }" isLive="true">`;	
							}else{
									str += `<div class="j-option-wrapper" style="width:33.33%;">
							<span class="btn btn-third bet-option-btn col" style="width:100%;" event_id = "${event_id}" id="bet-option-btn-${itemOdds[j]['ID']}" odd_id = "${itemOdds[j]['ID']}" price="${eval(itemOdds[j]['OD']+ " + 1").toFixed(2)}" bet="${na +" " + hd}" bet_name = "${oddData[item]['NA'] }" isLive="true">`;
							}
						

							str += `<span style="display:block;float:left;font-size:14px;">${na + " " + hd} </span> <span style="color:yellow;float:right!important;font-size:14px"> ${eval(itemOdds[j]['OD'] + " + 1").toFixed(2)}</span>
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

					}else if(itemOdds[i]['SY'] === "dc")
					{
						str += makeStyle_dc(itemOdds,item,suspended);
						break;
						
					}else if(itemOdds[i]['SY'] === "da" && itemOdds[i]['PY'] !== "_C")
					{
						str += 	makeStyle_da(itemOdds,item,suspended);
						break;
					}else if(itemOdds[i]['SY'] === "db" && itemOdds[i]['PY'] === "_c")
					{
						str += makeStyle_db(itemOdds,item,suspended);
						break;
					}else if(itemOdds[i]['SY'] === "ib")
					{
						str += makeStyle_ib(itemOdds,item,suspended);
						break;
					}else if(itemOdds[i]['SY'] === "db" && itemOdds[i]['PY'] === "da")
					{
						str += makeStyle_db_da(itemOdds,item,suspended);
						break;
					}else if(itemOdds[i]['SY'] === "du")
					{
						str += makeStyle_du(itemOdds,item,suspended);
						break;
					}
			}			
			i += 1;
		}
	}
	
	jQuery(`#div_data_${event_id}`).html(str);
	setSelectedBetsToBlue();
}

//function to make dc layout

function makeStyle_dc(odds_dc,betname, suspended)
{
	console.log("dc : " +betname + "  :su = " + suspended);
	let str = ``;
	if(suspended === 1)
	{
		str = `<div class="cat-containerR" style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}<span style="font-size:8px">    suspended</span></h4><div style="display:flex;justify-content:space-between;align-items:start">`;	
	}else{
		str = `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}</h4><div style="display:flex;justify-content:space-between;align-items:start">`;
	}
	
	
	
	

	let names = [];
	let i = 0;
	let totalItemOdds = odds_dc.length;
	let order = 0;
	let lastMA_na = "";
	while(i < totalItemOdds)
	{
		if(odds_dc[i]['type'] === "MA" && odds_dc[i]['OR'] === "0")
		{
			order = 0;
			str += `<div style="padding:5px"><h5>#</h5>`;
		}else if(odds_dc[i]['type'] === "PA"){
			names.push(odds_dc[i]['NA']);
			if(order === 0)
			{
				str += `<h5><span class="btn btn-third bet-option-btn col" style="font-size:14px">${odds_dc[i]['NA']}</span></h5>`;	
			}else{
				let na = names[ parseInt(odds_dc[i]['OR']) ];
				let hd = odds_dc[i]['HA'];
				

				if(!hd)
				{
					hd = "";	
				}
				if(na === hd){hd = "";}
				
				if(suspended === 1)
				{
					str += `<h5> <span class="btn btn-third bet-option-btn col" event_id = "${event_id}" odd_id = "${odds_dc[i]['ID']}" price="${eval(odds_dc[i]['OD']+ " + 1").toFixed(2)}" bet="${na +" " + lastMA_na}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px;cursor:not-allowed;">${eval(odds_dc[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}else{
					str += `<h5> <span class="btn btn-third bet-option-btn col" event_id = "${event_id}" id="bet-option-btn-${odds_dc[i]['ID']}" odd_id="${odds_dc[i]['ID']}" price="${eval(odds_dc[i]['OD']+ " + 1").toFixed(2)}" bet="${lastMA_na + " " + na}" bet_name = "${betname}" isLive="true" style="color:yellow;font-size:14px"  >  ${eval(odds_dc[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}
				
				
				
				
			}
		}else if(odds_dc[i]['type'] === "MA")
		{
			lastMA_na = odds_dc[i]['NA'];
			order =  parseInt(odds_dc[i]['OR']);
			str += `</div><div style="padding:5px"><h5>${odds_dc[i]['NA']}</h5>`
		}
		
		i += 1;
	}
	
	str += `	</div>
			</div>
		</div>`;

	return str;	
}

// function to make da layout

function makeStyle_da(odds_da,betname, suspended)
{
	console.log("da : " + betname + "  :su = " + suspended);
	
		let str = ``;
	if(suspended === 1)
	{
		str = `<div class="cat-containerR" style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}<span style="font-size:8px">    suspended</span></h4><div style="display:flex;justify-content:space-between;align-items:start">`;	
	}else{
		str = `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}</h4><div style="display:flex;justify-content:space-between;align-items:start">`;
	}
	
	let names = [];
	let i = 0;
	let totalItemOdds = odds_da.length;
	let order = 0;
	let lastMA_na = "";
	while(i < totalItemOdds)
	{
		if(odds_da[i]['type'] === "MA" && odds_da[i]['OR'] === "0")
		{
			order = 0;
			str += `<div style="padding:5px"><h5>#</h5>`
		}else if(odds_da[i]['type'] === "PA"){
			names.push(odds_da[i]['NA']);
			let na = names[ parseInt(odds_da[i]['OR'])];
			let hd = odds_da[i]['HD'];
			if(!na)
			{
				na = "";	
			}
			
			if(!hd)
			{
				hd = "";	
			}
			if(na === hd){na = "";}
			
			if(order === 0)
			{
				str += `<h5><span class="btn btn-third bet-option-btn col" style="font-size:14px">${odds_da[i]['NA']}</span></h5>`;	
			}else{
				if(suspended === 1)
				{
					str += `<h5> <span class="btn btn-third bet-option-btn col" event_id = "${event_id}"  odd_id = "${odds_da[i]['ID']}" price="${eval(odds_da[i]['OD']+ " + 1").toFixed(2)}" bet="${na +" : " + lastMA_na}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px;cursor:not-allowed;"><span style="color:white;font-size:10px">${" " + hd}</span> ${eval(odds_da[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;	
				}else{
					str += `<h5> <span class="btn btn-third bet-option-btn col" event_id = "${event_id}" id="bet-option-btn-${odds_da[i]['ID']}" odd_id = "${odds_da[i]['ID']}" price="${eval(odds_da[i]['OD']+ " + 1").toFixed(2)}" bet="${na + " - " + lastMA_na + " " + hd}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px"><span style="color:white;font-size:10px">${" " + hd}</span> ${eval(odds_da[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}
				
			}
		}else if(odds_da[i]['type'] === "MA")
		{
			lastMA_na = odds_da[i]['NA'];
			order =  parseInt(odds_da[i]['OR']);
			str += `</div><div style="padding:5px"><h5>${odds_da[i]['NA']}</h5>`
		}
		
		i += 1;
	}
	
	str += `	</div>
			</div>
		</div>`;

	return str;
}

// function to make db layout style

function makeStyle_db(odds_db,betname, suspended)
{
	console.log("db : " + betname + "  :su = " + suspended);
	let str = ``;
	if(suspended === 1)
	{
		str = `<div class="cat-containerR" style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}<span style="font-size:8px">    suspended</span></h4><div style="display:flex;justify-content:space-between;align-items:start">`;	
	}else{
		str = `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}</h4><div style="display:flex;justify-content:space-between;align-items:start">`;
	}
	
	let names = [];
	let i = 0;
	let totalItemOdds = odds_db.length;
	let order = 0;
	let lastMA_na = "";
	while(i < totalItemOdds)
	{
		if(odds_db[i]['type'] === "MA")
		{
			lastMA_na = odds_db[i]['NA'];
			if(order !== 0 )
			{
				str += `</div>`;
			}
			str += `<div style="padding:5px"><h5>${odds_db[i]['NA']}</h5>`;
			order += 1;
		}else if(odds_db[i]['type'] === "PA"){
			let na = odds_db[i]['NA'];
			let hd = odds_db[i]['HD'];
			if(!na)
			{
				na = "";	
			}
			
			if(!hd)
			{
				hd = "";	
			}
			if(na === hd){hd = "";}
			if(suspended === 1)
			{
				str += `<h5>
					<span class="btn btn-third bet-option-btn" event_id = "${event_id}"  odd_id = "${odds_db[i]['ID']}" price="${eval(odds_db[i]['OD']+ " + 1").toFixed(2)}" bet="${na+" "+hd +" : " + lastMA_na}" bet_name = "${betname +"-"+lastMA_na}" isLive="true" style="color:yellow;font-size:14px;cursor:not-allowed;"> <span style="color:white;font-size:10px">${na + " " + hd}</span> ${eval(odds_db[i]['OD'] + " + 1").toFixed(2)}</span>
</h5>`;	
			}else{
				str += `<h5>
					<span class="btn btn-third bet-option-btn" event_id = "${event_id}" id="bet-option-btn-${odds_db[i]['ID']}" odd_id = "${odds_db[i]['ID']}" price="${eval(odds_db[i]['OD']+ " + 1").toFixed(2)}" bet="${lastMA_na + " " + na + " " + hd}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px"><span style="color:white;font-size:10px">${na + " " + hd}</span> ${eval(odds_db[i]['OD'] + " + 1").toFixed(2)}</span>
</h5>`;
			}
		}
		
		i += 1;
	}
	
	str += `	</div>
			</div>
		</div>`;

	return str;
}

// function to make db layout style with da as PY

function makeStyle_db_da(odds_db,betname, suspended)
{
		console.log("db da : " + betname + "  :su = " + suspended);
	let str = ``;
	if(suspended === 1)
	{
		str = `<div class="cat-containerR" style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}<span style="font-size:8px">    suspended</span></h4><div style="display:flex;justify-content:space-between;align-items:start">`;	
	}else{
		str = `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}</h4><div style="display:flex;justify-content:space-between;align-items:start">`;
	}
	
	let names = [];
	let i = 0;
	let totalItemOdds = odds_db.length;
	let order = 0;
	let lastMA_na = "";
	while(i < totalItemOdds)
	{
		if(odds_db[i]['type'] === "MA" && odds_db[i]['OR'] === "0")
		{
			order = 0;
			str += `<div style="padding:5px"><h5>#</h5>`
		}else if(odds_db[i]['type'] === "PA"){
			names.push(odds_db[i]['NA']);
			let na = names[ parseInt(odds_db[i]['OR'])];
			let hd = odds_db[i]['HD'];
			let bet = lastMA_na + " " + hd;
			if(!na)
			{
				na = "";	
			}
			
			if(!hd)
			{
				hd = "";	
				bet = lastMA_na;
			}else if(hd.split(" ")[0] === "O")
			{
				hd = "Over " + odds_db[i]['HA'];	
				bet = hd;
			}else if(hd.split(" ")[0] === "U")
			{
				hd = "Under " + odds_db[i]['HA'];
				bet = hd;
			}
			if(na === hd){hd = "";}
			
			if(order === 0)
			{
				str += `<h5><span class="btn btn-third bet-option-btn col" style="font-size:14px">${odds_db[i]['NA']}</span></h5>`;	
			}else{
				if(suspended === 1)
				{
					str += `<h5> <span class="btn btn-third bet-option-btn" event_id = "${event_id}"  odd_id = "${odds_db[i]['ID']}" price="${eval(odds_db[i]['OD']+ " + 1").toFixed(2)}" bet="${lastMA_na + " " + na + " " + hd}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px;cursor:not-allowed;"><span style="color:white;font-size:10px">${na + " " + hd}</span>  ${eval(odds_db[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}else{
					str += `<h5> <span class="btn btn-third bet-option-btn" event_id = "${event_id}" id="bet-option-btn-${odds_db[i]['ID']}" odd_id = "${odds_db[i]['ID']}" price="${eval(odds_db[i]['OD']+ " + 1").toFixed(2)}" bet="${bet}" bet_name = "${betname + " - " + na}" isLive="true" style="color:yellow;font-size:14px"><span style="color:white;font-size:10px">${" " + hd}</span> ${eval(odds_db[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}
				
			}
		}else if(odds_db[i]['type'] === "MA")
		{
			lastMA_na = odds_db[i]['NA'];
			order =  parseInt(odds_db[i]['OR']);
			str += `</div><div style="padding:5px"><h5>${odds_db[i]['NA']}</h5>`
		}
		
		i += 1;
	}
	
	str += `	</div>
			</div>
		</div>`;

	return str;
}

// function to make ib layout style

function makeStyle_ib(odds_ib,betname, suspended)
{
	console.log("ib : " + betname + "  :su = " + suspended);
	let str = ``;
	if(suspended === 1)
	{
		str = `<div class="cat-containerR" style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}<span style="font-size:8px">    suspended</span></h4><div  class="cat-container" style="width:100%;align-items:start">`;	
	}else{
		str = `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}</h4><div class="cat-container" style="width:100%;align-items:start" >`;
	}
	
	
	let names = [];
	let i = 0;
	let totalItemOdds = odds_ib.length;
	let order = 0;
	while(i < totalItemOdds)
	{
		if(odds_ib[i]['type'] === "MA")
		{

		}else if(odds_ib[i]['type'] === "PA"){
			if(suspended === 1)
			{
				str += `<div class="j-option-wrapper" style="width:33.33%;"><h5><span class="btn btn-third bet-option-btn" event_id = "${event_id}" odd_id = "${odds_ib[i]['ID']}" price="${eval(odds_ib[i]['OD']+ " + 1").toFixed(2)}" bet="${odds_ib[i]['NA']}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px;cursor:not-allowed;"><span style="font-size:14px;color:white;"> ${odds_ib[i]['NA']}</span>  ${eval(odds_ib[i]['OD'] + " + 1").toFixed(2)}</span>
</h5></div>`;	
			}else{
				str += `<div class="j-option-wrapper" style="width:33.33%;" ><h5><span class="btn btn-third bet-option-btn" event_id = "${event_id}" id="bet-option-btn-${odds_ib[i]['ID']}" odd_id = "${odds_ib[i]['ID']}" price="${eval(odds_ib[i]['OD']+ " + 1").toFixed(2)}" bet="${odds_ib[i]['NA']}" bet_name = "${betname }" isLive="true" style="color:yellow;font-size:14px"><span style="font-size:14px;color:white;"> ${odds_ib[i]['NA']}</span>  ${eval(odds_ib[i]['OD'] + " + 1").toFixed(2)}</span>
</h5></div>`;
			}		
		}
		
		i += 1;
	}
	
	str += `	</div>
			</div>`;

	return str;
}


//function to make style du

function makeStyle_du(odds_du,betname,suspended){
	console.log("du : " + betname + "  :su = " + suspended);
	let str = ``;
	if(suspended === 1)
	{
		str = `<div style="background-color:#707070; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}<span style="font-size:8px">    suspended</span></h4><div style="width:100%;">`;	
	}else{
		str = `<div style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65; padding: 10px"><h4>${betname}</h4><div style="width:100%;" >`;
	}
	
	let names = [];
	let i = 0;
	let totalItemOdds = odds_du.length;
	let order = 0;
	let c = 0;
	let lastMA_na = "";
	
	while(i < totalItemOdds)
	{
		if(odds_du[i]['type'] === "MA" && odds_du[i]['SY'] === "du")
		{
			names = [];
			if(c !== 0)
			{
				str += `</div></div>`;	
			}
			
			str += `<div>
						<div style="display:flex;justify-content:space-between;align-items:start">
							<div>
								<div style="display:flex;justify-content:space-between"><span class="btn btn-third bet-option-btn">${odds_du[i]['NA']}</span>  <span class="btn btn-third bet-option-btn" style="text-align:right">${odds_du[i]['PC']}</span> </div>`;
			order = 0;
		}else if(odds_du[i]['type'] === "PA" )
		{
			names.push(odds_du[i]['NA']);
			let na = names[parseInt(odds_du[i]['OR'])];
			let hd = odds_du[i]['HD'];
			if(!na)
			{
				na = "";	
			}
			
			if(!hd)
			{
				hd = "";	
			}
			if(na === hd){hd = "";}
			
			
			if(order === 0)
			{
				str += `<h5 style="display:flex;justify-content:space-between"><span class="btn btn-third bet-option-btn" style="font-size:14px;" >${odds_du[i]['NA']}</span><span style="color:#888"> ${odds_du[i]['PC']}</span></h5>`;		
			}else{
				if(suspended === 1)
				{
					str += `<h5> <span class="btn btn-third bet-option-btn" event_id = "${event_id}" odd_id = "${odds_du[i]['ID']}" price="${eval(odds_du[i]['OD']+ " + 1").toFixed(2)}" bet="${na + " - "+ lastMA_na + " " + hd}" bet_name = "${betname}" isLive="true" style="color:yellow;font-size:14px;cursor:not-allowed;"><span style="color:white;font-size:10px">${" "}</span> ${eval(odds_du[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}else{
					str += `<h5> <span class="btn btn-third bet-option-btn" event_id = "${event_id}" id="bet-option-btn-${odds_du[i]['ID']}" odd_id = "${odds_du[i]['ID']}" price="${eval(odds_du[i]['OD']+ " + 1").toFixed(2)}" bet="${na + " - "+ lastMA_na + " " + hd}" bet_name = "${betname}" isLive="true" style="color:yellow;font-size:14px"><span style="color:white;font-size:10px">${hd}</span> ${eval(odds_du[i]['OD'] + " + 1").toFixed(2)}</span></h5>`;
				}
				
			}
		}else if(odds_du[i]['type'] === "MA" )
		{
			str += `</div>
					<div>
						<div class="btn btn-third bet-option-btn" >${odds_du[i]['NA']}</div>`;
			order = 1;
			c = 1;
			lastMA_na = odds_du[i]['NA'];
		}
				 
		i += 1;
	}
	
	str += `</div></div>`;
	return str;
}


function makeTabs(event_id,home,away)
{
	let str = `<ul class="nav nav-tabs nav-justified">`;
	let keys = ['main',"asian_lines","corners","goals","half","other","specials","cards","minutes","player","main_props","half_props","player_props","quarter_props","team_props"];
	let lastKey = "main";
	for(const item in betOdds[event_id])
	{
		
		console.log(item);
		if(keys.includes(item))
		{
			lastKey = item;
			str += `<li class="nav-item">
					<p class="nav-link data_category_${event_id}" id = "data-category-${event_id}-${item}" category_name="${item}" event_id="${event_id}" home="${home}" away="${away}" style="cursor:pointer">${item}</p>
				  </li>`;
		}
	}
	str += `</ul>`;
	
	jQuery(`#div_tabs_${event_id}`).html(str);
	jQuery(`#data-category-${event_id}-${lastKey}`).addClass("active");
	setOddsData(event_id,home,away,lastKey);
}

function setOddsData(event_id,home,away,key)
{
	console.log(betOdds);
	let str = ``;
	let oddData = betOdds[event_id][key]['sp'];
	for (const item in oddData) {
		
  		console.log(item)
		str += `<div class="cat-containerR" style="background-color:#585858; color: #fff ; border-top: 2px solid #367a65;"> 
					<div style="display: flex;">
						<span class="item-title-list" style="padding:15px;font-weight:700;width: 95%;font-size: 16px;cursor:pointer" data-toggle="collapse" data-target=#${event_id+oddData[item]['id']}> 
							${oddData[item]['name']} 
						</span>
						${oddData[item]['odds'].length > 0 ? `<span class="item-content-list"><span>` : ``} 
					</div>	
					<div class="cat-wrapperR collapse" style="width:100%; margin-left: 0px; margin-bottom: 0px;" role="group" aria-label="Basic example" id=${event_id+oddData[item]['id']}>
						<div class="cat-container" style="width:100%">`;
		if((oddData[item]['name'] === "Full Time Result") || (oddData[item]['name'] === "To Qualify") || (oddData[item]['name'] === "Full Time Result – Enhanced Prices") || (oddData[item]['name'] === "10 Minute Result") || (oddData[item]['name'] === "Draw No Bet") || (oddData[item]['name'] === "Half Time Result") || (oddData[item]['name'] === "Race to 20 Points"))
		{
			for(var i = 0 ; i < oddData[item]['odds'].length ; i++)
			{
				let name = "";
				if(oddData[item]['odds'][i]['name'] === '1')
				{
						name = home;
				}else if(oddData[item]['odds'][i]['name'] === '2' || oddData[item]['odds'][i]['name'] === "two")
				{
					name = away;		
				}else{
					name = "Draw";
				}
				str += `<div class="j-option-wrapper" style="width:33.33%;">
				  <span class="btn btn-third bet-option-btn col" style="width:100%;" event_id = "${event_id}" id="bet-option-btn-${oddData[item]['odds'][i]['id']}" odd_id = "${oddData[item]['odds'][i]['id']}" price="${oddData[item]['odds'][i]['odds']}" bet="${name}" bet_name = "${oddData[item]['name']}">
					${name}
					<span style="color:yellow"> ${oddData[item]['odds'][i]['odds']}</span>
				</span>	</div>`;
			} 
		}
		else if((oddData[item]['name'] === "1st Half Money Line 3-Way") || (oddData[item]['name'] === "2rd Half Money Line 3-Way"))
		{
			for(var i = 0 ; i < oddData[item]['odds'].length ; i++)
			{
				let name = "";
				if(oddData[item]['odds'][i]['name'] === '1')
				{
					name = home;
				}else if(oddData[item]['odds'][i]['name'] === '2' || oddData[item]['odds'][i]['name'] === "two")
				{
					name = away;		
				}else{
					name = "Tie";
				}
				str += `<div class="j-option-wrapper" style="width:33.33%;">
				  <span class="btn btn-third bet-option-btn col" style="width:100%;" event_id = "${event_id}" id="bet-option-btn-${oddData[item]['odds'][i]['id']}" odd_id = "${oddData[item]['odds'][i]['id']}" price="${oddData[item]['odds'][i]['odds']}" bet="${name}" bet_name = "${oddData[item]['name']}">
					${name}
					<span style="color:yellow"> ${oddData[item]['odds'][i]['odds']}</span>
				</span>	</div>`;
			}
		}else{
			for(var i = 0 ; i < oddData[item]['odds'].length ; i++)
			{
				let header = oddData[item]['odds'][i]['header'];
				let id = oddData[item]['odds'][i]['id'];
				let name = oddData[item]['odds'][i]['name'];
				let handicap = oddData[item]['odds'][i]['handicap'];
				let odds = oddData[item]['odds'][i]['odds'];
				if(header === '1')
				{
					header = home;
				}else if(header === '2' || header === "two")
				{
					header = away;		
				}
				
				
				if(header)
				{
					str += `<div class="col" style="width:auto;">
						${header}
						<hr style"color:green; height:1px">
						<span class="btn btn-secondary bet-option-btn" style="width:100%;" event_id = "${event_id}" id="bet-option-btn-${oddData[item]['odds'][i]['id']}" odd_id = "${oddData[item]['odds'][i]['id']}" price="${oddData[item]['odds'][i]['odds']}" bet="${oddData[item]['odds'][i]['name']+" - "+header+" "+handicap}" bet_name = "${oddData[item]['name']}">`;		
				}else{
					str += `<div class="col" style="width:auto;">
						<span class="btn btn-secondary bet-option-btn col" style="width:100%;" event_id = "${event_id}" id="bet-option-btn-${oddData[item]['odds'][i]['id']}" odd_id = "${oddData[item]['odds'][i]['id']}" price="${oddData[item]['odds'][i]['odds']}" bet="${oddData[item]['odds'][i]['name']}" bet_name = "${oddData[item]['name']}">`;
				}
				
				str += `
					<span style="display:block">${oddData[item]['odds'][i]['name'] ? oddData[item]['odds'][i]['name'] : ''+" - "+header+" "+handicap} <span style="color:yellow"> ${oddData[item]['odds'][i]['odds']}</span></span>
				</span>	</div>`;
			}
		}
	

		str += `</div>
			</div>
		</div>`;
		
	}
	
	
	jQuery(`#div_data_${event_id}`).html(str);
	setSelectedBetsToBlue();
	jQuery('.card .card-body .cat-containerR').on('click', '.item-title-list', function (response) {
		if(jQuery('.item-content-list').attr('class') == 'item-content-list'){
			jQuery(this).next().attr('class', 'item-content-list show');
			jQuery(this).next().css('visibility', 'hidden');
		}else{
			jQuery(this).next().attr('class', 'item-content-list');
			jQuery(this).next().css('visibility', 'visible');
		}
	});
}

function setSelectedBetsToBlue()
{
	jQuery(".bet-option-btn").css("background-color", "#585858");
	if(bet_selected != null)
	{
		bet_selected.forEach(function(arrayItem){
			let id = "#bet-option-btn-" + arrayItem['bet_option_id'];
			console.log(id);
			jQuery(`#bet-option-btn-${arrayItem['bet_option_id']}`).css("background-color", "#6c757d");
		});
	}
}

function updateBetDiv()
{
		var previousData = ``;
		if(bet_selected != null)
		{
			bet_selected.forEach(function (arrayItem) {
					previousData += `		   <div class="bet-option-slip-wrapper">
								<div class="slip-bet-event-name">
									${arrayItem.event}
								</div>
								<div class="slip-delete-option" onClick= deleteSlip("${arrayItem.bet_option_id}") >
									<b style="cursor: pointer;">X</b>
								</div>
								<div class="clear"></div>
								<div class="slip-bet-cat-name">
									${arrayItem.match}
									
								</div>
								<div class="slip-bet-odd" id="">
									Odd: ${arrayItem.price}
								</div>
								<div class="slip-bet-name">
								   ${arrayItem.bet_name}
									<p>${arrayItem.bet}</p>
								</div>
								<div class="clear"></div>
							</div>`;
				});
			
			
			}else{
				bet_selected = [];
			}
	
		
			if(previousData != ``)
			{
				jQuery('.bets-holder').html(previousData);	
				slipCount = 1;
			}else{
				jQuery('.bets-holder').html(`<div class="info-holder">
    		Your betting slip is empty.    
			</div>`);	
				bet_selected = [];
			}
		setSelectedBetsToBlue();
		console.log(bet_selected);
}


function deleteSlip(bet_option_id)
{
	removeByAttr(bet_selected,"bet_option_id",bet_option_id);
	localStorage.setItem("slips_json", JSON.stringify(bet_selected));
	updateBetDiv();
}

