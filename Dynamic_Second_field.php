<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function LoadSecondChoice(){
	
	echo '<input id="searchcountry" type="text" name="searchcountry" placeholder="Search for country"><br>';
	echo '<div id="selectcountry_div"></div>';
	echo'<script>';
	echo '
	
	$(document).ready(function(){
		$("#searchcountry").on("input",function(){
			$("#selectcountry_div").load("/wp-content/plugins/ShowMap/RefreshSelectCountry.php?timezone=" + $("#timezone").val() +"&searchcountry="+$("#searchcountry").val());
		});
	});
	
	';
	echo '</script>';
}
LoadSecondChoice();
?>
