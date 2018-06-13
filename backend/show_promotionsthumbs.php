<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Oceny promocji';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
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
			</div>
	
			<?php
			$stmt = $conn->prepare("SELECT promotion_thumbs.id, users.login, promotions.title, promotion_thumbs.created, promotion_thumbs.modified, promotion_thumbs.vthumbs FROM promotion_thumbs JOIN users ON promotion_thumbs.user_id = users.id JOIN promotions ON promotion_thumbs.promotion_id = promotions.id");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($thumbId, $login, $title, $created, $modified, $vthumb);

			echo "<table class='table table-bordered table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>User</th>";
			echo "<th>Promocja</th>";
			echo "<th>Utworzono</th>";
			echo "<th>Zmodyfikowano</th>";
			echo "<th>Ocena</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($stmt->fetch()){
			echo "<tr>";
			echo "<td>" . $thumbId . "</td>";
			echo "<td>" . $login . "</td>";
			echo "<td>" . $title . "</td>";
			echo "<td>" . $created . "</td>";
			echo "<td>" . $modified . "</td>";
			echo "<td>" . $vthumb . "</td>";
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