<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Dodaj promocję';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
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
		$created = date("Y-m-d H:i:s");
		$thumbs_up = 0;
		$thumbs_down = 0;
		$published = 1;

		$stmt = $conn->prepare("INSERT INTO promotions(user_id, domain_id, title, url, original_price_low, original_price_high, sale_price_low, sale_price_high, created, expired, thumbs_up, thumbs_down, published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iissddddssiii", $user, $domena, $name, $url, $start_price_low, $start_price_high, $sale_price_low, $sale_price_high, $created, $expiring, $thumbs_up, $thumbs_down, $published);
		$stmt->execute();
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
							echo "<option value=" . $domainId .">" . $alias . "</option>";
							}
						?>
						</select>
					</div>
					</div>

					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">Tytuł</label>
							<div class="col-sm-9">
								<input type="text" name="tytul"  class="form-control" id="input1" placeholder="Tytuł" />
							</div>
					</div>

					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">URL</label>
							<div class="col-sm-9">
								<input type="text" name="url"  class="form-control" id="input1" placeholder="URL" />
							</div>
					</div>
					
					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">Regular(low)</label>
							<div class="col-sm-9">
								<input type="number" name="start_price_low" min="0.00" step="0.01" value="0.00"  class="form-control" id="input1" placeholder="0.00" />
							</div>
					</div>
					
					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">Regular(high)</label>
							<div class="col-sm-9">
								<input type="number" name="start_price_high" min="0.00" step="0.01" value="0.00"  class="form-control" id="input1" placeholder="0.00" />
							</div>
					</div>

					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">Sale(low)</label>
							<div class="col-sm-9">
								<input type="number" name="sale_price_low" min="0.00" step="0.01" value="0.00"  class="form-control" id="input1" placeholder="0.00" />
							</div>
					</div>			
					
					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">Sale(high)</label>
							<div class="col-sm-9">
								<input type="number" name="sale_price_high" min="0.00" step="0.01" value="0.00"  class="form-control" id="input1" placeholder="0.00" />
							</div>
					</div>			

					<div class="form-group">
							<label for="input1" class="col-sm-3 control-label">Wygasa</label>
							<div class="col-sm-9">
								<input type="date" name="expiring" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>">
							</div>
					</div>					
					
					<div class="col-sm-3"></div>
					<input type="submit" class="btn btn-primary" value="Utwórz" />
					<a href="show_promotions.php" class="btn btn-warning">Anuluj</a>
				</form>
				
			</div>
		</div>
	</div>
</body>
</html>