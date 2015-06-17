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
<script src="query_build.js"></script>
</head>
<body>
	<div class="container-fluid">
		<?php include 'header.php'; ?>
		<div class="col-md-8 col-md-offset-2" id="main_wrapper">
			<?php echo '<h1>'.$_SESSION['db'] . " : " . $_SESSION['table'] . '</h1>'; ?>
			<form action="" method="post" id="main_form">
				<div id="all_queries"></div>
				<br> <br>
				<div id="all_joins"></div>
				<script type="text/javascript"> 
		  	$(window).load(function(){
			    var tables = <?php echo $js_tables; ?>;
			    var cur_table = <?php echo "'" . $table . "'" ;?>;
			    var table_columns = <?php echo $js_columns; ?>;
				var select_id = 0;
				var join_id = 0;
				$.curQuery = new Object();
				$.curQuery.cur_columns = [];
				$(".file-loc").hide();
				$("#busy").hide();
				$.ajaxSetup({
				    beforeSend:function(){
				        // show image here
				        $("#busy").show();
				    },
				    complete:function(){
				        // hide image here
				        $("#busy").hide();
				    }
				});

					$.each(tables, function(index, value){
						$("#db_overview").append("<div class='overview table_wrapper' id=" + value[0] + "> <div class='btn-default overview table_content'>" + value[0] + "</div></div>");
					}
					);
				
				  jQuery.fn.new_query = function(method) {
					  return this.each(function() {
						 $("#all_queries").append('<div class="query_wrapper"></div>');
						 $target = $(".query_wrapper:last");
						 $target.append('<h1>' + method + '</h1>');
						 $target.append('<button type="button" class="pull-right btn indent">¶</button>');
						 $target.append('<button type="button" class="pull-right btn btn-danger kill_query">REMOVE</button>');
						 $target.append('<select class="column_select form-control" name="column' + select_id + '"></select><br>');
							for (idx=0; idx< table_columns.length; idx++){
								$target.find(".column_select").append("<option value= '" + table_columns[idx][0] + "'> "+ table_columns[idx][0] + "</option>");
							};
							$target.append('<div class="where_wrapper"><input class="where_select" type="radio" checked="checked" name ="where' + select_id + '" value="exact"> is exactly <input type="radio" class="where_select" name ="where' + select_id + '" value="dropdown"> Select from dropdown <input type="radio" class="where_select" name ="where' + select_id + '" value="range"> is in a range <input type="radio" class="where_select" name ="where' + select_id + '" value="similar"> is similar to <input class="where_select" type="radio" name ="where' + select_id + '" value="in"> is in the results of another query <input type="radio" class="where_select" name ="where' + select_id + '" value="dropdown"> Select from dropdown <br><input class="where_text" name="where_text' + select_id + '" type="text" >  </div> <br><br>');
							$target.append('<div class="combine"><button type="button" class="and btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
							select_id ++;
							check_borders();
							  });
		  		   }
		
				  jQuery.fn.new_in_query = function(method) {
					  return this.each(function() {
						 $("#all_queries").append('<div class="in_wrapper pull-right"></div>');
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
						 $("#all_joins").append('<div class="join_wrapper">');
						 $target = $(".join_wrapper:last");
						 $target.append('<h1>Join</h1>');
						 $target.append('<button type="button" class="pull-right btn btn-danger kill_join">REMOVE</button><br><br><br>');
						 
						 $target.append('Table: <select name="table' + join_id + '" class="table_select form-control"><br>');
						 $target.append('Where it shares this column: <select name="table' + select_id + '" class="column_select form-control"><br>');
						 $target.append('<br>Caution: You will get a lot of rows if you use these:<br><label class="join_type_left"><input type="checkbox" class="join_type_left" value="">Keep all rows from <p class="join_type_left">table1</p></label><br>');
						 $target.append('<label class="join_type_right"><input type="checkbox" class="join_type_right" value="">Keep all rows from <p class="join_type_right">table2</p></label>');
						 join_id ++;
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
		
						target = $(event.target).parent().find(".column_select");
						  $(target).html(data);
						for (idx=0; idx< $.curQuery.cur_columns.length; idx++){
								if ($.inArray($.curQuery.cur_columns[idx], data) != -1){
									$(target).append("<option value= '" + $.curQuery.cur_columns[idx] + "'> "+ $.curQuery.cur_columns[idx] + "</option>");
								}
						}
							  },'json');
					$(event.target).siblings(".join_type_left").find("p.join_type_left").html(cur_table);
					$(event.target).siblings(".join_type_right").find("p.join_type_right").html($(event.target).val());
					preview_data();
				});
		
				//change results selection
				$("html").on('click', '.results_select', function(event) {			
					result_type = $(".results_select:checked:first").val();
					if (result_type == "files" || result_type == "s3")
					{
						$(".file-loc").show();
						target = $("#file-loc-center");
						$(target).empty();
						for (idx=0; idx< $.curQuery.cur_columns.length; idx++){
								$(target).append("<option value= '" + $.curQuery.cur_columns[idx] + "'> "+ $.curQuery.cur_columns[idx] + "</option>");
							}
					}
					else
					{
						$(".file-loc").hide();
					}
				});
				
				//remove button: remove this query, reset the previous combine box
				$("html").on('click', '.kill_query', function(event) {
					$(event.target).parents(".query_wrapper").remove();
					$(".query_wrapper:last").find(".combine").remove();
					$(".query_wrapper:last").append('<div class="combine"><button type="button" class="and btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
					check_borders();
					preview_data();
				});

				//drop down table fields in overview
				$("html").on('click', '.table_content', function(event) {
					target = $(event.target).parent();
					if ($(target).children().length > 1 ){ //close instead 
						$.each($(target).children(".field_wrapper"), function(){ $(this).remove()});
					} 
					else {
					$.post('getcolumns.php', {"table" :$(event.target).html()} , function( data ) {
			
						for (idx=0; idx< data.length; idx++){
									$(target).after().append("<div class='overview field_wrapper' id= '" + data[idx] + "'><div class='field_content btn-default' > "+ data[idx] + "</div></div>");
						}
							  },'json');
					}
				});

				//drop down field samples in overview
				$("html").on('click', '.field_content', function(event) {
					target = $(event.target).parent();
					if ($(target).children().length > 1 ){ //close instead 
						$.each($(target).children(".sample_wrapper"), function(){ $(this).remove()});
					} 
					else {
					$.post('getsample.php', {"table" :$(event.target).parents(".table_wrapper").attr('id'), "field": $(event.target).html()} , function( data ) {
						$(target).after().append("<div class='overview sample_wrapper'><div class='sample_content' >Sample entries:</div></div>");
						for (idx=0; idx< data.length; idx++){
							$(target).after().append("<div class='overview sample_wrapper' id= '" + data[idx] + "'><div class='sample_content' > "+ data[idx] + "</div></div>");
				}
							  },'json');
					}
				});
				
				
		
				//join remove button: remove this query, reset the previous combine box
				$("html").on('click', '.kill_join', function(event) {
					$(event.target).parents(".join_wrapper").remove();
					preview_data();			
				});

				//check preview when any form changes
				$("html").on('change', 'form', function(event) {
					preview_data();			
				});
		
				//add an open parens and check if need to indent 
				$("html").on('click', '.indent', function(event) {
					$(event.target).parents(".query_wrapper").toggleClass("indented");
					prev =null;
					check_borders();
				});
						
				//handle form submission here 
				$( "form" ).submit(function( event ) {
					result_type = $(".results_select:checked:first").val();
					if (result_type == "xls")
					{
						xls_results(cur_table);
					}
					else if (result_type == "csv")
					{
						csv_results(cur_table);
					}
					else if (result_type == "json")
					{
						json_results(cur_table);
					}
					else if (result_type == "files")
					{
						files_results(cur_table);
					}
					event.preventDefault();
					});
				$("#all_queries").new_query("I want rows where: ");
			});
		</script>

				<div class="join_button_wrapper">
					<button type="button" xls
						class="join_button btn-default col-md-4 col-md-offset-4">Combine
						with another table</button>
				</div>
				<div id="busy" class="col-md-4 col-md-offset-2">
					<h1>BUSY</h1>
				</div>
				<div class="results_wrapper">
					Return results as: <br> <br> <input class="results_select"
						type="radio" name="result_format" value="txt"> Text query <input
						class="results_select" type="radio" name="result_format"
						value="json">JSON object <input type="radio"
						class="results_select" name="result_format" value="xls"> .XLS <input
						type="radio" class="results_select" name="result_format"
						value="csv"> .CSV <input type="radio" name="result_format"
						value="files" class="results_select"> Files from column <input
						type="radio" name="result_format" value="s3"
						class="results_select"> S3 stream of files <br> <br>
					<div class="file-loc" id="loc-wrapper">
						How the file URLs will be built:<br> <input type="text"
							id="file-loc-left" value="http://"><select
							class="column_select form-control" id="file-loc-center"
							style="width: 33%;"></select><input id="file-loc-right"
							type="text">
					</div>
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
		<div class="col-md-2 col-md-offset-10" style="position: fixed;">
			<h3>Database Overview</h3>
			<br>
			<div id="db_overview"></div>
		</div>
		<div class="col-md-8 col-md-offset-2">
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



