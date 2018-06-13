<?php
require PHP_PATH . '/getUserName.php';
require PHP_PATH . '/checkUserVotePromotion.php';
require PHP_PATH . '/checkUserVoteComment.php';
require PHP_PATH . '/timeElapsedString.php';

$stmt = $conn->prepare("SELECT * FROM promotions WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$stmt->store_result();

$stmt->bind_result($id,
  $user_id,
  $domain_id,
  $title,
  $url,
  $orgPriceLow,
  $orgPriceHigh,
  $salePriceLow,
  $salePriceHigh,
  $created,
  $expired,
  $likes,
  $dislikes,
  $published);
$stmt->fetch();

if($orgPriceLow != 0 && $orgPriceHigh != 0) {
  $orgPrice = $orgPriceLow . ' - ' . $orgPriceHigh . 'zł';
} else if($orgPriceLow != 0) {
  $orgPrice = $orgPriceLow . 'zł';
} else {
  $orgPrice = null;
}

if($salePriceLow != 0 && $salePriceHigh != 0) {
  $salePrice = $salePriceLow . ' - ' . $salePriceHigh . 'zł';
} else if($salePriceLow != 0) {
  $salePrice = $salePriceLow . 'zł';
} else {
  $salePrice = 'Za darmo';
}

$userId = 0;
if (isset($_SESSION['user_id'])) {
	$userId = $_SESSION['user_id'];
}	

$userName = getUserName($conn, $user_id);

?>
<script src="<?php echo LIB_PATH . 'jquery.countdown.min.js'; ?>"></script>

<div class="small-container">
  <div class="promotion-container">
		
		<?php if(isset($err_msg)){ ?>
				<div class="success-alert-box alert-box">
					<?php if($userRole == 'moderator' || $userRole == 'administrator') { ?>
						<span class="check">Akceptuj <i class="fa fa-check" aria-hidden="true"></i></span>
					<?php } ?>
					<p><?php echo $err_msg; ?></p>
				</div>
				
				<?php if($userRole == 'moderator' || $userRole == 'administrator') { ?>
				<script>
					//publish promotion function
					function publishPromotion() {
						var id = <?php echo $id; ?>;
						var published = <?php echo $published; ?>;
						
						$.ajax({
							url: "ajax/post-publish-promotion.php",
							type: "POST",
							data: { promotion_id: id, published: published },
							success: function() {
								$( ".alert-box" ).remove();
							},
							error: function() {
								alert('Error occured');
							}
						});
					}
				
					$( ".check" ).click(function() {
						publishPromotion();
					});
				</script>
				<?php } ?>
		<?php } ?>
		
		<div id="<?php echo $id; ?>" class="promotion-content">
			<div class="col-33 promotion-selection">

				<div class="promotion-timer">
					<h2>Promocja kończy się za</h2>
					<div class="timer"><?php echo $expired; ?></div>
				</div>
				
				<div class="promotion-info">
					<?php if($orgPrice){ ?>
						<span class="original-price"><?php echo $orgPrice; ?></span>
					<?php } ?>
					<span class="price-info">
						<span class="price-tag">Cena</span><?php echo $salePrice; ?></span>
					
					<a class="shopping-button" href="<?php echo $url; ?>">
						<span><i class="fa fa-shopping-cart"></i> Przejdź do sklepu</span>
					</a>
				</div>

			 </div>	
		
			<div class="col-66 promotion-comments">

				<div class="promotion-header">
					<h1><?php echo $title; ?></h1>
					
					<div class="meta">
						<a class="author" href="<?php echo ROOT_URL . 'profile.php?u=' . $userName; ?>"><?php echo ucfirst( $userName ); ?></a>
						<span class="date"><?php echo timeElapsedString($created); ?></span>
					</div>
					
					<?php
						$userVote = checkUserVotePromotion($conn, $userId, $id);
					?>
					
					<div class="item-scope">
						<div class="like action <?php echo $userVote == "1"? "up" : ""; ?>" data-event-action="<?php echo $userVote == "1"? "remove-vote" : "upvote"; ?>">
							<span class="icon"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
							<span class="text"><?php echo $likes; ?></span>
						</div>
						
						<div class="dislike action <?php echo $userVote == "-1"? "down" : ""; ?>" data-event-action="<?php echo $userVote == "-1"? "remove-vote" : "downvote"; ?>">
							<span class="icon"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
							<span class="text"><?php echo $dislikes; ?></span>
						</div>
					
					</div>
				</div>
				
				<hr>
				
				<h2>Opinie</h2>
				
				<?php 
					$stmt = $conn->prepare("SELECT * FROM comments WHERE user_id = ? AND promotion_id = ?");
					$stmt->bind_param("ii", $userId, $id);
					$stmt->execute();
					$stmt->store_result();
				
					if ($stmt->num_rows == 0) {
						if (!isset($_SESSION['user_id'])) {
								echo "<div class='comment-container'>
								<div class='comment-register'>
								<p>Pisanie komentarzy jest dostępne dla użytkowników.</p>
								<a class='btn' href='" . ROOT_URL . "register.php'>Zarejestruj się!</a>
								</div>
								</div>";
						} else if ( $user_id == $_SESSION['user_id'] ) {
							//author of promotion can't give opinion
						} else {
								include RESOURCE_PATH . '/comment-form.php';
						}
					}
				?>
				
				<div class="comments-list">
					<?php
						$stmt = $conn->prepare("SELECT users.login, comments.user_id, comments.id, comments.content, comments.created, comments.modified, comments.published, comments.thumbs_up 
						FROM comments 
						INNER JOIN users
						ON comments.user_id = users.id 
						WHERE comments.promotion_id = ?
						ORDER BY comments.thumbs_up DESC");
						$stmt->bind_param("i", $_GET['id']);
						$stmt->execute();
						$stmt->store_result();
						
					if ($stmt->num_rows > 0) {
						$stmt->bind_result($comment_user, $comment_userId, $comment_id, $comment_content, $comment_created, $comment_modified, $comment_published, $comment_thumbs);
						
						while ($stmt->fetch()) {
							if($comment_published == 1){ ?>
								<div class="comment-item" data-value="<?php echo $comment_id; ?>">
									<div class="meta">
										<a class="author" href="<?php echo ROOT_URL . 'profile.php?u=' . $comment_user; ?>"><?php echo ucfirst( $comment_user ); ?></a>
										<span class="date"><?php echo timeElapsedString($comment_created); ?></span>
									</div>
									<div class="comment-content">
										<p><?php echo $comment_content; ?></p>
										<?php if(!empty($comment_modified)){ ?>
											<span class="modified"><?php echo 'Post był modyfikowany ' . timeElapsedString($comment_modified); ?></span>
										<?php } ?>
									</div>
									
									<?php
										$userCommVote = checkUserVoteComment($conn, $userId, $comment_id);
									?>
									
									<div class="item-scope">
										<div class="like action <?php echo $userCommVote == "1"? "up" : ""; ?>" data-event-action="<?php echo $userCommVote == "1"? "remove-vote" : "upvote"; ?>">
											<span class="icon"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
											<span class="text"><?php echo $comment_thumbs; ?></span>
										</div>
										<?php if($userId == $comment_userId) { ?>
										<div class="delete action" data-event-action="delete-comment">
											<span class="icon"><i class="fa fa-ban"></i></span>
										</div>
										<?php } ?>
									</div>
								</div>
							<?php }
						}
					} else {
							echo "<div class='no-result'>Jeszcze nikt nie dodał opinii na temat tej promocji.</div>";
					}
					
					?>
				</div>

			</div>
		</div>
	</div>

</div>

<script id="js-countdown" type="text/javascript">
$(".timer").countdown("<?php echo $expired; ?>", function(event) {
	$(this).html(
		event.strftime(''
		+ '<div class="timer-item"><span class="timer-value">%d</span> <div class="timer-unit">dni</div></div>'
		+ '<div class="timer-item"><span class="timer-value">%H</span> <div class="timer-unit">godz</div></div>'
		+ '<div class="timer-item"><span class="timer-value">%M</span> <div class="timer-unit">min</div></div>'
		+ '<div class="timer-item"><span class="timer-value">%S</span> <div class="timer-unit">sek</div></div>'));
});
</script>

<script id="js-promotion-vote">
//voting function
function votePromotion(e, dataEvent, id) {
	var vthumbs = 0;
	
	if(dataEvent == "upvote")
		vthumbs = 1;
	if(dataEvent == "downvote")
		vthumbs = -1;
	
	$.ajax({
		url: "ajax/post-promotions-vote.php",
		type: "POST",
		data: { promotion_id: id, vthumbs: vthumbs },
		dataType: 'json',
		success: function(data) {
			if($(e).is('.like')){
				if(dataEvent == "upvote"){
					$(e).addClass("up")
						.attr("data-event-action", "remove-vote");
					$(e).closest(".item-scope").find(".dislike").removeClass("down")
						.attr("data-event-action", "downvote");
				}
				else {
					$(e).removeClass("up")
						.attr("data-event-action", "upvote");
				}
			}
			
			if($(e).is('.dislike')){
				if(dataEvent == "downvote"){
					$(e).addClass("down")
						.attr("data-event-action", "remove-vote");
					$(e).closest(".item-scope").find(".like").removeClass("up").attr("data-event-action", "upvote");
				}
				else {
					$(e).removeClass("down")
						.attr("data-event-action", "downvote");
				}	
			}
			
			$(e).closest(".item-scope").find(".like > .text").html(data.thumbsUp);
			$(e).closest(".item-scope").find(".dislike > .text").html(data.thumbsDown);
			
		},
		error: function() {
			alert('Głosowanie jest dostępne dla zalogowanych.');
		}
	});
}

$( ".promotion-header .item-scope .action.like" ).click(function() {
	var dataEvent = $(this).attr("data-event-action");
	var id = $(this).closest(".promotion-content").attr("id");
	var e = $(this);
	
	votePromotion(e, dataEvent, id);
});
$( ".promotion-header .item-scope .action.dislike" ).click(function() {
	var dataEvent = $(this).attr("data-event-action");
	var id = $(this).closest(".promotion-content").attr("id");
	var e = $(this);	
	
	votePromotion(e, dataEvent, id);
});
</script>

<script id="js-comment-vote">
//voting function
function voteComment(e, dataEvent, id) {
	var vthumbs = 0;
	
	if(dataEvent == "upvote")
		vthumbs = 1;
	
	$.ajax({
		url: "ajax/post-comments-vote.php",
		type: "POST",
		data: { comment_id: id, vthumbs: vthumbs },
		dataType: 'json',
		success: function(data) {
			if($(e).is('.like')){
				if(dataEvent == "upvote"){
					$(e).addClass("up")
						.attr("data-event-action", "remove-vote");
				}
				else {
					$(e).removeClass("up")
						.attr("data-event-action", "upvote");
				}
			}
			
			$(e).closest(".item-scope").find(".like > .text").html(data.thumbsUp);
			
		},
		error: function() {
			alert('Głosowanie jest dostępne dla zalogowanych.');
		}
	});
}

$( ".comment-item .item-scope .action.like" ).click(function() {
	var dataEvent = $(this).attr("data-event-action");
	var id = $(this).closest(".comment-item").attr("data-value");
	var e = $(this);
	
	voteComment(e, dataEvent, id);
});
</script>

<script id="js-comment-delete">
//delete function
function deleteComment(e, id) {
	$.ajax({
		url: "ajax/post-delete-comment.php",
		type: "POST",
		data: { comment_id: id },
		success: function() {
			location.reload(); 
		},
		error: function() {
			alert('Wystąpił błąd przy próbie usunięcia tego komentarza.');
		}
	});
}

$( ".comment-item .item-scope .action.delete" ).click(function() {
	var id = $(this).closest(".comment-item").attr("data-value");
	var e = $(this).closest(".comment-item");
	
	if (confirm('Czy napewno chcesz usunąć swoją opinię?')) {
		deleteComment(e, id);
	}		
});
</script>
