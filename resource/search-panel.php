<?php
	$stmt = $conn->prepare("SELECT MIN(LEAST(sale_price_low, sale_price_high)), MAX(GREATEST(sale_price_low, sale_price_high)) FROM promotions");
	$stmt->execute();
	$stmt->store_result();

	$stmt->bind_result($min, $max);
	$stmt->fetch();
?>

<script id="js-search-panel">
var minValue = <?php echo $min; ?>;
var maxValue = <?php echo round($max, 0, PHP_ROUND_HALF_UP); ?>;
var uri = new URI();
var params = uri.search(true);

var minSlider = minValue;
var maxSlider = maxValue;
if(params.l)
	minSlider = params.l;
if(params.h)
	maxSlider = params.h;

$( function() {
	
	//slider for price
	$( ".ui-slider" ).slider({
	  range: true,
	  min: minValue,
	  max: maxValue,
	  values: [ minSlider, maxSlider ],
	  slide: function( event, ui ) {
	    $( "span[name='slide-min']" ).html( ui.values[ 0 ] );
			$( "span[name='slide-max']" ).html( ui.values[ 1 ] );
	  },
		stop: function( event, ui ) {
			uri = new URI();
			
			if(ui.values[0] == minValue)
				uri.removeSearch("l");
			else if( uri.hasSearch("l") )
				uri.setSearch("l", ui.values[0]);
			else
				uri.addSearch("l", ui.values[0]);
			
			if(ui.values[1] == maxValue)
				uri.removeSearch("h");
			else if( uri.hasSearch("h") )
				uri.setSearch("h", ui.values[1]);
			else
				uri.addSearch("h", ui.values[1]);
			
			if( uri.hasSearch("page") )
				uri.removeSearch("page");
			
			window.history.replaceState({}, document.title, uri);
			searchFilter();
		}
	});
	
	//sort-by panel
	$( ".sort-panel a" ).click(function() {
		uri = new URI();
		
		if( uri.hasSearch("sort") )
			uri.setSearch("sort", $(this).attr("data-value"));
		else
			uri.addSearch("sort", $(this).attr("data-value"));
		
		if( uri.hasSearch("page") )
			uri.removeSearch("page");
		
		window.history.replaceState({}, document.title, uri);
		searchFilter();
	});	
	
	//sort-by-website panel
	$( ".website-panel a" ).click(function() {
		uri = new URI();
		
		if( uri.hasSearch("site") )
			uri.setSearch("site", $(this).attr("data-value"));
		else
			uri.addSearch("site", $(this).attr("data-value"));
		
		if( uri.hasSearch("page") )
			uri.removeSearch("page");
		
		window.history.replaceState({}, document.title, uri);
		searchFilter();
	});
	
	$( "span[name='slide-min']" ).html( $( ".ui-slider" ).slider( "values", 0 ) );
	$( "span[name='slide-max']" ).html( $( ".ui-slider" ).slider( "values", 1 ) );
} );
</script>

<div class="sidebar">
    <div class="search-panel">
			<div id="demo"></div>
		
			<section class="sort-panel">
				<h4>Sortuj</h4>
				<ul class="list">
					<li><a data-value="trending">Trending</a></li>
					<li><a data-value="newest">Najnowsze</a></li>
					<li><a data-value="endSoon">Dobiegające końca</a></li>
					<li><a data-value="cheapest">Od najtańszych</a></li>
				<ul>
			</section>
		
			<section class="sort-panel">
				<h4>Zakres cenowy</h4>
				<div class="slider">
					<div class="ui-slider"></div>
				</div>
				<div class="slider-legend">
					<span name="slide-min" class="slider-value"></span>
					<span name="slide-max" class="slider-value"></span>
				</div>
			</section>
			
			<section class="website-panel">
				<h4>Strona</h4>
				<ul class="list">
					<?php
						$stmt = $conn->prepare("SELECT alias FROM domains WHERE banned = 0 OR alias IS NOT NULL ORDER BY alias ASC");
						$stmt->execute();
						$stmt->store_result();
						
						if ($stmt->num_rows > 0) {
							$stmt->bind_result($alias);
							
							while ($stmt->fetch()) {
								echo "<li><a data-value='" . $alias . "'>" . ucfirst($alias) . "</a></li>";
							}
						}
						else {
								echo '<p>Brak</p>';
						}
					
					?>
				</ul>
			</section>
			
    </div>
</div>
