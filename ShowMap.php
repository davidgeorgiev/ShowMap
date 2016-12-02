<?php
/*
Plugin Name: ShowMap
Description: Don't use this plugin because this is my first experiment and I don't know what I'm doing because nobody told me how to make a wordpress plugin. Use it at your own risk. Have a nice day, dear wordpress user!
Author: David Georgiev
Version: 1.0
*/

//add_action('init','show_my_google_map');

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
			$url = 'http://freegeoip.net/xml/'.$i.'.'.$j.'.1.1';
			$xml = new SimpleXMLElement(file_get_contents($url));
			if(($xml->Latitude!=0)&&($xml->Longitude!=0)){
				$counter++;
				//echo '<div><h1>Point: '.$counter.'</h1>';
				//echo '<p>'.$xml->Latitude.'</p>';
				//echo '<p>'.$xml->Longitude.'</p></div>';
				$myrows = $wpdb->get_results("INSERT INTO map_points_last (last_max_counter ,last_i, last_j) VALUES(".$counter.",".$i.",".$j.");");
				$myrows = $wpdb->get_results("INSERT INTO map_points (map_point_id ,lat, lng) VALUES(".$counter.",".$xml->Latitude.",".$xml->Longitude.")");
			}
			$j++;
		}
		$j=1;
		$i++;
	}
}

function show_my_google_map(){
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT map_point_id ,lat, lng FROM map_points;");

	$myapikey = 'your google api key here';
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
          zoom: 7,
          center: uluru1
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
?>
