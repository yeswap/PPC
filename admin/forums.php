<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set("display_errors", 1); # 0 - production, 1 - development
// Make sure SimplePie is included. You may need to change this to match the location of simplepie.inc.
require_once('../../simplepie/autoloader.php');
date_default_timezone_set('America/Los_Angeles');

// We'll process this feed with all of the default options.
$feed = new SimplePie();

// Set which feed to process.
$feed->set_feed_url(array(
	'http://www.howardforums.com/external.php?type=RSS1&forumids=518&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=234&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=347&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=726&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=527&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=364&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=335&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=249&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=330&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=343&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=336&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=344&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=551&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=711&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=712&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=576&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=714&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=721&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=702&lastpost=true',
	'http://www.howardforums.com/external.php?type=RSS1&forumids=739&lastpost=true',
	'https://www.reddit.com/r/NoContract/.rss',
	'https://www.reddit.com/r/TracFone/.rss',
	'https://www.reddit.com/r/StraightTalk/.rss',
	'https://www.reddit.com/r/freedompop/.rss',
	'https://www.reddit.com/r/CricketWireless/.rss',
	'https://www.reddit.com/r/MetroPCS/.rss',
	'https://www.reddit.com/r/ProjectFi/.rss',
	'http://micallen.freeforums.org/feed.php'
));

# $feed->set_cache_duration (600); //The cache duration
$feed->set_useragent('Mozilla/5.0 (X11; CrOS armv7l 10575.58.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');

// Run SimplePie.
$feed->init();

// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type();


// Let's begin our XHTML webpage code.  The DOCTYPE is supposed to be the very first thing, so we'll keep it on the same line as the closing-PHP tag.
?><!doctype html>

<html lang="en">
<head>
	<title>Forum Feeds</title>
	<meta charset="utf-8">
	<meta name = "viewport" content = "width = device-width, initial-scale=1"/>
	<style type="text/css">
	*{font-family:sans-serif;}
	img {
		max-width:98% !important;
        	height:auto !important;
	}
	hr.bar {
		color: blue;
		background-color: blue;
		height: 1px;
	}
	html{
	  width:100%;
	  background-color: gray;
	  padding:0;
	  margin:0;
	}
	p, div, a {
	  overflow-wrap: break-word;
	}
	#masthead{
	  text-align: center;
	  margin-bottom:1em;
	}
	#page-title {
    margin: .5em 0 0;
    font-size: 38px;
	}
	a {
    text-decoration: none;
  }
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
	@media (max-width: 640px) {
	  #news, html {margin:0};
	}
	</style>
</head>
<body>
<div id="news">
  <div id="masthead">

<?php

echo "<p id='updated'>Retrieved " . date('j-M-Y') . " at " . date('g:i a'). "</p>\n</div>\n";
echo "<hr class='bar'>\n";

// Create a new array to hold data in
$new = array();

// Loop through all of the items in the feed
foreach ($feed->get_items() as $item) {

	// Calculate 24 hours ago
	$yesterday = time() - (24*60*60);

	// Compare the timestamp of the feed item with 24 hours ago.
	//if ($item->get_date('U') > $yesterday) {

		// If the item was posted within the last 24 hours, store the item in our array we set up.
		$new[] = $item;
	//}
}

// Loop through all of the items in the new array and display whatever we want.
	foreach($new as $item) {
	  	$creator = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'creator');
			$subject = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'subject');
			$feedTitle = html_entity_decode($item->get_feed()->get_title());
			$permalink = $item->get_permalink();
			$itemTitle = html_entity_decode($item->get_title());
			$content = html_entity_decode($item->get_content());

?>
		<div class="item">
			<b><?php echo $feedTitle; ?> - <a href="<?php echo $permalink; ?>"><?php echo $itemTitle; ?></a></b>

			<p><small>Posted by <?php echo $creator[0]['data']; ?> in <?php echo $subject[0]['data']; ?> on <?php echo $item->get_date('j F Y | g:i a'); ?> </small></p>
		</div>
			<p><?php echo $item->get_content(); ?></p>

<?php
			$feedTitle = rawurlencode($feedTitle);
			$permalink = rawurlencode($permalink);
			$author = rawurlencode($creator[0]['data']);
			$itemTitle = rawurlencode($itemTitle);
			$content = rawurlencode($content);
?>
			
 		
 		<p><a href="https://admin.prepaidcompare.net/AddNews.php?mode=add&amp;table=NewsItems&amp;title=<?= $feedTitle ?>&amp;permalink=<?= $permalink ?>&amp;creator=<?= $author ?>&amp;subject=<?= $itemTitle ?>&amp;date= <?= $item->get_date('Y-m-d H:i') ?>&amp;content=<?= $content ?>">Add to News</a></a></p>
 		
 		<hr class="bar"/>
<?php } ?>
</div>
</body>
</html>
