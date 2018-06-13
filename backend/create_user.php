<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Dodaj użytkownika';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}		
	
	if(isset($_POST) & !empty($_POST)){
	$login = !empty($_POST['login']) ? trim($_POST['login']) : null;
	$login = strtolower($login);
	$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
	$password = !empty($_POST['pass']) ? trim($_POST['pass']) : null;
	$created = date("Y-m-d H:i:s");
	$stmt = $conn->prepare("SELECT login FROM users WHERE login = ?");
	$stmt->bind_param("s", $login);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows == 0) {
		//sprawdzamy czy email juz wystepuje w bazie
		$stmt = $conn->prepare("SELECT login FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows == 0) {
					//hashujemy haslo
					$passwordHash = password_hash($password, PASSWORD_BCRYPT);

					//dodajemy nowego uzytkownika
					$stmt = $conn->prepare("INSERT INTO users(role_id, login, password, email, created) VALUES (?, ?, ?, ?, ?)");
					$stmt->bind_param("issss", $_POST['rola'], $login, $passwordHash, $email, $created);
					$stmt->execute();
					$stmt->close();
					header("Location: show_users.php");

		} else {
			//Zwrócony SELECT zwrócił użytkownika, który już zajął ten email
			$err_msg = "Podany email jest już zajęty.";
		}

	} else {
		//Zwrócony SELECT zwrócił użytkownika, który już zajął ten login
		$err_msg = "Nazwa użytkownika jest już zajęta.";
	}
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
			    <label for="input1" class="col-sm-2 control-label">Login</label>
			    <div class="col-sm-10">
			      <input type="text" name="login"  class="form-control" id="input1" placeholder="Login" />
			    </div>
			</div>
 
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Hasło</label>
			    <div class="col-sm-10">
			      <input type="password" name="pass"  class="form-control" id="input1" placeholder="Hasło" />
			    </div>
			</div>
 
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">E-Mail</label>
			    <div class="col-sm-10">
			      <input type="email" name="email"  class="form-control" id="input1" placeholder="E-Mail" />
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
					echo "<option value=" . $roleId .">" . $name . "</option>";
					}
				?>
				</select>
			</div>
			</div>
			
			<div class="col-sm-2"></div>
			<input type="submit" class="btn btn-primary" value="Utwórz" />
			<a href="show_users.php" class="btn btn-warning">Anuluj</a>
		</form>
			</div>
		</div>
	</div>
</body>
</html>