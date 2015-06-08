<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>KL Front End</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  <script src="jquery-1.11.3.min.js"></script>
  
  <script type="text/javascript">
  	$(document).ready(function(){
		$(".table_select").change(function(){
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	        $(".column_select").innerHTML = xmlhttp.responseText;
	        }
			xmlhttp.open("GET","getmenu.php?q=" + $(".table_select").val(),true);
	        xmlhttp.send();
		});

	});
  </script>
  
  
  
  </head>
  <body>
  
  <div class="container-fluid">
  <?php include 'header.php'; ?>
  <div class="col-md-8 col-md-offset-2">
<?php

$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);
$db = $_POST['db'];
$_SESSION['db'] = $db;
echo '<h1>'.$_SESSION['db'] . '</h1>';
mysql_select_db ($db);
$table_list =  mysql_list_tables($db);
echo '<br>I want rows where: <br>';

echo '<form action="query_run.php" method="post">
<select name="table" id="table_select" class="form-control">'; // Open drop down box

while ($row = mysql_fetch_row($table_list)) {
	echo '<option value="'.$row[0] . '">'.$row[0] .'</option>';
}

echo '</select>';// Close drop down box
echo '<select id="column_select" class="form-control" name="column"></select>';

echo '<input type="radio" name ="relation" value="checked"> all rows ';
echo '<input type="radio" name ="relation" value="checked"> exactly ';
echo '<input type="radio" name ="relation"> similar to ';
echo '<input type="radio" name ="relation"> in ';

echo '<input type="text" name="value"><br><br>';

echo '<a>AND</a>  ';
echo '<a>OR</a><br>';

mysql_close($conn);
?>

Return results as:

<input type="radio" name ="result_format" value="txt"> Text query
<input type="radio" name ="result_format"> Python Object
<input type="radio" name ="result_format"> .XLS
<input type="radio" name ="result_format"> .CSV
<input type="radio" name ="result_format"> C Object
<input type="radio" name ="result_format"> S3 stream (for adding to an S3 bucket)
<br><br>

<input type="submit" name="submit"></form>
</div>
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>



