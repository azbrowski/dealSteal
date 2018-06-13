<?php 
	require __DIR__ . '/config.php';
	require ROOT_PATH . '/connect.php';
	require MODELS_PATH . '/promotion.php';	
	require PHP_PATH . '/currencyConverter.php';
	
	if (!isset($_SESSION['user_id'])) {
		header('Location: index.php');
		exit();
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		//Id uzytkownika
		$userId = $_SESSION['user_id'];	
		
		//Tytul promocji
		$title = !empty($_POST["title"]) ? $_POST["title"] : "";
		
		//Url promocji
		$url = !empty($_POST["url"]) ? $_POST["url"] : "";
		
		//Ceny
		if($_POST['currency'] == "PLN"){
			$orgPriceLow = !empty($_POST["old-price-low"]) ? $_POST["old-price-low"] : 0;
			$orgPriceHigh = !empty($_POST["old-price-high"]) ? $_POST["old-price-high"] : 0;
			$salePriceLow = !empty($_POST["new-price-low"]) ? $_POST["new-price-low"] : 0;	
			$salePriceHigh = !empty($_POST["new-price-high"]) ? $_POST["new-price-high"] : 0;
		} else {
			$orgPriceLow = !empty($_POST["old-price-low"]) ? currencyConverter($_POST['currency'], "PLN", $_POST["old-price-low"]) : 0;
			$orgPriceHigh = !empty($_POST["old-price-high"]) ? currencyConverter($_POST['currency'], "PLN", $_POST["old-price-high"]) : 0;
			$salePriceLow = !empty($_POST["new-price-low"]) ? currencyConverter($_POST['currency'], "PLN", $_POST["new-price-low"]) : 0;	
			$salePriceHigh = !empty($_POST["new-price-high"]) ? currencyConverter($_POST['currency'], "PLN", $_POST["new-price-high"]) : 0;
		}
		
		//Data utworzenia promocji
		$created = date("Y-m-d H:i:s");
		
		//Data wygasniecia promocji
		$expired = $_POST['date-expiration'];
		
		//Kciuk autora wpisu
		$thumb = 1;
		
		//Id domeny ze sprawdzeniem czy takowa istnieje
		$domain = parse_url($url, PHP_URL_HOST);
		$stmt = $conn->prepare("SELECT id, url, alias, banned FROM domains WHERE url = ?");
		$stmt->bind_param("s", $domain);
		$stmt->execute();
		$stmt->store_result();
		
		if ($stmt->num_rows > 0) {
			
			$stmt->bind_result($result_id, $result_url, $result_alias, $result_banned);
			$stmt->fetch();
			
			//Sprawdzamy czy wprowadzona domena nie jest zbanowana przez administracje
			if($result_banned == 1){
				$err_msg = 'Strona ' . $domain . ' jest zablokowana na naszej stronie i nie można z niej dodawać linków.';
			//Wszystko jest ok
			}
			
			$domainId = $result_id;
			
			if(empty($result_alias))
				$published = 0;
			else
				$published = 1;
			
		} else {
			
			//Nieznana domena wiec wstawiana promocja musi byc zatwierdzona przez moderatora
			$published = 0;
			
			//Dodajemy nowa domene do listy
			$stmt = $conn->prepare("INSERT INTO domains(url) VALUES (?)");
			$stmt->bind_param("s", $domain);
			$stmt->execute();
			
			//Wyciagamy id domeny z nowo dodanej domeny
			$stmt = $conn->prepare("SELECT id FROM domains WHERE url = ?");
			$stmt->bind_param("s", $domain);
			$stmt->execute();
			$stmt->store_result();
			
			$stmt->bind_result($result_id);
			$stmt->fetch();
			$domainId = $result_id;
			
		}
		
		if(!isset($err_msg)){
			$promocja = new Promotion( $userId, $domainId, $title, $url, $orgPriceLow, $orgPriceHigh, $salePriceLow, $salePriceHigh, $created, $expired, $published );
			//echo $promocja->userId .'<br>';
			//echo $promocja->domainId .'<br>';
			//echo $promocja->title .'<br>';	
			//echo $promocja->url .'<br>';	
			//echo $promocja->orgPriceLow .'<br>';
			//echo $promocja->orgPriceHigh .'<br>';
			//echo $promocja->salePriceLow .'<br>';
			//echo $promocja->salePriceHigh .'<br>';
			//echo $promocja->created .'<br>';
			//echo $promocja->expired .'<br>';
			//echo $promocja->published .'<br>';

			$stmt = $conn->prepare("INSERT INTO promotions( 
				user_id, 
				domain_id, 
				title,
				url,
				original_price_low, 
				original_price_high, 
				sale_price_low, 
				sale_price_high, 
				created, 
				expired, 
				thumbs_up,
				published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("iissddddssii",
				$promocja->userId,
				$promocja->domainId,
				$promocja->title,
				$promocja->url,
				$promocja->orgPriceLow,
				$promocja->orgPriceHigh,
				$promocja->salePriceLow,
				$promocja->salePriceHigh,
				$promocja->created,
				$promocja->expired,
				$thumb,
				$promocja->published);
			
			$stmt->execute();
			
			//grab id of added promotion
			$lastId =  $conn->insert_id;

			//add vote up for own content
			$stmt = $conn->prepare("INSERT INTO promotion_thumbs(user_id, promotion_id, created, vthumbs) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("iisi", $userId, $lastId, $created, $thumb);
			$stmt->execute();
			
			$stmt->close();
			$conn->close();	
			
			header('Location: ' . ROOT_URL . "comments.php?id=" . $lastId);
			exit;
			
		}
	}
	
	$pageTitle = 'Dodaj promocję';
	
	include RESOURCE_PATH . '/header.php'; 
	include RESOURCE_PATH . '/add-promotion-form.php'; 
	include RESOURCE_PATH . '/footer.php'; 
	
?>