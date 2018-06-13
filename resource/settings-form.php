<?php
if (!isset($_SESSION['user_id'])) {
	header('Location: index.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$passwordAttempt = !empty($_POST['confirm']) ? trim($_POST['confirm']) : null;

	$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
	$stmt->bind_param("i", $_SESSION['user_id']);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		$stmt->bind_result($password);
		$stmt->fetch();

		$validPassword = password_verify($passwordAttempt, $password);

		if($validPassword){

			$newpass = !empty($_POST['password']) ? trim($_POST['password']) : null;
			$email = !empty($_POST['email']) ? trim($_POST['email']) : null;

			if($newpass == null){
				$stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
				$stmt->bind_param("si", $email, $_SESSION['user_id']);
				$stmt->execute();
			} else {
				$passwordHash = password_hash($newpass, PASSWORD_BCRYPT);

				$stmt = $conn->prepare("UPDATE users SET password = ?, email = ? WHERE id = ?");
				$stmt->bind_param("ssi", $passwordHash, $email, $_SESSION['user_id']);
				$stmt->execute();
			}

		} else {
			$err_msg = "Nieprawidłowe hasło.";
		}

	} else {
		die('Cos poszlo nie tak.');
	}

}

$stmt = $conn->prepare("SELECT id, login, password, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();

$stmt->bind_result($id, $login, $password, $email);
$stmt->fetch();

?>
<div class="settings-container">
	<h1>Ustawienia konta</h1>
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

	<form id="settingsForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
		<div class="form-group">
			<label for="password" class="control-label">Nowe hasło</label>
			<div class="controls">
				<input type="password" id="password" class="long" name="password">
			</div>
		</div>

		<div class="form-group">
			<label for="email" class="control-label">E-mail</label>
			<div class="controls">
				<input type="text" id="email" class="long" name="email" value="<?php echo $email; ?>">
			</div>
		</div>

		<hr>

		<div class="form-group">
			<label for="confirm" class="control-label">Obecne hasło<br>
			</label>
			<div class="controls">
				<input type="password" id="confirm" class="long" name="confirm" required>
				<span class="note">Zatwierdź zmiany wprowadzając obecne hasło.</span>
			</div>
		</div>

		<div class="controls">
			<button class="btn" type="submit" name="submit">Zapisz zmiany</button>
		</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#settingsForm").validate({
			rules: {
				confirm: {
					required: true,
				}
			}
		});
	});
</script>
