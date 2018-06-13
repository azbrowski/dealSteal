<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';

	$userId = $_SESSION['user_id'];
	$userRole = $_SESSION['user_role'];
	
	$domainId = $_POST['domain_id'];
	
	if(trim($userRole) == 'moderator' || trim($userRole) == 'administrator'){
		$stmt = $conn->prepare("UPDATE domains SET banned = 1 WHERE id = ?");
		$stmt->bind_param('i', $domainId);
		$stmt->execute();
	}
	
}
?>