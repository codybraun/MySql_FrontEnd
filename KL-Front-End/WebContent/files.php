<?php session_start();
include 'query_parse.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);

$zip = new ZipArchive();
$tmp_file = tempnam('.','') . ".zip";
$zip->open($tmp_file, ZipArchive::CREATE);

$rows = array();
$parsed_query = parse_query(false);
$column_to_iter = $parsed_query['file_info'][1];
$left = $parsed_query['file_info'][0];
$right = $parsed_query['file_info'][2];

$files = array();
$i =0;
foreach ($parsed_query['response'] as $row){
	//gets hit with 403s
	//$download = file_get_contents($left . $row[$column_to_iter] . $right);
	$url = $left . $row[$column_to_iter] . $right;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	$download= curl_exec($ch);
	curl_close($ch);
	$zip->addFromString($i . ".pdf", $download);
	$i ++;
}
$zip->close();
echo basename($tmp_file) ;
?>