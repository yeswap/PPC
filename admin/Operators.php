<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Operators</title>
  </head>
  <body>
    <?Php
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

//Conect to db
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	if ($connection->connect_errno > 0) {
     		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}
    	
//Get Network name
    	$networkID = $_GET["id"];
    	$sql1 = "SELECT * FROM Networks where NetworkID =$networkID";
    	if (!$result1 = $connection->query($sql1)) {
        	die ('There was an error running Networks  query[' . $connection->error . ']');
    	}
    	
    	$rows = $result1->num_rows;
      $cols = $result1->field_count;
    	
    	if($rows > 0) {
    	  $row = $result1->fetch_array();
    	  $dtlUrl =  "tblEdit.php?id=".$row['NetworkID']."&amp;mode=edit&amp;table=Networks";
    	} else {
    	  die ('NetworkID '.$networkID.' does not exist');
    	}

//Start table
      echo '<table class="gridTable" id="opsTable">'."\n";    // Container open
      echo '<tr><th><a href="'.$dtlUrl.'">'.htmlspecialchars($row['Name']).'</a></th><th></th></tr>';
//Get Operator rows
    	$sql2 = "SELECT * FROM Operators where NetworkID = $networkID";

    	if (!$result2 = $connection->query($sql2)) {
      	die ('There was an error running Operators query[' . $connection->error . ']');
    	}
    	$rows = $result2->num_rows;
      $cols = $result2->field_count;
    	if($rows > 0) {
  	    while ($row = $result2->fetch_array()) {
  	      $dtlUrl =  "Plans.php?id=".$row['OperatorID']."&amp;network=$networkID";
  	      $profileUrl =  "makeProfile.php?id=".$row['OperatorID'];
  	      echo '<tr>';	// Start a new row
  		    echo '<td><a href="'.$dtlUrl.'">'.htmlspecialchars($row['Name']).'</a></td>';
  		    echo '<td><a href="'.$profileUrl.'">Profile</a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	}
      echo '</table>'."\n<br />\n";  // Close the container

        echo '<div><a href="tblEdit.php?mode=add&table=Operators&FK='.$networkID.'">Add Operator</a></div>';
        echo '<div><a href="/dbedit.php">Go Home</a></div>';

     $connection->close();
    ?>
  </body>
</html>
