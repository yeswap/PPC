<?php
#if ( function_exists("DebugBreak") ) {
#DebugBreak();
#}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set("error_log", "/usr/home/yeswap/php-error.log");
error_reporting(E_ALL & ~E_NOTICE);
$filename = "../cm/cm2020.inc";
$handle = fopen($filename, "r+");
$content = fread($handle, filesize($filename));
fclose($handle);
//Make a backup
/*
$tmpfname = tempnam("../cm", "cm");
$handle = fopen($tmpfname, "w");
if(fwrite($handle, $content)=== FALSE) {
  echo "Cannot write to file ($tmpfname)";
  exit;
}
fclose($handle);
*/
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      #main {
        max-width: 800px;
        margin: 0 auto;
        background: white;
      }
      html {
        background: gray;
      }
      #content{
        height: 90vh;
      }
      #save{
        margin: 1em 10% 1em 90%;
        background: gray;
        color: white;
      }
    </style>  
    <script src="https://cdn.tiny.cloud/1/uh3kdg7fxrs8y8nppepc7wh0lanndqlwy9ji0pk7bf7kw7gd/tinymce/5/tinymce.min.js" referrerpolicy="origin">
    </script>
    <script>
      tinymce.init({
        selector: 'textarea#content',
        mobile: {
          menubar: true
        },
        toolbar_mode: 'floating',
        plugins: 'code lists charmap preview hr wordcount searchreplace image link media table tc help',
        menubar: 'file edit view insert format tools table tc help',
        toolbar: 'undo redo | image media pageembed link anchor | bold italic underline strikethrough | fontselect fontsizeselect formatselect | code charmap | numlist bullist checklist | forecolor backcolor removeformat | pagebreak | emoticons | fullscreen'
      });
    </script>
  </head>
  <body>
  <div id="main">
<?php 
if (!empty($_POST)){
  $handle = fopen($filename, "w");
  if(fwrite($handle, $_POST["content"])=== FALSE) {
    echo "Cannot write to file ($tmpfname)";
    exit;
  }else{ ?>
    <h1>File Updated</h1>
    <p><a class="button" href="http://prepaidcompare.net/cm/update.php">Publish</a></p>
    <?php  }
 fclose($handle);
}else{ ?>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <textarea id="content" name='content'  rows='50'>
      <?=$content ?>
    </textarea>
    <input id="save" type="submit" value="Save">
  </form>
<?php } ?>
  <div>
  </body>
</html>