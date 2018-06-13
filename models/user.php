<?php
	
	class User
	{
		private $id;
		public $login;
		public $password;
		public $email;
		public $created;
		
		public function __construct( $Login, $Password, $Email, $Created ){
			$this->login 					= $Login;
			$this->password 			= $Password;
			$this->email 					= $Email;
			$this->created 				= $Created;
		}
	}
	
?>