<?php
//returns user points for comments
function getUserCommentPoints($conn, $id){
	
	$stmt = $conn->prepare("SELECT SUM(comment_thumbs.vthumbs) AS sum FROM comments LEFT JOIN comment_thumbs ON comments.id = comment_thumbs.comment_id WHERE comments.user_id = ?");
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