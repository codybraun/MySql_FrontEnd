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

//get the list of tables for use in javascript
$rows = array();
while($row = mysql_fetch_array($table_list)) {
	$rows[] = $row;
}
$js_tables = json_encode($rows);

echo '<br>I want rows where: <br> <form action="query_run.php" method="post"><div id="all_queries"></div></form>';

mysql_close($conn);
?>

  <script type="text/javascript"> 
  	$(document).ready(function(){

	    var tables = <?php echo $js_tables; ?>;

		  jQuery.fn.new_query = function() {
			  return this.each(function() {
				  $target = $(this);
				  $target.html('<div class="query-wrapper"><select name="table" class="table_select form-control"><br>');
					for (idx=0; idx< tables.length; idx++){
						$target.find(".table_select").append("<option value= '" + tables[idx]["Tables_in_arxiv"] + "'> "+tables[idx]["Tables_in_arxiv"] + "</option>");
					};
					$target.find(".query-wrapper").append('<select class="column_select form-control" name="column"></select><br>');
					$target.find(".query-wrapper").append('<div class="Where_wrapper"><input class="where_select" type="radio" name ="where" checked="checked" value ="all" > all rows <input class="where_select" type="radio" name ="where" value="exact"> exactly <input type="radio" class="where_select" name ="where" value="similar"> similar to <input class="where_select" type="radio" name ="where" value="in"> in <input type="radio" class="where_select" name ="where" value="dropdown"> dropdown <br><input class="where_text" type="text" >  </div> <br><br>');
					$target.find(".query-wrapper").append('<div class="combine"><a class="and">AND</a><a class="or">OR</a><br></div>');
					  });
  		   }


		 jQuery.fn.parser = function() {
			query = "";
			table = $(this).find(".table_select").val();
			query = query + table;
			column = $(this).find(".column_select").val();
			query = query + " FROM " + column;
			return query;
		 }

		 $("html").on('change', '.where_select', function(event) {
			 if ($(event.target).val() != "exact" && $(event.target).val() != "similar"){
			 	$(event.target).parent().find(".where_text").hide();
			 }
			 else{
				 $(event.target).parent().find(".where_text").show();
			 }
		 });
		 
		$("html").on('change', '.table_select', function(event) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	        	$target = $(event.target).siblings(".column_select");
	        	$target.html(xmlhttp.responseText);
	        }
			xmlhttp.open("GET","getmenu.php?q=" + $(".table_select").val(),true);
	        xmlhttp.send();
		});
		
		$("html").on('click', '.and', function(event) {
			$(event.target).parent(".combine").addClass("and");
			$(event.target).parent(".combine").new_query();
		});

		$("html").on('click', '.or', function(event) {
			$(event.target).parent(".combine").addClass("or");
			$(event.target).parent(".combine").new_query();
		});

		setInterval(function() {
			query = "SELECT * FROM ";
			$(".query-wrapper").each(function (){
				table = $(this).find(".table_select").val();
				query = query + table + " ";
				column = $(this).find(".column_select").val();
				query = query + " WHERE " + column + " ";
				search_string = $(this).find(".where_text").val();

				if ($(this).find("input[name=where]:checked").val()=="all"){
				//don't need to handle this?
				}
				else if ($(this).find("input[name=where]:checked").val()=="exact"){
					query= query + ' = "' + search_string + '"';
				}
				else if ($(this).find("input[name=where]:checked").val()=="similar"){
					query= query + " LIKE %" + search_string + "%";
				}

				if ($(this).find(".combine").hasClass("and")){
					query = query + " AND ";
				}
				else if ($(this).find(".combine").hasClass("or")){
					query = query + " OR ";
				}
				
			});
			query = query + ";"
			$("#query_div").text(query);
		}, 1000);


		setInterval(function() {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
	        	$("#results_div").html(xmlhttp.responseText);
	        }
			xmlhttp.open("GET","getpreview.php?q=" + query);
	        xmlhttp.send();
	}, 5000);

		$("#all_queries").new_query();
	});
  </script>

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
<div class="col-md-1" style="position:fixed;"><h3>Query Preview</h3><br><div id="query_div"></div></div>
<div class="col-md-1 col-md-offset-10" style="position:fixed;"><h3>Results Preview</h3><br><br><div id="results_div"></div></div></div>
</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>



