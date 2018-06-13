<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';

	$userId = $_SESSION['user_id'];

	$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE to_user = ?");
	$stmt->bind_param('i', $userId);
	$stmt->execute();
	
}
?>