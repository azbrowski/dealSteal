<?php
	$conn = new mysqli(
		$config['db']['host'], 
		$config['db']['user'], 
		$config['db']['pass'], 
		$config['db']['name']);
		
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	if (!isset($_SESSION)) {
		session_start();
	}
?>