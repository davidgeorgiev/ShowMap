<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function LoadThirdChoice(){
	
	global $wpdb;
	$country = "";
	if($_GET["country"]!="0"){
		$country = "WHERE country_name = \"".$_GET["country"]."\"";
	}
	$mysqlquery = "SELECT DISTINCT city_name FROM map_points ".$country." ORDER BY city_name ASC;";
	//echo $mysqlquery;
	$myrows = $wpdb->get_results($mysqlquery);
	echo '<label for="text">cities: '.count($myrows).' </label>';
	echo '<select id="city" name="city">';
	echo '<option selected="selected" value="0">Select a city</option>';
		foreach($myrows as $key => $row) {
			echo '<option value="'.$row->city_name.'">'.$row->city_name.'</option>';
		}
		
	echo '</select>';
}
LoadThirdChoice();
?>
