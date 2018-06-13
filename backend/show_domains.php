<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Domeny';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	} 
	
	if(isset($_GET) & !empty($_GET)){
		$deleteid = $_GET['delete_id'];
		$stmt = $conn->prepare("DELETE FROM domains WHERE id = ?");
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
				<a href="create_domain.php" class="btn btn-success">Dodaj domenę</a>
			</div>
	
			<?php
			$stmt = $conn->prepare("SELECT id, url, alias, banned FROM domains");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($domainId, $url, $alias, $banned);

			echo "<table class='table table-bordered table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Adres</th>";
			echo "<th>Alias</th>";
			echo "<th>Zbanowana</th>";
			echo "<th><center><i>Opcje</i></center></th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($stmt->fetch()){
			echo "<tr>";
			echo "<td>" . $domainId . "</td>";
			echo "<td>" . $url . "</td>";
			echo "<td>" . $alias . "</td>";
			echo "<td>" . $banned . "</td>";
			echo "<td>";
			echo "<center><a href='update_domain.php?id=". $domainId ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
			echo "<a href='show_domains.php?delete_id=". $domainId ."' title='Delete Record' data-toggle='tooltip' onclick='return confirmDelete()'><span class='glyphicon glyphicon-trash'></span></a></center>";
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