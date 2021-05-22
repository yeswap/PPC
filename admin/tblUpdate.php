<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <?Php
    /*
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
    */
      error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
      ini_set("display_errors", 1); # 0 - production, 1 - development
      
    function convert_smart_quotes($text) {
      // First, replace UTF-8 characters.
      $text = str_replace(
        array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6", "\xc2\xa0"),
        array("'", "'", '"', '"', '-', '--', '...', ' '),
        $text);
      // Next, replace their Windows-1252 equivalents.
      $text = str_replace(
        array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)), 
        array("'", "'", '"', '"', '-', '--', '...'),
        $text);
      // Replace &nbsp;
      $text =  str_replace('&nbsp;', ' ', $text);
      
      return $text;
    }

      echo '</head>';
      echo '<body>';

    
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	$connection->set_charset("utf8");
    	
    	if ($connection->connect_errno > 0) {
     		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}
      $sTable = $_GET['table'];
    	// Get Table Metadata
    	$sql= "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='yeswap_ppcompare' AND TABLE_NAME='$sTable'";
    	if (!$columns = $connection->query($sql)) {
     	  die ('There was an error running query[' . $connection->error . ']');
    	}
    	
    	// Put Metadata in an $colArray array
			$sFK ='';
			$sPK = '';
			$colArray = array();
    	while($row = $columns->fetch_array(MYSQLI_ASSOC)){
        array_push($colArray, $row);
				if($row["COLUMN_KEY"] == 'PRI'){
					$sPK = $row["COLUMN_NAME"];
				}
				if ($row["COLUMN_KEY"] == 'MUL') {
						$sFK = $_GET[$row["COLUMN_NAME"]];
					}


//        print_r($row);
//        echo "<br />\n";
    	}

      // Build Update SQL
    	if($_GET['mode']=='edit' && $_GET['SubmitButn'] == "Update"){
        $sql = "UPDATE $sTable SET ";
        $iCount = 0;
        foreach($colArray as $column){
          $iCount++;
          if ($column["COLUMN_NAME"] != $sPK){
            $myData = convert_smart_quotes($_GET[$column["COLUMN_NAME"]]);
            $sql .= $column["COLUMN_NAME"].'="'.$connection->real_escape_string($myData).'"';

           // $sql .= $column["COLUMN_NAME"].'="'.$connection->real_escape_string($_GET[$column["COLUMN_NAME"]]).'"';
            if($iCount < sizeof($colArray)){
              $sql .= ', ';
            }
          }
        }
        $sql .= " WHERE $sPK=".$_GET["id"];
        
      // Build Insert SQL
      } else if ($_GET['mode']=='add'){
          $sql= "INSERT INTO $sTable (";
          $iCount = 0;
          foreach($colArray as $column){
            $iCount++;
            if ($column["COLUMN_KEY"] == 'PRI'){
              $sPK = $column["COLUMN_NAME"];
            }else{
            	$sql .= $column["COLUMN_NAME"];
            	if($iCount < sizeof($colArray)){
               $sql .= ', ';
            	}
						}
          }
          $sql .= ") VALUES(";
          $iCount = 0;
//          echo "Get=";
//          print_r($_GET);
          foreach($colArray as $column){
            $iCount++;
            if ($column["COLUMN_NAME"] != $sPK){
              if ($column["DATA_TYPE"] == 'date'){
                 $sql .= "'".date("Y-m-d",strtotime($_GET[$column["COLUMN_NAME"]]))."'"; // Need to format dates as YYYY-MM-DD
              }else{
                $myData = convert_smart_quotes($_GET[$column["COLUMN_NAME"]]);
                $sql .= "'".$connection->real_escape_string($myData)."'";
              }
              if($iCount < sizeof($colArray)){
                  $sql .= ', ';
              }
          }
      }
      $sql .= ')';
    } else if($_GET['mode']=='edit' && $_GET['SubmitButn'] == "Delete"){
      $sql = "DELETE FROM $sTable WHERE $sPK=".$_GET["id"];
    }
    echo "mode = " . $_GET['mode']. "<br />\n";
    echo "sPK = $sPK<br />\n";
    echo "sql = $sql<br />\n";

    if ($connection->query($sql) === TRUE) {
      echo "Success<br />\n";
    } else {
      echo "Error: " . $connection->error . "sql= $sql<br />\n";
    }

    $connection->close();
    if ($sTable == 'FamilyPlans'
    || $sTable == 'DataAddons'
    || $sTable == 'TextAddons'
    || $sTable == 'VoiceAddons'){
      $sFK=$_GET["PlanID"];
      echo "<div><a href='tblEdit.php?mode=edit&table=Plans&id=$sFK'>Return to Plan</a></div>";
    }else{
      echo "<div><a href='$sTable.php?id=$sFK'>Return to $sTable List?</a></div>";
    }
    echo "<div><a href='tblEdit.php?mode=add&table=$sTable&FK=$sFK'>Add a new row to $sTable?</a></div>";
    ?>
			<div><a href="/dbedit.php">Go Home</a></div>
		</div>
  </body>
</html>
