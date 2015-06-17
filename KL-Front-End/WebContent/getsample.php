<?php
session_start();
?>
<?php
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);

$table = $_POST['table'];
$field = $_POST['field'];
$query = "SELECT " . $field . " FROM " . $table . " LIMIT 3;";

mysql_select_db ($_SESSION['db']);
$response = mysql_query($query);

//check if got response
if (!$response || $response==null) {
	$response  = 'Invalid query: ' . mysql_error() . "\n";
	$response .= 'Whole query: ' . $query;
	echo $response;
}

//put responses in array to return in json
else{
	$response_rows = array();
	while($r = mysql_fetch_row($response)) {
		$response_rows[] = $r[0];
	}
	echo json_encode($response_rows);
}
mysql_close($conn);
?>