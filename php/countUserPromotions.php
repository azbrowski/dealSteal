<?php
//returns count of user promotions
function countUserPromotions($conn, $userId){
	
	$stmt = "SELECT COUNT(*) as count FROM promotions WHERE user_id = " . $userId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$count = $row['count'];
	
	if(!empty($count))
		return $count;
	return 0;
}
?>