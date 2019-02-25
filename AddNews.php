<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <style type="text/css">
    	html{
    	  width:100%;
    	  padding:0;
    	  margin:0;
    	}
    	body{
    	  height:100%;
    	  padding:1em;
    	  background-color:white;
    	}
    	textarea{
    	  width:100%;
      }
    </style>
    <title>Add News Item</title>
  </head>
  <body>
    <form action="/tblUpdate.php">

    <?Php
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
      require_once ("../../html2text/html2text.php");
      require_once ("Readability.inc.php");
      require_once('PPClibrary.php');
      $mode="add";
      $id="";
      $title="";
      $creator="";
      $permalink="";
      date_default_timezone_set('America/Los_Angeles');
      $date=date("Y-m-d H:i");
      $subject="";
      $content="";
      
      function url_get_contents ($Url) {
          if (!function_exists('curl_init')){
              die('CURL is not installed!');
          }
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $Url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $output = curl_exec($ch);
          curl_close($ch);
          return $output;
      }
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	
    	if ($connection->connect_errno > 0) {
     		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}
    	$sTable='NewsItems';
      if (isset($_GET["mode"]) && $_GET["mode"]=='edit'){
    	  $sql = "SELECT title,permalink,creator,subject,UNIX_TIMESTAMP(date) as date,content FROM $sTable";
    	  // Add code to fill vars from SQL query results
      }
      if(isset($_GET["id"])) { $id = $_GET["id"];}
      if(isset($_GET["mode"])) { $mode = $_GET["mode"];}
      if(isset($_GET["title"])) { $title = $_GET["title"];}
      if(isset($_GET["creator"])){ $creator = $_GET["creator"];}
      if(isset($_GET["permalink"])){
        $permalink = $_GET["permalink"];
        $html = url_get_contents ($permalink);
        $Readability = new Readability($html); // default charset is utf-8
      }
      if(isset($_GET["date"])){ $date = $_GET["date"];}
      if(isset($_GET["subject"])){ $subject = $_GET["subject"];}
      if(isset($_GET["content"])){
        $content = convert_html_to_text($_GET["content"]);
      }elseif((isset($_GET["permalink"]))){
        try {
            $aryContent = $Readability->getContent(); // throws an exception when no suitable content is found
        }catch (Exception $e) {
          echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        $content=myTruncate(convert_html_to_text($aryContent['content']), 500);
      }
?>
          <input type="hidden" name="id" value="<?=$id ?>">
          <input type="hidden" name="mode" value="<?=$mode ?>">
          Site Name:<br>
          <input style='width:100%' type="text" name="title" autofocus="autofocus" value="<?=$title ?>">
          <br><br>
          URL:<br>
          <input style='width:100%' type="text" name="permalink" value="<?=$permalink ?>">
          <br><br>
          Author:<br>
          <input style='width:100%' type="text" name="creator" value="<?=$creator ?>">
          <br><br>
          Item Title:<br>
          <input style='width:100%' type="text" name="subject" value="<?=$subject ?>">
          <br><br>
          Date Posted:<br>
          <input type="text" name="date" value="<?=$date ?>">
          <br><br>
          Exerpt:<br>
          <textarea name='content' rows='15'><?=$content ?></textarea>
          <br><br>
          <input type="hidden" name="table" value="<?=$sTable ?>">
          
          <input type="submit" name="SubmitButn" value="Update"/>
          <input type="submit" name="SubmitButn" value="Delete"
            onclick="return confirm('Are you sure you want delete this record?')"/>
      </form>

      <?php
      if((isset($_GET["permalink"]))){
        $tags = $Readability->getMetaTags();
        foreach ($tags as $key => $value) {
          echo $key. ": <textarea style='width:100%' rows='10'>$value</textarea><br>" ;
        }
      }
        //print_r ($tags);
      ?>
  </body>
</html>
