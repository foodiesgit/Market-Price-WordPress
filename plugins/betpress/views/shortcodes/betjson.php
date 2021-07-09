<?php
global $wpdb;

$page = 1;
$perPage = 0;
$total  = 0;
if(isset($_GET['getPage']))
{
	$page = intval($_GET['getPage']);
}


//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">


<?php

$curl = curl_init();
// echo "page : ".$page;
curl_setopt_array($curl, [
	CURLOPT_URL => "https://api.b365api.com/v1/bet365/upcoming?sport_id=18&token=87521-RYCHtUfNcS5XQM&per_page=100&page=".$page,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"x-rapidapi-host: betsapi2.p.rapidapi.com",
		"x-rapidapi-key: dd691c4e3cmsh8e8242a6dce1bebp1e7e24jsn7c992a6f0e33"
	],
]);



$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	$response = json_decode($response,true);
	
// 	print_r($response);
	
}
?>
<?php
global $wpdb;
$user_ID = get_current_user_id();
$check = $wpdb->get_results('SELECT bet_options_ids FROM ' . $wpdb->prefix . 'bp_slips where status = "unsubmitted" AND user_id = ' . $user_ID);
$str_val = $check[0]->bet_options_ids;
$timezone = get_user_meta($user_ID, 'gmt_offset', true);
?>

<?php
// if(!$response['success'] == 1)
// {
// 		wp_die();
// }
// 

	$page = $response['pager']['page'];
	$perPage = $response['pager']['per_page'];
	$total = $response['pager']['total'];

	$remaining  = $total - ($page*$perPage);
?>
<nav aria-label="Page navigation example">
	
	<ul class="pagination">
			<?php  if(($page*$perPage) > $perPage){ ?>
			 	<li class="page-item"><a class="page-link"  href="https://passport.betcurry.com/?getPage=<?php echo $page-1 ?>">Previous page</a></li>
			<?php } ?>
			
			<?php if($remaining > 0){ ?>
				<li class="page-item"><a class="page-link"  href="https://passport.betcurry.com/?getPage=<?php echo $page+1 ?>">Next page</a></li>
			<?php } ?>
	  </ul>

</nav>

<?php
foreach($response['results'] as $key => $value)
{
	?>
	<div class="card" style="width: 100%;" event_id = "<?php echo $value['id'] ?>" >
	  <div class="card-body" event_name="<?php echo $value['league']['name'] ?>" match = "<?php echo $value['home']['name'] ?> v <?php echo $value['away']['name'] ?>">
		<h5 class="card-title" league_id='<?php echo $value['league']['id'] ?>' style="width:100%;float:left;display:inline" ><?php echo $value['league']['name'] ?></h5>
		  
		  <span class="div_home" style="color:#444;width:100%;float:left;font-size:15px;"><?php echo $value['home']['name'] ?> </span>
		  <span class="div_away" style="color:#444;width:100%;float:left;font-size:15px;"><?php echo $value['away']['name'] ?> </span>
		  
		  <span style="color:#bbb;width:100%;float:left;display:inline;font-size:11px">
			  <?php echo betpress_local_tz_time($value['time']); ?>
		  </span>		  
		  
		  <button data-toggle="collapse" data-target="#div_<?php  echo $value['id']?>" class="btn btn-outline-dark get_odds" event_id = "<?php echo $value['id'] ?>" home="<?php echo $value['home']['name'] ?>" away="<?php echo $value['away']['name'] ?>" >getOdds</button>
		  <div id="div_<?php  echo $value['id']?>" class="collapse">
				<div id="div_tabs_<?php  echo $value['id']?>">
					
			  	</div>
			  	<div id="div_data_<?php  echo $value['id']?>">
					
			  </div>
			</div>
	  </div>
	</div>
	<hr style="height:2px; color:black;">

<?php
}
?>

<nav aria-label="Page navigation example">
	
	<ul class="pagination">
			<?php  if(($page*$perPage) > $perPage){ ?>
			 	<li class="page-item"><a class="page-link"  href="https://passport.betcurry.com/?getPage=<?php echo $page-1 ?>">Previous page</a></li>
			<?php } ?>
			
			<?php if($remaining > 0){ ?>
				<li class="page-item"><a class="page-link"  href="https://passport.betcurry.com/?getPage=<?php echo $page+1 ?>">Next page</a></li>
			<?php } ?>
	  </ul>

</nav>

<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>





















    
