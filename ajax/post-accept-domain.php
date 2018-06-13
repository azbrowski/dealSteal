<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';

	$userId = $_SESSION['user_id'];
	$userRole = $_SESSION['user_role'];
	
	$domainId = $_POST['domain_id'];
	$name = strtolower($_POST['name']);
	
	if(trim($userRole) == 'moderator' || trim($userRole) == 'administrator'){
		$stmt = $conn->prepare("UPDATE domains SET alias= ? WHERE id = ?");
		$stmt->bind_param('si', $name, $domainId);
		$stmt->execute();
	}
	
}
?>