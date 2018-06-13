<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/checkUserVoteComment.php';

	$userId = $_SESSION['user_id'];	
	$commentId = $_POST['comment_id'];
	$vthumbs = $_POST['vthumbs'];
	
	$date = date("Y-m-d H:i:s");
	
	$stmt = "SELECT COUNT(*) as count FROM comment_thumbs WHERE user_id = " . $userId . " AND comment_id = " . $commentId;
	//echo $stmt . '<br>';
	
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$rowCount = $row['count'];
	
	//echo 'Rows: ' . $rowCount . "<br>";
	
	//user already voted
	if($rowCount == 1){
		
		$stmt = "UPDATE comment_thumbs SET ";
		$stmt .= "modified='" . $date . "', ";
		$stmt .= "vthumbs=" . $vthumbs;
		$stmt .= " WHERE user_id = " . $userId . " AND comment_id = " . $commentId;
		
		$result = $conn->query($stmt);
		
	//user didn't vote yet
	} else {
		
		$stmt = "INSERT INTO comment_thumbs(user_id, comment_id, created, vthumbs) VALUES (";
		$stmt .= $userId . ", ";
		$stmt .= $commentId . ", ";
		$stmt .= "'" . $date . "', ";
		$stmt .= $vthumbs . ")";
		
		$result = $conn->query($stmt);
		
	}
	
	//get thumbs up from comments votes log
	$stmt = "SELECT COUNT(*) as count FROM comment_thumbs WHERE vthumbs = 1 AND comment_id = " . $commentId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$thumbs_up = $row['count'];
	
	//update comment with thumbs
	//echo "Likes: " . $thumbs_up . "<br>";
	//echo "This user vote value: " . checkUserVoteComment($conn, $userId, $commentId) . "<br>";
	$stmt = "UPDATE comments SET ";
	$stmt .= "thumbs_up=" . $thumbs_up;
	$stmt .= " WHERE id = " . $commentId;
	
	$result = $conn->query($stmt);
	
	//echo $stmt . "<br>";
	
	$response = array("thumbsUp" => $thumbs_up);
	echo json_encode($response);
}
?>