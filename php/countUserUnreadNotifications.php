<?php
//returns number of certain user's unread notifications
function countUserUnreadNotifications($conn, $userId){
	
	$stmt = "SELECT COUNT(*) as count FROM messages WHERE is_read = 0 AND to_user = " . $userId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$count = $row['count'];
	
	if(!empty($count))
		return $count;
	return 0;
}
?>