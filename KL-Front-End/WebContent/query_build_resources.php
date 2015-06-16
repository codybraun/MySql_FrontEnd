<?
$(window).load(function(){
    var tables = echo $js_tables;
    var cur_table = echo "'" . $table . "'" ;
    var table_columns = echo $js_columns;
	var select_id = 0;
	var join_id = 0;
	$.curQuery = new Object();
	$.curQuery.cur_columns = [];
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
				$target.append('<div class="where_wrapper"><input class="where_select" type="radio" checked="checked" name ="where' + select_id + '" value="exact"> is exactly <input type="radio" class="where_select" name ="where' + select_id + '" value="similar"> is similar to <input class="where_select" type="radio" name ="where' + select_id + '" value="in"> is in <input type="radio" class="where_select" name ="where' + select_id + '" value="dropdown"> Select from dropdown <br><input class="where_text" name="where_text' + select_id + '" type="text" >  </div> <br><br>');
				$target.append('<div class="combine"><button type="button" class="and btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
				select_id ++;
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
			 $target.append('<button type="button" class="pull-right btn btn-danger kill_join">REMOVE</button>');
			 
			 $target.append('Table: <select name="table' + join_id + '" class="table_select form-control"><br>');
			 $target.append('Where it shares this column: <select name="table' + select_id + '" class="column_select form-control"><br>');
			 $target.append('<label class="join_type_left"><input type="checkbox" class="join_type_left" value="">Keep all rows from <p class="join_type_left">table1</p></label><br>');
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
		
	});

	//change results selection
	$("html").on('click', '.results_select', function(event) {			

	});
	
	//remove button: remove this query, reset the previous combine box
	$("html").on('click', '.kill_query', function(event) {
		$(event.target).parents(".query_wrapper").remove();
		$(".query_wrapper:last").find(".combine").remove();
		$(".query_wrapper:last").append('<div class="combine"><button type="button" class="and btn-default col-md-3 col-md-offset-2">AND</button><button type="button" class="or btn-default col-md-3 col-md-offset-2">OR</button></div><br>');
		
	});

	//join remove button: remove this query, reset the previous combine box
	$("html").on('click', '.kill_join', function(event) {
		$(event.target).parents(".join_wrapper").remove();			
	});

	//add an open parens and check if need to indent 
	$("html").on('click', '.indent', function(event) {
		$(event.target).parents(".query_wrapper").toggleClass("indented");
		prev =null;
		$(".query_wrapper").each(function(){
			if (prev != null && prev.hasClass("indented") == $(this).hasClass("indented")){
				$(this).css("border-top-width","0px");
				prev.css("border-bottom-width","0px");
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


function post_data () {
		output_array = {};
		prev = null;
		$.curQuery.queryCount = 0;
		//package data for query builder
		
		$(".query_wrapper").each(function (idx){
			query_dict = {"type": "QUERY", "column":$(this).find(".column_select").val(), "where":$(this).find(".where_select:checked").val(), "where_text":$(this).find(".where_text").val() ,"subquery":" ", "combine" : "", "left_parens":"false", "right_parens":"false"};
			if ($(this).find(".combine").hasClass('and')){
				query_dict['combine'] = 'and';
			}
			else if ($(this).find(".combine").hasClass('or')){
				query_dict['combine'] = 'or';
			}
			
			
			if (prev != null && prev.hasClass("indented") && !$(this).hasClass("indented")){
				query_dict['right_parens'] = 'true';
			}
			else if (prev != null && !prev.hasClass("indented") && $(this).hasClass("indented")){
				query_dict['left_parens'] = 'true';
			}
			
			prev = $(this);
			$.curQuery.queryCount = idx;
			output_array[idx]= query_dict;
		});

		$(".join_wrapper").each(function (idx){
			query_dict = {"type":"JOIN", "table":$(this).find(".table_select").val(), "column":$(this).find(".column_select").val() };
			output_array[idx + $.curQuery.queryCount + 1]= query_dict;
		});
		
		
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
					if ($.inArray(column_name, $.curQuery.cur_columns)==-1){
						$.curQuery.cur_columns.push(column_name);
					}
					$(resp_table).find("tr:last").append("<td>" + column_name + "</td>");
				  });
			  $.each(response, function(idx) {
				$(resp_table).append("<tr>");
				  $.each(columns , function() {
					column_name= this.name;
					$(resp_table).find("tr:last").append("<td>" + response[idx][column_name] + "</td>");
				  });
			  });
		}, 'json');
}
			
interval_check = setInterval(post_data, 5000);

	//handle form submission here 
	$( "form" ).submit(function( event ) {
		output_array = {};
		prev = null;
		$.curQuery.queryCount = 0;
		//package data for query builder
		
		$(".query_wrapper").each(function (idx){
			query_dict = {"type": "QUERY", "column":$(this).find(".column_select").val(), "where":$(this).find(".where_select:checked").val(), "where_text":$(this).find(".where_text").val() ,"subquery":" ", "combine" : "", "left_parens":"false", "right_parens":"false"};
			if ($(this).find(".combine").hasClass('and')){
				query_dict['combine'] = 'and';
			}
			else if ($(this).find(".combine").hasClass('or')){
				query_dict['combine'] = 'or';
			}
			
			if (prev != null && prev.hasClass("indented") && !$(this).hasClass("indented")){
				query_dict['right_parens'] = 'true';
			}
			else if (prev != null && !prev.hasClass("indented") && $(this).hasClass("indented")){
				query_dict['left_parens'] = 'true';
			}
			
			prev = $(this);
			$.curQuery.queryCount = idx;
			output_array[idx]= query_dict;
		});

		$(".join_wrapper").each(function (idx){
			query_dict = {"type":"JOIN", "table":$(this).find(".table_select").val(), "column":$(this).find(".column_select").val() };
			output_array[idx + $.curQuery.queryCount + 1]= query_dict;
		});
					
		//send everything to query builder
		if ($(".results_select:checked:first").val()=='csv'){
			$.post('csv.php', output_array, function( data ) {
				  //parsed_results = jQuery.parseJSON(data);
				  var blob=new Blob([data]);
    				var link=document.createElement('a');
    				link.href=window.URL.createObjectURL(blob);
    				link.download="KLcsv.csv";
    				link.click();
			}, 'text');
		}
	
		else if ($(".results_select:checked:first").val()=='xls'){
				$.post('xls.php', output_array, function( data ) {
					  //parsed_results = jQuery.parseJSON(data);
					  var blob=new Blob([data]);
	    				var link=document.createElement('a');
	    				link.href=window.URL.createObjectURL(blob);
	    				link.download="KLxls.xls";
	    				link.click();
				}, 'text');
		}
		console.log($(".results_select:checked:first").val());
		event.preventDefault();
		});
	
	$("#all_queries").new_query("I want rows where: ");
		
		
	});

?>