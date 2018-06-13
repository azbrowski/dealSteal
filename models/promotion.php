<?php
	
	class Promotion
	{
		public $userId;
		public $domainId;
		public $title;
		public $url;
		public $orgPriceLow;
		public $orgPriceHigh;
		public $salePriceLow;
		public $salePriceHigh;
		public $created;
		public $expired;
		public $published;
		
		public function __construct( $UserId, $DomainId, $Title, $Url, $OrgPriceLow = NULL, $OrgPriceHigh = NULL, $SalePriceLow = NULL, $SalePriceHigh = NULL, $Created, $Expired, $Published ){
			$this->userId 				= $UserId;
			$this->domainId 			= $DomainId;
			$this->title 					= $Title;
			$this->url 						= $Url;
			$this->orgPriceLow 		= $OrgPriceLow;
			$this->orgPriceHigh 	= $OrgPriceHigh;
			$this->salePriceLow 	= $SalePriceLow;
			$this->salePriceHigh 	= $SalePriceHigh;
			$this->created 				= $Created;
			$this->expired				= $Expired;
			$this->published 			= $Published;
		}
	}
	
?>