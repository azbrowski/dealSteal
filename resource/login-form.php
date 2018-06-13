<?php

if (isset($_SESSION['user_id'])) {
	header('Location: index.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require PHP_PATH . '/getUserRole.php';

	$login = !empty($_POST['login']) ? trim($_POST['login']) : null;
	$login = strtolower($login);
	$passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;

	$stmt = $conn->prepare("SELECT id, login, password FROM users WHERE login = ?");
	$stmt->bind_param("s", $login);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		//Przypisujemy wynik SELECTa do zmiennych
		$stmt->bind_result($id, $login, $password);
		while ($stmt->fetch()) {
			//Sprawdzamy poprawność hasła
			$validPassword = password_verify($passwordAttempt, $password);

			if($validPassword){
				//Udało się podać dobre dane, zatem można przydzielić sesję
				$_SESSION['user_id'] = $id;
				$_SESSION['login'] = $login;
				$_SESSION['logged_in'] = time();
				$_SESSION['user_role'] = getUserRole($conn, $id);

				//Mozna przejsc na strone glowna
				header('Location: index.php');
				exit;

			} else {
				//Podane hasło było nieprawidłowe
				$err_msg = "Podany login bądź hasło są nieprawidłowe.";
			}
		}
	} else {
		//Zwrocony SELECT posiada 0 wierszy, zatem nie ma takiego uzytkownika
		$err_msg = "Podany login bądź hasło są nieprawidłowe.";
	}

}

?>

<div class="login-container">
	<h1>Zaloguj się</h1>
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

	<form id="loginForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
		<div class="form-group">
			<label for="login">Login</label>
			<input type="text" id="login" class="long" name="login" required>
		</div>

		<div class="form-group">
			<label for="password">Hasło</label>
			<input type="password" id="password" class="long" name="password" required>
		</div>

		<hr>

		<button class="btn" type="submit" name="submit">Wyślij</button>
	</form>
	<div class="register-note">
		<span class="note">Nie masz jeszcze konta? <a href="register.php">Zarejestruj się!</a></span>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#loginForm").validate({
			rules: {
				login: {
					required: true
				},
				password: {
					required: true
				}
			},
			messages: {
				login: {
					required: 'Wpisz swój login.'
				},
				password: {
					required: 'Wpisz swoje hasło.'
				}
			}
		});

	});
</script>
