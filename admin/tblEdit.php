<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <?Php
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      $sTable = $_GET["table"];
      echo "<title>Edit $sTable</title>\n";
    ?>
		<style type = "text/css">
			input, label {margin-bottom:1ex;display: inline-block;}
			fieldset div {display: inline-block;}
			input {width:10ex;min-width:25ex;}
			#dataAddonsTable, #textAddonsTable, #voiceAddonsTable {
			  float: right;
			  display: inline-block;
			  margin-left: 1em;
			  padding: .5em;
			  border: 2px solid threedface;
			}
			#familyPlansTable {
			  width: max-content;
			  display: inline-block;
			  border: 2px solid threedface;
			  padding: .5em;
			}
			#addOns {
			  width: max-content;
			  display: inline-block;
			  margin-top: 1em;
			}
			textarea{
			  width:100%;
			}
		</style>
  </head>
  <body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <form action="/tblUpdate.php">

    <?Php
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	
    	if ($connection->connect_errno > 0) {
     		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}

			$sTable = $_GET['table'];

			// Get Foreign Key Constraint (Modify if multiple FKs in table)
			$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.key_column_usage WHERE CONSTRAINT_SCHEMA = 'yeswap_ppcompare' AND TABLE_NAME = '$sTable' AND REFERENCED_TABLE_NAME IS NOT NULL";
			//echo $sql;
			if (!$columns = $connection->query($sql)) {
     	  die ('There was an error running query[' . $connection->error . ']');
    	}
    	$sFK = '';
    	$row = $columns->fetch_array(MYSQLI_ASSOC);
      $sFK = $row["COLUMN_NAME"];

    	// Get Table Metadata
    	$sql= "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='yeswap_ppcompare' AND TABLE_NAME='$sTable'";
    	
    	if (!$columns = $connection->query($sql)) {
     	  die ('There was an error running query[' . $connection->error . ']');
    	}

    	// Put Metadata in an $colArray array
    	$colArray = array();
    	while($row = $columns->fetch_array(MYSQLI_ASSOC)){
        //printf ("%s (%s) (%s)\n", $row["COLUMN_NAME"], $row["DATA_TYPE"], $row["COLUMN_KEY"]);
        //echo "<br />\n";
        array_push($colArray, $row);
        //echo "<div>column=".$row["COLUMN_NAME"].". Key=".$row["COLUMN_KEY"].".</div>";
				if($row["COLUMN_KEY"] == 'PRI'){
					$sPK = $row["COLUMN_NAME"];
				}
//				if($row["COLUMN_KEY"] == 'MUL'){
//					$sFK = $row["COLUMN_NAME"];
//				}
    	}
 			// Get Column Values
      if($_GET["mode"]=='edit'){
        $sql2 = "SELECT * FROM $sTable where $sPK = ".$_GET["id"];
        echo "\t".'<input type="hidden" name="id" value="'.$_GET["id"].'">'."\n";

      }else{
        $sql2 = "SELECT * FROM $sTable where $sPK = 1";
      }

    	if (!$result = $connection->query($sql2)) {
          die ('There was an error running query[' . $connection->error . ']');
    	}
      $rows = $result->num_rows;

      if ($rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
      }
			$iCount = 0;
			echo "<fieldset>\n";
      foreach($colArray as $column){
        $iCount++;
        if ($column["DATA_TYPE"] == 'text'){
          echo"<div style='display:block;'>\n<label for = '".$column["COLUMN_NAME"]."'>".$column["COLUMN_NAME"].":</label>&nbsp;";
		  echo "<textarea rows='5' name=".$column["COLUMN_NAME"]." >";
			if (isset($row[$column["COLUMN_NAME"]])){
				echo $row[$column["COLUMN_NAME"]];
			}
			echo "</textarea></div>\n";
        }elseif ($column["COLUMN_KEY"] != 'PRI'){
        	echo"<div>\n<label for = '".$column["COLUMN_NAME"]."'>".$column["COLUMN_NAME"].":</label>&nbsp;";
					if ($column["DATA_TYPE"] == 'date'){
						echo "<input type='date'";					}elseif ($column["DATA_TYPE"] == 'text'){
					}else{
						echo "<input class='inputbox' type='text'";
					}
					echo " name='".$column["COLUMN_NAME"]."' ";
				 	echo " title='".$column["COLUMN_COMMENT"]."' ";
					if($_GET["mode"]=='edit'){
						echo 'value="'.$row[$column["COLUMN_NAME"]].'">'."\n</div>\n";
          }else{
            if($column["COLUMN_NAME"] == $sFK){
              echo "value='".$_GET["FK"]."'>\n</div>\n";
            }else{
						  echo "value=''>\n</div>\n";
            }
				  }
        }
      }

   ?>
    	<br /><br />
    	<input type="hidden" name="mode" value="<?=$_GET["mode"] ?>">
      <input type="hidden" name="table" value="<?=$sTable ?>">

      <input type="submit" name="SubmitButn" value="Update"/>
      <input type="submit" name="SubmitButn" value="Delete"
        onclick="return confirm('Are you sure you want delete this record?')"
      />
			</fieldset>
    </form>
<?Php
   if ($sTable == 'Plans'){
    if($_GET["mode"]=='edit'){
      $sOperator = $row[$sFK];
    }else{
      $sOperator = $_GET["FK"];
    }
   }
  if (($sTable == 'Plans') && ($_GET["mode"]=='edit')){
//Start table
      echo '<div id="addOns"><div id="familyPlansTable">';
      echo '<table class="gridTable">'."\n";    // Container open
      echo '<tr><th>Family Plans</th></tr>';
//Get Family Plans rows
    	$sql2 = "SELECT * FROM FamilyPlans where PlanID = ".$_GET["id"].' order by NumLines';

    	if (!$result2 = $connection->query($sql2)) {
      	die ('There was an error running Family Plans query[' . $connection->error . ']' . 'SQL ='. $sql2);
    	}
    	$rows = $result2->num_rows;
      $cols = $result2->field_count;
    	if($rows > 0) {
  	    while ($row = $result2->fetch_array()) {
  	      $dtlUrl =  "tblEdit.php?mode=edit&table=FamilyPlans&id=".$row['FamilyPlanID']."&plan=".$_GET["id"];
  	      echo '<tr>';	// Start a new row
  		    echo '<td><a href="'.$dtlUrl.'">'.$row['NumLines'].' '. htmlspecialchars('$'.$row['Price']).' </a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	}
      echo '</table>'."\n";  // Close the container
      echo '<div><a href="tblEdit.php?mode=add&table=FamilyPlans&FK='.$_GET["id"].'">Add Family Plan</a></div></div>';
      echo '<div id="dataAddonsTable">'."\n";
      echo '<table class="gridTable">'."\n";    // Container open
      echo '<tr><th>Data Addons</th></tr>';
//Get Data Plans rows
    	$sql2 = "SELECT * FROM DataAddons where PlanID = ".$_GET["id"].' order by Cost';

    	if (!$result2 = $connection->query($sql2)) {
      	die ('There was an error running Data Addons query[' . $connection->error . ']' . 'SQL ='. $sql2);
    	}
    	$rows = $result2->num_rows;
      $cols = $result2->field_count;
    	if($rows > 0) {
  	    while ($row = $result2->fetch_array()) {
  	      $dtlUrl =  "tblEdit.php?mode=edit&table=DataAddons&id=".$row['DataAddonID']."&plan=".$_GET["id"];
  	      echo '<tr>';	// Start a new row
  		    echo '<td><a href="'.$dtlUrl.'">'.$row['Cost'].' '. number_format($row['Data'], 2, '.', '').' </a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	}
      echo '</table>'."\n";  // Close the container
      echo '<div><a href="tblEdit.php?mode=add&table=DataAddons&FK='.$_GET["id"].'">Add Data Addon</a></div>';
      echo "</div>\n"; //close dataAddOns div
  
      echo '<div id="textAddonsTable">'."\n";
      echo '<table class="gridTable">'."\n";    // Container open
      echo '<tr><th>Text Addons</th></tr>';
      $sql3 = "SELECT * FROM TextAddons where PlanID = "
      .$_GET["id"].' order by Cost';

    	if (!$result3 = $connection->query($sql3)) {
      	die ('There was an error running Text Addons query[' . $connection->error . ']' . 'SQL ='. $sql2);
    	}
    	$rows = $result3->num_rows;
      $cols = $result3->field_count;
    	if($rows > 0) {
  	    while ($row = $result3->fetch_array()) {
  	      $dtlUrl =  "tblEdit.php?mode=edit&table=TextAddons&id=".$row['TextAddonID']."&plan=".$_GET["id"];
  	      echo '<tr>';	// Start a new row
  		    echo '<td><a href="'.$dtlUrl.'">'.$row['Cost'].' '. number_format($row['Texts'], 2, '.', '').' </a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	}
    echo '</table>'."\n";  // Close the container
    echo '<div><a href="tblEdit.php?mode=add&table=TextAddons&FK='.$_GET["id"].'">Add Text Addon</a></div>';
    echo "</div>\n"; //close TextAddOns div

    echo '<div id="voiceAddonsTable">'."\n";
    echo '<table class="gridTable">'."\n";    // Container open
    echo '<tr><th>Voice Addons</th></tr>';
    $sql4 = "SELECT * FROM VoiceAddons where PlanID = "
    .$_GET["id"].' order by Cost';

    	if (!$result4 = $connection->query($sql4)) {
      	die ('There was an error running Voice Addons query[' . $connection->error . ']' . 'SQL ='. $sql2);
    	}
    	$rows = $result4->num_rows;
      $cols = $result4->field_count;
    	if($rows > 0) {
  	    while ($row = $result4->fetch_array()) {
  	      $dtlUrl =  "tblEdit.php?mode=edit&table=VoiceAddons&id=".$row['VoiceAddonID']."&plan=".$_GET["id"];
  	      echo '<tr>';	// Start a new row
  		    echo '<td><a href="'.$dtlUrl.'">'.$row['Cost'].' '. number_format($row['Minutes'], 2, '.', '').' </a></td>';
   	      echo '</tr>'."\n";	 //  Close the row
    	  }
    	}
    echo '</table>'."\n";  // Close the container
    echo '<div><a href="tblEdit.php?mode=add&table=VoiceAddons&FK='.$_GET["id"].'">Add Voice Addon</a></div>';
    echo "</div>\n"; //close TextAddOns div

    echo "</div>\n"; //close addOns div
  }
  
  if ($sTable == 'Plans'){
    echo '<div><a href="Plans.php?id='.$sOperator.'">Return to Plans</a></div>';
  }
  $connection->close();
?>
  <div><a href="/dbedit.php">Home</a></div>
  </body>
    <script type="text/javascript">
        function Expander() {
    	
    this.start = function () {
    			
        $(".inputbox").keydown(function(e) {
               			
            this.style.width = 0;
            var newWidth = this.scrollWidth + 10;
    			
            if( this.scrollWidth >= this.clientWidth )
                newWidth += 10;
    				
            this.style.width = newWidth + 'px';
    			
        });
    		
    }
    	
  }
    
  $(function() {
     	window.app = new Expander();
     	window.app.start();
  });
    </script>
  
</html>
