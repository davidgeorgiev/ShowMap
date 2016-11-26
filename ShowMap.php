<?php
/*
Plugin Name: ShowMap
Description: Don't use this plugin because this is my first experiment and I don't know what I'm doing because nobody told me how to make a wordpress plugin. Use it at your own risk. Have a nice day, dear wordpress user!
Author: David Georgiev
Version: 0.0.0.1
*/

add_action('init','show_google_map');

function show_google_map(){
	$myapikey = '';
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
      function initMap() {
        var uluru = {lat: -25.363, lng: 131.044};
        var map = new google.maps.Map(document.getElementById("map"), {
          zoom: 4,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key='.$myapikey.'&callback=initMap">
    </script>';
}
?>
