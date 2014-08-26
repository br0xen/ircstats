<?php

function getDateString($time) {
  // This function formats the date string appropriately
  // e.g. - If $time is today, don't show the date
  $start_today = strtotime(date('Y-m-d').' 00:00:00');
  $end_today = strtotime(date('Y-m-d').' 23:59:59');
  if($time < $start_today || $time > $end_today) {
    return date('Y-m-d H:i:s', $time);
  } else {
    return date('H:i:s', $time);
  }
}

function convert_ascii($string) {
  // Replace Single Curly Quotes
  $search[]  = chr(226).chr(128).chr(152);
  $replace[] = "'";
  $search[]  = chr(226).chr(128).chr(153);
  $replace[] = "'";
  // Replace Smart Double Curly Quotes
  $search[]  = chr(226).chr(128).chr(156);
  $replace[] = '"';
  $search[]  = chr(226).chr(128).chr(157);
  $replace[] = '"';
  // Replace En Dash
  $search[]  = chr(226).chr(128).chr(147);
  $replace[] = '--';
  // Replace Em Dash
  $search[]  = chr(226).chr(128).chr(148);
  $replace[] = '---';
  // Replace Bullet
  $search[]  = chr(226).chr(128).chr(162);
  $replace[] = '*';
  // Replace Middle Dot
  $search[]  = chr(194).chr(183);
  $replace[] = '*';
  // Replace Ellipsis with three consecutive dots
  $search[]  = chr(226).chr(128).chr(166);
  $replace[] = '...';
  // Apply Replacements
  $string = str_replace($search, $replace, $string);
  // Remove any non-ASCII Characters
  $string = preg_replace("/[^\x01-\x7F]/","", $string);
  return $string;
}

function extract_links($str) {
  $match = array();
  preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $str, $match);
  return $match;
}

function linkify($str, $target="") {
  $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
  return preg_replace($pattern, "<a href=\"$1\" ".(!empty($target)?"target=\"".$target."\"":"")." rel=\"nofollow\">$1</a>", $str);
}

function removeLinks($str) {
  $pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
  return preg_replace($pattern, "", $str);
}

function sanitize($str) {
  return str_replace(array('<','>'), array('&lt;','&gt;'), $str);
}

?>