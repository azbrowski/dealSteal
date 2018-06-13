<?php
//returns user role
function getUserRole($conn, $id){
	
	$stmt = "SELECT roles.name as role FROM users INNER JOIN roles ON users.role_id = roles.id WHERE users.id = " . $id;
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$role = $row['role'];
	
	if(!empty($role))
		return $role;
	return 0;
}
?>