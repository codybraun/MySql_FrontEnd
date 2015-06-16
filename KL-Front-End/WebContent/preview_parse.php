<?php
function parse_query (){
	$sub_query = '';
	$join_query = '';

	foreach ($_POST as $key => $value){
		//handle joins
		if ($value['type'] == "JOIN"){
			$join_query = $value['table'] . " ON " . $_SESSION['table'] . "." . $value['column'] . " = " . $value['table'] . "." . $value['column'];
			if ($value['right']=='false' && $value['left']=='false'){
				$join_query = " INNER JOIN " . $join_query;
			}
			else if ($value['right']=='true' && $value['left']=='false')
			{
			}
		}
		//handle queries
		else if ($value['type'] != "JOIN"){
			if ($value['right_parens'] == 'true'){
				$sub_query = $sub_query . ') ';
			}
			$sub_query = $sub_query . " " . strtoupper($combine);

			if ($value['left_parens'] == 'true'){
				$sub_query = $sub_query . ' (';
			}
			$sub_query = $sub_query . " " . $value['column'];
			$search = $value['where_text'];
				
			if ($value['where'] == 'exact'){
				$sub_query = $sub_query . ' = "' . $search . '"';
			}
			else if ($value['where'] == "similar"){
				$sub_query = $sub_query . ' LIKE "%' . $search . '%"';
			}
			$combine = $value['combine'];

		}
	}
	$query = "SELECT * FROM " . $_SESSION['table'] . $join_query . " WHERE " . $sub_query;
	$query = $query . ";";

	mysql_select_db ($_SESSION['db']);
	$response = mysql_query(substr($query, 0, -1) . " LIMIT 10;");

	//check if got response
	if (!$response || $response==null) {
		$response  = 'Invalid query: ' . mysql_error() . "\n";
		$response .= 'Whole query: ' . $query;
		return array("query" => $query, "response"=> [$response], "columns" => []);
	}

	//put responses in array to return in json
	else{
		for($i = 0; $i < mysql_num_fields($response); $i++) {
			$columns[] = mysql_fetch_field($response, $i);
		}
		$response_rows = array();
		while($r = mysql_fetch_assoc($response)) {
			$response_rows[] = $r;
		}
		return array("query" => $query, "response"=> $response_rows, "columns" => $columns);
	}

}
?>