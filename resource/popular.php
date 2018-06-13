<?php
	$limitPopular = 8;

	$stmt = $conn->prepare("SELECT domains.alias, COUNT(promotions.id) AS count FROM domains LEFT JOIN promotions ON domains.id = promotions.domain_id WHERE domains.banned = 0 AND domains.alias IS NOT NULL GROUP BY domains.alias ORDER BY count DESC LIMIT ?");
	$stmt->bind_param("i", $limitPopular);
	$stmt->execute();
	$stmt->store_result();
	
	if ($stmt->num_rows > 0) { ?>
		<div class="popular-nav">
			<div class="container">
				<ul class="popular-nav-menu">
	<?php
		$stmt->bind_result($alias, $count);
		
		while ($stmt->fetch()) {
			$alt = ucfirst($alias) . ' (' . $count . ')';
			
			echo "<li><a href='" . ROOT_URL . "?site=" . $alias . "' title='" . $alt . "'>" . ucfirst($alias) . "</a></li>";
		} ?>
					</ul>
			</div>
		</div>
	<?php }
	else {
			echo '<p>Brak</p>';
	}	

?>