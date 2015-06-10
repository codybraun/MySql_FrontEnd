<?php session_start(); ?>   
<?php
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];

// Create connection
$conn = mysql_connect($servername, $username, $password);
$query = htmlspecialchars($_GET["q"]);
$query = substr($query, 0, -1) . " LIMIT 10;";
mysql_select_db ($_SESSION['db']);
$results =  mysql_query($query, $conn);

if (!$results) {
	echo "[" . $query . "] is not yet a valid query";
	echo  mysql_error();
}
else{
	while ($row = mysql_fetch_array($results)) {
		echo $row[0];
	}
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
