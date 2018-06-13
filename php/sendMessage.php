<?php
//sends message from user A to user B
function sendMessage($conn, $from, $to, $content){
	
	$created = date("Y-m-d H:i:s");
	
	$stmt = $conn->prepare("INSERT INTO messages(from_user, to_user, content, created) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("iiss", $from, $to, $content, $created);
	$bool = $stmt->execute();
	
	return $bool;
}
?>