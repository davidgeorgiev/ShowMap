<?php
/*
Plugin Name: ShowMap
Description: Don't use this plugin because this is my first experiment and I don't know what I'm doing because nobody told me how to make a wordpress plugin. Use it at your own risk. Have a nice day, dear wordpress user!
Author: David Georgiev
Version: 1.0
*/

//add_action('init','show_my_google_map');

function search_cities_and_show($maxnum){
	global $wpdb;
	$counter = 0;
	$myrows = $wpdb->get_results("TRUNCATE TABLE map_points;");
	for($i = 1;$i <= 100; $i++){
		for($j = 1; $j <= 100; $j++){	
			$url = 'http://freegeoip.net/xml/'.$i.'.'.$j.'.1.1';
			$xml = new SimpleXMLElement(file_get_contents($url));
			if(($xml->Latitude!=0)&&($xml->Longitude!=0)){
				$counter++;
				//echo '<div><h1>Point: '.$counter.'</h1>';
				//echo '<p>'.$xml->Latitude.'</p>';
				//echo '<p>'.$xml->Longitude.'</p></div>';
				$myrows = $wpdb->get_results("INSERT INTO map_points (map_point_id ,lat, lng) VALUES(".$counter.",".$xml->Latitude.",".$xml->Longitude.")");
			}
		}
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
