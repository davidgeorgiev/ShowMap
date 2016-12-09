<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function RefreshTimeZoneField(){
	global $wpdb;
	$mywhere = "";
	if(isset($_GET["searchtimezone"])){
		$mywhere = 'WHERE timezone LIKE "%'.$_GET["searchtimezone"].'%"';
	}
	$mysqlquery = "SELECT DISTINCT timezone FROM map_points ".$mywhere." ORDER BY timezone ASC;";
	//echo $mysqlquery;
	$myrows = $wpdb->get_results($mysqlquery);
	echo '
	<label for="text">timezones: '.count($myrows).' </label>';
	echo '<select id="timezone" name="timezone">';
	echo '<option selected="selected" value="0">Select a timezone</option>';
		foreach($myrows as $key => $row) {
			echo '<option value="'.$row->timezone.'">'.$row->timezone.'</option>';
		}	
			
	echo '</select>
	';
}
RefreshTimeZoneField();

?>
