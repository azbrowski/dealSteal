<?php

include PHP_PATH . '/countUserUnreadNotifications.php';
include PHP_PATH . '/countDomainsToManage.php';
include PHP_PATH . '/countPromotionsToManage.php';

if (isset($_SESSION['user_id'])) {
	//dla testow
	//var_dump($_SESSION);
}

?>
<!DOCTYPE html>
<html>
  <head>
		<title><?php echo $pageTitle ?> - <?php echo $siteName; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH . 'style.css'; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH . 'forms.css'; ?>">
		<link rel="stylesheet" href="<?php echo CSS_PATH . 'font-awesome.min.css'; ?>">
    <script src="<?php echo LIB_PATH . 'jquery-3.2.1.min.js'; ?>"></script>
		<script src="<?php echo LIB_PATH . 'jquery-ui.min.js'; ?>"></script>
		<script src="<?php echo LIB_PATH . 'jquery.validate.min.js'; ?>"></script>
		<script src="<?php echo LIB_PATH . 'jquery.validate.additional-methods.min.js'; ?>"></script>
		<script src="<?php echo LIB_PATH . 'jquery.validate.messages_pl.min.js'; ?>"></script>
		<script src="<?php echo LIB_PATH . 'jquery.mask.min.js'; ?>"></script>
		<script src="<?php echo LIB_PATH . 'URI.js'; ?>"></script>
  </head>
  <body>
		<nav class="primary-nav">
			<div class="container">
				<a class="primary-nav-logo" href="<?php echo ROOT_URL; ?>">
					<?php echo $siteName; ?>
				</a>
				<div class="primary-nav-search">
					<form id="searchForm" action="<?php echo ROOT_URL; ?>" method="get" autocomplete="off" role="search">
						<input id="search-query" name="search" placeholder="Szukaj">
						<button class="search-action" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
					</form>
				</div>
				<ul class="primary-nav-actions">

					<?php if (isset($_SESSION['user_id'])) { ?>

						<li>
							<a href="<?php echo ROOT_URL . 'add-promotion.php'; ?>">&#8203;<i class="fa fa-plus-circle fa-lg" title="Dodaj promocję"></i></a>
						</li>
						
						<?php
							$countNotifications = countUserUnreadNotifications($conn, $_SESSION['user_id']);
						?>
						<li class="notification">
							<a id="msg-dropdown-toggle" class="dropdown-toggle" href="javascript:void(0)" onclick="toggleNotifications()" title="Notyfikacje"><i class="fa fa-bell"></i>
							<?php if($countNotifications > 0) {?>
							<span class="notification-num"><i class="fa fa-exclamation-circle"></i></span>
							<?php } ?>
							</a>
							<div id="msg-dropdown" class="dropdown-menu">
								<h2>Notyfikacje</h2>
								<span class='read-all'>Oznacz wszystkie jako przeczytane</span>
								<div class="notification-list">
								<?php
									$stmt = $conn->prepare("SELECT id, from_user, content, created FROM messages WHERE to_user = ? AND is_read = 0 ORDER BY id DESC");
									$stmt->bind_param('i', $_SESSION['user_id']);
									$stmt->execute();
									$stmt->store_result();
									
									if ($stmt->num_rows > 0) {
										$stmt->bind_result($id, $from, $content, $created);
										while ($stmt->fetch()) {
											$html = "<div class='notification-item' data-value='" . $id ."'>";
											$html .= "<span class='close'><i class='fa fa-times'></i></span>";
											$html .= $content;
											$html .= "</div>";
											echo $html;
										}
									} else {
										echo "<div class='notification-no-result'><i>Brak powiadomień.</i></div>";
									}
								?>
								</div>
							</div>
						</li>
						<script>
						function readMessage(e, id) {
							$.ajax({
								url: "ajax/post-read-message.php",
								type: "POST",
								data: { message_id: id },
								success: function() {
									$(e).remove();
								},
								error: function() {
									alert('Wystąpił błąd przy próbie oznaczenia notyfikacji jako przeczytanej.');
								}
							});
						}

						function readAllMessages(e) {
							$.ajax({
								url: "ajax/post-read-all-messages.php",
								type: "POST",
								success: function() {
									$(e).empty();
									$(e).html("<div class='notification-no-result'><i>Brak powiadomień.</i></div>");
								},
								error: function() {
									alert('Wystąpił błąd przy próbie oznaczenia wszystkich notyfikacji jako przeczytanych.');
								}
							});
						}
						
						$( ".notification-item .close" ).click(function() {
							var e = $(this).closest(".notification-item");
							var id = $(e).attr('data-value');
							
							readMessage(e, id);
						});
						
						$( "#msg-dropdown .read-all" ).click(function() {
							var e = $(this).closest("#msg-dropdown").find(".notification-list");
							
							readAllMessages(e);
						});
						</script>
						
						<?php if(trim($_SESSION['user_role']) == 'moderator' || trim($_SESSION['user_role']) == 'administrator'){ 
						
						$countDomains = countDomainsToManage($conn);
						$countPromotions = countPromotionsToManage($conn);
						
						?>
						<li class="mod">
							<a id="mod-dropdown-toggle" class="dropdown-toggle" href="javascript:void(0)" onclick="toggleModMenu()" title="Narzędzia moderatorskie"><i class="fa fa-wrench"></i>
							<?php if($countDomains + $countPromotions > 0) {?>
							<span class="notification-num"><i class="fa fa-exclamation-circle"></i></span>
							<?php } ?>
							</a>
							<div id="mod-dropdown" class="dropdown-menu">
									<a class="dropdown-item" href="<?php echo ROOT_URL . 'manage-domains.php'; ?>">Strony
									<?php if(!empty($countDomains)) {?>
									<span class="num-count"><?php echo $countDomains; ?></span>
									<?php } ?>
								</a>
								<a class="dropdown-item" href="<?php echo ROOT_URL . 'manage-promotions.php'; ?>">
									Promocje
									<?php if(!empty($countPromotions)) {?>
									<span class="num-count"><?php echo $countPromotions; ?></span>
									<?php } ?>
								</a>
								
								<?php if(trim($_SESSION['user_role']) == 'administrator'){ ?>
								<hr>
								<a class="dropdown-item" href="<?php echo BACK_URL . 'admin_panel.php'; ?>">
									Panel Administratora
								</a>
								<?php } ?>
							</div>
						</li>
						<?php } ?>

						<li class="user">
							<a id="user-dropdown-toggle" class="dropdown-toggle has-arrow" href="javascript:void(0)" onclick="toggleMenu()"><?php echo ucfirst( $_SESSION['login'] ); ?></a>
							<div id="user-dropdown" class="dropdown-menu">
								<a class="dropdown-item" href="<?php echo ROOT_URL . 'profile.php?u=' . $_SESSION['login'];?>">Profil</a>
								<hr>
								<a class="dropdown-item" href="<?php echo ROOT_URL . 'settings.php'; ?>">Ustawienia</a>
								<a class="dropdown-item" href="<?php echo ROOT_URL . 'logout.php'; ?>">Wyloguj się</a>
							</div>
						</li>

						<script>
						function toggleMenu() {
							document.getElementById("user-dropdown").classList.toggle("show");
							document.getElementById("user-dropdown-toggle").classList.toggle("open");
						}
						
						function toggleNotifications() {
							document.getElementById("msg-dropdown").classList.toggle("show");
							document.getElementById("msg-dropdown-toggle").classList.toggle("open");
						}
						
						<?php if(trim($_SESSION['user_role']) == 'moderator' || trim($_SESSION['user_role']) == 'administrator'){ ?>
						function toggleModMenu() {
							document.getElementById("mod-dropdown").classList.toggle("show");
							document.getElementById("mod-dropdown-toggle").classList.toggle("open");
						}
						<?php } ?>

						window.onclick = function(e) {
							if (!e.target.matches('#user-dropdown-toggle')) {
								var userDropdown = document.getElementById("user-dropdown");
								var userDropdownToggle = document.getElementById("user-dropdown-toggle");

									if (userDropdown.classList.contains('show')) {
										userDropdown.classList.remove('show');
										userDropdownToggle.classList.remove('open');
									}
							}
							
							
							if (!e.target.closest('#msg-dropdown')){
								if (!e.target.matches('#msg-dropdown-toggle')) {
									var msgDropdown = document.getElementById("msg-dropdown");
									var msgDropdownToggle = document.getElementById("msg-dropdown-toggle");
								
									if (msgDropdown.classList.contains('show')) {
										msgDropdown.classList.remove('show');
										msgDropdownToggle.classList.remove('open');
									}
								}
							}
							
							<?php if(trim($_SESSION['user_role']) == 'moderator' || trim($_SESSION['user_role']) == 'administrator'){ ?>
							if (!e.target.matches('#mod-dropdown-toggle')) {
								var modDropdown = document.getElementById("mod-dropdown");
								var modDropdownToggle = document.getElementById("mod-dropdown-toggle");

									if (modDropdown.classList.contains('show')) {
										modDropdown.classList.remove('show');
										modDropdownToggle.classList.remove('open');
									}
							}
							<?php } ?>
						}
						</script>
						
					<?php } else { ?>

						<li class="user">
							<a href="<?php echo ROOT_URL . 'login.php'; ?>">Zaloguj się <i class="fa fa-user-circle"></i></a>
						</li>

					<?php } ?>

				</ul>
			</div>
		</nav>
	<div class="content">
