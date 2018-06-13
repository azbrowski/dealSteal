<?php
//returns user points for promotions
function getUserPromotionPoints($conn, $id){
	
	$stmt = $conn->prepare("SELECT SUM(promotion_thumbs.vthumbs) AS sum FROM promotions LEFT JOIN promotion_thumbs ON promotions.id = promotion_thumbs.promotion_id WHERE promotions.user_id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->store_result();
	
	$stmt->bind_result($sum);
	$stmt->fetch();
	
	if(!empty($sum))
		return $sum;
	return 0;
}
?>