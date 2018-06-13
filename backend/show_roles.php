<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Role';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	} 		
	
	if(isset($_GET) & !empty($_GET)){
		$deleteid = $_GET['delete_id'];
		$stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
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
				<a href="create_role.php" class="btn btn-success">Dodaj rolę</a>
			</div>
	
			<?php
			$stmt = $conn->prepare("SELECT * FROM roles");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($roleId, $name);

			echo "<table class='table table-bordered table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Nazwa roli</th>";
			echo "<th><center><i>Opcje</i></center></th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($stmt->fetch()){
			echo "<tr>";
			echo "<td>" . $roleId . "</td>";
			echo "<td>" . $name . "</td>";
			echo "<td>";
			echo "<center><a href='update_role.php?update_id=". $roleId ."' title='Edytuj' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
			echo "<a href='show_roles.php?delete_id=". $roleId ."' title='Usuń' data-toggle='tooltip' onclick='return confirmDelete()'><span class='glyphicon glyphicon-trash'></span></a></center>";
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