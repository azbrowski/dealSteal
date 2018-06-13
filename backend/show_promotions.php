<?php
	require '../config.php';
	require ROOT_PATH . '/connect.php';	
	$pageTitle = 'Promocje';

	if (trim($_SESSION['user_role']) != 'administrator'){
		header("Location: ../index.php");
		exit();
	} 	
	
	if(isset($_GET) & !empty($_GET)){
		$deleteid = $_GET['delete_id'];
		$stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
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
		<a href="create_promotion.php" class="btn btn-success">Dodaj promocję</a>
	</div>

	<?php
	$stmt = $conn->prepare("SELECT promotions.id, users.login, domains.url, promotions.title, promotions.url, promotions.original_price_low, promotions.original_price_high, promotions.sale_price_low, promotions.sale_price_high, promotions.created, promotions.expired, promotions.thumbs_up, promotions.thumbs_down, promotions.published FROM promotions JOIN users ON promotions.user_id = users.id JOIN domains ON promotions.domain_id = domains.id");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($promotionId, $login, $domain, $title, $url, $original_price_low, $original_price_high, $sale_price_low, $sale_price_high, $created, $expired, $thumbs_up, $thumbs_down, $published);
		
	echo "<table class='table table-bordered table-striped'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Użytkownik</th>";
	echo "<th>Domena</th>";
	echo "<th>Tytuł</th>";
	echo "<th>Adres URL</th>";
	echo "<th>Cena przed obniżką(low)</th>";
	echo "<th>Cena przed obniżką(high)</th>";
	echo "<th>Cena promocyjna(low)</th>";
	echo "<th>Cena promocyjna(high)</th>";
	echo "<th>Utworzono</th>";
	echo "<th>Ważna do</th>";
	echo "<th>Kciuki w górę</th>";
	echo "<th>Kciuki w dół</th>";
	echo "<th>Opublikowana</th>";
	echo "<th><center><i>Opcje</i></center></th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	while($stmt->fetch()){
	echo "<tr>";
			echo "<td>" . $promotionId . "</td>";
	echo "<td>" . $login . "</td>";
											echo "<td>" . $domain . "</td>";
	echo "<td>" . $title . "</td>";
	echo "<td>" . $url . "</td>";
	echo "<td>" . $original_price_low . "</td>";
	echo "<td>" . $original_price_high . "</td>";
	echo "<td>" . $sale_price_low . "</td>";
	echo "<td>" . $sale_price_high . "</td>";
	echo "<td>" . $created . "</td>";
	echo "<td>" . $expired . "</td>";
	echo "<td>" . $thumbs_up . "</td>";
	echo "<td>" . $thumbs_down . "</td>";
	echo "<td>" . $published . "</td>";
											echo "<td>";
		echo "<center><a href='update_promotion.php?id=". $promotionId ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
													echo "<a href='show_promotions.php?delete_id=". $promotionId ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash' onclick='return confirmDelete()'></span></a></center>";
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
	</script>
	<script type='text/javascript'>
		function confirmDelete()
		{
			return confirm("Potwierdź usunięcie");
		}
	</script>
	</div>
</div>
</body>
</html>