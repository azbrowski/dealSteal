<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/sendMessage.php';

	$userId = $_SESSION['user_id'];
	$userRole = $_SESSION['user_role'];
	
	$promotionId = $_POST['promotion_id'];
	
	if(trim($userRole) == 'moderator' || trim($userRole) == 'administrator'){
		//get user ID which will get notification that promotion was deleted
		$stmt = "SELECT user_id, title FROM promotions WHERE id = " . $promotionId;
		$result = $conn->query($stmt);
		$row = $result->fetch_assoc();
		$toUser = $row['user_id'];
		$promotionTitle = $row['title'];	
		
		$stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
		$stmt->bind_param('i', $promotionId);
		$stmt->execute();
		
		//send message
		if(strlen($promotionTitle) > 16)
			$promotionTitle = substr($promotionTitle, 0, 16) . "...";
		
		sendMessage($conn, $userId, $toUser, "Niestety, twoja promocja '" . $promotionTitle . "' nie spełniła wymaganych kryteriów i została usunięta.");	
	}
	
}
?>