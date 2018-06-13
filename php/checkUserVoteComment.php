<?php
//returns vote value of specific user from specific comment
function checkUserVoteComment($conn, $userId, $commentId){
	
	$stmt = "SELECT vthumbs FROM comment_thumbs WHERE user_id = " . $userId . " AND comment_id = " . $commentId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$vthumbs = $row['vthumbs'];
	
	if(!empty($vthumbs))
		return $vthumbs;
	return 0;
}
?>