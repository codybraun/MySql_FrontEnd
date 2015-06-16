<?php session_start(); ?>
<?php
include 'query_parse.php';
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);
$db = $_POST['db'];
mysql_select_db ($db);

echo (json_encode(parse_query(true)));

mysql_close($conn);
?>
