<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';

	$userId = $_SESSION['user_id'];
	$messageId = $_POST['message_id'];

	$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
	$stmt->bind_param('i', $messageId);
	$stmt->execute();
	
}
?>