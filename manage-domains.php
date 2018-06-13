<?php 
	require __DIR__ . '/config.php';
	require ROOT_PATH . '/connect.php';
	
	if(trim($_SESSION['user_role']) != 'moderator' && trim($_SESSION['user_role']) != 'administrator'){
		header('Location: index.php');
		exit;
	}	
	
	$pageTitle = 'Zarządzaj domenami';
	
	include RESOURCE_PATH . '/header.php'; 
?>
	
<div class="small-container management-page">
	<h1>Zarządzaj domenami</h1>
	<span class="note">Poniżej znajduje się lista nieznanych domen, które wymagają uwagi. Jeżeli podana strona jest bezpieczna i w jakimś stopniu oferuje promocje na oprogramowanie należy przydzielić jej nazwę, w innym wypadku takowa domena powinna zostać zbanowana.</span>

	<div class="management-table">

		<?php
			$stmt = $conn->prepare("SELECT id, url FROM domains WHERE alias IS NULL AND banned = 0");
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows > 0) {
				$stmt->bind_result($id, $url);
				while ($stmt->fetch()) {
					$html = "<div class='row'>";
					$html .= "<input class='id' type='hidden' value='" . $id . "' name='id'>";
					$html .= "<div class='title'><a href='http://". $url . "'>" . $url . "</a></div>";
					$html .= "<div class='input'><input class='long' type='text' placeholder='Sugerowana nazwa'></div>";
					$html .= "<div class='action'>
					<span class='btn-action accept'>Akceptuj <i class='fa fa-check' aria-hidden='true'></i></span>
					<span class='btn-action ban'>Zbanuj <i class='fa fa-times' aria-hidden='true'></i></span>
					</div>";
					$html .= "</div>";
					echo $html;
				}
			} else {
				echo "<div class='row'><p>Brak domen wymagających uwagi.</p></div>";
			}
		?>
	</div>
	
	<div class="domains-info">
		<div class="accepted-domains">
			<h1>Przyjęte domeny</h1>
			<div class="management-table">
			<?php
				$stmt = $conn->prepare("SELECT id, url, alias FROM domains WHERE alias IS NOT NULL AND banned = 0");
				$stmt->execute();
				$stmt->store_result();
				
				if ($stmt->num_rows > 0) {
					$stmt->bind_result($id, $url, $alias);
					while ($stmt->fetch()) {
						$html = "<div class='row'>";
						$html .= "<input class='id' type='hidden' value='" . $id . "' name='id'>";
						$html .= "<div class='title'><a href='http://". $url . "'>" . $url . "</a></div>";
						$html .= "<div class='alias'>" . $alias . "</div>";
						$html .= "</div>";
						echo $html;
					}
				} else {
					echo "<div class='row'><p>Brak domen.</p></div>";
				}
				?>
			</div>
		</div>
		
		<span class="gap"></span>
		
		<div class="banned-domains">
			<h1>Zbanowane domeny</h1>
			<div class="management-table">
				<?php
				$stmt = $conn->prepare("SELECT id, url FROM domains WHERE banned = 1");
				$stmt->execute();
				$stmt->store_result();
				
				if ($stmt->num_rows > 0) {
					$stmt->bind_result($id, $url);
					while ($stmt->fetch()) {
						$html = "<div class='row'>";
						$html .= "<input class='id' type='hidden' value='" . $id . "' name='id'>";
						$html .= "<div class='title'><a href='http://". $url . "'>" . $url . "</a></div>";
						$html .= "</div>";
						echo $html;
					}
				} else {
					echo "<div class='row'><p>Brak domen.</p></div>";
				}
				?>
			</div>
		</div>
	</div>
	
</div>	

<script>
function acceptDomain(e, id) {
	var name = $(e).find(".input input").val();
	
	$.ajax({
		url: "ajax/post-accept-domain.php",
		type: "POST",
		data: { domain_id: id, name: name },
		success: function() {
			$(e).remove();
		},
		error: function() {
			alert('Wystąpił problem przy próbie zaakceptowania domeny.');
		}
	});	
	
}

function banDomain(e, id) {
	$.ajax({
		url: "ajax/post-ban-domain.php",
		type: "POST",
		data: { domain_id: id },
		success: function() {
			$(e).remove();
		},
		error: function() {
			alert('Wystąpił problem przy próbie zbanowania domeny.');
		}
	});		
	
}

$( ".action .accept" ).click(function() {
	var e = $(this).closest(".row");
	var id = $(e).find('input[type="hidden"]').val();
	
	if( !$(e).find(".input input").val() ){
		$(e).find(".input input").addClass("error");
	}
	else {
		$(e).find(".input input").removeClass("error");
		if (confirm('Czy wprowadzona nazwa jest prawidłowa? Po zaakceptowaniu, jedynie administrator będzie mógl ją zmienić.')) {
			acceptDomain(e, id);
		}
	}
});

$( ".action .ban" ).click(function() {
	var e = $(this).closest(".row");
	var id = $(e).find('input[type="hidden"]').val();	
	
	if (confirm('Napewno chcesz zbanować podaną domenę? Po zaakceptowaniu, jedynie administrator będzie mógł ją przywrócić.')) {
			banDomain(e, id);
	}
});

</script>
	
<?php
	include RESOURCE_PATH . '/footer.php'; 
?>
