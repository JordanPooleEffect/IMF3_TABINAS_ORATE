<?php 
	$connection = new mysqli('localhost', 'root','','dbtabinasf3');
	
	if (!$connection){
		die (mysqli_error($mysqli));
	}
		
?> 