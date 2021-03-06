<?php require_once '../database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['type'] == "Person")
{
	header('Location: ../login.php');
}
?>

<html>
    <head>
        <title>C19PHCS - Edit HealthRecommendation</title>
		<link rel="stylesheet" href="../style.css">
</head>

<?php include('../Employee/dropdownmenu.html');?>

<form action="editHR.php" method="POST">
		<br><br>
		<div class="formDiv">
			<label for="address"><h3 class="instruction">Please enter the Recommendation's ID:</h3><br></label>
			<input type="text" name="recommend_id" required <?php echo isset($_POST['recommend_id']) ? 'value="' . $_POST['recommend_id'] . '"'  : 'value=""'; ?>>
		</div>
		<br>
		<!-- this goes to the up php code -->

	<?php
	//moved here for the first condition
	$cols = $db->prepare('DESCRIBE comp353.publichealthrecommendation');
	$cols->execute();
	$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
	array_pop($table_fields);

	//to check if I have filled the full form (not just medical nbr)
	if(isset($_POST['searchEdit_Recommend']) && count($_POST) > 2)
	{
		$insertStr = 'UPDATE comp353.publichealthrecommendation SET ';

		for ($i=0; $i < count($table_fields); $i++) 
		{ 
			$index = "attrib" . $i;

			// single quotes need an escape to appear
			if($table_fields[$i] == 'Description')
			{
				$rework = $_POST[$index];
				$newDescription = str_replace("'","''", $rework);
				$insertStr = $insertStr . $table_fields[$i] . " = '" . $newDescription . "', ";
			}
			else {
				$insertStr = $insertStr . $table_fields[$i] . " = '" . $_POST[$index] . "', ";
			}	
		}

		//to remove the last comma
		$insertStr = substr($insertStr, 0, -2);

		$insertStr = $insertStr . " WHERE Recommendation_ID = :recommend_id";

		$addPsn = $db->prepare($insertStr);
		$addPsn->bindParam(':recommend_id', $_POST['recommend_id']);
		$outcome = $addPsn->execute();
		
		if($outcome)
		{
			echo "<p>Change successful.</p>";
		}
		else
		{
			echo "<p>Error: Something went wrong. Please try again.</p>";
		}
	}
	//to check for medical record submit
	else if(!empty($_POST['recommend_id']) && isset($_POST['searchEdit_Recommend']))
	{
		//Not * so order is specific
		$person = $db->prepare('SELECT * FROM comp353.publichealthrecommendation WHERE Recommendation_ID = :recommend_id AND isDeleted = 1');
		$person->bindParam(':recommend_id', $_POST['recommend_id']);
		$person->execute();
		//to get index type
		$results = $person->fetch(PDO::FETCH_BOTH);
		if(is_array($results) && count($results) > 0)
		{
			echo "<br><h3 class= \"edit\">" . $results['Recommendation_ID'] . "'s Current Information:</h3><br>
		<table border='1'>
	<thead>
	    <tr>";

	    //previous place for table_fields
		foreach ($table_fields as $value)
		{
			echo "<th>$value</th>";
		}
		echo "</tr></thead><tbody>";

		echo "<tr>";

		array_pop($results);
		array_pop($results);
		
		//divide 2 since its both index and attribute name
		for($i=0; $i < count($results)/2; $i++)
		{
		//adds a row for said person & no check if its empty
		echo "<td>'$results[$i]'</td>";
		}

		echo "</tr></tbody></table>";
		
		echo "<br><br>";

		echo "<div class= \"formDiv\">
				<h3 class= \"instruction\">Please enter edits:</h3><br>";
		//remove this if changing the key is allowed.
		for ($i=0; $i <count($table_fields); $i++) 
		{ 
			

			if($table_fields[$i] == "Description")
			{
			echo 
			"<div>
			<br>
			<label for=\"attrib" . $i . "\">" . $table_fields[$i] . ":<br></label>
			<textarea class=\"areaStyle\" name=\"attrib" . $i . "\" rows=\"40\" cols=\"180\">" . $results[$i] . "</textarea>";
			break;	
			}
			else
			{
			echo 
			"<div>
			<br>
			<label for=\"attrib" . $i . "\">" . $table_fields[$i] . ":<br></label>
			<input type=\"text\" name=\"attrib" . $i . "\" required value=\"" . $results[$i];
			}

			if($results[$i] != $_POST['recommend_id'])
			{
			 echo "\" ></div>";
			}
			else
			{
				echo "\" readonly></div>";
			}
		}

		echo "</div>";
		echo "<br>";

		}
		else
		{
			echo "<h3 class=\"instruction\">None with the Recommendation ID: \"" . $_POST['recommend_id'] . "\" were found.</h3>";
		}
	}	
	?>

		<input type="submit" name="searchEdit_Recommend" value="Edit" />
	</form>
</body>
</html>

