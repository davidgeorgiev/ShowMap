<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function RefreshSelectCountry(){
	global $wpdb;
	$mywhere = "";
	$myand = "";
	$ifandneeded = 0;
	$timezone = "";
	if(isset($_GET["timezone"])){
		if(($_GET["timezone"]!="0")&&($_GET["timezone"]!="undefined")){
			$mywhere = " WHERE ";
			$ifandneeded+=1;
			$timezone = " timezone = \"".$_GET["timezone"]."\"";
		}
	}
	$searchcountry = "";
	if(isset($_GET["searchcountry"])){
		if(($_GET["searchcountry"]!="0")&&($_GET["timezone"]!="undefined")){
			$mywhere = " WHERE ";
			$ifandneeded+=1;
			$searchcountry = " country_name LIKE \"%".$_GET["searchcountry"]."%\"";
		}
	}
	if($ifandneeded == 2){
		$myand = " AND ";
	}
	
	$mysqlquery = "SELECT DISTINCT country_name FROM map_points ".$mywhere.$timezone.$myand.$searchcountry." ORDER BY country_name ASC;";
	//echo $mysqlquery;
	$myrows = $wpdb->get_results($mysqlquery);

	echo '<label for="text">countries: '.count($myrows).' </label>';
	echo '<select id="country" name="country">';
	echo '<option selected="selected" value="0">Select a country</option>';
		foreach($myrows as $key => $row) {
			echo '<option value="'.$row->country_name.'">'.$row->country_name.'</option>';
		}
	
	echo '</select>';
}
RefreshSelectCountry();
?>
