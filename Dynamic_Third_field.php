<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function LoadThirdChoice(){
	global $wpdb;
	$country = "";
	$timezone = "";
	$whereword = "";
	$andword = "";
	$ifneededand = 0;
	if(isset($_GET["country"])){
		if($_GET["country"]!="0"){
			$whereword = " WHERE ";
			$ifneededand++;
			$country = "country_name = \"".$_GET["country"]."\"";
		}
	}
	if(isset($_GET["timezone"])){
		if($_GET["timezone"]!="0"){
			$whereword = " WHERE ";
			$ifneededand++;
			$timezone = " timezone = \"".$_GET["timezone"]."\"";
		}
	}
	if($ifneededand == 2){
		$andword = " AND ";
	}
	$mysqlquery = "SELECT DISTINCT city_name FROM map_points ".$whereword.$country.$andword.$timezone." ORDER BY city_name ASC;";
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
