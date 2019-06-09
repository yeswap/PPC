<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set("display_errors", 1); # 0 - production, 1 - development
ob_start();
?>
    <div id="chooser">
      <h2><b>Prepaid Plan Finder</b></h2>
      <p>
        Needed per month:
        <span class="inputGroup"><label for="mins">Minutes</label>
          <input id="mins" type = "number" class="textfield"/>
        </span>
        <span class="inputGroup"><label for="texts">Texts</label>
          <input id="texts" type = "number" size="1" class="textfield"/>
        </span>
        <span class="inputGroup"><label for="MB">Data </label>
        <input id="MB" type = "number" size="1" class="textfield"/>
        <label for="MBGB"><span class="hiddenLabel">Choose MB or GB</span></label><select id="MBGB">
          <option label="MB" value="MB">MB</option>
          <option label="GB" value="GB">GB</option>
        </select>
        </span>
      </p>
      <p>
      On:
        <span class="chk"><input id="ATTyes" type="checkbox" checked="checked"/>
        <label for="ATTyes">AT&amp;T</label></span>
        <span class="chk"><input id="VZWyes" type="checkbox" checked="checked"/>
        <label for="VZWyes">Verizon</label></span>
        <span class="chk"><input id="TMOyes" type="checkbox" checked="checked"/>
        <label for="TMOyes">T-Mobile</label></span>
        <span class="chk"><input id="SPRyes" type="checkbox" checked="checked"/>
        <label for="SPRyes">Sprint</label></span>
      </p>
      <p>
        <label for="lines">Lines Needed: </label>
        <select id="lines">
          <option value="1" selected>1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
        </select>
      </p>
      <p>
      Only show plans with:
        <span class="chk"><input id="hotspot" type="checkbox"/>
        <label for="hotspot">Hotspot</label></span>
        <span class="chk"><input id="roaming" type="checkbox"/>
        <label for="roaming">Domestic roaming</label></span>
        <span class="chk"><input id="iosMMS" type="checkbox"/>
        <label for="iosMMS">iOS MMS</label></span>
        <span class="chk"><input id="unlimTrot" type="checkbox"/>
        <label for="unlimTrot">Unlim slow data</label></span>
      </p>
      <p>
        <span class="chk"><input id="Autopay" type="checkbox"/>
        <label for="Autopay">Apply Autopay Discounts</label></span>
      <p>
        <input id="calc" type="button" value="Find Plans" onclick = "showMsg(); setTimeout(findPlans,5);"/>
      </p>
      
    </div>
  <div id="wrap">
<?Php
    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

      function stripZeros($data){
        $data = "$data"; //convert to string
        $data +=0; //strip trailing zeros
        return $data;
      }
      
      function buildFamilyPlanObject($connection){
        $sql = "select * from FamilyPlans order by PlanID, NumLines";
        if (!$result = $connection->query($sql)) {
            die ('There was an error running FamilyPlans query [' . $connection->error . ']');
        }
        $FmlyPlnAry = array();
        $rows = $result->num_rows;
        if($rows > 0) {
          $PlanID = -1;
          $bInit = False;
          while ($row = $result->fetch_array()) {
            $FmlyPlnAry[$row["PlanID"]][$row["NumLines"]]=array( $row["Price"],
              $row["MinutesShared"],
              $row["TxtsShared"],
              $row["DataShared"],
              $row["AutoPayDiscount"]
              );
          }
          return $FmlyPlnAry;
      }
    }

      function buildDataAddonsObject($connection){
        abstract class eData{
          const Cost = 0;
          const Validity = 1;
          const Data = 2;
        }
        $sql = "select * from DataAddons order by PlanID, Cost";
        if (!$result = $connection->query($sql)) {
            die ('There was an error running DataAddons query [' . $connection->error . ']');
        }
        $rows = $result->num_rows;
        $cols = $result->field_count;
        if($rows > 0) {
          $PlanID = -1;
          $DataAddonsAry = array();
          while ($row = $result->fetch_array()) {
            if ($PlanID !== $row["PlanID"]) {
              $PlanID = $row["PlanID"];
              $subkey = 1;
            }else{
              $subkey += 1;
            }
            $TmpAry = array();
            $TmpAry[eData::Cost] = $row["Cost"];
            $TmpAry[eData::Validity] = $row["Validity"];
            $TmpAry[eData::Data] = $row["Data"];
            $DataAddonsAry[$row["PlanID"].$subkey] = $TmpAry;
          }
          return $DataAddonsAry;
        }
      }
      
      function buildMinsAddonsObject($connection){
        abstract class eMins{
          const Cost = 0;
          const Validity = 1;
          const Mins = 2;
        }
        $sql = "select * from VoiceAddons order by PlanID, Cost";
        if (!$result = $connection->query($sql)) {
            die ('There was an error running VoiceAddons query [' . $connection->error . ']');
        }
        $rows = $result->num_rows;
        $cols = $result->field_count;
        if($rows > 0) {
          $PlanID = -1;
          $MinsAddonsAry = array();
          while ($row = $result->fetch_array()) {
            if ($PlanID !== $row["PlanID"]) {
              $PlanID = $row["PlanID"];
              $subkey = 1;
            }else{
              $subkey += 1;
            }
            $TmpAry = array();
             $TmpAry[eMins::Cost] = $row["Cost"];
            $TmpAry[eMins::Validity] = $row["Validity"];
            $TmpAry[eMins::Mins] = $row["Minutes"];
            $MinsAddonsAry[$row["PlanID"].$subkey] = $TmpAry;
          }
          return $MinsAddonsAry;
        }
      }
      
      function buildTxtsAddonsObject($connection){
        abstract class eTxts{
          const Cost = 0;
          const Validity = 1;
          const Txts = 2;
        }
        $sql = "select * from TextAddons order by PlanID, Cost";
        if (!$result = $connection->query($sql)) {
            die ('There was an error running TextAddons query [' . $connection->error . ']');
        }
        $rows = $result->num_rows;
        $cols = $result->field_count;
        if($rows > 0) {
          $PlanID = -1;
          $TxtsAddonsAry = array();
          while ($row = $result->fetch_array()) {
            if ($PlanID !== $row["PlanID"]) {
              $PlanID = $row["PlanID"];
              $subkey = 1;
            }else{
              $subkey += 1;
            }
            $TmpAry = array();
             $TmpAry[eTxts::Cost] = $row["Cost"];
            $TmpAry[eTxts::Validity] = $row["Validity"];
            $TmpAry[eTxts::Txts] = $row["Texts"];
            $TxtsAddonsAry[$row["PlanID"].$subkey] = $TmpAry;
          }
          return $TxtsAddonsAry;
        }
      }
      function buildOprMeta($connection){
        abstract class eOpr{
          const URL = 0;
          const Taxes = 1;
          const Notes = 2;
          const NameSuffix = 3;
        }
        $sql = "select * from Operators order by OperatorID";
        if (!$result = $connection->query($sql)) {
            die ('There was an error running Operators Meta query [' . $connection->error . ']');
        }
        $rows = $result->num_rows;
        $cols = $result->field_count;
        if($rows > 0) {
          $OprMetaAry = array();
          while ($row = $result->fetch_array()) {
            $TmpAry = array();
            $TmpAry[eOpr::URL] = $row["URL"];
            $TmpAry[eOpr::Taxes] = $row["Taxes"];
            $TmpAry[eOpr::Notes] = $row["Notes"];
            $TmpAry[eOpr::NameSuffix] = $row["NameSuffix"];
            $OprMetaAry[$row["OperatorID"]] = $TmpAry;
          }
          return $OprMetaAry;
        }
      }
      $connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
      
      if ($connection->connect_errno > 0) {
        die ('Unable to connect to database [' . $connection->connect_error . ']');
      }
    //  $sql = "SELECT * FROM Plans";
      $sql = "select Plans.ID as PlanID, Plans.MonthlyCost as MonthlyCost, Operators.Name as Operator, Networks.Name as Network, Plans.Name as Plan, Cost, CostType, Minutes, Texts, AppleSupportLevel & 1=1 as MMS, Data, OverageThrottle, Plans.Notes as Notes, isPayGo, HasRollover, LineFee, MultiLine, Plans.OperatorID, AllowsHotspot, Hotspot_HS_Limit, Hotspot_HS_Throttle, HotspotThrottle, TextRoaming, VoiceRoaming, DataRoaming, AutopayDiscount from Plans join Operators on Plans.OperatorID = Operators.OperatorID join Networks on Operators.NetworkID = Networks.NetworkID order by MonthlyCost, Network";
      if (!$result = $connection->query($sql)) {
          die ('There was an error running query[' . $connection->error . ']');
      }
      $rows = $result->num_rows;
      $cols = $result->field_count;
      if($rows > 0) {
        echo '<table id ="planTbl" title="Click any row for more informaton about that plan">'."\n";
        echo "<thead>\n";
        echo '<tr><th>Cost per Month</th><th>Brand</th><th>Net&shy;work</th><th>Mins</th><th>Txts</th><th>Data</th></tr>'."\n";
        echo '<tr><th colspan ="6" id="caption">Click any row below for more informaton</th></tr>'."\n";
        echo "</thead>\n";
      $rowPersist=array(); //an array of data we want to persist accross calc runs table changes
      $rowMeta=array(); //array of plan data
      while ($row = $result->fetch_array()) {
        $PlanID = $row["PlanID"];
        $tempPersist=array();
        $tempRow=array();
        //hide multiline plans initially
        if ($row["MultiLine"]){
          echo "<tr style='display:none'>";
        }else{
          echo '<tr>';  // Start a new row
        }
        echo '<td>'.$row['MonthlyCost'].'</td>';
        $tempPersist[]=$row['MonthlyCost'];
        echo '<td>'.$row['Operator'].'</td>';
        switch ($row['Network']){
          case "AT&T":
            $network = "ATT";
            break;
          case "Sprint":
            $network = "SPR";
            break;
          case "T-Mobile":
            $network = "TMO";
            break;
          case "Verizon";
            $network = "VZW";
            break;
          case "Sprint + T-Mobile + US Cellular";
            $network = "Fi";
            break;
        }
        echo '<td>'.$network.'</td>';
        $tempRow[]=$row['Plan'];
        $tempRow[]=$row['Cost'];
        $tempRow[]=$row['CostType'];
        /*
        switch ($row['CostType']) {
          case "M":
            $costType = "month";
            break;
           case "3":
            $costType = "30 days";
            break;
           case "4":
            $costType = "120 days";
            break;
           case "6":
            $costType = "60 days";
            break;
           case "9":
            $costType = "90 days";
            break;
          case "D":
            $costType = "day";
            break;
          case "S":
            $costType = "180 days";
            break;
           case "Y":
            $costType = "year";
            break;
           case "0":
            $costType = "doesn't expire";
            break;
         default:
           $costType = "";
       }
      echo '<td class="optional">'.$costType.'</td>';
      */
      
      switch ($row['Minutes']) {
        case -1:
          $minutes = "Unlimited";
          break;
        case $row['Minutes'] == 0:
          $minutes = "None";
          break;
        case $row['Minutes'] > 1:
          $minutes = stripZeros($row['Minutes'])."";
          break;
         default:
           $minutes = '$'.$row['Minutes'].'/ea';
          if ($row['Minutes'] < 1) {
             $minutes = $row['Minutes']*100 ."¢ ea";
           }else{
             $minutes = '$'.$row['Minutes'].' ea';
           }
       }
      echo '<td>'.$minutes.'</td>';
      $tempPersist[]=$minutes;
      switch ($row['Texts']) {
        case -1:
          $texts = "Unlimited";
          break;
        case $row['Texts'] == 0:
          $texts = "None";
          break;
        case $row['Texts'] > 1:
          $texts = stripZeros($row['Texts'])."";
          break;
         default:
           if ($row['Texts'] < 1) {
             $texts = $row['Texts']*100 ."¢ ea";
           }else{
             $texts = '$'.$row['Texts'].' ea';
           }
       }
      echo '<td>'.$texts.'</td>';
      $tempPersist[]=$texts;
      switch (true) {
        case $row['Data'] == -1:
          $data = "Unlimited";
          break;
        case $row['Data'] == 0:
          $data = "None";
          break;
        case $row['Data'] > 1023:
          $data = round($row['Data']/1024,2) . ' GB';
          break;
        case $row['Data'] == 2.05:
          $data = $row['Data'];
          $data = '$2.05/MB';
          break;
        case $row['Data'] > .99:
          $data = $row['Data'];
          $data = stripZeros($data) . ' MB';
          break;
         default:
           if ($row['Data'] < 1) {
             $data = $row['Data']*100;
             $data = number_format($data, 2, '.', '');
             $data = stripZeros($data) . '¢/MB';
           }else{
             $data = $row['Data'];
             $data = '$'.stripZeros($data).'/MB';
           }
       }
      echo '<td>'.$data.'</td>';
      $tempPersist[]=$row['Data'];
      /*
      switch ($row['OverageThrottle']) {
        case -1:
          $throttle = "Hard capped";
          break;
        case 0:
          $throttle = "N/A";
          break;
         default:
           $throttle = $row['OverageThrottle'].' kbps';
       }
      */
      $tempRow[]=$row['OverageThrottle'];
      //echo '<td class="optional">'.$throttle.'</td>';
      echo '<td class="hidden">'.$row['PlanID'].'</td>';
      //echo '<td class="hidden">'.$row['isPayGo'].'</td>';
      //echo '<td class="hidden">'.$row['HasRollover'].'</td>';
      //echo '<td class="hidden">'.$row['MMS'].'</td>';
      //echo '<td class="hidden">'.$row['Plan'].'</td>';
      echo '</tr>'."\n";   //  Close the row
      $tempRow[]=$row['isPayGo'];
      $tempRow[]=$row['HasRollover'];
      $tempRow[]=$row['LineFee'];
      $tempRow[]=$row['MultiLine'];
      $tempRow[]=$row['Notes'];
      $tempRow[]=$row['OperatorID'];
      $tempRow[]=$row['AllowsHotspot'];
      $tempRow[]=$row['Hotspot_HS_Limit'];
      $tempRow[]=$row['Hotspot_HS_Throttle'];
      $tempRow[]=$row['HotspotThrottle'];
      $tempRow[]=$row['TextRoaming'];
      $tempRow[]=$row['VoiceRoaming'];
      $tempRow[]=$row['DataRoaming'];
      $tempRow[]=$row['AutopayDiscount'];
      $tempRow[]=$row['MMS']; //iosMMS support
      $tempRow[]=''; // ShowWork
      $rowMeta[$PlanID][]=$tempRow;
      $rowPersist[$PlanID][]=$tempPersist;
      }
      echo '</table>'."\n";  // Close the container
      echo '</div>'."\n";  // end div id wrap
      $JSON3 = json_encode($rowMeta, JSON_NUMERIC_CHECK);
      $JSON4 = json_encode($rowPersist, JSON_NUMERIC_CHECK);
    }
    $FmlyPlnAry = buildFamilyPlanObject($connection);
    $JSON = json_encode($FmlyPlnAry);
    $DataAddonsAry = buildDataAddonsObject($connection);
    $JSON2 = json_encode($DataAddonsAry);
    $MinsAddonsAry = buildMinsAddonsObject($connection);
    $MinsJSON = json_encode($MinsAddonsAry);
    $TxtsAddonsAry = buildTxtsAddonsObject($connection);
    $TxtsJSON = json_encode($TxtsAddonsAry);
    $OprMetaAry = buildOprMeta($connection);
    $OprJSON = json_encode($OprMetaAry);
    ?>
  </div>
  <script>
    var FmlyPlns = <?= $JSON ?>;
    var DataAddons = <?= $JSON2 ?>;
    var RowAry =  <?= $JSON3 ?>;
    var Persist = <?= $JSON4 ?>;
    var MinsAddons = <?= $MinsJSON ?>;
    var TxtsAddons = <?= $TxtsJSON ?>;
    var OprMeta = <?= $OprJSON ?>;
  </script>
<?php
$compare = ob_get_contents();
ob_end_clean();
$comparefile = fopen("compare.inc.php", "w");
fwrite($comparefile, $compare);
fclose($comparefile);
?>