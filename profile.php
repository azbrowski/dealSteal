<?php 
	require __DIR__ . '/config.php';
	require ROOT_PATH . '/connect.php';	
	
	$stmt = $conn->prepare("SELECT login FROM users WHERE login = ?");
	$stmt->bind_param("s", $_GET['u']);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows == 0) {
		header('Location: index.php');
		exit;
	}
	
	$stmt->bind_result($u);
	$stmt->fetch();	
	
	$pageTitle = ucfirst( $u );	
	
	include RESOURCE_PATH . '/header.php'; 
	include RESOURCE_PATH . '/view-profile.php'; 
	include RESOURCE_PATH . '/footer.php'; 
	
?>