<?php>
//Make a backup
#if ( function_exists("DebugBreak") ) {
#DebugBreak();
#}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set("error_log", "/usr/home/yeswap/php-error.log");
error_reporting(E_ALL & ~E_NOTICE);
$filename = "../deals/index.html";
$handle = fopen($filename, "r+");
$content = fread($handle, filesize($filename));
fclose($handle);

$bufname = "../deals/previous.html";
$handle = fopen($bufname, "w");
if(fwrite($handle, $content)=== FALSE) {
  echo "Cannot write to file ($bufname)";
  exit;
}
fclose($handle);
<html>
  <head>
    <title>Deals Page Updated</title>
</head>
  <h1>Deals Page Updated</h1>
</html>