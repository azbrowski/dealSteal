<?php
//returns user name
function getUserName($conn, $id){
	
	$stmt = "SELECT login FROM users WHERE id = " . $id;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$login = $row['login'];
	
	if(!empty($login))
		return $login;
	return 0;
}
?>