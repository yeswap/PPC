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

  $sql = "select n.`Name` nName,NameSuffix,`Technologies`,`Bands`,`CoverageSqMiles`,`CoveragePOPs`,n.`Notes` nNotes,o.`Name` oName, `URL`, `NumberOfLines`,`Founded`,`Taxes`,o.`Notes` oNotes, URL, Phone, Twitter, Facebook, Instagram, Email, Chat, APN, Roaming, CoverageMap, SupportHours, Forum, noMMS from Operators o join Networks n  on n.NetworkID = o.NetworkID where OperatorID = ".$_GET["id"];

  
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
  } else {
        die ('OperatorID '.$_GET["id"].' does not exist');
  }
  
  function pageName($string){
    $string = strtolower($string);
    $string = preg_replace('/[^a-zA-Z0-9]/i','-',$string);
    $string = preg_replace("/(-){2,}/",'$1',$string).".html";
    return $string;
  }

  function stripZeros($data){
    $data = "$data"; //convert to string
    $data +=0; //strip trailing zeros
    return $data;
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
      case "1":
        $validity = " expires with plan month";
        break;
      case "D":
        $validity = " valid for one day";
        break;
      case "9":
        $validity = " valid for 90 days";
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
<link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
<link rel="manifest" href="../site.webmanifest">
<link rel="mask-icon" href="../safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@yeswap" />
<meta name="twitter:title" content="<?= $oName ?> Profile" />
<meta name="twitter:description" content="<?= $oName ?> plans, pricing, contact information, coverage map, date founded, network and bands used.">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta name="twitter:image" content="https://prepaidcompare.net/apple-icon.png" />

<meta property="fb:app_id" content="448013232393047"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= $oName ?> Profile" />
<meta property="og:description" content="<?= $oName ?> plans, pricing, contact information, coverage map, date founded, network and bands used.">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta property="og:url" content="https://prepaidcompare.net<?= $sPageName ?>" />
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
      "https://www.howardforums.com/member.php/262421-Yeswap",
      "https://plus.google.com/u/0/+DennisBournique"]
    },
  "description" : "Profile of US prepaid cellphone operator <?= $oName ?> with plans, pricing, contact information, coverage map, date founded, network and bands used.",
  "publisher": "PrepaidCompare"
}
]
</script>

<style>
/* =Reset default browser CSS. Based on work by Eric Meyer: http://meyerweb.com/eric/tools/css/reset/index.html
-------------------------------------------------------------- */

html, body, div, span, applet, object, iframe,
h1, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
  border: 0;
  font-family: inherit;
  font-size: 100%;
  font-style: inherit;
  font-weight: inherit;
  margin: 0;
  outline: 0;
  padding: 0;
  vertical-align: baseline;
}
  body,page {
    max-width:100%;
    font-family: sans-serif;
  }
  a, a:hover, a:active {
      color: #006000;
  }
  #branding{
    margin-bottom: 3pt;
  }
  img {
    max-width:98% !important;
    height:auto !important;
  }
  p {margin: 1em 0;}

  html{
    width:100%;
    background: -webkit-linear-gradient(top, rgba(38, 38, 38, 0.5) 0%,rgba(38, 38, 38, 0.5) 17%,rgba(38, 38, 38, 0) 100%);
    background: linear-gradient(to bottom, rgba(38, 38, 38, 0.5) 0%,rgba(38, 38, 38, 0.5) 17%,rgba(38, 38, 38, 0) 100%);
    background-color: #E0DED7;
    padding:0;
    margin:0;
    min-height: 100%;
  }
  #page {
    float:none;
    padding:0 .25em;
    margin: 1.5em auto;
    left:0;
    width:auto;
    max-width:690px;
    right:auto;
    overflow:hidden;
    background-color:white;
    box-shadow: 0 5px 18px rgba(0, 0, 0, 0.3);
    -moz-box-shadow: 0 5px 16px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0 5px 18px rgba(0, 0, 0, 0.3);
  }
  #contact{
    padding-top: .5em;
  }
  #footer{
    padding: 1em 0;
    margin-top: 1em;
    margin-right: 1em;
    border-top: 1px solid #007d15;
    display: table;
    width: 100%;
    table-layout: fixed;
  }
  #footer a {
      display: table-cell;
      text-align: center;
  }
  .separator {clear:both;}
  #site-title {
    border-bottom: 1px solid #007d15;
    text-align: center;
    padding:.7em 1em .7em .4em;
  }
  #site-title a {
    color: black;
  }
  #site-title h1 {
    display: inline-block;
  }
  #site-title h1 a {
    font-size: 38px;
    font-weight: bold;
    line-height: 28px;
    text-decoration: none;
  }
 
  a {
    text-decoration: none;
  }
  a:hover, a:active{text-decoration: underline;}
  #page-title a:hover, #page-title a:active{text-decoration: none;}

  #subtitle, #updated {
    margin: .5em 0;
    word-wrap: break-word;
    color:gray;
  }
  body{height:100%;}
  #news{
    padding:1em;
    margin:1.5em auto 0;
    max-width:640px;
    background-color:white;
  }
  /* =Menu
  -------------------------------------------------------------- */
  #menu-container{
   border-bottom: 1px solid #007d15;
   overflow: hidden;
  }
  #menu-container nav{
    height:auto;
    width:auto;
    float:left;
  }
  ul.menu li {
    display: inline;
    margin: 0;
  }
  
  ul.menu li a {
    padding-top: 5px;
    padding-right: 15px;
    padding-bottom: 5px;
    padding-left: 15px;
    margin-bottom : 0px;
    text-decoration: none;
    font-family: Verdana,Helvetica,Arial,Sans-Serif;
    font-style: normal;
    font-variant: normal;
    font-size: 80%;
    font-size-adjust: none;
    font-stretch: normal;
    font-weight: bold;
    line-height: normal;
    margin-top: 0pt;
    margin-right: 0pt;
    margin-bottom: 0pt;
    margin-left: 0pt;
    color: black;
    background-color: #4caf50;
    background-image: none;
    background-repeat: repeat;
    background-attachment: scroll;
  }
  
  
  ul.menu li a:hover {
    background-color: #007d15;
    background-image: none;
    background-repeat: repeat;
    background-attachment: scroll;
    color: white;
    text-decoration: none;
  }
  
  ul.menu li.current_page_item a, ul.menu li.current_page_item a:hover {
    color: white;
    background-color: #007d15;
    background-image: none;
    background-repeat: repeat;
    background-attachment: scroll;
  }
  #planlist{
    padding-left: 0;
    margin-left: 0;
    border-bottom: 1px solid gray;
  }
  
  #planlist li
  {
  list-style: none;
  margin: 0;
  padding: 0.25em;
  border-top: 1px solid gray;
  }
  
  #APN {
    box-shadow: 1px 1px 5px #555 inset;
    padding: .5em;
    margin: .5em;
    width: auto;
  }
    ul.menu {
    margin-top:2px;
  }
  #socialicons img{
    height:25px;
    width:auto;
  }
  #socialicons a{
    position: relative;
    top: -15%;
  }
  #socialicons {
    float: right;
  }
  @media (max-width: 640px) {
    #news, #page, html {margin:0}
  }
  @media screen and (max-width: 61em){
    .item {padding: 0 .5em;}
    ul.menu {padding-left:.3em;}
  }
  @media (max-width: 40em) {
    #site-title h1 a {
      font-size: 24px;
      line-height: 20px;
    }
    ul.menu li a {
      padding-right: 5px;
      padding-left: 5px;
    }
    #news {
      padding-top: .2em;
    }
    h2 {font-size: 1.2em;}
  }
  </style>
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
   <li class="current_page_item"><a href="/profiles/" title="Operator Profiles">Operators</a></li>
   <li class="page_item"><a href="/about.html">About</a></li>
   <li class="page_item"><a href="/privacy.html" title="Privacy Policy">Privacy</a></li>
   <li class="page_item"><a href="/help.html">Help</a></li>
 </ul>
</nav>
  <div id="socialicons">
    <a target="_blank" title="Twitter" rel="noopener" href="https://www.twitter.com/yeswap"><img alt="twitter" src="twit.png" border="0"></a>
    <a targt="_blank" title="Facebook" rel="noopener" href="https://www.facebook.com/ PrepaidCompare"><img alt="Facebook" src="fb.png" border="0"></a>
  </div>
  <div class="separator"></div>
</div>
 <div class="item">
<?php
      
      echo "<h2>".$oName." Profile</h2>\n";
      
      echo "<div><b>Founded:</b> ".strftime("%G",strtotime($row["Founded"]))."</div>";
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
        echo "<div><b>Roaming:</b> None";
      }
      if ($row["CoverageMap"]){
        echo "<div><b>CoverageMap:</b> ".formatURL($row["CoverageMap"])."</div>\n";
      }
      if ($row["oNotes"]){
        echo "<div><b>Notes:</b> ".$row["oNotes"]."</div>\n";
      }
      if($row["noMMS"]){
        echo "<div><b>MMS supported on iOS?</b> No.</div>";
      }else{
        echo "<div><b>MMS supported on iOS?</b> Yes.</div>";
      }
      echo "<div><b>Network:</b> ".$row["nName"]."</div>\n";
      echo "<div><b>Bands:</b> ".$row["Bands"]."</div>\n";
      echo "<div><b>Approximate Coverage:</b> ";
      echo $row["CoverageSqMiles"]." million square miles, ";
      echo $row["CoveragePOPs"]. " million people</div>\n";
      echo "<div>".$row["nNotes"]."</div>\n";

      echo "<h2>Plans</h2>\n";
      $sql = "select * from Plans where OperatorID =".$_GET["id"]." order by Cost";
      if (!$result = $connection->query($sql)) {
        die ('There was an error running the Plans query[' . $connection->error . ']');
      }
      
      $rows = $result->num_rows;
      echo "<ul id='planlist'>";
      while ($row = $result->fetch_array()) {
        echo "<li><b><i>".$row["Name"]."</i></b><br/>";
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
          echo "<b>Auto Pay Discount:</b> ".$row['AutopayDiscount'].".";
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
          $data = "None";
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
      echo '<b>Data: </b> '.$data;
      if ($row['Data'] != 0) {
        switch ($row['OverageThrottle']) {
         case -1:
           $throttle = " Hard capped";
           break;
         case 0:
           $throttle = "";
           break;
          default:
            $throttle = " at high speed, followed by unlimited data at ". $row['OverageThrottle'].' kbps';
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
        }
        if($AllowsHotspot > 1){
          $sHotspot .= " for $" . $AllowsHotspot . "/mo";
        }
        
      }else{
       $sHotspot = 'none';
      }
      echo "<b>Hotspot:</b> ".$sHotspot.".\n";
      $planID = $row["ID"];
      $sqlFP = "select NumLines, Price, AutoPayDiscount, DataShared, Notes from FamilyPlans where PlanID =".$planID."  order by NumLines";
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
          if ($rowFP["AutoPayDiscount"] != "0.00"){
            $sFP .= ", $".$rowFP["AutoPayDiscount"]. " autopay discount";
          }
          if ($rowFP["Notes"]){
            $sNotes = "<br><b>Family Plan Notes: </b>".$rowFP["Notes"];
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
          echo "<br/>\n" . "$". stripZeros($rowVA["Cost"]) . " for " . $rowVA["Minutes"]. " minutes ";
          echo formatValidity($rowVA["Validity"]);
      }
      if ($rowsTA) {
          echo "<br/>\n<b>Messaging Addons:</b>";
      }
      while ($rowTA = $resultTA->fetch_array()) {
        echo "<br/>\n" . "$". stripZeros($rowTA["Cost"]) . " for " . $rowTA["Texts"]. " messages ";
        echo formatValidity($rowTA["Validity"]);
      }
      if ($rowsDA) {
          echo "<br/>\n<b>Data Addons:</b>";
      }
      while ($rowDA = $resultDA->fetch_array()) {
        if($rowDA['Data'] > 1023){
          $data = $rowDA['Data']/1024 . ' GB';
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