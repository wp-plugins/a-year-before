<?php
/*
Plugin Name: A Year Before
Version: 0.5.3
Plugin URI: http://wuerzblog.de/2006/12/27/wordpress-plugin-a-year-before/
Author: Ralf Thees
Author URI: http://wuerzblog.de/
Description: Gibt die Artikel an, die vor einem Jahr oder einer beliebigen Zeitspanne veröffentlicht wurden.
*/

function ayb_posts( $para='') {
  global $wpdb;
  $dday=0;
  $dmonth=0;
  $dyear=0;
  $before="<li>";
  $after="</li>";
  $showdate=1;
  $dateformat="d.m.y";
  $notfound="Keine Beitrag an diesem Tag.";
  $parameter = explode('&', $para);
  $i = 0;
  while ($i < count($parameter)) {
   $b = split('=', $parameter[$i]);
   switch ($b[0]) {
      case "day":
        $dday=urldecode($b[1]);
        break;
      case "month":
        $dmonth=urldecode($b[1]);
        break;
      case "year":
        $dyear=urldecode($b[1]);
        break;
      case "before":
        $before=urldecode($b[1]);
        break;
      case "after":
        $after=urldecode($b[1]);
        break;
      case "notfound":
        $notfound=htmlspecialchars(urldecode($b[1]));
        break;
      case "showdate":
        $showdate=urldecode($b[1]);
        break;
      case "dateformat":
        $dateformat=urldecode($b[1]);
        break;
   }

   $i++;
}
   if ($dday==0 && $dmonth==0 && $dyear==0) {
      $dyear=1;
  }

  $datum  = getdate(mktime(0, 0, 0, date("m")-$dmonth, date("d")-$dday, date("Y")-$dyear));


   $q="SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_status='publish' AND post_password='' AND YEAR(post_date_gmt)=".$datum['year']." AND MONTH(post_date_gmt)=".$datum['mon']." AND DAYOFMONTH(post_date_gmt)=".$datum['mday']." ORDER BY post_date_gmt";
  $result = $wpdb->get_results($q, OBJECT);
  if ($showdate) {
      $pdate='<span class="ayb_date">'.date($dateformat,mktime(0, 0, 0, date("m")-$dmonth  , date("d")-$dday, date("Y")-$dyear))."</span> ";
    } else {
      $pdate='';
    }
  if ($result) {
    foreach ($result as $post)
    	{
    		$plink = get_permalink($post->ID);
    		$ptitle= $post->post_title;
        echo $before.$pdate.'<a href="'.$plink.'" class="ayb_link">'.$ptitle.'</a>'.$after."\r";
    	}
    } else {

        echo $before.$pdate.'<span class="ayb_notfound">'.$notfound.'</span>'.$after."\r";
    }
}

?>
