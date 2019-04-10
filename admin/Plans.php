<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Plans</title>
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
    	
//Get Operator name
    	$sql1 = "SELECT * FROM Operators where OperatorID = ".$_GET["id"];
    	if (!$result1 = $connection->query($sql1)) {
        	die ('There was an error running the Operators query[' . $connection->error . ']');
    	}
    	
    	$rows = $result1->num_rows;
      $cols = $result1->field_count;
    	
    	if($rows > 0) {
    	  $row = $result1->fetch_array();
    	  $dtlUrl =  "tblEdit.php?mode=edit&table=Operators&id=".$row['OperatorID'];
    	} else {
    	  die ('OperatorID '.$_GET["id"].' does not exist');
    	}

//Start table
      echo '<table class="gridTable" id="plansTable">'."\n";    // Container open
      echo '<tr><th><a href="'.$dtlUrl.'">'.htmlspecialchars($row['Name']).'</a></th></tr>';
//Get Operator rows
    	$sql2 = "SELECT * FROM Plans where OperatorID = ".$_GET["id"].' order by Cost';

    	if (!$result2 = $connection->query($sql2)) {
      	die ('There was an error running Operators query[' . $connection->error . ']');
    	}
    	$rows = $result2->num_rows;
      $cols = $result2->field_count;
    	if($rows > 0) {
  	    while ($row = $result2->fetch_array()) {
  	      $dtlUrl =  "tblEdit.php?mode=edit&table=Plans&id=".$row['ID'];
  	      echo '<tr>';	// Start a new row
  		    echo '<td><a href="'.$dtlUrl.'">'.htmlspecialchars('$'.$row['Cost'].' '.$row['Name']).'</a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	}
     echo '</table>'."\n<br />\n";  // Close the container
     
    echo '<div><a href="tblEdit.php?mode=add&table=Plans&FK='.$_GET["id"].'">Add Plan</a></div>';
    echo '<div><a href="/dbedit.php">Go Home</a></div>';
     $connection->close();
    ?>
  </body>
</html>
