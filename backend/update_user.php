<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Edytuj użytkownika';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}		
	
	if(isset($_GET) & !empty($_GET)){
		$updateid = $_GET['id'];
	}		
	
	if(isset($_POST) & !empty($_POST)){
		$ident = !empty($_POST['updating_id']) ? trim($_POST['updating_id']) : null;
		$login = !empty($_POST['login']) ? trim($_POST['login']) : null;
		$password = !empty($_POST['password']) ? trim($_POST['password']) : null;
		$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
		$rola = !empty($_POST['rola']) ? trim($_POST['rola']) : null;
		if (empty($_POST['password'])){
			$stmt = $conn->prepare("UPDATE users SET login = ?, email = ?, role_id = ? WHERE id = ?");
			$stmt->bind_param("ssii", $login, $email, $rola ,$ident);
			$stmt->execute();
			$stmt->close();
		} else {
			$passwordHash = password_hash($password, PASSWORD_BCRYPT);
			$stmt = $conn->prepare("UPDATE users SET login = ?, password = ?, email = ?, role_id = ? WHERE id = ?");
			$stmt->bind_param("sssii", $login, $passwordHash, $email, $rola, $ident);
			$stmt->execute();
			$stmt->close();
		}
		header("Location: show_users.php");
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
					$stmt = $conn->prepare("SELECT role_id, login, email FROM users WHERE id = ?");
					$stmt->bind_param("i", $updateid);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($selectedRole, $login, $mail);
					$stmt->fetch();
			?>
			
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Login</label>
			    <div class="col-sm-10">
			      <input type="text" name="login"  class="form-control" id="input1" value="<?php echo $login; ?>" placeholder="<?php echo $login; ?>" />
				  <input type="hidden" name="updating_id"  value="<?php echo $updateid; ?>"/>
			    </div>
			</div>
			
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Hasło</label>
			    <div class="col-sm-10">
			      <input type="password" name="password"  class="form-control" id="input1"/>
			    </div>
			</div>
			
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">E-Mail</label>
			    <div class="col-sm-10">
			      <input type="text" name="email"  class="form-control" id="input1" value="<?php echo $mail; ?>" placeholder="<?php echo $mail; ?>" />
			    </div>
			</div>
			
			<div class="form-group">
				<label for="input1" class="col-sm-2 control-label">Rola</label>
				<div class="col-sm-10">
					<select name="rola" class="form-control">
					<?php
						$stmt = $conn->prepare("SELECT id, name FROM roles");
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($roleId, $name);
						 
						while($stmt->fetch()){
						echo "<option value='" . $roleId ."' ".(($roleId==$selectedRole)?'selected="selected"':"").">" . $name . "</option>";
						}
					?>
					</select>
				</div>
			</div>
			
			<div class="col-sm-2"></div>
			<input type="submit" class="btn btn-primary" value="Edytuj" />
			<a href="show_users.php" class="btn btn-warning">Anuluj</a>
			</form>
			</div>
		</div>
	</div>
</body>
</html>