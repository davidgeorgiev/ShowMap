<?php
/*
Plugin Name: ShowMap
Description: Don't use this plugin because this is my first experiment and I don't know what I'm doing because nobody told me how to make a wordpress plugin. Use it at your own risk. Have a nice day, dear wordpress user!
Author: David Georgiev
Version: 3.5
*/

define("GOOGLEAPIKEY", "your google api key");
error_reporting(E_ALL);
ini_set('display_errors', 1);

function get_lat_lng_from_nekudo_please($firstdigit,$seconddigit){
	$country = 0;
	$latitude = 0;
	$longitude = 0;
	$timezone = 0;
	$city = 0;
	
	$server_error = 0;
	$index = 0;
	$index2 = 0;
	$url = 'http://geoip.nekudo.com/api/'.$firstdigit.'.'.$seconddigit.'.1.1';
	$jsondata = file_get_contents($url);
	$myarray = (json_decode($jsondata, true));
	
	
	if (is_array($myarray) || is_object($myarray)){
		foreach($myarray as $key => $row1) {
			if (is_array($row1) || is_object($row1)){
				foreach($row1 as $key => $row2) {
					$index++;
					if($index == 1){
						$country = $row2;
					}
					if($index == 4){
						$latitude = $row2;
					}
					if($index == 5){
						$longitude = $row2;
					}
					if($index == 6){
						$timezone = $row2;
					}
					//echo '<p>'.$index.' '.$row2.'</p>';
				}
			}else{
				$index2++;
				if($index2 == 1){
					$city = $row1;
				}
				//echo "<p>".$row1."</p>";
			}
		}
	}
	if($latitude&&$longitude){
		if(!$city){
			$city = "_unknown_";
		}
		if(!$country){
			$country = "_unknown_";
		}
		if(!$timezone){
			$timezone = "_unknown_";
		}
		return array($latitude,$longitude,$country,$city,$timezone);
	}else{
		return -1;
	}
}
function get_lat_and_lng_from_freegeoip($firstdigit,$seconddigit){
	$server_error = 0;
	$url = 'http://freegeoip.net/xml/'.$firstdigit.'.'.$seconddigit.'.1.1';
	$myFileContents = file_get_contents($url);
	$xml = new SimpleXMLElement($myFileContents);
	if(($xml->Latitude!=0)&&($xml->Longitude!=0)){
		return array($xml->Latitude,$xml->Longitude);
	}else{
		return -1;
	}
}

function reset_tables(){
	global $wpdb;
	$myrows = $wpdb->get_results("DROP TABLE map_points;");
	$myrows = $wpdb->get_results("DROP TABLE map_points_last;");
	$myrows = $wpdb->get_results("CREATE TABLE map_points(map_point_id int,country_name varchar(64),city_name varchar(64),timezone varchar(64),lat float,lng float);");
	$myrows = $wpdb->get_results("CREATE TABLE map_points_last(last_max_counter int,last_i int, last_j int);");
	$myrows = $wpdb->get_results("INSERT INTO map_points_last (last_max_counter ,last_i, last_j) VALUES(1,1,1);");
}

function search_cities_and_show($maxnum){
	global $wpdb;
	$counter = 0;
	$myrows = $wpdb->get_results("SELECT last_max_counter, last_i, last_j FROM map_points_last;");
	foreach($myrows as $key => $row) {
		$counter = $row->last_max_counter;
		$i = $row->last_i;
		$j = $row->last_j;
	}
	//$myrows = $wpdb->get_results("TRUNCATE TABLE map_points;");
	while($i <= $maxnum){
		while($j <= $maxnum){
			$lat_and_lng = get_lat_lng_from_nekudo_please($i,$j);
			if($lat_and_lng!=-1){
				$lat = $lat_and_lng[0];
				$lng = $lat_and_lng[1];
				$country = $lat_and_lng[2];
				$city = $lat_and_lng[3];
				$timezone = $lat_and_lng[4];
				//echo '<div><h1>Point: '.$counter.'</h1>';
				//echo '<p>'.$country.'</p>';
				//echo '<p>'.$city.'</p>';
				//echo '<p>'.$timezone.'</p>';
				//echo '<p>'.$lat.'</p>';
				//echo '<p>'.$lng.'</p>';
				//echo '</div>';
				$myrows = $wpdb->get_results("INSERT INTO map_points (map_point_id,country_name,city_name,timezone,lat,lng) VALUES(".$counter.",\"".$country."\",\"".$city."\",\"".$timezone."\",".$lat.",".$lng.")");
				$myrows = $wpdb->get_results("INSERT INTO map_points_last (last_max_counter ,last_i, last_j) VALUES(".$counter.",".$i.",".$j.");");
				$counter++;
			}
			$j++;
		}
		$j=1;
		$i++;
	}
}

function ShowErronNoPointsInThisInterval(){
	echo '<h1>No points found in this interval</h1>';
}

function ShowDropDownMenu(){
	
	echo '<form role="form" action = Simple.php method="post">';
	
	echo '<input id="searchtimezone" type="text" name="searchtimezone" placeholder="Search for timezone">';
	echo '<div id="timezone_div"></div>';
	echo '
	<div id="country_div"></div>
	<div id="city_div"></div>
	';
	/*
	
	
	$mysqlquery = "SELECT DISTINCT city_name FROM map_points;";
	$myrows = $wpdb->get_results($mysqlquery);
	echo '<label for="text">city</label>';
	echo '<select name="city">';
	echo '<option selected="selected" value="0">nothing</option>';
		foreach($myrows as $key => $row) {
			echo '<option value="'.$row->city_name.'">'.$row->city_name.'</option>';
		}	
			
	echo '</select>';
	*/
	echo '<button type="submit">Filter</button></form>';
	echo '<script src="/wp-content/plugins/ShowMap/jquery.min.js"></script>';
	echo'<script>';
	echo '
	
	$(document).ready(function(){
		$("#searchtimezone").on("input",function(){
			$("#timezone_div").load("/wp-content/plugins/ShowMap/TimeZoneRefreshSearch.php?searchtimezone=" + $("#searchtimezone").val());
			$("#country_div").load("/wp-content/plugins/ShowMap/Dynamic_Second_field.php?timezone=" + $("#timezone").val());
			$("#city_div").load("/wp-content/plugins/ShowMap/Dynamic_Third_field.php?timezone=" + $("#timezone").val());
		});
		$("#timezone_div").change(function(){
			$("#country_div").load("/wp-content/plugins/ShowMap/Dynamic_Second_field.php?timezone=" + $("#timezone").val());
			$("#city_div").load("/wp-content/plugins/ShowMap/Dynamic_Third_field.php?timezone=" + $("#timezone").val());
		});
		$("#country_div").change(function(){
			$("#city_div").load("/wp-content/plugins/ShowMap/Dynamic_Third_field.php?timezone=" + $("#timezone").val()+"&country=" + $("#country").val());
		});
	});
	
	';
	echo '</script>';
}

function show_my_google_map($minid,$maxid){
	global $wpdb;
	$filters_are_active = 0;
	$firstid = 0;
	$ifcountry = "";
	$ifcity = "";
	$iftimezone = "";
	$country = "";
	$city = "";
	$timezone = "";
	if($minid==0){
		$minid=1;
	}
	if($maxid==0){
		$mysqlquery = "SELECT * FROM map_points;";
		$myrows = $wpdb->get_results($mysqlquery);
		$maxid=count($myrows);
	}
	
	if(isset($_POST['country'])){
		if($_POST['country']!="0"){
			$filters_are_active = 1;
			$ifcountry = " AND country_name = \"".$_POST['country']."\"";
			$country = " COUNTRY - ".$_POST['country'];
		}
	}
	if(isset($_POST['city'])){
		if($_POST['city']!="0"){
			$filters_are_active = 1;
			$ifcity = " AND city_name = \"".$_POST['city']."\"";
			$city = " CITY - ".$_POST['city'];
		}
	}
	if(isset($_POST['timezone'])){
		if($_POST['timezone']!="0"){
			$filters_are_active = 1;
			$iftimezone = " AND timezone = \"".$_POST['timezone']."\"";
			$timezone = " TIMEZONE - ".$_POST['timezone'];
		}
	}
	if($filters_are_active){
		echo "<h2> Activated filters: </h2><p>".$country."</p><p>".$city."</p><p>".$timezone."</p></h2>";
	}
	$mysqlquery = "SELECT map_point_id ,lat, lng FROM map_points WHERE map_point_id <= ".$maxid." AND map_point_id >= ".$minid.$ifcountry.$ifcity.$iftimezone.";";
	$myrows = $wpdb->get_results($mysqlquery);
	$myapikey = GOOGLEAPIKEY;
	ShowDropDownMenu();
	if(empty($myrows)){
		ShowErronNoPointsInThisInterval();
	}else{
		echo '<style>
		  #map {
		    height: 400px;
		    width: 100%;
		   }
		</style>
		<div id="map"></div>
		<script>
		  function initMap() {';
		  	foreach($myrows as $key => $row) {
		  		if($firstid == 0){
		  			$firstid = $row->map_point_id;
		  		}
				echo 'var uluru'.$row->map_point_id.' = {lat: '.$row->lat.', lng: '.$row->lng.'};';
			}
		echo'
		    var map = new google.maps.Map(document.getElementById("map"), {
		      zoom: 1,
		      center: uluru'.$firstid.'
		    });';
		    foreach($myrows as $key => $row) {
				echo 'var marker = new google.maps.Marker({position: uluru'.$row->map_point_id.',map: map});';
			}
		echo'
		  }
		</script>
		<script async defer
		src="https://maps.googleapis.com/maps/api/js?key='.$myapikey.'&callback=initMap">
		</script>';
    }
}
function mainGoogleMapDavidsPlugin($reset,$search,$show,$maxip){
	if($reset){
		reset_tables();
	}
	if($search){
		search_cities_and_show($maxip);
	}
	if($show){
		if(isset($_GET['minid'])){
			$myminid = $_GET['minid'];
		}else{
			$myminid = 0;
		}
		if(isset($_GET['maxid'])){
			$mymaxid = $_GET['maxid'];
		}else{
			$mymaxid = 0;
		}
		show_my_google_map($myminid,$mymaxid);
	}
}
?>
