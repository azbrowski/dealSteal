<?php
require PHP_PATH . '/timeElapsedString.php';
require PHP_PATH . '/getUserRole.php';
require PHP_PATH . '/countUserPromotions.php';
require PHP_PATH . '/countUserComments.php';
require PHP_PATH . '/getUserCommentPoints.php';
require PHP_PATH . '/getUserPromotionPoints.php';

$stmt = $conn->prepare("SELECT id, login, created FROM users WHERE login = ?");
$stmt->bind_param("s", $_GET['u']);
$stmt->execute();
$stmt->store_result();

$stmt->bind_result($user_id, $user_login, $user_created);
$stmt->fetch();

$userRole = getUserRole($conn, $user_id);
$data = timeElapsedString($user_created);

?>

<div class="small-container profile-page">
	<div class="user-card">
		<h1><?php echo ucfirst( $user_login ); ?></h1>
		<?php if($userRole != 'user') { ?>
		<p><?php echo ucfirst( $userRole ); ?></p>
		<?php } ?>
	</div>
	<div class="user-grid">
		<div class="user-item">
			<h4>Liczba promocji</h4>
			<h3><?php echo countUserPromotions($conn, $user_id); ?></h3>
		</div>
		<div class="user-item">
			<h4>Liczba opinii</h4>
			<h3><?php echo countUserComments($conn, $user_id); ?></h3>
		</div>		
		<div class="user-item">
			<h4>Punkty promocji</h4>
			<h3><?php echo getUserPromotionPoints($conn, $user_id); ?></h3>
		</div>
		<div class="user-item">
			<h4>Punkty opinii</h4>
			<h3><?php echo getUserCommentPoints($conn, $user_id); ?></h3>
		</div>
		
	</div>
</div>