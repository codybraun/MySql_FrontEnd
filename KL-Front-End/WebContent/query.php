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
mysql_select_db ($db);
$db_list =  mysql_query("SHOW DATABASES");
echo 'Select a database to query: ';
echo '<form action="query_build.php" method="post" >
<select name="db" class="form-control db_select">'; //db dropdown

while ($row = mysql_fetch_object($db_list)) {
	echo '<option value="'.$row->Database . '">'.$row->Database .'</option>';
}

echo '</select>';
echo 'Select a table from that database: <select name="table" class="form-control table_select"><br><br>'; //table dropdown


mysql_close($conn);
?>
<input type="submit" class=".btn-default"></form>
</div>
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    
    <script type="text/javascript"> 
  	$(document).ready(function(){
		 
		$("html").on('change', '.db_select', function(event) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	        	$target = $(event.target).siblings(".table_select");
	        	$target.html(xmlhttp.responseText);
	        }
			xmlhttp.open("GET","gettablemenu.php?q=" + $(".db_select").val(),true);
	        xmlhttp.send();
		});
  	});
  </script>
    
  </body>
</html>



