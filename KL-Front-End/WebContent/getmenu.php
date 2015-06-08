<?php session_start(); ?>   
<?php
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);
$table = htmlspecialchars($_GET["q"]);
$query = "SHOW COLUMNS FROM ". $table . ";";
mysql_select_db ($_SESSION['db']);
$field_list =  mysql_query($query, $conn);

if (!$field_list) {
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $query;
	die($message);
}
while ($row = mysql_fetch_array($field_list)) {
	echo "<option value=". $row[0]. ">".$row[0] ."</option>";
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
