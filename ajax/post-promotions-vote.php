<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/checkUserVotePromotion.php';

	$userId = $_SESSION['user_id'];	
	$promotionId = $_POST['promotion_id'];
	$vthumbs = $_POST['vthumbs'];
	
	$date = date("Y-m-d H:i:s");
	
	$stmt = "SELECT COUNT(*) as count FROM promotion_thumbs WHERE user_id = " . $userId . " AND promotion_id = " . $promotionId;
	//echo $stmt . '<br>';
	
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$rowCount = $row['count'];
	
	//echo 'Rows: ' . $rowCount . "<br>";
	
	//user already voted
	if($rowCount == 1){
		
		$stmt = "UPDATE promotion_thumbs SET ";
		$stmt .= "modified='" . $date . "', ";
		$stmt .= "vthumbs=" . $vthumbs;
		$stmt .= " WHERE user_id = " . $userId . " AND promotion_id = " . $promotionId;
		
		$result = $conn->query($stmt);
		
	//user didn't vote yet
	} else {
		
		$stmt = "INSERT INTO promotion_thumbs(user_id, promotion_id, created, vthumbs) VALUES (";
		$stmt .= $userId . ", ";
		$stmt .= $promotionId . ", ";
		$stmt .= "'" . $date . "', ";
		$stmt .= $vthumbs . ")";
		
		$result = $conn->query($stmt);
		
	}
	
	//get thumbs up from promotions votes log
	$stmt = "SELECT COUNT(*) as count FROM promotion_thumbs WHERE vthumbs = 1 AND promotion_id = " . $promotionId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$thumbs_up = $row['count'];
	
	//get thumbs down from promotions votes log
	$stmt = "SELECT COUNT(*) as count FROM promotion_thumbs WHERE vthumbs = -1 AND promotion_id = " . $promotionId;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$thumbs_down = $row['count'];	
	
	//update promotions with thumbs
	//echo "Likes: " . $thumbs_up . "   Dislikes: " . $thumbs_down . "<br>";
	//echo "This user vote value: " . checkUserVotePromotion($conn, $userId, $promotionId) . "<br>";
	$stmt = "UPDATE promotions SET ";
	$stmt .= "thumbs_up=" . $thumbs_up . ", ";
	$stmt .= "thumbs_down=" . $thumbs_down;
	$stmt .= " WHERE id = " . $promotionId;
	
	$result = $conn->query($stmt);
	
	//echo $stmt . "<br>";
	
	$response = array("thumbsUp" => $thumbs_up, "thumbsDown" => $thumbs_down);
	echo json_encode($response);
}
?>