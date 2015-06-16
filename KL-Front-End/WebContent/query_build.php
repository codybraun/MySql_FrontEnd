<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>

<?php
	
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);
	
//grab db and table from first page
$db = $_POST['db'];
$table = $_POST['table'];
$_SESSION['db'] = $db;
$_SESSION['table'] = $table;
	

	
//get lists of tables and columns from main table
mysql_select_db ($db);
$table_list =  mysql_list_tables($db);
$query = "SHOW COLUMNS FROM ". $table . ";";
mysql_select_db ($_SESSION['db']);
$field_list =  mysql_query($query, $conn);
$rows = array();
while ($row = mysql_fetch_array($field_list)) {
	$rows[] = $row;
}
$js_columns = json_encode($rows);
//get the list of tables for use in javascript
$rows = array();
while($row = mysql_fetch_array($table_list)) {
	$rows[] = $row;
}
$js_tables = json_encode($rows);
mysql_close($conn);
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>KL Front End</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/custom.css">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<script src="jquery-1.11.3.min.js"></script>

</head>
<body>
	<div class="container-fluid">
		<?php include 'header.php'; ?>
		<div class="col-md-8 col-md-offset-2" id="main_wrapper">
		<?php echo '<h1>'.$_SESSION['db'] . " : " . $_SESSION['table'] . '</h1>'; ?>
		<form action="" method="post" id="main_form"><div id="all_queries"></div> <br><br> <div id="all_joins"></div>

		
	<div class="join_button_wrapper">
	<button type="button"
		class="join_button btn-default col-md-4 col-md-offset-4">JOIN</button>
	</div>
	
	<div class="results_wrapper">
		<br> <br>Return results as: <br><br>
		<input class="results_select" type="radio" name="result_format" value="txt"> Text query 
		<input class="results_select" type="radio" name="result_format" value="python">Python Object
		<input type="radio" class="results_select" name="result_format" value="xls"> .XLS 
		<input type="radio" class="results_select" name="result_format" value="csv"> .CSV 
		<input type="radio" name="result_format" value="c" class="results_select"> C Object 
		<input type="radio" name="result_format" value="s3" class="results_select"> S3 stream of files
		 <br> <br>
		<button type="submit" name="submit" id="submit_button"
			class="submit_button btn-default col-md-4 col-md-offset-4">Submit</button>
	</div>
	
	</form>
		</div>
<div class="col-md-1" style="position: fixed;">
	<h3>Query Preview</h3>
	<br>
	<div id="query_div"></div>
</div>
<div class="col-md-1 col-md-offset-10" style="position: fixed;">
	<h3>Results Preview</h3>
	<br> <br>
	<div id="response_div">
		<table class="table table-bordered table-striped" id="resp_table"></table>
	</div>
		</div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>
</body>
</html>



