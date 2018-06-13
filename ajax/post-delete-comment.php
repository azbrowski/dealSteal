<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';

	$userId = $_SESSION['user_id'];
	$commentId = $_POST['comment_id'];
	
	$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
	$stmt->bind_param('i', $commentId);
	$stmt->execute();
	
}
?>