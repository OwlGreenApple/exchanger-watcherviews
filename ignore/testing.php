<?php
	echo "PHP works!<br/><br/>";
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	
	// Create connection
	$conn = mysqli_connect($servername, $username, $password);
	
	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected to MYSQL successfully";


?> 