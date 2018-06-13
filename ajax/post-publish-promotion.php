<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/sendMessage.php';

	$userId = $_SESSION['user_id'];
	$userRole = $_SESSION['user_role'];
	
	$promotionId = $_POST['promotion_id'];
	$published = $_POST['published'];
	
	if($published == 0) $published = 1;
	else $published = 0;
	
	if(trim($userRole) == 'moderator' || trim($userRole) == 'administrator'){
		$stmt = "UPDATE promotions";
		$stmt .= " SET published=" . $published;
		$stmt .= " WHERE id = " . $promotionId;
		
		$result = $conn->query($stmt);
		
		//get user ID which will get notification that promotion was accepted
		$stmt = "SELECT user_id, title from promotions WHERE id = " . $promotionId;
		$result = $conn->query($stmt);
		$row = $result->fetch_assoc();
		$toUser = $row['user_id'];
		$promotionTitle = $row['title'];
		
		if(strlen($promotionTitle) > 16)
			$promotionTitle = substr($promotionTitle, 0, 16) . "...";
		
		sendMessage($conn, $userId, $toUser, "Twoja promocja '" . $promotionTitle . "' została pomyślnie zatwierdzona!");
		
	}
	
}
?>