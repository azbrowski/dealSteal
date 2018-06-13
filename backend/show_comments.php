<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Komentarze';
	
	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	}
	
	if(isset($_GET) & !empty($_GET)){
		$deleteid = $_GET['delete_id'];
		$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
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
			</div>
			<?php

			// Attempt select query execution
			$stmt = $conn->prepare("SELECT comments.id, users.login, promotions.title, comments.content, comments.created, comments.published, comments.thumbs_up FROM comments JOIN users ON comments.user_id = users.id JOIN promotions ON comments.promotion_id = promotions.id");
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($commentId, $login, $title, $content, $created, $published, $thumbsup);

			echo "<table class='table table-bordered table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Użytkownik</th>";
			echo "<th>Promocja</th>";
			echo "<th>Treść</th>";
			echo "<th>Data utworzenia</th>";
			echo "<th>Opublikowany</th>";
			echo "<th>Ocena</th>";
			echo "<th><center><i>Opcje</i></center></th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($stmt->fetch()){
			echo "<tr>";
			echo "<td>" . $commentId . "</td>";
			echo "<td>" . $login . "</td>";
			echo "<td>" . $title . "</td>";
			echo "<td>" . $content . "</td>";
			echo "<td>" . $created . "</td>";
			echo "<td>" . $published . "</td>";
			echo "<td>" . $thumbsup . "</td>";
			echo "<td>";
					echo "<center><a href='show_comments.php?delete_id=". $commentId ."' title='Delete Record' data-toggle='tooltip' onclick='return confirmDelete()'><span class='glyphicon glyphicon-trash'></span></a></center>";
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