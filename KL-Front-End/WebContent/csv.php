<?php session_start();
include 'query_parse.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);

$csv_file = fopen('php://output', 'w');
foreach (parse_query(false)['response'] as $line)
  {
  fputcsv($csv_file, $line);
  }
fclose($csv_file);
?>