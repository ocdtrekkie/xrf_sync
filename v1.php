<?php
require_once("includes/global.php");
$access_key=mysqli_real_escape_string($xrf_db, $_GET['access_key']); //00accesskey
$message_type=mysqli_real_escape_string($xrf_db, $_GET['message_type']); //heartbeat, alert
$destination=mysqli_real_escape_string($xrf_db, $_GET['destination']); //server, broadcast
$message=mysqli_real_escape_string($xrf_db, $_GET['message']); //ciphertext

$identifysender = mysqli_prepare($xrf_db, "SELECT descr, static, last_ip_addr FROM y_nodes WHERE access_key=?");
mysqli_stmt_bind_param($identifysender, "s", $access_key);
mysqli_stmt_execute($identifysender);
mysqli_stmt_store_result($identifysender);
if (mysqli_stmt_num_rows($identifysender) == 1)
{
	mysqli_stmt_bind_result($identifysender, $descr, $static, $last_ip_addr);
	mysqli_stmt_fetch($identifysender);
	echo "Message authenticated as $descr. It is a $message_type bound for $destination.";
	
	$new_ip_addr = getenv("REMOTE_ADDR");
	if ($new_ip_addr != $last_ip_addr && $static == 1) {
		echo " Static IP change detected on always-on node.";
		$logipchange = mysqli_prepare($xrf_db, "INSERT INTO g_log (uid, date, event) VALUES (?, NOW(), ?)");
		$logiptext = "Sync: Node " . $descr . " IP changed from " . $last_ip_addr . " to " . $new_ip_addr . ".";
		mysqli_stmt_bind_param($logipchange, "is", $xrf_myid, $logiptext);
		mysqli_stmt_execute($logipchange) or die(mysqli_error($xrf_db));
		}
	
	$updatenode = mysqli_prepare($xrf_db, "UPDATE y_nodes SET last_seen = NOW(), last_ip_addr = ? WHERE access_key = ?");
	mysqli_stmt_bind_param($updatenode, "ss", $new_ip_addr, $access_key);
	mysqli_stmt_execute($updatenode) or die(mysqli_error($xrf_db));
}
else { echo "Access denied."; }

mysqli_close($xrf_db);
?>