<?php
//returns vote value of specific user from specific promotion
function checkUserVotePromotion($conn, $userId, $promotionId){
	
	$stmt = "SELECT vthumbs FROM promotion_thumbs WHERE user_id = " . $userId . " AND promotion_id = " . $promotionId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$vthumbs = $row['vthumbs'];
	
	if(!empty($vthumbs))
		return $vthumbs;
	return 0;
}
?>