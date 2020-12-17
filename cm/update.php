<?php
#if ( function_exists("DebugBreak") ) {
#DebugBreak();
#}
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set("error_log", "/usr/home/yeswap/php-error.log");
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
?>
<!doctype html>
<!--[if IE 6]>
<html id="ie6" lang="en-US">
<![endif]-->
<!--[if IE 7]>
<html id="ie7" lang="en-US">
<![endif]-->
<!--[if IE 8]>
<html id="ie8" lang="en-US">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="2020 Black Friday prepaid and unlocked phone deals">
<meta name="author" content="PrepaidCompare.net">
<title>Black Friday Deal Tracker</title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="alternate" media="handheld" href="https://prepaidcompare.net/" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@yeswap" />
<meta name="twitter:title" content="Black Friday Deal Tracker" />
<meta name="twitter:description" content="All the Black Friday prepaid and unlocked phone deals in one place"/>
<meta name="twitter:image" content="https://prepaidcompare.net/apple-icon.png" />
<meta name="twitter:creator" content="@yeswap" />

<meta property="fb:app_id" content="448013232393047"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Black Friday Deal Tracker" />
<meta property="og:description" content="All the Black Friday prepaid and unlocked phone deals in one place" />
<meta property="og:url" content="https://prepaidcompare.net/bf" />
<meta property="og:site_name" content="PrepaidCompare" />
<meta property="og:image" content="https://prepaidcompare.net/ms-icon-310x310.png" />

<script type="application/ld+json">
[
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "https://prepaidcompare.net/bf",
  "logo": "https://prepaidcompare.net/logo.png",
  "ethicsPolicy" : "Prepaidcompare.net is non-commercial and strives to be impartial. We don't accept payment from mobile operators or vendors and list deals based solely on value.",
  "potentialAction": "Find all the Black Friday prepaid and unlocked phone deals in one place",
  "name" : "PrepaidCompare"
},
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "https://prepaidcompare.net/bf/",
  "name": "Black Friday Deal Tracker",
   "author": {
      "@type": "Person",
      "name": "Dennis Bournique",
       "sameAs" : [ "https://www.facebook.com/wapreview",
      "https://www.reddit.com/user/yeswap",
      "https://twitter.com/yeswap",
      "https://www.howardforums.com/member.php/262421-Yeswap"]
    },
  "description" : "A list of all the Black Friday prepaid and unlocked phone deals I’ve discovered to date. It will be updated when new deals surface.",
  "publisher": "PrepaidCompare"
}
]
</script>

<link rel="stylesheet" href="/page.css">
</head>
<body>
<div id="page">
<div id="main">
<div id="branding" role="banner">
<div id="site-title">
  <h1><a href="http://prepaidcompare.net/" title="Prepaid Compare" rel="home">prepaid<span style="color:#00BB00">compare</span></a></h1>
</div>
<div class="separator"></div>
</div><!-- #branding -->
<div id="menu-container">
  <nav>
    <ul class="menu">
      <li class="page_item"><a href="/">Home</a></li>
      <li class="current_page_item"><a href="/bf" title="BlackFriday">BlackFriday</a></li>
      <li class="page_item"><a href="/profiles/" title="Operator Profiles">Operators</a></li>
      <li class="page_item hidable"><a href="/about.html" title="About">About</a></li>
      <li class="page_item"><a href="/privacy.html" title="Privacy">Privacy</a>
      <li class="page_item hidable"><a href="/help.html" title="help">Help</a></li>
    </ul>
  </nav>
  <div id="socialicons">
    <a target="_blank" title="Twitter" rel="noopener" href="https://www.twitter.com/yeswap"><img alt="twitter" src="/twit.png" border="0"></a>
    <a targt="_blank" title="Facebook" rel="noopener" href="https://www.facebook.com/ PrepaidCompare"><img alt="Facebook" src="/fb.png" border="0"></a>
  </div>
  <div class="separator"></div>
</div>
<div id="content">
  <div class="item">
    <!--Item goes here-->
    <?Php
      include "bf2020.inc";
    ?>
</div> <!-- item -->
</div> <!-- content -->
  <div id="footer">
    <a href="/">Home</a>
    <a href="about.html">About</a>
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
$sitefile = fopen("index.html", "w");
fwrite($sitefile, $site);
fclose($sitefile);
?>