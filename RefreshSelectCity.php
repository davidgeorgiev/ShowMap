<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function RefreshSelectCity(){
	global $wpdb;
	$country = "";
	$timezone = "";
	$searchcity = "";
	$whereword = "";
	
	$ifcountry = 0;
	$iftimezone = 0;
	$ifsearchcity = 0;
	if(isset($_GET["country"])){
		if(($_GET["country"]!="0")&&($_GET["country"]!="undefined")){
			$whereword = " WHERE 1=1";
			$country = " AND country_name = \"".$_GET["country"]."\"";
		}
	}
	if(isset($_GET["timezone"])){
		if(($_GET["timezone"]!="0")&&($_GET["timezone"]!="undefined")){
			$whereword = " WHERE 1=1";
			$timezone = " AND timezone = \"".$_GET["timezone"]."\"";
		}
	}
	if(isset($_GET["searchcity"])){
		if(($_GET["searchcity"]!="0")&&$_GET["searchcity"]!="undefined"){
			$whereword = " WHERE 1=1";
			$searchcity = " AND city_name LIKE \"%".$_GET["searchcity"]."%\"";
		}
	}
	
	$mysqlquery = "SELECT DISTINCT city_name FROM map_points ".$whereword.$country.$timezone.$searchcity." ORDER BY city_name ASC;";
	$myrows = $wpdb->get_results($mysqlquery);
	
	echo '<label for="text">cities: '.count($myrows).' </label>';
	echo '<select id="city" name="city">';
	echo '<option selected="selected" value="0">Select a city</option>';
		foreach($myrows as $key => $row) {
			echo '<option value="'.$row->city_name.'">'.$row->city_name.'</option>';
		}
		
	echo '</select>';
}
RefreshSelectCity();
?>
