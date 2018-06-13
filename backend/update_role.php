<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Edytuj rolę';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}		
	
	if(isset($_GET) & !empty($_GET)){
		$updateid = $_GET['update_id'];
	}		
	
	if(isset($_POST) & !empty($_POST)){
		//print($_POST['role']);
		$ident = !empty($_POST['updating_id']) ? trim($_POST['updating_id']) : null;
		//echo $ident;
		$role = !empty($_POST['role']) ? trim($_POST['role']) : null;
		$stmt = $conn->prepare("UPDATE roles SET name = ? WHERE id = ?");
		$stmt->bind_param("si", $role, $ident);
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
			<?php
					$stmt = $conn->prepare("SELECT name FROM roles WHERE id = ? ");
					$stmt->bind_param("i", $updateid);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($name);
					$stmt->fetch();
			?>
			    <label for="input1" class="col-sm-2 control-label">Nazwa</label>
			    <div class="col-sm-10">
			      <input type="text" name="role"  class="form-control" id="input1" value="<?php echo $name; ?>" placeholder="<?php echo $name; ?>" />
				  <input type="hidden" name="updating_id"  value="<?php echo $updateid; ?>"/>
			    </div>
			</div>
			
			<div class="col-sm-2"></div>
			<input type="submit" class="btn btn-primary" value="Edytuj" />
			<a href="show_roles.php" class="btn btn-warning">Anuluj</a>
		</form>
		</div>
	</div>
</div>
</body>
</html>