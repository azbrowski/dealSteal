<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Dodaj rolę';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}	
	
	if(isset($_POST) & !empty($_POST)){
		//print($_POST['role']);
		
		$role = !empty($_POST['role']) ? trim($_POST['role']) : null;
		$stmt = $conn->prepare("INSERT INTO roles(name) VALUES (?)");
		$stmt->bind_param("s", $role);
		$stmt->execute();
		$stmt->close();
		header("Location: show_roles.php");
	}
	
?>
<!DOCTYPE html>
<?php include 'admin_header.php';?>
<body>
	<div class="wrapper">

		<?php include 'admin_navbar.php';?>
		
		<div class="content">
			<div class="row">
				<form method="post" class="form-horizontal col-md-6 col-md-offset-3">
				<h2><?php echo $pageTitle; ?></h2>
					<div class="form-group">
							<label for="input1" class="col-sm-2 control-label">Nazwa</label>
							<div class="col-sm-10">
								<input type="text" name="role"  class="form-control" id="input1" placeholder="Nazwa roli" />
							</div>
					</div>
					
					<div class="col-sm-2"></div>
					<input type="submit" class="btn btn-primary" value="Zatwierdź" />
				</form>
		
			</div>
		</div>
	</div>
</body>
</html>