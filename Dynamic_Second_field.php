<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function LoadSecondChoice(){
	global $wpdb;
	$timezone = "";
	if($_GET["timezone"]!="0"){
		$timezone = "WHERE timezone = \"".$_GET["timezone"]."\"";
	}
	$mysqlquery = "SELECT DISTINCT country_name FROM map_points ".$timezone." ORDER BY country_name ASC;";
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
LoadSecondChoice();
?>
