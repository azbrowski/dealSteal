<?php 
if ($_SERVER['REQUEST_METHOD'] == 'GET'){

	require '../config.php';
	require ROOT_PATH . '/connect.php';
	require PHP_PATH . '/Pagination.php';
	require PHP_PATH . '/checkUserVotePromotion.php';	
	require PHP_PATH . '/countPromotionComments.php';

	$userId = 0;
	if (isset($_SESSION['user_id'])) {
		$userId = $_SESSION['user_id'];
	}	
	
	$page = !empty($_GET['page']) ? $_GET['page'] : 1;
	$limit = 10;
	
	$start = ($page - 1) * $limit;
	if($start < 0) 
		$start = 0;

	$whereLow = $whereHigh = $orderBy = $whereSite = $whereSearch = "";
	
	//general conditional
	$where = " WHERE promotions.published = 1 AND domains.banned = 0 AND domains.alias IS NOT NULL";
	
	//low slider conditional		
	if(!empty($_GET['l']))
		if(is_numeric($_GET['l']))
			$whereLow = " AND GREATEST(promotions.sale_price_low, promotions.sale_price_high) >= " . $_GET['l'];
	//high slider conditional		
	if(!empty($_GET['h']) || isset($_GET['h']))
		if(is_numeric($_GET['h']))
			$whereHigh = " AND GREATEST(promotions.sale_price_low, promotions.sale_price_high) <= " . $_GET['h'];	
	//site conditional		
	if(!empty($_GET['site']))
		$whereSite = " AND domains.alias = '" . $_GET['site'] . "'";
	//search conditional		
	if(!empty($_GET['search']))
		$whereSearch = " AND promotions.title LIKE '%" . $_GET['search'] . "%'";
	
	//order by	
	if(!empty($_GET['sort'])){
		if($_GET['sort'] == "trending")
			$orderBy = " ORDER BY 
    LOG10(ABS(promotions.thumbs_up - promotions.thumbs_down) + 1) * SIGN(promotions.thumbs_up - promotions.thumbs_down)
    + (UNIX_TIMESTAMP(promotions.created) / 300000) DESC";
		if($_GET['sort'] == "newest")
			$orderBy = " ORDER BY promotions.created DESC";
		if($_GET['sort'] == "endSoon")
			$orderBy = " ORDER BY promotions.expired ASC";
		if($_GET['sort'] == "cheapest")
			$orderBy = " ORDER BY promotions.sale_price_low ASC";
	} else {
		$orderBy = " ORDER BY 
    LOG10(ABS(promotions.thumbs_up - promotions.thumbs_down) + 1) * SIGN(promotions.thumbs_up - promotions.thumbs_down)
    + (UNIX_TIMESTAMP(promotions.created) / 300000) DESC";
	}
	
	//get total number of rows
	$stmt = "SELECT COUNT(*) as count FROM promotions INNER JOIN domains ON promotions.domain_id = domains.id";
	$stmt .= $where;
	if($whereLow)
		$stmt .= $whereLow;
	if($whereHigh)
		$stmt .= $whereHigh;
	if($whereSite)
		$stmt .= $whereSite;
	if($whereSearch)
		$stmt .= $whereSearch;
	
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$rowCount = $row['count'];
	
	//initialize pagination class
	$pageConfig = array(
		'currentPage'=>$page, 
		'totalRows'=>$rowCount, 
		'perPage'=>$limit);
	$pagination = new Pagination($pageConfig);
	
	//get rows
	$stmt = "SELECT domains.alias, promotions.id, promotions.title, promotions.url, promotions.original_price_low, promotions.original_price_high, promotions.sale_price_low, promotions.sale_price_high, promotions.thumbs_up, promotions.thumbs_down FROM promotions INNER JOIN domains ON promotions.domain_id = domains.id";
	$stmt .= $where;
	if($whereLow)
		$stmt .= $whereLow;
	if($whereHigh)
		$stmt .= $whereHigh;
	if($whereSite)
		$stmt .= $whereSite;
	if($whereSearch)
		$stmt .= $whereSearch;
	
	//order data by
	$stmt .= $orderBy;
	
	//limit data
	$stmt = $stmt . " LIMIT " . $start . "," . $limit;
	
	$result = $conn->query($stmt);
	
	if($rowCount > 0){
	?>
	
	<div class="result-toolbar">
		<div class="total-result"><?php echo $pagination->showing();?></div>
		<div class="pagination"><?php echo $pagination->createLinks(); ?></div>
	</div>	
	
	<ul class="result-list">	
		<?php
			while($row = $result->fetch_assoc()){
		?>		
			<li class="row">
				<div id="<?php echo $row['id']; ?>" class="result-item">
					<div class="item-header">
						<h4>
							<a href="<?php echo $row['url']; ?>"><?php echo $row['title']; ?></a>
						</h4>
						<a class="domain" data-value="<?php echo $row['alias']; ?>"><?php echo $row['alias']; ?></a>
					</div>
					
					<?php 
						if($row['original_price_low'] != 0 && $row['original_price_high'] != 0)
							$orgPrice = $row['original_price_low'] . 'zł do ' . $row['original_price_high'] . 'zł';
						else if($row['original_price_low'] != 0)
							$orgPrice = $row['original_price_low'] . 'zł';
						else
							$orgPrice = null;
						
						if($row['sale_price_low'] != 0 && $row['sale_price_high'] != 0)
							$salePrice = $row['sale_price_low'] . 'zł do ' . $row['sale_price_high'] . 'zł';
						else if($row['sale_price_low'] != 0)
							$salePrice = $row['sale_price_low'] . 'zł';
						else
							$salePrice = 'Za darmo';
					?>
					
					<?php if(!empty($orgPrice)){ ?>
						<span class="original-price"><?php echo $orgPrice; ?></span>
					<?php } ?>
					<span class="price"><?php echo $salePrice; ?></span>
					
					<div class="item-scope">
					
						<?php
							$commentCount = countPromotionComments($conn, $row['id']);
						?>
						
						<div class="comment action">
							<a href="<?php echo ROOT_URL . 'comments.php?id=' . $row['id']; ?>">
								<span class="icon"><i class="fa fa-comment" aria-hidden="true"></i></span>
								<span class="text"><?php echo $commentCount; ?></span>
							</a>
						</div>
						<div class="separator"></div>
						
						<?php
							$userVote = checkUserVotePromotion($conn, $userId, $row['id']);
						?>
						
						<div class="like action <?php echo $userVote == "1"? "up" : ""; ?>" data-event-action="<?php echo $userVote == "1"? "remove-vote" : "upvote"; ?>">
							<span class="icon"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
							<span class="text"><?php echo $row['thumbs_up']; ?></span>
						</div>
						<div class="dislike action <?php echo $userVote == "-1"? "down" : ""; ?>" data-event-action="<?php echo $userVote == "-1"? "remove-vote" : "downvote"; ?>">
							<span class="icon"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
							<span class="text"><?php echo $row['thumbs_down']; ?></span>
						</div>
					</div>
				</div>
			</li>
		<?php
			}
		?>
	</ul>
	
	<div class="result-toolbar">
		<div class="pagination"><?php echo $pagination->createLinks(); ?></div>
	</div>	

<?php 
	}
	else {
?>
	<div class="no-results">
		<span class="emoji">(╯°□°)╯︵ ┻━┻</span>
		<span class="no-results-text">Nie znaleziono żadnych promocji. Spróbuj zmienić swoje ustawienia filtrów albo je <a href="<?php echo ROOT_URL; ?>">zresetuj</a>.</span>
	</div>
<?php	
		}
	}
?>

<script>
//sort-by page
$( ".pagination a" ).click(function() {
	if( uri.hasSearch("page") )
		uri.setSearch("page", $(this).attr("data-value"));
	else
		uri.addSearch("page", $(this).attr("data-value"));
	
	window.history.replaceState({}, document.title, uri);
	searchFilter();
});	

//sort-by-website with item domain
$( ".item-header .domain" ).click(function() {
	if( uri.hasSearch("site") )
		uri.setSearch("site", $(this).attr("data-value"));
	else
		uri.addSearch("site", $(this).attr("data-value"));
	
	window.history.replaceState({}, document.title, uri);
	searchFilter();
});

//voting function
function vote(e, dataEvent, id) {
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
					$(e).addClass("up");
					$(e).attr("data-event-action", "remove-vote");
					$(e).closest(".item-scope").find(".dislike").removeClass("down");
					$(e).closest(".item-scope").find(".dislike").attr("data-event-action", "downvote");
				}
				else {
					$(e).removeClass("up");
					$(e).attr("data-event-action", "upvote");
				}
			}
			
			if($(e).is('.dislike')){
				if(dataEvent == "downvote"){
					$(e).addClass("down");
					$(e).attr("data-event-action", "remove-vote");
					$(e).closest(".item-scope").find(".like").removeClass("up");
					$(e).closest(".item-scope").find(".like").attr("data-event-action", "upvote");
				}
				else {
					$(e).removeClass("down");
					$(e).attr("data-event-action", "downvote");
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

$( ".item-scope .action.like" ).click(function() {
	var dataEvent = $(this).attr("data-event-action");
	var id = $(this).closest(".result-item").attr("id");
	var e = $(this);
	
	vote(e, dataEvent, id);
});
$( ".item-scope .action.dislike" ).click(function() {
	var dataEvent = $(this).attr("data-event-action");
	var id = $(this).closest(".result-item").attr("id");
	var e = $(this);	
	
	vote(e, dataEvent, id);
});

</script>
