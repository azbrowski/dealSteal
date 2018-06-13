<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Dodaj domenę';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}	
	
	if(isset($_POST) & !empty($_POST)){
	$url = !empty($_POST['url']) ? trim($_POST['url']) : null;
	$alias = !empty($_POST['alias']) ? trim($_POST['alias']) : null;


	$stmt = $conn->prepare("INSERT INTO domains(url, alias, banned) VALUES (?, ?, ?)");
	$stmt->bind_param("ssi", $url, $alias, $_POST['banned']);
	$stmt->execute();
	$stmt->close();
	header("Location: show_domains.php");
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
			    <label for="input1" class="col-sm-2 control-label">WWW</label>
			    <div class="col-sm-10">
			      <input type="text" name="url"  class="form-control" id="input1" placeholder="WWW" />
			    </div>
			</div>
 
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Alias</label>
			    <div class="col-sm-10">
			      <input type="text" name="alias"  class="form-control" id="input1" placeholder="Alias" />
			    </div>
			</div>
 
			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-10">
				<select name="banned" class="form-control">
					<option value="0">Dostępna</option>
					<option value="1">Zablokowana</option>
				</select>
			</div>
			</div>
			
			<div class="col-sm-2"></div>
			<input type="submit" class="btn btn-primary" value="Utwórz" />
			<a href="show_domains.php" class="btn btn-warning">Anuluj</a>
		</form>
			</div>
		</div>
	</div>
</body>
</html>