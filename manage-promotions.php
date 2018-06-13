<?php 
	require __DIR__ . '/config.php';
	require ROOT_PATH . '/connect.php';
	
	if(trim($_SESSION['user_role']) != 'moderator' && trim($_SESSION['user_role']) != 'administrator'){
		header('Location: index.php');
		exit;
	}	
	
	$pageTitle = 'Zarządzaj promocjami';
	
	include RESOURCE_PATH . '/header.php'; 
?>
	
<div class="small-container management-page">
	<h1>Zarządzaj promocjami</h1>
	<span class="note">Poniżej znajduje się lista promocji, które wymagają uwagi. Jeżeli podana promocja spełnia wymogi, należy ją upublicznić, w innym wypadku usunąć.</span>

	<div class="management-table">

		<?php
			$stmt = $conn->prepare("SELECT id, title, created FROM promotions WHERE published = 0");
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows > 0) {
				$stmt->bind_result($id, $title, $created);
				while ($stmt->fetch()) {
					$html = "<div class='row'>";
					$html .= "<input class='id' type='hidden' value='" . $id . "' name='id'>";
					$html .= "<div class='title'><a href='" . ROOT_URL . "comments.php?id=" . $id . "'>" . substr($title,0,90) . "</a></div>";
					$html .= "<div class='date'>" . $created . "</div>";
					$html .= "<div class='action'>
					<span class='btn-action accept'>Akceptuj <i class='fa fa-check' aria-hidden='true'></i></span>
					<span class='btn-action delete'>Usuń <i class='fa fa-times' aria-hidden='true'></i></span>
					</div>";
					$html .= "</div>";
					echo $html;
				}
			} else {
				echo "<div class='row'><p>Brak promocji wymagających uwagi.</p></div>";
			}
		?>
	</div>
	
</div>	

<script>
function publishPromotion(e, id) {
	var name = $(e).find(".input input").val();
	var published = 0;
	
	$.ajax({
		url: "ajax/post-publish-promotion.php",
		type: "POST",
		data: { promotion_id: id, published: published },
		success: function() {
			$(e).remove();
		},
		error: function() {
			alert('Wystąpił błąd przy próbie zaakceptowania promocji.');
		}
	});
}

function deletePromotion(e, id) {
	var name = $(e).find(".input input").val();
	
	$.ajax({
		url: "ajax/post-delete-promotion.php",
		type: "POST",
		data: { promotion_id: id },
		success: function() {
			$(e).remove();
		},
		error: function() {
			alert('Wystąpił błąd przy próbie usunięcia promocji.');
		}
	});
}

$( ".action .accept" ).click(function() {
	var e = $(this).closest(".row");
	var id = $(e).find('input[type="hidden"]').val();
	
	publishPromotion(e, id);
});

$( ".action .delete" ).click(function() {
	var e = $(this).closest(".row");
	var id = $(e).find('input[type="hidden"]').val();
	
	if (confirm('Napewno chcesz usunąć tą pozycję?')) {
		deletePromotion(e, id);
	}	
});

</script>
	
<?php
	include RESOURCE_PATH . '/footer.php'; 
?>
