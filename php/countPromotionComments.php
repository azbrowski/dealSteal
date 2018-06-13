<?php
//returns number of comments for specific promotion
function countPromotionComments($conn, $promotionId){
	
	$stmt = "SELECT COUNT(*) as count FROM comments WHERE promotion_id = " . $promotionId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$count = $row['count'];
	
	if(!empty($count))
		return $count;
	return 0;
}
?>