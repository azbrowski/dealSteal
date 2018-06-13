<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Dodaj powiadomienie';

	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}	
	
	if(isset($_POST) & !empty($_POST)){
		$is_read = 0;
		$created = date("Y-m-d H:i:s");
		$message = $_POST['message'];
		$from_user = $_POST['from_user'];
		$to_user = $_POST['to_user'];
		$stmt = $conn->prepare("INSERT INTO messages(from_user, to_user, content, created, is_read) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssi", $from_user, $to_user, $message, $created, $is_read);
		$stmt->execute();
		//print_r($stmt->error_list);
		$stmt->close();
		header("Location: show_messages.php");
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
						<label for="input1" class="col-sm-2 control-label">Od</label>
						<div class="col-sm-10">
							<select name="from_user" class="form-control">
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
						<label for="input1" class="col-sm-2 control-label">Do</label>
						<div class="col-sm-10">
							<select name="to_user" class="form-control">
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
							<label for="input1" class="col-sm-2 control-label">Treść</label>
							<div class="col-sm-10">
								<textarea type="text" name="message"  class="form-control" id="input1" placeholder="Treść" /></textarea>
							</div>
					</div>					
					
					<div class="col-sm-2"></div>
					<input type="submit" class="btn btn-primary" value="Utwórz" />
					<a href="show_messages.php" class="btn btn-warning">Anuluj</a>
				</form>
			</div>
		</div>
	</div>
</body>
</html>