<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Add News Item</title>
  </head>
  <body>
    <form action="/tblUpdate.php">

    <?Php
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
      $mode="";
      $id="";
      $title="";
      $creator="";
      $permalink="";
      $date=date("Y-m-d H:i");
      $subject="";
      $content="";
    
    	$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');
    	
    	if ($connection->connect_errno > 0) {
     		die ('Unable to connect to database [' . $connection->connect_error . ']');
    	}
    	$sTable='NewsItems';
      if (isset($_GET["mode"]) && $_GET["mode"]=='edit'){
    	  $sql = "SELECT title,permalink,creator,subject,UNIX_TIMESTAMP(date) as date,content FROM $sTable";
    	  echo "\t".'<input type="hidden" name="id" value="'.$_GET["id"].'">'."\n";
      }else{
        if(isset($_GET["title"])) { $title = $_GET["title"];}
        if(isset($_GET["creator"])){ $creator = $_GET["creator"];}   	
        if(isset($_GET["permalink"])){ $permalink = $_GET["permalink"];}
        if(isset($_GET["date"])){ $date = $_GET["date"];}
        if(isset($_GET["subject"])){ $subject = $_GET["subject"];}
        if(isset($_GET["content"])){ $content = $_GET["content"];}
      }
?>
          <form action="/tblUpdate.php">
          <input type="hidden" name="id" value="<?=$id ?>">
          <input type="hidden" name="mode" value="<?=$_GET["mode"] ?>">
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
          <input style='width:100%' type="text" name="content" value="<?=$content ?>">
          <br><br>
        	<input type="hidden" name="mode" value="<?=$_GET["mode"] ?>">
          <input type="hidden" name="table" value="<?=$sTable ?>">
          
          <input type="submit" name="SubmitButn" value="Update"/>
          <input type="submit" name="SubmitButn" value="Delete"
            onclick="return confirm('Are you sure you want delete this record?')"/>        
      </form>
  </body>
</html>
