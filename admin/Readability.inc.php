<?php
// vim: set et sw=4 ts=4 sts=4 ft=php fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * PHP Readability
 *
 * Readability PHP 
 *      http://code.google.com/p/arc90labs-readability/
 *
 * ChangeLog:
 *      [+] 2014-02-08 Add lead image param and improved get title function.
 *      [+] 2013-12-04 Better error handling and junk tag removal.
 *      [+] 2011-02-17 Initial version
 *
 * @date   2013-12-04
 * 
 * @author mingcheng<i.feelinglucky#gmail.com>
 * @link   http://www.gracecode.com/
 * 
 * @author Tuxion <team#tuxion.nl>
 * @link   http://tuxion.nl/
 */

define("READABILITY_VERSION", 0.21);

class Readability {
    // The name of the tag to save the result of the decision
    const ATTR_CONTENT_SCORE = "contentScore";

    // DOM The parsing class currently only supports UTF-8 encoding
    const DOM_DEFAULT_CHARSET = "utf-8";

    // What is displayed when parsing fails
    const MESSAGE_CAN_NOT_GET = "Readability was unable to parse this page for content.";

    // DOM Parsing class (built in to PHP5)
    protected $DOM = null;

    // Source code that needs to be parsed
    protected $source = "";

    // List of parent elements
    private $parentNodes = array();

    // Tags to be deleted
    // Note: added extra tags from https://github.com/ridcully
    private $junkTags = Array("style", "form", "iframe", "script", "button", "input", "textarea", 
                                "noscript", "select", "option", "object", "applet", "basefont",
                                "bgsound", "blink", "canvas", "command", "menu", "nav", "datalist",
                                "embed", "frame", "frameset", "keygen", "label", "marquee", "link");

    // Attributes to be deleted
    private $junkAttrs = Array("style", "class", "onclick", "onmouseover", "align", "border", "margin");


    /**
     * Constructor
     *      @param $input_char The encoding of the string. Default utf-8, can be omitted
     */
    function __construct($source, $input_char = "utf-8") {
        $this->source = $source;

        // DOM Parsing classes can only handle UTF-8
        $source = mb_convert_encoding($source, 'HTML-ENTITIES', $input_char);

        // Preprocess HTML tags, eliminate redundant tags, etc.
        $source = $this->preparSource($source);

        // Generate a DOM parsing class
        $this->DOM = new DOMDocument('1.0', $input_char);
        try {
            //libxml_use_internal_errors(true);
            // There will be some error messages, that can be ignored. :^)
            if (!@$this->DOM->loadHTML('<?xml encoding="'.Readability::DOM_DEFAULT_CHARSET.'">'.$source)) {
                throw new Exception("Parse HTML Error!");
            }

            foreach ($this->DOM->childNodes as $item) {
                if ($item->nodeType == XML_PI_NODE) {
                    $this->DOM->removeChild($item); // remove hack
                }
            }

            // insert proper
            $this->DOM->encoding = Readability::DOM_DEFAULT_CHARSET;
        } catch (Exception $e) {
            // ...
        }
    }


    /**
     * Preprocess HTML tags so they can be accurately processed by DOM parsing classes
     *
     * @return String
     */
    private function preparSource($string) {
        // Eliminate redundant HTML-encoded tags to avoid parsing errors
        preg_match("/charset=([\w|\-]+);?/", $string, $match);
        if (isset($match[1])) {
            $string = preg_replace("/charset=([\w|\-]+);?/", "", $string, 1);
        }

        // Replace all doubled-up <BR> tags with <P> tags, and remove fonts.
        $string = preg_replace("/<br\/?>[ \r\n\s]*<br\/?>/i", "</p><p>", $string);
        $string = preg_replace("/<\/?font[^>]*>/i", "", $string);

        // @see https://github.com/feelinglucky/php-readability/issues/7
        //   - from http://stackoverflow.com/questions/7130867/remove-script-tag-from-html-content
        $string = preg_replace("#<script(.*?)>(.*?)</script>#is", "", $string);

        return trim($string);
    }


    /**
     * Delete all $TagName tags in the DOM element
     *
     * @return DOMDocument
     */
    private function removeJunkTag($RootNode, $TagName) {
        
        $Tags = $RootNode->getElementsByTagName($TagName);
        
        //Note: always index 0, because removing a tag removes it from the results as well.
        while($Tag = $Tags->item(0)){
            $parentNode = $Tag->parentNode;
            $parentNode->removeChild($Tag);
        }
        
        return $RootNode;
        
    }

    /**
     * Delete all unneeded attributes in an element
     */
    private function removeJunkAttr($RootNode, $Attr) {
        $Tags = $RootNode->getElementsByTagName("*");

        $i = 0;
        while($Tag = $Tags->item($i++)) {
            $Tag->removeAttribute($Attr);
        }

        return $RootNode;
    }

    /**
     * A box model that captures the main content of the page based on the score
     * The decision algorithm comes from：http://code.google.com/p/arc90labs-readability/
     *
     * @return DOMNode
     */
    private function getTopBox() {
        // Get all the paragraphs on the page
        $allParagraphs = $this->DOM->getElementsByTagName("p");

        // Study all the paragraphs and find the chunk that has the best score.
        // A score is determined by things like: Number of <p>'s, commas, special classes, etc.
        $i = 0;
        while($paragraph = $allParagraphs->item($i++)) {
            $parentNode   = $paragraph->parentNode;
            $contentScore = intval($parentNode->getAttribute(Readability::ATTR_CONTENT_SCORE));
            $className    = $parentNode->getAttribute("class");
            $id           = $parentNode->getAttribute("id");

            // Look for a special classname
            if (preg_match("/(comment|meta|footer|footnote)/i", $className)) {
                $contentScore -= 50;
            } else if(preg_match(
                "/((^|\\s)(section|post|hentry|entry[-]?(content|text|body)?|article[-]?(content|text|body)?)(\\s|$))/i",
                $className)) {
                $contentScore += 25;
            }

            // Look for a special ID
            if (preg_match("/(comment|meta|footer|footnote)/i", $id)) {
                $contentScore -= 50;
            } else if (preg_match(
                "/^(post|hentry|entry[-]?(content|text|body)?|article[-]?(content|text|body)?)$/i",
                $id)) {
                $contentScore += 25;
            }

            // Add a point for the paragraph found
            // Add points for any commas within this paragraph
            if (strlen($paragraph->nodeValue) > 10) {
                $contentScore += strlen($paragraph->nodeValue);
            }

            // Save the judgment score of the parent element
            $parentNode->setAttribute(Readability::ATTR_CONTENT_SCORE, $contentScore);

            // Save the parent element of the paragraph for quick access next time
            array_push($this->parentNodes, $parentNode);
        }

        $topBox = null;

        // Assignment from index for performance. 
        //     See http://www.peachpit.com/articles/article.aspx?p=31567&seqNum=5 
        for ($i = 0, $len = sizeof($this->parentNodes); $i < $len; $i++) {
            $parentNode      = $this->parentNodes[$i];
            $contentScore    = intval($parentNode->getAttribute(Readability::ATTR_CONTENT_SCORE));
            $orgContentScore = intval($topBox ? $topBox->getAttribute(Readability::ATTR_CONTENT_SCORE) : 0);

            // by raywill, 2016-9-2
            // for case: <div><p>xxx</p></div><div><p>yyy</p></div>
            if ($parentNode && $topBox && $topBox->parentNode
              && $parentNode !== $topBox
              && $parentNode->parentNode === $topBox->parentNode
              && $this->scoreMatch($parentNode, $topBox)) { // trust same level

              $topScore = intval($topBox->getAttribute(Readability::ATTR_CONTENT_SCORE));
              $topBox = $topBox->parentNode;
              $topBox->setAttribute(Readability::ATTR_CONTENT_SCORE, $topScore + $contentScore);
            } else if ($contentScore && $contentScore > $orgContentScore) {

              $topBox = $parentNode;
            }
        }

        // At this point, $topBox should be the main element of the page content that has been determined.
        return $topBox;
    }

    protected function scoreMatch($n1, $n2) {
      $n1Score = intval($n1->getAttribute(Readability::ATTR_CONTENT_SCORE));
      $n2Score = intval($n2->getAttribute(Readability::ATTR_CONTENT_SCORE));
      return ($n1Score > 0 && $n2Score > 0);
    }

    /**
     * Get the HTML page title
     *
     * @return String
     */
    public function getTitle() {
        $split_point = ' - ';
        $titleNodes = $this->DOM->getElementsByTagName("title");

        if ($titleNodes->length 
            && $titleNode = $titleNodes->item(0)) {
            // @see http://stackoverflow.com/questions/717328/how-to-explode-string-right-to-left
            $title  = trim($titleNode->nodeValue);
            $result = array_map('strrev', explode($split_point, strrev($title)));
            return sizeof($result) > 1 ? array_pop($result) : $title;
        }

        return null;
    }


    /**
     * Get Leading Image Url
     *
     * @return String
     */
    public function getLeadImageUrl($node) {
        $images = $node->getElementsByTagName("img");

        if ($images->length){
			$i = 0;
			while($leadImage = $images->item($i++)) {
				$imgsrc = $leadImage->getAttribute("src");
				$imgdatasrc = $leadImage->getAttribute("data-src");
				$imgsrclast =  $imgsrc ? $imgsrc : $imgdatasrc;
				list($img['width'],$img['height'])=getimagesize($imgsrclast);
				if($img['width'] > 150 && $img['height'] >150){
					return $imgsrclast;
				}
				
			}
		}

        return null;
    }


    /**
     * Get the main content of the page
     *
     * @return Array
     */
    public function getContent() {
        if (!$this->DOM) return false;

        // Get page title
        $ContentTitle = $this->getTitle();

        // Get the main content of the page
        $ContentBox = $this->getTopBox();
        
        //Check if we found a suitable top-box.
        if($ContentBox === null)
            throw new RuntimeException(Readability::MESSAGE_CAN_NOT_GET);
        
        // Copy content to a new DOMDocument
        $Target = new DOMDocument;
        $Target->appendChild($Target->importNode($ContentBox, true));

        // Delete unwanted tags
        foreach ($this->junkTags as $tag) {
            $Target = $this->removeJunkTag($Target, $tag);
        }

        // Delete unwanted attributes
        foreach ($this->junkAttrs as $attr) {
            $Target = $this->removeJunkAttr($Target, $attr);
        }

        $content = mb_convert_encoding($Target->saveHTML(), Readability::DOM_DEFAULT_CHARSET, "HTML-ENTITIES");

        // Multiple data, returned as an array
        return Array(
            'lead_image_url' => $this->getLeadImageUrl($Target),
            'word_count' => mb_strlen(strip_tags($content), Readability::DOM_DEFAULT_CHARSET),
            'title' => $ContentTitle ? $ContentTitle : null,
            'content' => $content
        );
    }
    
    public function getMetaTags(){
      $tags = array();
      foreach($this->DOM->getElementsByTagName('meta') as $metaTag) {
        if($metaTag->getAttribute('name') != "") {
          $tags[$metaTag->getAttribute('name')] = $metaTag->getAttribute('content');
        }
        elseif ($metaTag->getAttribute('property') != "") {
          $tags[$metaTag->getAttribute('property')] = $metaTag->getAttribute('content');
        }
      }
      return $tags;
  }

    function __destruct() { }
}

