<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$login = !empty($_POST['login']) ? trim($_POST['login']) : null;
	$login = strtolower($login);
	$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
	$pass = !empty($_POST['password']) ? trim($_POST['password']) : null;
	$created = date("Y-m-d H:i:s");

	//sprawdzamy czy uzytkownik o takim samym loginie juz wystepuje w bazie
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
					$passwordHash = password_hash($pass, PASSWORD_BCRYPT);

					//dodajemy nowego uzytkownika
					$stmt = $conn->prepare("INSERT INTO users(login, password, email, created) VALUES (?, ?, ?, ?)");
					$stmt->bind_param("ssss", $login, $passwordHash, $email, $created);
					$stmt->execute();
					$stmt->close();

					$conn->close();

					header('Location: login.php');
					exit;

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
<div class="register-container">
	<h1>Zarejestruj się</h1>
	<hr>

	<?php if(isset($err_msg)){ ?>
			<div class="error-alert-box alert-box">
				<span class="close"><i class="fa fa-times" aria-hidden="true"></i></span>
				<p><?php echo $err_msg; ?></p>
			</div>
			<script>
				$( ".close" ).click(function() {
					$( ".alert-box" ).remove();
				});
			</script>
	<?php } ?>

	<form id="registerForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
		<div class="form-group">
			<label for="login">Login</label>
			<input type="text" id="login" class="long" name="login" required>
		</div>

		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="text" id="email" class="long" name="email" required>
		</div>

		<div class="form-group">
			<label for="password">Hasło</label>
			<input type="password" id="password" class="long" name="password" required>
		</div>

		<hr>

		<button class="btn" type="submit" name="submit">Wyślij</button>
	</form>
	<div class="login-note">
		<span class="note">Masz już konto? <a href="login.php">Zaloguj się!</a></span>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#registerForm").validate({
			rules: {
				login: {
					required: true,
					lettersonly: true
				},
				email: {
					required: true,
					email: true
				},
				password: {
					required: true
				}
			},
			messages: {
				login: {
					lettersonly: 'Login powinien składać się wyłącznie z liter.'
				}
			}
		});

	});
</script>
