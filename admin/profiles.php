<?Php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  ini_set("log_errors", 1);
  ini_set("error_log", "/usr/home/yeswap/public_html/prepaidcompare.net/admin/php-error.log");
  ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Prepaid Cellphone Operator Profiles</title>
<meta name="description" content="Profiles of all US prepaid cellphone operators with plans, pricing, contact information, coverage map, date founded, network and bands used.">
<meta name="keywords" content="prepaid cellular, plans, pricing, contact information, coverage map, date founded, network, bands" />
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
<meta name="twitter:title" content="Prepaid Cellphone Operator Profiles" />
<meta name="twitter:description" content="Profiles of all US prepaid cellphone operators with plans, pricing, contact information, coverage map, date founded, network and bands used.">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta name="twitter:image" content="https://prepaidcompare.net/apple-icon.png" />

<meta property="fb:app_id" content="448013232393047"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Prepaid Cellphone Operator Profiles" />
<meta property="og:description" content="Profiles of all US prepaid cellphone operators with plans, pricing, contact information, coverage map, date founded, network and bands used." />
<meta property="og:url" content="https://prepaidcompare.net/profiles/" />
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
  "url": "https://prepaidcompare.net/profiles/",
  "name": "Prepaid Cellphone Operator Profiles",
   "author": {
      "@type": "Person",
      "name": "Dennis Bournique",
       "sameAs" : [ "https://www.facebook.com/wapreview",
      "https://www.reddit.com/user/yeswap",
      "https://twitter.com/yeswap",
      "https://www.howardforums.com/member.php/262421-Yeswap",
      "https://plus.google.com/u/0/+DennisBournique"]
    },
  "description" : "Profiles of all US prepaid cellphone operators with plans, pricing, contact information, coverage map, date founded, network and bands used.",
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
  #header{padding-left: .5em;}
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
  #grid{
    display: grid;
    grid-template-columns: auto auto auto auto;
    padding: 1em;
    grid-gap: .5em;
  }
  #grid a{
    background-color: rgba(255, 255, 255, 0.8);
    border: 2px outset lightgreen;
    padding: .3em;
    display: flex;
    text-align: center;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }
  #site-title {
    border-bottom: 1px solid #007d15;
    text-align: center;
    padding:.7em 1em .7em .4em;
  }
  .separator {clear:both;}
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
    #grid{
      grid-template-columns: auto auto auto;
    }
  }
  @media (max-width: 30em) {
    #grid{
      grid-template-columns: auto auto;
    }
  }
  @media (max-width: 20em) {
    #grid{
      grid-template-columns: auto;
    }
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
  <h2>Prepaid Cellphone Operator Profiles</h2>
  <div id= "header">
    Click or tap the buttons below to view profiles of  US prepaid operators showing each operator's plans, pricing, contact information, coverage map, date founded, network, bands used and more.
  </div>

<?php
  function pageName($string){
    $string = strtolower($string);
    $string = preg_replace('/[^a-zA-Z0-9]/i','-',$string);
    $string = preg_replace("/(-){2,}/",'$1',$string).".html";
    return $string;
  }
  function makeProfile($id){
    //$_REQUEST['id'] = $id;
    //include("makeProfile.php");
    exec("wget --http-user=yeswap --http-password=august -O /dev/null  https://admin.prepaidcompare.net/makeProfile.php?id=$id");
  }
  
//Conect to db
  $connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
  if ($connection->connect_errno > 0) {
     die ('Unable to connect to database [' . $connection->connect_error . ']');
  }
  
//Get Operator

  $sql = "select OperatorID, Name, NameSuffix from Operators order by Name, NameSuffix";
  if (!$result = $connection->query($sql)) {
      die ('There was an error running the Operators query [' . $connection->error . ']');
  }
  
  $rows = $result->num_rows;
  
  if($rows > 0) {
    echo '<div id="grid">';
    while ($row = $result->fetch_array()) {
      $Name = $row['Name'];
      if($row["NameSuffix"]){
        $Name .= " " . $row["NameSuffix"];
      }
      $sPageName= pageName($Name);
      echo "<a href = '/profiles/$sPageName'>$Name</a>";
      makeProfile($row["OperatorID"]);
    }
    echo "</div>";
  } else {
        die ($sql.' returned no rows');
  }
 ?>
 </div> <!-- item -->
  <div id="footer">
    <a href="/">Home</a>
    <a href="/about.html">About</a>
    <a href="privacy.html">Privacy</a>
    <a href="help.html">Help</a>
  </div>
</div> <!-- main -->
</div> <!-- page -->
</body>
</html>
<?php
$site = ob_get_contents();
ob_end_clean();
$filename = "/usr/home/yeswap/public_html/prepaidcompare.net/profiles/index.html";
$sitefile = fopen($filename, "w");
fwrite($sitefile, $site);
fclose($sitefile);
?>