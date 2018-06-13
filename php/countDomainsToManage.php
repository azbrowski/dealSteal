<?php
//returns number of domains that needs attention from moderators
function countDomainsToManage($conn){
	
	$stmt = "SELECT COUNT(*) as count FROM domains WHERE alias IS NULL AND banned = 0";
	$result = $conn->query($stmt);
	$row = $result->fetch_assoc();
	$count = $row['count'];
	
	if(!empty($count))
		return $count;
	return 0;
}
?>