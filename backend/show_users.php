<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Użytkownicy';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}
	
	if(isset($_GET) & !empty($_GET)){
		$deleteid = $_GET['delete_id'];
		$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
		$stmt->bind_param("i", $deleteid);
		$stmt->execute();
	}
?>
<!DOCTYPE html>
<?php include 'admin_header.php';?>
<body>
	<div class="wrapper">
	
		<?php include 'admin_navbar.php';?>

		<div class="content">
			<div class="header">
				<h2><?php echo $pageTitle; ?></h2>
				<a href="create_user.php" class="btn btn-success">Dodaj użytkownika</a>
			</div>
	
			<?php

			// Attempt select query execution
			$stmt = $conn->prepare("SELECT users.id, users.login, users.password, users.email, users.created, roles.name FROM users JOIN roles ON users.role_id = roles.id");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($userId, $login, $password, $email, $created, $roleName);

			echo "<table class='table table-bordered table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Rola</th>";
			echo "<th>Login</th>";
			echo "<th>Hasło</th>";
			echo "<th>E-Mail</th>";
			echo "<th>Data utworzenia</th>";
			echo "<th><center><i>Opcje</i></center></th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($stmt->fetch()){
			echo "<tr>";
			echo "<td>" . $userId . "</td>";
			echo "<td>" . $roleName . "</td>";
			echo "<td>" . $login . "</td>";
			echo "<td>" . $password . "</td>";
			echo "<td>" . $email . "</td>";
			echo "<td>" . $created . "</td>";
			echo "<td>";
			echo "<center><a href='update_user.php?id=". $userId ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
			echo "<a href='show_users.php?delete_id=". $userId ."' title='Delete Record' data-toggle='tooltip' onclick='return confirmDelete()'><span class='glyphicon glyphicon-trash'></span></a></center>";
			echo "</td>";
			echo "</tr>";
			}
			echo "</tbody>";                            
			echo "</table>";
			?>  
			<script type="text/javascript">
			$(document).ready(function(){
					$('[data-toggle="tooltip"]').tooltip();   
			});

			function confirmDelete()
			{
				return confirm("Potwierdź usunięcie");
			}
			</script>
		</div>
	</div>
</body>
</html>