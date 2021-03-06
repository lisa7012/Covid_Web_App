<?php require_once '../database.php';

if(!isset($_SESSION['user_id']))
{
	header('Location: ../login.php');
}
?>

<html>
<head>
	<title>C19PHCS - Regions Info</title>
	<link rel="stylesheet" href="../style.css">
</head>

<?php 
if($_SESSION['type'] == "Person")
{
	include('../Person/dropdownmenu.html');
}
else
{
	include('../Employee/dropdownmenu.html');
}

echo "<br>";?>

<h2>Regions information:</h2><br>

<?php
	//TODO: Find out about parents thing!!
	$psns = $db->prepare('SELECT * FROM comp353.Region WHERE isDeleted = 1');
	$psns->execute();
	$results = $psns->fetchAll(PDO::FETCH_ASSOC);
	if(is_array($results) && count($results) > 0)
	{
		echo "
	<table border='1'>
	<thead>
	    <tr>";

	    $cols = $db->prepare('DESCRIBE comp353.Region');
		$cols->execute();
		$table_fields = $cols->fetchAll(PDO::FETCH_COLUMN);
		array_pop($table_fields);
		foreach ($table_fields as $value)
		{
			echo "<th>$value</th>";
		}
		echo "</tr></thead><tbody>";

	    
		foreach($results as $places)
		{
		array_pop($places);
		echo "<tr>";
			foreach ($places as $place)
			{
				//if its empty
				if($place == '')
				{
					echo "<td>None</td>";
				}
				else
				{
					echo "<td>'$place'</td>";
				}
			}
		echo "</tr>";	
		}
	}
	echo "</tbody>
	</table>";
	?>

</body>
</html>

