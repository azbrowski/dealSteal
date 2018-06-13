<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Edytuj powiadomienie';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}		
	
	if(isset($_GET) & !empty($_GET)){
		$updateid = $_GET['id'];
	}	

	if(isset($_POST) & !empty($_POST)){
		$ident = !empty($_POST['updating_id']) ? trim($_POST['updating_id']) : null;
		$is_read = 0;
		$created = date("Y-m-d H:i:s");
		$message = $_POST['message'];
		$from_user = $_POST['from_user'];
		$to_user = $_POST['to_user'];
		$stmt = $conn->prepare("UPDATE messages SET from_user = ?, to_user = ?, content = ?, created = ? WHERE id = ?");
		$stmt->bind_param("iissi", $from_user, $to_user, $message, $created, $ident);
		$stmt->execute();
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
			<?php
					$stmt = $conn->prepare("SELECT from_user, to_user, content FROM messages WHERE id = ? ");
					$stmt->bind_param("i", $updateid);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($from_user, $to_user, $content);
					$stmt->fetch();
			?>
					
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
						echo "<option value='" . $userId ."' ".(($userId==$from_user)?'selected="selected"':"").">" . $login . "</option>";
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
						echo "<option value='" . $userId ."' ".(($userId==$to_user)?'selected="selected"':"").">" . $login . "</option>";
						}
					?>
					</select>
				</div>
			</div>
			
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Treść</label>
			    <div class="col-sm-10">
			      <textarea type="text" name="message"  class="form-control" id="input1" placeholder="Treść"><?php echo $content;?></textarea>
				  <input type="hidden" name="updating_id"  value="<?php echo $updateid; ?>"/>
			    </div>
			</div>		

			<div class="col-sm-2"></div>
			<input type="submit" class="btn btn-primary" value="Edytuj" />
			<a href="show_messages.php" class="btn btn-warning">Anuluj</a>
		</form>
		</div>
	</div>
</div>
</body>
</html>