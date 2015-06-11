<?php session_start(); ?>
<?php

$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);
$db = $_POST['db'];
mysql_select_db ($db);

$query = "SELECT * FROM " . $_SESSION['table'] . " WHERE ";
foreach ($_POST as $key => $value){
	$sub_query = "";
	if ($value['right_parens'] == 'true'){
		$sub_query = ')' . $sub_query;
	}
	if ($value['left_parens'] == 'true'){
		$sub_query = '(' . $sub_query;
	}
	$sub_query = $sub_query . " " . $value['column'];
	$search = $value['where_text'];
	
	if ($value['where'] == 'exact'){
		$sub_query = $sub_query . ' = "' . $search . '"';
	}
	else if ($value['where'] == "similar"){
		$sub_query = $sub_query . " LIKE %" . $search . "%";
	}
	
	$query = $query . $sub_query . " " . strtoupper($value['combine']);
}
$query = $query . ";";

mysql_select_db ($_SESSION['db']);
$response = mysql_query(substr($query, 0, -1) . " LIMIT 10;");

//check if got response
if (!$response) {
	$response  = 'Invalid query: ' . mysql_error() . "\n";
	$response .= 'Whole query: ' . $query;
	echo $response;
}

//put responses in array to return in json
else{
	$response_rows = array();
	while($r = mysql_fetch_array($response)) {
		$response_rows[] = $r;
	}
}

for($i = 0; $i < mysql_num_fields($response); $i++) {
	$columns[] = mysql_fetch_field($response, $i);
}

echo json_encode(array("query" => $query, "response"=> $response_rows, "columns" => $columns));

mysql_close($conn);
?>
