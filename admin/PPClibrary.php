<?php
function myTruncate($string, $limit, $break=" ", $pad="...")
{
  // return with no change if string is shorter than $limit
  $string = strip_tags($string);
  //remove tabs, newlines and multiple spaces
  $string=trim(preg_replace('/[\s\t\n\r\s]+/', ' ', $string));
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}
?>