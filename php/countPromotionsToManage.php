<?php
//returns number of promotions that needs attention from moderators
function countPromotionsToManage($conn){
	
	$stmt = "SELECT COUNT(*) as count FROM promotions WHERE published = 0";
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$count = $row['count'];
	
	if(!empty($count))
		return $count;
	return 0;
}
?>