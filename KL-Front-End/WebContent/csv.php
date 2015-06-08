<?php session_start();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);

$query = $_SESSION['query'];
echo $query;
$results =  mysql_query($query, $conn);
echo $results;
$csv_file = fopen('php://output', 'w');
fputcsv($csv_file, $results);

?>