<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Compare Prepaid Plans</title>

	<style type = "text/css">
		th {font-weight: bold}
		table {
		  table-layout:fixed; 
			width: 100%;
			margin-bottom: 1em;
			overflow-y: hidden;
			border: 1px solid #ddd;
			min-height: .01%;
			overflow-x: auto;
			border-collapse: collapse;
		}
		td, th {
			border: 1px solid #ddd;
			padding: 1px;
			word-wrap:break-word;
			overflow-wrap: break-word;
			-ms-hyphens: auto;
      -moz-hyphens: auto;
      -webkit-hyphens: auto;
      hyphens: auto;
		}
		.textfield {width: 10ex;}
    th.hidden, td.hidden, th.optional, td.optional {display: none;}
    th, td {font-size: smaller;}
    @media screen and (min-width: 700px) {
      th.optional, td.optional {display: table-cell;}
      th, td {font-size: medium;}
    }
	</style>
  </head>
  
  <body onLoad="displayTable (arr)">
		<div>
			Needed per month:
			<label for="mins">Minutes</label>
			<input id="mins" type = "text" class="textfield"/>
			<label for="texts">Texts</label>
			<input id="texts" type = "text" size="1" class="textfield"/>
			<label for="MB">Data in GB</label>
			<input id="MB" type = "text" size="1" class="textfield"/>
		</div>
		<div>
			<label for="ATTyes">AT&T</label>
			<input id="ATTyes" type="checkbox" checked="checked" onchange="showhideRow(this.checked,'AT&T');"/>
			<label for="VZWyes">Verizon</label>
			<input id="VZWyes" type="checkbox" checked="checked" onchange="showhideRow(this.checked,'Verizon');"/>
			<label for="TMOyes">T-Mobile</label>
			<input id="TMOyes" type="checkbox" checked="checked" onchange="showhideRow(this.checked,'T-Mobile');"/>
			<label for="SPRyes">Sprint</label>
			<input id="SPRyes" type="checkbox" checked="checked" onchange="showhideRow(this.checked,'Sprint');"/>
  	</div>
  	<div>
		  <label for="lines">Number of Lines</label>
		  <select id="lines">
        <option value="1" selected>1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
      </select>
  	</div>  	
		<div>
			<input id="calc" type="button" value="Find Plans" onclick="displayTable(arr);" />
		</div>
		<table>
      <thead>
        <tr><th>Cost / Month</th><th>Operat&shy;or</th><th>Net&shy;work</th><th>Plan</th><th class="optional">Cost</th><th class="optional">Cost&shy;Type</th><th>Minutes</th><th>Texts</th><th class="optional">MMS</th><th>Data</th><th class="optional">Thrott&shy;led</th><th class="hidden">PlanID</th></tr>
        </thead>
        <tbody>
        </tbody>
    </table>  
    <script type="text/javascript" src="makeArray.js">
	  </script>
	<script type="text/javascript">
		var arr; 
<?Php
    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	
    	if ($connection->connect_errno > 0) {
    		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}
    //	$sql = "SELECT * FROM Plans";
      $sql = "select Plans.ID as ID, Plans.MonthlyCost as MonthlyCost, Operators.Name as Operator, Networks.Name as Network, Plans.Name as Plan, Cost, CostType, Minutes, Texts, MMS, Data, OverageThrottle, Plans.Notes as Notes, isPayGo, HasRollover from Plans join Operators on Plans.OperatorID = Operators.OperatorID join Networks on Operators.NetworkID = Networks.NetworkID order by MonthlyCost";
    	if (!$result = $connection->query($sql)) {
        	die ('There was an error running query[' . $connection->error . ']');
    	}
      $rows = $result->num_rows;
      $cols = $result->field_count;
			$cnt = 0;
// var arr = [[89, 77, 78],[76, 82, 81],[91, 94, 89]];
    	if($rows > 0) {
				echo 'arr = ['."\n";    // Begin global array definition. 
				while ($row = $result->fetch_array()) {
					echo ('[0'); // 0 is calculated cost column
					for ($col = 0; $col < $cols; $col++) {
						echo (',"'.$row[$col].'"');
					}
					$cnt++;
					if ($cnt < $rows) {
						echo("],\n"); // close row
					}else{
						echo("]\n"); // last row, no comma
					}
				}
				echo("];\n"); // close array
			}
?>
	</script>
  </body>
</html>
