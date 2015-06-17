<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>KL Front End</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
</head>
<body>


	<?php
	$servername = "klab.c3se0dtaabmj.us-west-2.rds.amazonaws.com";
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];

	// Create connection
	$conn = mysql_connect($servername, $username, $password);
	mysql_select_db ($db);
	$db_list =  mysql_query("SHOW DATABASES");

	mysql_close($conn);
	?>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>


	<script type="text/javascript"> 
  	$(document).ready(function(){

  	  	$("#busy").hide();
  	  	//always display/hide the busy box
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
  	  	
		 //change databases, therefore need to update table dropdown
		$("html").on('change', '.db_select', function(event) {			
			$.post('gettables.php', {"db" :$(event.target).val()} , function( data ) {
				target = $(event.target).parent().find(".table_select");
				target.empty();
				for (idx=0; idx< data.length; idx++){
							$(target).append("<option value= '" + data[idx] + "'> "+ data[idx] + "</option>");
							tables = data;
							$("#db_overview").empty();
							$.each(tables, function(index, value){
								$("#db_overview").append("<div class='overview table_wrapper' id=" + value + "> <div class='btn-default overview table_content'>" + value + "</div></div>");
							}
							);
							}
					  },'json');
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
		
  	});



  	
  
  </script>

	<div class="container-fluid">
		<?php include 'header.php'; ?>
		<div class="col-md-7 col-md-offset-2" id="main_wrapper">
			<div id="busy" class="col-md-4 col-md-offset-2">
		<h1>BUSY</h1>
	</div>
			Select a database to query:
			<form action="query_build.php" method="post">
				<select name="db" class="form-control db_select">
					<?php 
					while ($row = mysql_fetch_object($db_list)) {
						echo '<option value="'.$row->Database . '">'.$row->Database .'</option>';
					}
					?>
				</select> Select a table from that database: <select name="table"
					class="form-control table_select"><br>
					<br>
					<input type="submit" class=".btn-default">
			
			</form>
		</div>
		<div class="col-md-2 ">
			<h3>Database Overview</h3>
			<br>
			<div id="db_overview"></div>
		</div>
	</div>
</body>
</html>



