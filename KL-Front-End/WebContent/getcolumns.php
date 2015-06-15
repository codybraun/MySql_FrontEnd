<?php session_start(); ?>   
<?php
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];
 
// Create connection
$conn = mysql_connect($servername, $username, $password);
$table = htmlspecialchars($_POST["table"]);
$query = "SHOW COLUMNS FROM ". $table . ";";
mysql_select_db ($_SESSION['db']);
$field_list =  mysql_query($query, $conn);

if (!$field_list) {
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $query;
	die($message);
}

while($r = mysql_fetch_array($field_list)) {
		$columns[] = $r["Field"];
}

echo json_encode($columns);

mysql_close($conn);
?>