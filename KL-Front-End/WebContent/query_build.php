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
			
			//headers
			echo '<h1>'.$_SESSION['db'] . " : " . $_SESSION['table'] . '</h1>';
			
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

			echo '<form action="" method="post"><div id="all_queries"></div>';

			mysql_close($conn);
			?>

			<script type="text/javascript"> 
  	$(document).ready(function(){
	    var tables = <?php echo $js_tables; ?>;
	    var table_columns = <?php echo $js_columns; ?>;
		var select_id = 0;
		  jQuery.fn.new_query = function(method) {
			  return this.each(function() {
				 $("#all_queries").append('<div class="query_wrapper">');
				 $target = $(".query_wrapper:last");
				 $target.append('<h1>' + method + '</h1>');
				 $target.append('<button type="button" class="pull-right btn indent">¶</button>');
				 $target.append('<button type="button" class="pull-right btn btn-danger kill_query">REMOVE</button>');
				 $target.append('<select class="column_select form-control" name="column' + select_id + '"></select><br>');
					for (idx=0; idx< table_columns.length; idx++){
						$target.find(".column_select").append("<option value= '" + table_columns[idx][0] + "'> "+ table_columns[idx][0] + "</option>");
					};
					$target.append('<div class="where_wrapper"><input class="where_select" type="radio" checked="checked" name ="where' + select_id + '" value="exact"> is exactly <input type="radio" class="where_select" name ="where' + select_id + '" value="similar"> is similar to <input class="where_select" type="radio" name ="where' + select_id + '" value="in"> is in <input type="radio" class="where_select" name ="where' + select_id + '" value="dropdown"> Select from dropdown <br><input class="where_text" name="where_text' + select_id + '" type="text" >  </div> <br><br>');
					$target.append('<div class="combine"><button type="button" class="and btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
					select_id ++;
					  });
  		   }

		  jQuery.fn.new_in_query = function(method) {
			  return this.each(function() {
				 $("#all_queries").append('<div class="in_wrapper pull-right">');
				 $target = $(".in_wrapper:last");
				 $target.append('<h1>' + method + '</h1>');
				 $target.append('<button type="button" class="pull-right btn btn-danger kill_query">REMOVE</button>');
				 $target.append('<select class="column_select form-control" name="column' + select_id + '"></select><br>');
					for (idx=0; idx< table_columns.length; idx++){
						$target.find(".column_select").append("<option value= '" + table_columns[idx][0] + "'> "+ table_columns[idx][0] + "</option>");
					};
					$target.append('<div class="where_wrapper"><input class="where_select" type="radio" checked="checked" name ="where' + select_id + '" value="exact"> is exactly <input type="radio" class="where_select" name ="where' + select_id + '" value="similar"> is similar to <input class="where_select" type="radio" name ="where' + select_id + '" value="in"> is in <input type="radio" class="where_select" name ="where' + select_id + '" value="dropdown"> Select from dropdown <br><input class="where_text" name="where_text' + select_id + '" type="text" >  </div> <br><br>');
					$target.append('<div class="combine"><button type="button" class="and_in btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or_in btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
					select_id ++;
					  });
  		   }

			//add a join selecting box
		  jQuery.fn.new_join = function() {
			  return this.each(function() {
				 $("#main_wrapper").append('<div class="join_wrapper">');
				 $target = $(".join_wrapper:last");
				 $target.append('<h1>Join</h1>');
				 
				 $target.append('Table: <select name="table' + select_id + '" class="table_select form-control"><br>');
				 $target.append('Where it shares this column: <select name="table' + select_id + '" class="column_select form-control"><br>');
					
				 select_id ++;
				 for (idx=0; idx< tables.length; idx++){
						$target.find(".table_select").append("<option value= '" + tables[idx]["Tables_in_arxiv"] + "'> "+tables[idx]["Tables_in_arxiv"] + "</option>");
					};
				 
					  });
  		   }
		   

		 //"where" selection 
		 $("html").on('change', '.where_select', function(event) {
			 if ($(event.target).val() != "exact" && $(event.target).val() != "similar"){
			 	$(event.target).parent().find(".where_text").hide();
			 }
			 else{
				 $(event.target).parent().find(".where_text").show();
			 }
			 
			 if ($(event.target).val() == "in")
			{
				 $(event.target).parents("#all_queries").new_in_query("In");	 
			 }
			 
		 });

		//and click handler
		$("html").on('click', '.and', function(event) {
			$(event.target).parent(".combine").addClass("and");
			$(event.target).parent(".combine").hide();
			$(event.target).parents("#all_queries").new_query("and");
		});

		//or click handler
		$("html").on('click', '.or', function(event) {
			$(event.target).parent(".combine").addClass("or");
			$(event.target).parent(".combine").hide();
			$(event.target).parents("#all_queries").new_query("or");
		});

		//and in click handler
		$("html").on('click', '.and_in', function(event) {
			$(event.target).parent(".combine").addClass("and");
			$(event.target).parent(".combine").hide();
			$(event.target).parents("#all_queries").new_in_query("and");
		});

		//or in click handler
		$("html").on('click', '.or_in', function(event) {
			$(event.target).parent(".combine").addClass("or");
			$(event.target).parent(".combine").hide();
			$(event.target).parents("#all_queries").new_in_query("or");
		});

		//add a new join box
		$("html").on('click', '.join_button', function(event) {
			$(event.target).new_join();
		});

		//change tables in a join box
		$("html").on('click', '.table_select', function(event) {
			$.post('getcolumns.php', {"table" :$(event.target).val()} , function( data ) {

				target = $(event.target).find(".column_select");
				  $(target).html(data);
				  console.log(data);
				  console.log(cur_columns);
				for (idx=0; idx< cur_columns.length; idx++){
						if ($.inArray(cur_columns[idx], data)){
							$(target).find(".table_select").append("<option value= '" + cur_columns[idx] + "'> "+ cur_columns[idx] + "</option>");
						}
				}
					  },'json');
		});
		
		//remove button: remove this query, reset the previous combine box
		$("html").on('click', '.kill_query', function(event) {
			$(event.target).parents(".query_wrapper").remove();
			$(".query_wrapper:last").find(".combine").remove();
			$(".query_wrapper:last").append('<div class="combine"><button type="button" class="and btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
			
		});

		//add an open parens and check if need to indent 
		$("html").on('click', '.indent', function(event) {
			$(event.target).parents(".query_wrapper").toggleClass("indented");
			prev =null;
			$(".query_wrapper").each(function(){
				if (prev != null && prev.hasClass("indented") == $(this).hasClass("indented")){
					$(this).css("border-top-width","0px");
					prev.css("border-bottom-width","0px");
					console.log("here");
				}
				else if (prev != null)
				{
					$(this).css("border-top-width","2px");
					prev.css("border-bottom-width","2px");
				}
				prev = $(this);
				if ($(this).hasClass("indented")){
					$(this).outerWidth("75%");	
				}
				else {
					$(this).outerWidth("100%");	
				}	
			});
		});
		
		setInterval(function() {
			output_array = {};
			prev = null;
			cur_columns = [];

			//package data for query builder
			$(".query_wrapper").each(function (idx){
				query_dict = {"column":$(this).find(".column_select").val(), "where":$(this).find(".where_select:checked").val(), "where_text":$(this).find(".where_text").val() ,"subquery":" ", "combine" : "", "left_parens":"false", "right_parens":"false"};
				if ($(this).find(".combine").hasClass('and')){
					query_dict['combine'] = 'and';
				}
				else if ($(this).find(".combine").hasClass('or')){
					query_dict['combine'] = 'or';
				}
				
				if (prev != null){
				console.log(prev.hasClass("indented") + " " + !$(this).hasClass("indented"));
				}
				
				if (prev != null && prev.hasClass("indented") && !$(this).hasClass("indented")){
					query_dict['right_parens'] = 'true';
					console.log("right");
				}
				else if (prev != null && !prev.hasClass("indented") && $(this).hasClass("indented")){
					query_dict['left_parens'] = 'true';
					console.log("left");
				}
				
				prev = $(this);
				
				output_array[idx]= query_dict;
			});
			console.log(output_array);
			
			//send everything to query builder
			$.post('query_run.php', output_array, function( data ) {
				  //parsed_results = jQuery.parseJSON(data);
				  $("#query_div").html(data.query);
				  response = data.response;
				  columns = data.columns;
				  $("#resp_table").empty();
				  $(resp_table).append("<tr>");
				  $.each(columns , function() {
						column_name= this.name;
						cur_columns.push(column_name);
						$(resp_table).find("tr:last").append("<td>" + column_name + "</td>");
					  });
				  $.each(response, function(idx) {
					$(resp_table).append("<tr>");
					  $.each(columns , function() {
						column_name= this.name;
						$(resp_table).find("tr:last").append("<td>" + response[idx][column_name] + "</td>");
					  });
				  });
				  console.log(data);
			}, 'json');
	}, 5000);

		//handle form submission here 
		$( "form" ).submit(function( event ) {
			output_array = {};
			prev = null;

			//package data for query builder
			$(".query_wrapper").each(function (idx){
				query_dict = {"column":$(this).find(".column_select").val(), "where":$(this).find(".where_select:checked").val(), "where_text":$(this).find(".where_text").val() ,"subquery":" ", "combine" : "", "left_parens":"false", "right_parens":"false"};
				if ($(this).find(".combine").hasClass('and')){
					query_dict['combine'] = 'and';
				}
				else if ($(this).find(".combine").hasClass('or')){
					query_dict['combine'] = 'or';
				}
				
				if (prev != null){
				console.log(prev.hasClass("indented") + " " + !$(this).hasClass("indented"));
				}
				
				if (prev != null && prev.hasClass("indented") && !$(this).hasClass("indented")){
					query_dict['right_parens'] = 'true';
					console.log("right");
				}
				else if (prev != null && !prev.hasClass("indented") && $(this).hasClass("indented")){
					query_dict['left_parens'] = 'true';
					console.log("left");
				}
				
				prev = $(this);
				
				output_array[idx]= query_dict;
			});
			console.log(output_array);
			
			//send everything to query builder
			$.post('query_run.php', output_array, function( data ) {
				  //parsed_results = jQuery.parseJSON(data);
				  $("#query_div").html(data.query);
				  response = data.response;
				  columns = data.columns;
				  $("#resp_table").empty();
				  $(resp_table).append("<tr>");
				  $.each(columns , function() {
						column_name= this.name;
						$(resp_table).find("tr:last").append("<td>" + column_name + "</td>");
					  });
				  $.each(response, function(idx) {
					$(resp_table).append("<tr>");
					  $.each(columns , function() {
						column_name= this.name;
						$(resp_table).find("tr:last").append("<td>" + response[idx][column_name] + "</td>");
					  });
				  });
				  console.log(data);
			}, 'json');
			  event.preventDefault();
			});
		
		$("#all_queries").new_query("I want rows where: ");
		
		
	});
  </script>
			
		</div>
		<div class="col-md-1" style="position: fixed;">
			<h3>Query Preview</h3>
			<br>
			<div id="query_div"></div>
		</div>
		<div class="col-md-1 col-md-offset-10" style="position: fixed;">
			<h3>Results Preview</h3>
			<br> <br>
			<div id="response_div"><table class="table table-bordered table-striped" id="resp_table"></table></div>
		</div>
		
	</div>
	<br><br>
	<button type="button" class="join_button btn-default col-md-4 col-md-offset-4">JOIN</button>
	
	<div class="results_select col-md-8 col-md-offset-2"><br><br>Return results as: <input type="radio" name="result_format"
				value="txt"> Text query <input type="radio" name="result_format">
			Python Object <input type="radio" name="result_format"> .XLS <input
				type="radio" name="result_format"> .CSV <input type="radio"
				name="result_format"> C Object <input type="radio"
				name="result_format"> S3 stream (for adding to an S3 bucket) <br> <br>
			<button type="submit" name="submit" id="submit_button" class="submit_button btn-default col-md-4 col-md-offset-2">Submit</button></div>
			</form>
	
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>
</body>
</html>



