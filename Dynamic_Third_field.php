<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../wp-load.php';

function LoadThirdChoice(){
	echo '<input id="searchcity" type="text" name="searchcity" placeholder="Search for city"><br>';
	echo '<div id="selectcity_div"></div>';
	echo'<script>';
	echo '
	
	$(document).ready(function(){
		$("#searchcity").on("input",function(){
			$("#selectcity_div").load("/wp-content/plugins/ShowMap/RefreshSelectCity.php?timezone=" + $("#timezone").val() +"&searchcity="+$("#searchcity").val()+"&country="+$("#country").val());
		});
	});
	
	';
	echo '</script>';
}
LoadThirdChoice();
?>
