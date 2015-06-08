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
		$("#table_select").change(function(){
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	        document.getElementById("column_select").innerHTML = xmlhttp.responseText;
	        }
			xmlhttp.open("GET","getmenu.php?q=" + $("#table_select").val(),true);
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
echo $_SESSION['db'];
mysql_select_db ($db);
echo $_POST['result_format'];
$query =  "SELECT * FROM " . $_POST['table'] . " WHERE " . $_POST['column'] . " = " . $_POST['value'] . ";";
if ($_POST['result_format']=='txt')
{
	echo $query;
}
else if ($_POST['result_format']=='csv')
{
	$_SESSION['query'] = $query;
	echo '<script type="text/javascript">
	location.replace("csv.php");
	</script>';
	
}

mysql_close($conn);
?>


</div>
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>



