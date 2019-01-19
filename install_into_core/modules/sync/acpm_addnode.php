<?php
require("ismodule.php");
$do = $_GET['do'];
if ($do == "add")
{
$descr = mysqli_real_escape_string($xrf_db, $_POST['descr']);
$static = mysqli_real_escape_string($xrf_db, $_POST['static']);
$static = (int)$static;
$access_key = "00" . xrf_generate_password(126);
$acc1 = substr($access_key, 0, 64);
$acc2 = substr($access_key, 64, 64);

$addnode = mysqli_prepare($xrf_db, "INSERT INTO y_nodes (descr, access_key, static) VALUES(?, ?, ?)");
mysqli_stmt_bind_param($addnode,"ssi", $descr, $access_key, $static);
mysqli_stmt_execute($addnode) or die(mysqli_error($xrf_db));

echo "<p>Node added. $descr's access key is:</p><p><font size=2>$acc1<br>$acc2</font></p>";
}
else
{
echo "<b>Add New Sync Node</b><p>";

echo "<form action=\"acp_module_panel.php?modfolder=$modfolder&modpanel=addnode&do=add\" method=\"POST\">
<table><tr><td><b>Nickname:</b></td><td><input type=\"text\" name=\"descr\" size=\"20\"></td></tr>
<tr><td><b>Always On?</b></td><td><select name=\"static\"><option value=\"0\">No</option><option value=\"1\">Yes</option></select></td></tr>
<tr><td></td><td><input type=\"submit\" value=\"Add\"></td></tr></table></form>";
}
?>