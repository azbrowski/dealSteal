<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/getUserName.php';	
	$pageTitle = 'Powiadomienia';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	} 	
	
	if(isset($_GET) & !empty($_GET)){
		$deleteid = $_GET['delete_id'];
		$stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
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
				<a href="create_message.php" class="btn btn-success">Dodaj powiadomienie</a>
			</div>
	
			<?php
			$stmt = $conn->prepare("SELECT id, from_user, to_user, content, created, is_read FROM messages");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($messageId, $from_user, $to_user, $content, $created, $is_read);


			echo "<table class='table table-bordered table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Od</th>";
			echo "<th>Do</th>";
			echo "<th>Wiadomość</th>";
			echo "<th>Data utworzenia</th>";
			echo "<th>Czy odczytano</th>";
			echo "<th><center><i>Opcje</i></center></th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($stmt->fetch()){
			echo "<tr>";
			echo "<td>" . $messageId . "</td>";
			echo "<td>" . getUserName($conn, $from_user) . "</td>";
			echo "<td>" . getUserName($conn, $to_user) . "</td>";
			echo "<td>" . $content . "</td>";
			echo "<td>" . $created . "</td>";
			echo "<td>" . $is_read . "</td>";
			echo "<td>";
			echo "<center><a href='update_message.php?id=". $messageId ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
			echo "<a href='show_messages.php?delete_id=". $messageId ."' title='Delete Record' data-toggle='tooltip' onclick='return confirmDelete()'><span class='glyphicon glyphicon-trash'></span></a></center>";
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