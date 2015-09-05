<?php
/*
	WRAPPERS! AKA functions that can be used in multiple projects, regardless
	what the projects consist of.
*/


// Insert records into the database.
	function PDOinsert($table_name, $columns_array, $values_array, $db)
	{
	// Implode the columns so that at the end they will be separated
	// with a comma as a list of columns like this - table1, table2, table3 and so on.
			$columns = implode(',', $columns_array);
	// We do the same with the $values_array
			$values = implode(',',  $values_array);
		//	print $values;
	// The placeholders of our query. We choose "?"
	// because otherwise it will be impossible to
	// mention each value by name like this - :value1, :value2, :value3.
			$value_placeholders = '?';
	// We count the $values_array and depending the number
	// of elements in the $values_array, we we append the $value_placeholders
	// variable with "?" which will serve as placeholders in the query.
		for($i = 1; $i < count($values_array); $i++)
		{
			$value_placeholders .=' , ?';
		}
	// We construct the query.
	 	$sql = "INSERT INTO {$table_name}({$columns})
				VALUES($value_placeholders)";

	// We prepare it.
	$query = $db->prepare($sql);
	$y = 0;
	// We bind the parameters depending how 
	// many elements there are in the $values_array.
	for ($x=1; $x <= count($values_array); $x++) 
	{ 		
		$query->bindParam($x,$values_array[$columns_array[$y]]);
		$y++;
	}
	// We execute the query.

		if($query->execute() === true){
	// We return the last inserted ID.
		return $db->lastInsertId();	
	}
	// If the execution of the query fails, we print an error message
	else{
		print 'There was an error with inserting the records on the database! Please try again later';
	}

} // End of the function.


/*
	Complex select where the condition for selecting is mandatory and you could join tables.
	Returns multidimentional array.
*/
	function PDOselect($columns_array, $table_name, $condition, $value_for_condition
						, $db, $join = null, $second_table = null, $condition_to_join = null){
		if(is_array($columns_array)){
				$columns = implode(',', $columns_array);
		}else{
			$columns = $columns_array;
		}
		 	$sql = "SELECT {$columns} FROM  {$table_name}
		 			$join $second_table
		 			$condition_to_join
					WHERE {$condition} = {$value_for_condition}
					";
	$query = $db->prepare($sql);
	$query->execute();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)){
			$results[] = $row;
		}
	// Check if there is data in the $results array
		if(isset($results))
		{
			return $results;
		}
	}
/*
	Selects one row from a table. Returns an array holding one line of results.
*/
		function PDOselect_all_from_table_single_row(
			  $table_name
			, $condition = null
			, $operator = null
			, $value_for_condition = null
			, $db)
		{
		
		 	$sql = "SELECT * FROM  {$table_name}
					WHERE {$condition} {$operator} {$value_for_condition}";
				//	print $sql;
			$query = $db->prepare($sql);
			$query->execute();
			$result =  $query->fetch(PDO::FETCH_ASSOC);
			return $result;
	}
/*
	Select more than one records in a table. Conditional optional.
	Returns all the results in a multidimensional array.
*/
	function PDOselect_all_from_table_many_rows(
		$table_name
		, $condition = null
		, $operator = null
		, $value_for_condition = null
		, $db)
		{
		
		 	$sql = "SELECT * FROM  {$table_name}
					WHERE {$condition} {$operator} {$value_for_condition}";
			$query = $db->prepare($sql);
			$query->execute();
			while ($row = $query->fetch(PDO::FETCH_ASSOC)){
			$results[] = $row;
		}
		return (isset($results)) ? $results : false;
	}


function PDOupdate($table_name, $columns_string, $new_values, $condition, $value_for_condition, $db)
{	
	$columns_array = $columns_string;
	$values_array = $new_values;
	$columns_count = count($columns_array);
	$update_query = '';
	// Count the values to decide how many "?" to put as placeholders
	for ($i=0; $i < $columns_count ; $i++) 
	{ 
		$update_query .= "$columns_array[$i]=?";
		if($i != $columns_count - 1)
		{
			$update_query .= ',';
		}
	}
	$sql = "UPDATE $table_name
			SET $update_query
			WHERE $condition = $value_for_condition";
			
	$query = $db->prepare($sql);
	$i = 0;
	for ($x=1; $x <= $columns_count; $x++) 
	{ 		
		$query->bindParam($x,$values_array[$i]);
		$i++;
	}
	$query->execute();
}
/*
	Checks whether all array values are empty.
	$exceptions_arrays hold the array keys' values we do not want to check.
*/
function check_all_fields_empty($fields_array, $exceptions_array = array())
{
	$number_of_fields = count($fields_array);
	foreach ($fields_array as $key => $value)
	 {
		if(empty($value) and !in_array($key, $exceptions_array))
		{
			return false;
		}
	}
		return true;
}

/*
    Check whether the user has clicked the submit
	button in a form. The reason we are not simply 
	using if(isset($_POST(GET)['SUBMIT_BUTTON'])) 
	is because it would not work on IE 10 -  when the user would press enter instead of the button.
*/
	function submit_pressed($method)
	{
		$method = strtoupper($method);
		if ($_SERVER['REQUEST_METHOD'] == $method) 
		{
			return true;
		}
		return false;
	}

/* 
	$counter checks the number in front of the word, like "2" or "1".
	$word is the word in front of the numbers.
	If the $counter is more than 1, adds -s to $word.
 */
function suffix_plural($counter, $word)
{
  return ($counter == 1) ? $word : $word."s";
}

/*
	 Remove/replace invalid characters in the custom URLs
 	 generated from the title of the post.
 	'Cause otherwise the URLs will look like scrambled eggs.
*/
	function clean_url($url)
	{
		$search = array('-',' ','$','%','.','\'','"','?');
		$replace = array('','-','','','','','','');
		$cleaned_url = strtolower(str_replace($search, $replace, $url));
		return $cleaned_url;
	}