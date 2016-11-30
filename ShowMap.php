<?php
/*
Plugin Name: ShowMap
Description: Don't use this plugin because this is my first experiment and I don't know what I'm doing because nobody told me how to make a wordpress plugin. Use it at your own risk. Have a nice day, dear wordpress user!
Author: David Georgiev
Version: 0.0.0.1
*/

add_action('init','show_google_map');

function show_google_map(){
	global $wpdb;
	$myrows = $wpdb->get_results("SELECT map_point_id ,lat, lng FROM map_points;");

	$myapikey = 'your api key here';
	echo '
	<style>
      #map {
        height: 400px;
        width: 100%;
       }
    </style>
    <h3>My Google Maps Demo</h3>
    <div id="map"></div>
    <script>
      function initMap() {';
      	foreach($myrows as $key => $row) {
			echo 'var uluru'.$row->map_point_id.' = {lat: '.$row->lat.', lng: '.$row->lng.'};';
		}
	echo'
        var map = new google.maps.Map(document.getElementById("map"), {
          zoom: 12,
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
