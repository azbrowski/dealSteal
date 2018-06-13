<?php
//returns converted currency based on Free Currency Converter API
function currencyConverter($from_Currency, $to_Currency, $amount) {
	$from_Currency = urlencode($from_Currency);
	$to_Currency = urlencode($to_Currency);
	
  $url = file_get_contents('http://free.currencyconverterapi.com/api/v3/convert?q=' . $from_Currency . '_' . $to_Currency . '&compact=ultra');
  $json = json_decode($url, true);
  $converted_currency = $json[$from_Currency . '_' . $to_Currency] * $amount;
  
	return $converted_currency;
}
?>