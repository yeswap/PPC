<?Php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  ini_set("log_errors", 1);
  ini_set("error_log", "/usr/home/yeswap/public_html/prepaidcompare.net/admin/php-error.log");
  ob_start();
  
//Conect to db
  $connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
  if ($connection->connect_errno > 0) {
     die ('Unable to connect to database [' . $connection->connect_error . ']');
  }
  
//Get Operator

  $sql = "select n.`Name` nName,NameSuffix,`Technologies`,`Bands`,`CoverageSqMiles`,`CoveragePOPs`,n.`Notes` nNotes,o.`Name` oName, `URL`, `NumberOfLines`,`Founded`,`Taxes`,o.`Notes` oNotes, URL, Phone, Twitter, Facebook, Instagram, Email, Chat, APN, Roaming, CoverageMap, SupportHours, Forum, noMMS, VoLTE, VoWiFi from Operators o join Networks n  on n.NetworkID = o.NetworkID where OperatorID = ".$_GET["id"];

  
  if (!$result1 = $connection->query($sql)) {
      die ('There was an error running the Operators query[' . $connection->error . ']');
  }
  
  $rows = $result1->num_rows;
  $cols = $result1->field_count;
  
  if($rows > 0) {
    $row = $result1->fetch_array();
    $dtlUrl =  "tblEdit.php?mode=edit&table=Operators&id=".$_GET["id"];
    
    $oName = $row['oName'];

    if($row["NameSuffix"]){
        $oName .= " " . $row["NameSuffix"];
    }
    $sPageName= pageName($oName);
    $oName = htmlspecialchars($oName);
  } else {
        die ('OperatorID '.$_GET["id"].' does not exist');
  }
  
  function pageName($string){
    $string = strtolower($string);
    $string = preg_replace('/[^a-zA-Z0-9]/i','-',$string);
    $string = preg_replace("/(-){2,}/",'$1',$string).".html";
    return $string;
  }
/*
  function stripZeros($data){
    $data = "$data"; //convert to string
    $data +=0; //strip trailing zeros
    return $data;
  }
*/
  function stripZeros($n){
      return ((floor($n) == round($n, 2)) ? number_format($n) : number_format($n, 2));
  }
  function formatPhoneNo($sPhoneNo){
    if ($sPhoneNo){
      return "<a href='tel:".urlencode($sPhoneNo)."'>$sPhoneNo</a>";
    }
    return "none";
  }
  function formatSocial($row){
    $sRetVal="";
    $sBefore="";
    if ($row["Twitter"]){
      $sRetVal .= $sBefore."<a href='".$row["Twitter"]."'>Twitter</a>";
      $sBefore = ", ";
    }
    if ($row["Facebook"]){
      $sRetVal .= $sBefore."<a href='".$row["Facebook"]."'>Facebook</a>";
      $sBefore = ", ";
    }
    if ($row["Instagram"]){
      $sRetVal .= $sBefore."<a href='".$row["Instagram"]."'>Instagram</a> ";
      $sBefore = ", ";
    }
    if ($row["Forum"]){
      $sRetVal .= $sBefore."<a href='".$row["Forum"]."'>User Forum</a> ";
    }
    if ($sRetVal){
      $sRetVal ="<b>Social Media:</b> ". $sRetVal;
    }
    return $sRetVal;
  }
  function formatURL($URL){
    return '<a href="'.$URL.'">'.preg_replace("(^https?://)", "", $URL )."</a>";
  }
  function formatEmail($email){
    return '<a ="mailto:'.$email.'">'.$email."</a>";
  }
  function formatChat($chat){
    if (strpos($chat, "http")!==FALSE){
      return "<a href=".$chat.">Live Chat</a>";
    }else{
      return $chat;
    }
  }
  function formatValidity($sValidity){
    switch($sValidity) {
      case "3":
        $validity = " valid for 30 days";
        break;
      case "M":
        $validity = " valid for one month";
        break;
      case "1":
        $validity = " expires with plan month";
        break;
      case "D":
        $validity = " valid for one day";
        break;
      case "9":
        $validity = " valid for 90 days";
        break;
      case "4":
        $validity = " valid for 120 days";
        break;
      case "6":
        $validity = " valid for 60 days";
        break;
      case "S":
        $validity = " valid for 180 days";
        break;
      case "W":
        $validity = " valid for 7 days";
        break;
      case "0":
      case "-1":
        $validity = " doesn't expire";
        break;
      default:
        $validity = "";
    }
    return $validity;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?= $oName ?> Profile</title>
<meta name="description" content="<?= $oName ?> plans, pricing, features, contact information, coverage map, date founded, network and bands used.">
<meta name="keywords" content="<?= $oName ?>, plans, pricing, contact information, coverage map, date founded, network, bands" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=rMJdgme0zY">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=rMJdgme0zY">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=rMJdgme0zY">
<link rel="manifest" href="/site.webmanifest?v=rMJdgme0zY">
<link rel="mask-icon" href="/safari-pinned-tab.svg?v=rMJdgme0zY" color="#5bbad5">
<link rel="shortcut icon" href="/favicon.ico?v=rMJdgme0zY">
<meta name="apple-mobile-web-app-title" content="PPCompare">
<meta name="application-name" content="PPCompare">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@yeswap" />
<meta name="twitter:title" content="<?= $oName ?> Profile" />
<meta name="twitter:description" content="<?= $oName ?> plans, pricing, contact information, coverage map, date founded, network and bands used.">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta name="twitter:image" content="https://prepaidcompare.net/apple-icon.png" />

<meta property="fb:app_id" content="448013232393047"/>
<meta property="og:locale" content="en_us" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= $oName ?> Profile" />
<meta property="og:description" content="<?= $oName ?> plans, pricing, contact information, coverage map, date founded, network and bands used.">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta property="og:url" content="https://prepaidcompare.net/<?= $sPageName ?>" />
<meta property="og:site_name" content="PrepaidCompare" />
<meta property="og:image" content="https://prepaidcompare.net/apple-icon.png" />
<script type="application/ld+json">
[
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "https://prepaidcompare.net",
  "logo": "https://prepaidcompare.net/logo.png",
  "ethicsPolicy" : "Prepaidcompare.net is non-commercial and strives to be impartial. We don't accept payment from operators and list plans solely by monthly cost. We aim to include all operators and plans that offer competitive pricing at one or more price points.",
  "potentialAction": "Find the best prepaid plan for your budget",
  "name" : "PrepaidCompare"
},
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "https://prepaidcompare.net/profiles/<?= $sPageName ?>",
  "name": "<?= $oName ?> Cellphone Operator Profile",
   "author": {
      "@type": "Person",
      "name": "Dennis Bournique",
       "sameAs" : [ "https://www.facebook.com/wapreview",
      "https://www.reddit.com/user/yeswap",
      "https://twitter.com/yeswap",
      "https://www.howardforums.com/member.php/262421-Yeswap"]
    },
  "description" : "Profile of US prepaid cellphone operator <?= $oName ?> with plans, pricing, contact information, coverage map, date founded, network and bands used.",
  "publisher": "PrepaidCompare"
}
]
</script>
<link rel="stylesheet" href="profile.css">
</head>
<div id="page">
  <div id="main">
  <div id="branding" role="banner">
  <div id="site-title">
  <h1><a href="http://prepaidcompare.net/" title="Prepaid Compare" rel="home">prepaid<span style="color:#007d15">compare</span></a>
  </h1>
</div>
<div class="separator"></div>
</div><!-- #branding -->
<div id="menu-container">
<nav>
 <ul class="menu">
   <li class="page_item"><a href="/">Home</a></li>
   <li class="page_item"><a href="/deals" title="Deals">Deals</a></li>
   <li class="page_item"><a href="/profiles/" title="Operator Profiles">Operators</a></li>
   <li class="page_item hidable"><a href="/about.html">About</a></li>
   <!-- <li class="page_item hidable"><a href="/privacy.html" title="Privacy Policy">Privacy</a></li> -->
   <li class="page_item"><a href="/help.html">Help</a></li>
 </ul>
</nav>
  <div id="socialicons">
    <a target="_blank" title="Twitter" rel="noopener" href="https://www.twitter.com/yeswap"><img alt="twitter" src="twit.png" border="0"></a>
    <a targt="_blank" title="Facebook" rel="noopener" href="https://www.facebook.com/PrepaidCompare"><img alt="Facebook" src="fb.png" border="0"></a>
  </div>
  <div class="separator"></div>
</div>
<div id="content">
 <div class="item">
<?php
      
      echo "<h2>".$oName." Profile</h2>\n";
      
      echo "<div><b>Founded:</b> ".strftime("%G",strtotime($row["Founded"]))."</div>";
      echo "<div><b>Network:</b> ".htmlspecialchars($row["nName"])."</div>\n";
      $nLines = $row["NumberOfLines"];
      if ($nLines >= 1000){
        $sLines = $nLines/1000 . " millon.";
      }else{
        $sLines = number_format($nLines * 1000,0,".",",")."."
        ;
      }
      echo "<div><b>Approx Number of Lines:</b> ".$sLines."</div>";
      $nTaxes = $row["Taxes"];
      switch($nTaxes) {
        case 0:
          $sTaxes = "none.";
          break;
        case 1:
          $sTaxes = "point of sale (sales tax, etc.) only. Typically under 10%.";
          break;
        case 2:
          $sTaxes = "Point of sale plus telecom taxes and fees (USF, cost recovery fee, etc), averages about 20%.";
          break;
      }
      echo "<div><b>Taxes collected:</b> ".$sTaxes."</div>\n";
      echo "<div><b>Website:</b> ".formatURL($row["URL"])." <b>Phone:</b> ".formatPhoneNo($row["Phone"])."</div>\n";
      if ($row["SupportHours"]){
        echo "<div><b>Support Hours:</b> ".$row["SupportHours"]."</div>\n";
      }
      echo "<div>".formatSocial($row)."</div>\n";
      if ($row["Email"]){
        echo "<div><b>Email:</b> ".formatEmail($row["Email"])."</div>\n";
      }
      if ($row["Chat"]){
        echo "<div><b>Chat:</b> ".formatChat($row["Chat"])."</div>\n";
      }
      if ($row["APN"]){
        echo "<div><b>APN:</b> (Only non-default values are shown. Any fields not listed below should be left at their default values, which may be blank.<div id='APN'>".nl2br(trim($row["APN"]))."</div></div>\n";
      }
      if ($row["Roaming"]){
        echo "<div><b>Roaming:</b> ".$row["Roaming"]."</div>\n";
      }else{
        echo "<div><b>Roaming:</b> None</div>\n";
      }
      if ($row["CoverageMap"]){
        echo "<div><b>CoverageMap:</b> ".formatURL($row["CoverageMap"])."</div>\n";
      }
      if ($row["oNotes"]){
        echo "<div><b>Notes:</b> ".$row["oNotes"]."</div>\n";
      }
      if($row["noMMS"]){
        echo "<div><b>MMS supported on iOS?</b> No</div>";
      }else{
        echo "<div><b>MMS supported on iOS?</b> Yes</div>";
      }
    
      echo '<div><b>VoLTE</b> ' .$row["VoLTE"]."</div>\n";;
      echo ' <div><b>WiFi Calling:</b> '.$row["VoWiFi"]."</div>\n";
           
      echo "<div><b>Bands:</b> ".$row["Bands"].".</div>\n";
      echo "<div><b>Approximate Coverage:</b> ";
      echo $row["CoverageSqMiles"]." million square miles, ";
      echo $row["CoveragePOPs"]. " million people.</div>\n";
      echo "<div>".$row["nNotes"]."</div>\n";

      echo "<h2>Plans</h2>\n";
      $sql = "select * from Plans where OperatorID =".$_GET["id"]." order by Cost";
      if (!$result = $connection->query($sql)) {
        die ('There was an error running the Plans query[' . $connection->error . ']');
      }
      
      $rows = $result->num_rows;
      echo "<ul id='planlist'>";
      while ($row = $result->fetch_array()) {
        echo "<li><h3>".$row["Name"]."</h3>";
        echo "<b>Cost:</b> $".stripZeros($row["Cost"]);
        switch ($row['CostType']) {
           case "M":
             $costType = " per month";
             break;
            case "3":
             $costType = " per 30 days";
             break;
            case "4":
             $costType = " per 120 days";
             break;
            case "6":
             $costType = " per 60 days";
             break;
            case "9":
             $costType = " per 90 days";
             break;
           case "D":
             $costType = " per day";
             break;
           case "S":
             $costType = " per 180 days";
             break;
            case "Y":
             $costType = " per year";
             break;
            case "0":
             $costType = " doesn't expire";
             break;
          default:
            $costType = "";
        }
        echo $costType;
    
        if ($row['LineFee'] != 0){
          $monCost = $row["Cost"] + $row['LineFee'];
          echo " + $".stripZeros($row['LineFee']) . " line fee = $".stripZeros($monCost);
        }
        echo ".";
        if ($row['AutopayDiscount']!=0){
          echo " <b>Auto Pay Discount:</b> $".$row['AutopayDiscount'].".";
        }
        echo "<br/>\n";
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
      echo '<b>Minutes: </b> '.$minutes.', ';

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
      echo '<b>Texts: </b> '.$texts.', ';

      switch (true) {
        case $row['Data'] == -1:

          $data = "Unlimited";
          break;
        case $row['Data'] == 0:
          if ($row['OverageThrottle']>1){
            $data = "Unlimited at ". $row['OverageThrottle'].' kbps';
          }else{
            $data = "None";
          }
          break;
        case $row['Data'] > 1023:
          $data = round($row['Data']/1024,2) . ' GB';
          break;
        case $row['Data'] == 2.05:
          $data = '$'.stripZeros($row['Data']).'/MB';
          break;
        case $row['Data'] >= 1:
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
      if ($row['DataShared'] != 0){
        $data .= " shared";
      }
      if ($row["BaseThrottle"]>1){
        if ($row["BaseThrottle"]>1024){
          $baseSpeed = " at " . $row["BaseThrottle"]/1024 . " mbps";
        }else{
          $baseSpeed = " at " . $row["BaseThrottle"] . " kbps"; 
        }
      }else{
        $baseSpeed = " at high speeds";
      }
      echo '<b>Data: </b> '.$data;
      if ($row['Data'] != 0) {
        switch ($row['OverageThrottle']) {
         case -1:
           $throttle = $baseSpeed . " hard capped";
           break;
         case 0:
           $throttle = $baseSpeed;
           break;
          default:
            $throttle = $baseSpeed . ", followed by unlimited data at ". $row['OverageThrottle'].' kbps';
          }
          echo $throttle;
        }
      echo ".<br/>\n";
      $AllowsHotspot = $row["AllowsHotspot"];
      $Hotspot_HS_Limit = $row["Hotspot_HS_Limit"];
      $Hotspot_HS_Throttle = $row["Hotspot_HS_Throttle"];
      $HotspotThrottle = $row["HotspotThrottle"];
      if($AllowsHotspot){
        
        switch (true) {
          case $Hotspot_HS_Limit == -1:
            $sHotspot = "unlimited";
            break;
          case $row['Data'] == 0:
            $sHotspot = "If data addon purchased, addon amount";
            break;
          case $Hotspot_HS_Limit >= 1024:
            $sHotspot = $Hotspot_HS_Limit /1024 ." GB";
            break;
          case $Hotspot_HS_Limit == 0:
            $sHotspot = $data;
            break;
          default:
            $sHotspot = $Hotspot_HS_Limit . " MB";
        }
        switch (true) {
          case $Hotspot_HS_Throttle == 0:
            $sHotspot .= " at high speeds" ;
            break;
          case $Hotspot_HS_Throttle >= 1024:
            $sHotspot .= " at " . $Hotspot_HS_Throttle / 1024 ." mbps";
            break;
          default:
            $sHotspot .= " at " . $Hotspot_HS_Throttle . "kbps";
        }
        switch (true) {
          case $HotspotThrottle >= 1024:
            $sHotspot .= " then unlimited at " . $HotspotThrottle /1024 ."mbps";
            break;
          case $HotspotThrottle > 1:
            $sHotspot .= " then unlimited at " . $HotspotThrottle ."kbps";
            break;
          case $Hotspot_HS_Limit != -1:
            $sHotspot .= ", hardcapped";
        }
        if($AllowsHotspot > 1){
          $sHotspot .= " for $" . $AllowsHotspot . "/mo";
        }
        
      }else{
       $sHotspot = 'none';
      }
      echo "<b>Hotspot:</b> ".$sHotspot.".\n";
      $planID = $row["ID"];
      $sqlFP = "select NumLines, Price, AutoPayDiscount, DataShared, Notes, MinutesShared from FamilyPlans where PlanID =".$planID."  order by NumLines";
      if (!$resultFP = $connection->query($sqlFP)) {
          die ('There was an error running FamilyPlans query [' . $connection->error . ']');
      }
      $rowsFP = $resultFP->num_rows;
      if($rowsFP > 0) {
          $sFP="<br/><b>Family Plans:</b><br/>";
        $sBefore = "";
        $sNotes = "";
        while ($rowFP = $resultFP->fetch_array()) {
          $sFP .= $sBefore.$rowFP["NumLines"]." lines: $".$rowFP["Price"];
          $shared="";
          if($rowFP["MinutesShared"]==1){
            $shared = " minutes";
            if($rowFP["DataShared"]==1){
              $shared .= " and data";
            }
          }elseif($rowFP["DataShared"]==1){
            $shared .= " data";
          }
          if($shared != ""){
            $sFP .= $shared . " shared";
          }
          if ($rowFP["AutoPayDiscount"] != "0.00"){
            $sFP .= ", $".$rowFP["AutoPayDiscount"]. " autopay discount";
          }
          if ($rowFP["Notes"]){
            $sNotes = "<br><b>Family Plan Notes: </b>".nl2br(trim($rowFP["Notes"]));
          }
          $sBefore = "<br/>";
        }
        echo $sFP . ". " . $sNotes."\n";
      }

      $sqlVA = "select * from VoiceAddons where PlanID =".$planID." order by Cost";
      if (!$resultVA = $connection->query($sqlVA)) {
      	die ('There was an error running VoiceAddons query [' . $connection->error . ']');
    	}
      $rowsVA = $resultVA->num_rows;
      
      $sqlTA = "select * from TextAddons where PlanID =".$planID." order by Cost";
      if (!$resultTA = $connection->query($sqlTA)) {
      	die ('There was an error running TextAddons query [' . $connection->error . ']');
    	}
      $rowsTA = $resultTA->num_rows;
      
      $sqlDA = "select * from DataAddons where PlanID =".$planID." order by Cost";
      if (!$resultDA = $connection->query($sqlDA)) {
      	die ('There was an error running DataAddons query [' . $connection->error . ']');
    	}
      $rowsDA = $resultDA->num_rows;
      
      if ($rowsVA) {
        echo "<br/>\n<b>Minutes Addons:</b>";
      }
      while ($rowVA = $resultVA->fetch_array()) {
          echo "<br/>\n" . "$". stripZeros($rowVA["Cost"]) . " for ";
          if ($rowVA["Minutes"] == -1){
            echo "unlimited minutes";
          }else{
            echo $rowVA["Minutes"]. " minutes ";
          }
          echo formatValidity($rowVA["Validity"]);
      }
      if ($rowsTA) {
          echo "<br/>\n<b>Messaging Addons:</b>";
      }
      while ($rowTA = $resultTA->fetch_array()) {
        echo "<br/>\n" . "$". stripZeros($rowTA["Cost"]) . " for ";
        if ($rowTA["Texts"] == -1){
          echo "unlimited messages";
        }else{
          echo $rowTA["Texts"]. " messages ";
        }
        echo formatValidity($rowTA["Validity"]);
      }
      if ($rowsDA){
          echo "<br/>\n<b>Data Addons:</b>";
      }
      while ($rowDA = $resultDA->fetch_array()) {
        if($rowDA['Data'] > 1023){
          $data = $rowDA['Data']/1024 . ' GB';
        }elseif($rowDA['Data'] == -1){
          $data = 'unlimited data';
        }else{
          $data = $rowDA['Data'];
          $data = stripZeros($data) . ' MB';
        }
        echo "<br/>\n" . "$". stripZeros($rowDA["Cost"]) . " for " . $data;
        echo formatValidity($rowDA["Validity"]);
      }
      if ($row["Notes"]){
        echo "<br/><b>Plan Notes: </b>" . $row["Notes"];
      }
      echo "</li>\n";
    }
    echo "</ul>\n";
    $connection->close();
     
    ?>
<div id="contact">I make every effort to insure the information on this site is accurate. Please report errors on this <a href="https://docs.google.com/forms/d/10HEgk4vO7ym_0LJ7JHPdebj5ocJDWe-kM6MfSKznXx0/">form</a> or by messaging me on <a href="https://twitter.com/yeswap">Twitter</a>, <a href="https://www.facebook.com/PrepaidCompare/">Facebook</a>, <a href="https://www.reddit.com/user/yeswap">Reddit</a> or <a href="https://www.howardforums.com/member.php/262421-Yeswap">HowardForums</a>.
</div>
</div> <!-- item -->
</div> <!-- content -->
  <div id="footer">
    <a href="/">Home</a>
    <a href="/about.html">About</a>
    <a href="/privacy.html">Privacy</a>
    <a href="/help.html">Help</a>
  </div>
</div> <!-- main -->
</div> <!-- page -->
</body>
</html>
<?php
$site = ob_get_contents();
ob_end_clean();
$filename = "/usr/home/yeswap/public_html/prepaidcompare.net/profiles/". $sPageName;
$sitefile = fopen($filename, "w");
fwrite($sitefile, $site);
fclose($sitefile);
?>