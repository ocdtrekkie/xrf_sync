<?php
require_once("includes/global.php");
$access_key=mysqli_real_escape_string($xrf_db, $_GET['access_key']); //00accesskey
$message_type=mysqli_real_escape_string($xrf_db, $_GET['message_type']); //heartbeat, alert, command, message
$destination=mysqli_real_escape_string($xrf_db, $_GET['destination']); //server, broadcast, NODE
$message=mysqli_real_escape_string($xrf_db, $_GET['message']); //ciphertext

$identifysender = mysqli_prepare($xrf_db, "SELECT pool_id, descr, static, last_ip_addr FROM y_nodes WHERE access_key=?");
mysqli_stmt_bind_param($identifysender, "s", $access_key);
mysqli_stmt_execute($identifysender);
mysqli_stmt_store_result($identifysender);
if (mysqli_stmt_num_rows($identifysender) == 1)
{
	mysqli_stmt_bind_result($identifysender, $senderpool_id, $descr, $static, $last_ip_addr);
	mysqli_stmt_fetch($identifysender);
	echo "Sender authenticated as $descr. It is a $message_type bound for $destination.";
	
	$new_ip_addr = getenv("REMOTE_ADDR");
	if ($new_ip_addr != $last_ip_addr && $static == 1) {
		echo " Static IP change detected on always-on node.";
		$logipchange = mysqli_prepare($xrf_db, "INSERT INTO g_log (uid, date, event) VALUES (?, NOW(), ?)");
		$logiptext = "Sync: Node " . $descr . " IP changed from " . $last_ip_addr . " to " . $new_ip_addr . ".";
		mysqli_stmt_bind_param($logipchange, "is", $xrf_myid, $logiptext);
		mysqli_stmt_execute($logipchange) or die(mysqli_error($xrf_db));
		}
	
	$updatenode = mysqli_prepare($xrf_db, "UPDATE y_nodes SET last_seen = NOW(), last_ip_addr = ?, user_agent = ? WHERE access_key = ?");
	$user_agent = mysqli_real_escape_string($xrf_db, $_SERVER['HTTP_USER_AGENT']);
	mysqli_stmt_bind_param($updatenode, "sss", $new_ip_addr, $user_agent, $access_key);
	mysqli_stmt_execute($updatenode) or die(mysqli_error($xrf_db));
	
	if ($message_type == "message" && $destination != "server" && $destination != "broadcast") {
		// This is a message to another node
		$identifyrecvr = mysqli_prepare($xrf_db, "SELECT pool_id FROM y_nodes WHERE descr=?");
		mysqli_stmt_bind_param($identifyrecvr, "s", $destination);
		mysqli_stmt_execute($identifyrecvr);
		mysqli_stmt_store_result($identifyrecvr);
		if (mysqli_stmt_num_rows($identifyrecvr) == 1)
		{
			mysqli_stmt_bind_result($identifyrecvr, $recvrpool_id);
			mysqli_stmt_fetch($identifyrecvr);
			if ($recvrpool_id == $senderpool_id) {
				$storemessage = mysqli_prepare($xrf_db, "INSERT INTO y_messages (source, dest, sent, mesg) VALUES (?, ?, NOW(), ?)");
				mysqli_stmt_bind_param($storemessage, "sss", $descr, $destination, $message);
				mysqli_stmt_execute($storemessage) or die (mysqli_error($xrf_db));
				echo "Message queued for delivery.";
			} else { echo "Unauthorized inter-pool communication."; }
		} else { echo "Unknown destination."; }
	}
}
else { echo "Access denied."; }

mysqli_close($xrf_db);
?>
