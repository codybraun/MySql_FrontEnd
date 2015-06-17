<?php session_start(); ?>   
<?php
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];
 
// Create connection
$conn = mysql_connect($servername, $username, $password);
$db = $_POST['db'];
mysql_select_db ($db);
$table_list =  mysql_list_tables($db);
$response_rows = array();
while ($row = mysql_fetch_row($table_list)) {
	$response_rows[] =  $row[0];
}
echo json_encode($response_rows);
mysql_close($conn);
?>