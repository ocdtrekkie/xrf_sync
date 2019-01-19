<?php
require_once("includes/global.php");
$access_key=mysqli_real_escape_string($xrf_db, $_GET['access_key']); //00accesskey
$message_type=mysqli_real_escape_string($xrf_db, $_GET['message_type']); //heartbeat, alert
$destination=mysqli_real_escape_string($xrf_db, $_GET['destination']); //server, broadcast
$message=mysqli_real_escape_string($xrf_db, $_GET['message']); //ciphertext

$identifysender = mysqli_prepare($xrf_db, "SELECT descr FROM y_nodes WHERE access_key=?");
mysqli_stmt_bind_param($identifysender,"s", $access_key);
mysqli_stmt_execute($identifysender);
mysqli_stmt_store_result($identifysender);
if (mysqli_stmt_num_rows($identifysender) == 1)
{
	mysqli_stmt_bind_result($identifysender, $sendername);
	mysqli_stmt_fetch($identifysender);
	echo "Message authenticated as $sendername. It is a $message_type bound for $destination.";
}
else { echo "Access denied."; }

mysqli_close($xrf_db);
?>