<?php session_start(); ?>   
<?php
$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
$username = $_SESSION['username'];
$password = $_SESSION['password'];
 
// Create connection
$conn = mysql_connect($servername, $username, $password);
$db = htmlspecialchars($_GET["q"]);
mysql_select_db ($db);
$table_list =  mysql_list_tables($db);

while ($row = mysql_fetch_row($table_list)) {
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
