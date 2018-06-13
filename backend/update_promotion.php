<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Edytuj promocję';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}		
	
	if(isset($_GET) & !empty($_GET)){
		$updateid = $_GET['id'];
	}	
	if(isset($_POST) & !empty($_POST)){
		$domena = $_POST['domena'];
		$user = $_POST['user'];
		$name = $_POST['tytul'];
		$url = $_POST['url'];
		$start_price_low = $_POST['start_price_low'];
		$start_price_high = $_POST['start_price_high'];
		$sale_price_low = $_POST['sale_price_low'];
		$sale_price_high = $_POST['sale_price_high'];
		$expiring = $_POST['expiring'];

		$stmt = $conn->prepare("UPDATE promotions SET user_id = ?,  domain_id = ?, title = ?, url = ?, original_price_low = ?, original_price_high = ?, sale_price_low = ?, sale_price_high = ?, expired = ? WHERE id = ?");
		$stmt->bind_param("iissddddsi", $user, $domena, $name, $url, $start_price_low, $start_price_high, $sale_price_low, $sale_price_high, $expiring, $_POST['updating_id']);
		$stmt->execute();
		//print_r($stmt->error_list);
		$stmt->close();
		header("Location: show_promotions.php");
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
			
				<?php
						$stmt = $conn->prepare("SELECT title, domain_id, url, original_price_low, original_price_high, sale_price_low, sale_price_high, expired FROM promotions WHERE id = ?");
						$stmt->bind_param("i", $updateid);
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($title, $selectedDomain, $url, $original_price_low, $original_price_high, $sale_price_low, $sale_price_high, $expired);
						$stmt->fetch();
				?>
				<div class="form-group">
				<label for="input1" class="col-sm-3 control-label">User</label>
				<div class="col-sm-9">
					<select name="user" class="form-control">
					<?php
						$stmt = $conn->prepare("SELECT id, login FROM users");
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($userId, $login);
						 
						while($stmt->fetch()){
						echo "<option value=" . $userId .">" . $login . "</option>";
						}
					?>
					</select>
				</div>
				</div>
				
				<div class="form-group">
				<label for="input1" class="col-sm-3 control-label">Domena</label>
				<div class="col-sm-9">
					<select name="domena" class="form-control">
					<?php
						$stmt = $conn->prepare("SELECT id, alias FROM domains WHERE alias IS NOT NULL AND banned = 0");
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($domainId, $alias);
						 
						while($stmt->fetch()){
						echo "<option value='" . $domainId ."' " .(($domainId==$selectedDomain)?'selected="selected"':""). ">" . $alias . "</option>";
						}
					?>
					</select>
				</div>
				</div>
	 
				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">Tytuł</label>
						<div class="col-sm-9">
							<input type="text" name="tytul"  class="form-control" id="input1" value="<?php echo $title;?>" placeholder="Tytuł" />
						<input type="hidden" name="updating_id"  value="<?php echo $updateid; ?>"/>
						</div>
				</div>
	 
				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">URL</label>
						<div class="col-sm-9">
							<input type="text" name="url"  class="form-control" id="input1" value="<?php echo $url;?>" placeholder="URL" />
						</div>
				</div>
				
				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">Regular(low)</label>
						<div class="col-sm-9">
							<input type="number" name="start_price_low" min="0.00" step="0.01" value="0.00"  class="form-control" value="<?php echo $original_price_low;?>" id="input1" placeholder="0.00" />
						</div>
				</div>
				
				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">Regular(high)</label>
						<div class="col-sm-9">
							<input type="number" name="start_price_high" min="0.00" step="0.01" value="0.00"  class="form-control" value="<?php echo $original_price_high;?>" id="input1" placeholder="0.00" />
						</div>
				</div>

				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">Sale(low)</label>
						<div class="col-sm-9">
							<input type="number" name="sale_price_low" min="0.00" step="0.01" value="0.00"  class="form-control" value="<?php echo $sale_price_low;?>" id="input1" placeholder="0.00" />
						</div>
				</div>			
				
				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">Sale(high)</label>
						<div class="col-sm-9">
							<input type="number" name="sale_price_high" min="0.00" step="0.01" value="0.00"  class="form-control" value="<?php echo $sale_price_high;?>" id="input1" placeholder="0.00" />
						</div>
				</div>			

				<div class="form-group">
						<label for="input1" class="col-sm-3 control-label">Wygasa</label>
						<div class="col-sm-9">
							<input type="date" name="expiring" min="<?php echo date("Y-m-d"); ?>" value="<?php echo $expired; ?>">
						</div>
				</div>
				
				<div class="col-sm-3"></div>
				<input type="submit" class="btn btn-primary" value="Edytuj" />
				<a href="show_promotions.php" class="btn btn-warning">Anuluj</a>
			</form>
	</div>
</div>
</body>
</html>