function preview(data) {
	$("#query_div").html(data.query);
	response = data.response;
	console.log(response);
	columns = data.columns;
	$("#resp_table").empty();
	$(resp_table).append("<tr>");
	$.each(columns, function() {
		column_name = this.name;
		if ($.inArray(column_name, $.curQuery.cur_columns) == -1) {
			$.curQuery.cur_columns.push(column_name);
		}
		$(resp_table).find("tr:last").append("<td>" + column_name + "</td>");
	});
	$.each(response, function(idx) {
		$(resp_table).append("<tr>");
		$.each(columns, function() {
			column_name = this.name;
			$(resp_table).find("tr:last").append(
					"<td>" + response[idx][column_name] + "</td>");
		});
	});
}

function preview_data() {
	$.curQuery.queryCount = 0;
	// package data for query builder
	output_array = package_form();

	// send everything to query builder
	$.post('preview_query_run.php', output_array, function(data) {
		// parsed_results = jQuery.parseJSON(data);
		preview(data);
	}, 'json');
}

function package_urls(){
	url_array = {"left":$("#file-loc-left").val(), "center":$("#file-loc-center").val(), "right":$("#file-loc-right").val()};
}

function package_form() {
	output_array = {};
	$(".query_wrapper").each(
			function(idx) {
				query_dict = {
					"type" : "QUERY",
					"column" : $(this).find(".column_select").val(),
					"where" : $(this).find(".where_select:checked").val(),
					"where_text" : $(this).find(".where_text").val(),
					"subquery" : " ",
					"combine" : "",
					"left_parens" : "false",
					"right_parens" : "false"
				};
				if ($(this).find(".combine").hasClass('and')) {
					query_dict['combine'] = 'and';
				} else if ($(this).find(".combine").hasClass('or')) {
					query_dict['combine'] = 'or';
				}

				if (prev != null && prev.hasClass("indented")
						&& !$(this).hasClass("indented")) {
					query_dict['right_parens'] = 'true';
				} else if (prev != null && !prev.hasClass("indented")
						&& $(this).hasClass("indented")) {
					query_dict['left_parens'] = 'true';
				}

				prev = $(this);
				$.curQuery.queryCount = idx;
				output_array[idx] = query_dict;
			});

	$(".join_wrapper").each(function(idx) {
		query_dict = {
			"type" : "JOIN",
			"table" : $(this).find(".table_select").val(),
			"column" : $(this).find(".column_select").val(),
			"left" : $(this).find(".join_type_left").is(':checked'),
			"right" : $(this).find(".join_type_right").is(':checked')
		};
		output_array[idx + $.curQuery.queryCount + 1] = query_dict;
	});
	console.log(output_array);
	return output_array;
}

function check_borders() {
	prev = null;
	$(".query_wrapper").each(
			function() {
				if (prev != null
						&& prev.hasClass("indented") == $(this).hasClass(
								"indented")) {
					$(this).css("border-top-width", "0px");
					prev.css("border-bottom-width", "0px");
				} else if (prev != null
						&& !prev.hasClass("indented") == !($(this)
								.hasClass("indented"))) {
					$(this).css("border-top-width", "0px");
					prev.css("border-bottom-width", "0px");
				} else if (prev != null
						&& !prev.hasClass("indented") == ($(this)
								.hasClass("indented"))) {
					$(this).css("border-top-width", "2px");
					prev.css("border-bottom-width", "2px");
				} else if (prev == null) {
					$(this).css("border-top-width", "2px");
				}
				prev = $(this);
				if ($(this).hasClass("indented")) {
					$(this).outerWidth("75%");
				} else {
					$(this).outerWidth("100%");
				}
			});
}

function xls_results(cur_table)
{
	// package data for query builder
	output_Array = package_form();

	$.post('xls.php', output_array, function(data) {
		// parsed_results = jQuery.parseJSON(data);
		var blob = new Blob([ data ]);
		var link = document.createElement('a');
		link.href = window.URL.createObjectURL(blob);
		link.download = cur_table + ".xls";
		link.click();
	}, 'text');
}

function csv_results(cur_table)
{
	// package data for query builder
	output_Array = package_form();
	url_array = package_urls();
	$.post('files.php', {"output_array":output_array, "url_array":url_array}, function(data) {
		// parsed_results = jQuery.parseJSON(data);
		var blob = new Blob([ data ]);
		var link = document.createElement('a');
		link.href = window.URL.createObjectURL(blob);
		link.download = cur_table + ".zip";
		link.click();
	}, 'text');
}

function json_results(cur_table)
{
	// package data for query builder
	output_Array = package_form();

	$.post('json.php', output_array, function(data) {
		// parsed_results = jQuery.parseJSON(data);
		var blob = new Blob([ data ]);
		var link = document.createElement('a');
		link.href = window.URL.createObjectURL(blob);
		link.download = cur_table + ".json";
		link.click();
	}, 'text');
}
