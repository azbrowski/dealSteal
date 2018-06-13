<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Edytuj domenę';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}		
	
	if(isset($_GET) & !empty($_GET)){
		$updateid = $_GET['id'];
	}		
	
	if(isset($_POST) & !empty($_POST)){
		$ident = !empty($_POST['updating_id']) ? trim($_POST['updating_id']) : null;
		$url = !empty($_POST['url']) ? trim($_POST['url']) : null;
		$alias = !empty($_POST['alias']) ? trim($_POST['alias']) : null;
		$banned = $_POST['banned'];
		
		$stmt = $conn->prepare("UPDATE domains SET url = ?, alias = ?, banned = ? WHERE id = ?");
		$stmt->bind_param("ssii", $url, $alias, $banned, $ident);
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
			<?php
					$stmt = $conn->prepare("SELECT url, alias, banned FROM domains WHERE id = ?");
					$stmt->bind_param("i", $updateid);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($url, $alias, $banned);
					$stmt->fetch();
			?>
			    <label for="input1" class="col-sm-2 control-label">URL</label>
			    <div class="col-sm-10">
			      <input type="text" name="url"  class="form-control" id="input1" value="<?php echo $url; ?>" placeholder="<?php echo $url; ?>" />
				  <input type="hidden" name="updating_id"  value="<?php echo $updateid; ?>"/>
			    </div>
			</div>
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Alias</label>
			    <div class="col-sm-10">
			      <input type="text" name="alias"  class="form-control" id="input1" value="<?php echo $alias; ?>" placeholder="<?php echo $alias; ?>" />
			    </div>
			</div>
			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-10">
				<select name="banned" class="form-control">
					<option value="0" <?php echo (($banned==0)?'selected="selected"':""); ?>>Dostępna</option>
					<option value="1" <?php echo (($banned==1)?'selected="selected"':""); ?>>Zablokowana</option>
				</select>
			</div>
			</div>
			
			<div class="col-sm-2"></div>
			<input type="submit" class="btn btn-primary" value="Edytuj" />
			<a href="show_domains.php" class="btn btn-warning">Anuluj</a>
		</form>
		</div>
	</div>
</div>
</body>
</html>