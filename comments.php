<?php
	require 'config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/getUserRole.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$comment_user_id = $_SESSION['user_id'];
		$promotion_id = $_GET['id'];
		$comment = !empty($_POST['comment']) ? trim($_POST['comment']) : null;
		$created = date("Y-m-d H:i:s");
		$thumb = 1;
		$published = 1;
		
		$stmt = $conn->prepare("INSERT INTO comments(user_id, promotion_id, content, created, thumbs_up, published) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iissii", $comment_user_id, $promotion_id, $comment, $created, $thumb, $published);
		$stmt->execute();
		
		//grab id of added comment
		$lastId =  $conn->insert_id;

		//add vote up for own content
		$stmt = $conn->prepare("INSERT INTO comment_thumbs(user_id, comment_id, created, vthumbs) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("iisi", $comment_user_id, $lastId, $created, $thumb);
		$stmt->execute();
		
		$stmt->close();
		
		header('Location: ' . ROOT_URL . 'comments.php?id=' . $_GET['id']);
		exit();
		
	}	
	
	$stmt = $conn->prepare("SELECT user_id, title, published FROM promotions WHERE id = ?");
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($userId, $title, $published);
	$stmt->fetch();

	//logged user id
	$loggedUser = 0;
	if(isset($_SESSION['user_id'])) $loggedUser = $_SESSION['user_id'];
	$userRole = getUserRole($conn, $loggedUser);

	$err = 0;
	
	if($published == 0){
		$err_msg = "Ta promocja wymaga weryfikacji ze strony moderacji, zanim zostanie upubliczniona.";
		
		if(empty($loggedUser))
			$err = 1;
		
		if($loggedUser != $userId)
			if(trim($userRole) != 'moderator' && trim($userRole) != 'administrator')
				$err = 1;
		
	}

	if(!empty($err)){
		header('Location: index.php');
		exit;
	}

	$pageTitle = $title;

	include RESOURCE_PATH . '/header.php';
	include RESOURCE_PATH . '/popular.php';
	include RESOURCE_PATH . '/comment.php';
	include RESOURCE_PATH . '/footer.php';
?>
