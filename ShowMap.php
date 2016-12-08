<?php
/*
Plugin Name: ShowMap
Description: Don't use this plugin because this is my first experiment and I don't know what I'm doing because nobody told me how to make a wordpress plugin. Use it at your own risk. Have a nice day, dear wordpress user!
Author: David Georgiev
Version: 2.0
*/

//add_action('init','show_my_google_map');


function get_lat_lng_from_nekudo_please($firstdigit,$seconddigit){
	$server_error = 0;
	$index = 0;
	$url = 'http://geoip.nekudo.com/api/'.$firstdigit.'.'.$seconddigit.'.1.1';
	$jsondata = file_get_contents($url);
	$myarray = (json_decode($jsondata, true));
	
	if (is_array($myarray) || is_object($myarray)){
		foreach($myarray as $key => $row1) {
			if (is_array($row1) || is_object($row1)){
				foreach($row1 as $key => $row2) {
					$index++;
					if($index == 4){
						$latitude = $row2;
					}
					if($index == 5){
						$longitude = $row2;
					}
					//echo '<p>'.$index.' '.$row2.'</p>';
				}
			}
		}
	}
	if($latitude&&$longitude){
		return array($latitude,$longitude);
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
	$myrows = $wpdb->get_results("CREATE TABLE map_points(map_point_id int,lat float,lng float);");
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
				//echo '<div><h1>Point: '.$counter.'</h1>';
				//echo '<p>'.$lat.'</p>';
				//echo '<p>'.$lng.'</p></div>';
				$myrows = $wpdb->get_results("INSERT INTO map_points (map_point_id ,lat, lng) VALUES(".$counter.",".$lat.",".$lng.")");
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

function show_my_google_map($minid,$maxid){
	if($minid==0){
		$minid=1;
	}
	if($maxid==0){
		$maxid=10000;
	}
	global $wpdb;
	$mysqlquery = "SELECT map_point_id ,lat, lng FROM map_points WHERE map_point_id <= ".$maxid." AND map_point_id >= ".$minid.";";
	$myrows = $wpdb->get_results($mysqlquery);

	$myapikey = 'your api key here';
	if(empty($myrows)){
		ShowErronNoPointsInThisInterval();
	}
	echo '
	<style>
      #map {
        height: 400px;
        width: 100%;
       }
    </style>
    <div id="map"></div>
    <script>
      function initMap() {';
      	foreach($myrows as $key => $row) {
			echo 'var uluru'.$row->map_point_id.' = {lat: '.$row->lat.', lng: '.$row->lng.'};';
		}
	echo'
        var map = new google.maps.Map(document.getElementById("map"), {
          zoom: 1,
          center: uluru'.$minid.'
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
function mainGoogleMapDavidsPlugin($reset,$search,$show){
	if($reset){
		reset_tables();
	}
	if($search){
		search_cities_and_show(255);
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
