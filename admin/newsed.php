<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>News Editor</title>
  </head>
  <body>
    <?Php
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
    
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	
    	if ($connection->connect_errno > 0) {
     		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}
      $sql = "SELECT * FROM NewsItems order by NewsItemID desc";

    	if (!$result = $connection->query($sql)) {
        	die ('There was an error running query[' . $connection->error . ']');
    	}
      $rows = $result->num_rows;
      $cols = $result->field_count;
    	if($rows > 0) {
    	  
        echo '<table class="gridTable" id="editTable">'."\n";    // Container open
        echo '<tr><th>News Items</th></tr>';
    	  while ($row = $result->fetch_array()) {
    	    $dtlUrl =  "tblEdit.php?mode=edit&amp;table=NewsItems&amp;id=".$row['NewsItemID']."&amp;mode=edit";
    	    echo '<tr>';	// Start a new row
    		  echo '<td><a href="'.$dtlUrl.'">'.htmlspecialchars($row['subject']).'</a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	  echo '</table>'."\n<br />\n";  // Close the container

        echo '<a href="tblEdit.php?mode=add&amp;table=NewsItems">Add News Item</a>';
      }
      $connection->close();
    ?>
  </body>
</html>
