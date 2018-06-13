<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';
	$pageTitle = "Strona Główna";
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	} 
?>
<!DOCTYPE html>
<html>
<?php include 'admin_header.php';?>
<body>
	<div class="wrapper">
		<?php include 'admin_navbar.php';?>
		<div class="content">
		<h2>Witaj w panelu administratora</h2>
		</div>
	</div>
</body>
</html>