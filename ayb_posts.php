<?php
/*
Plugin Name: A Year Before
Version: 0.6beta2
Plugin URI: http://wuerzblog.de/2006/12/27/wordpress-plugin-a-year-before/
Author: Ralf Thees
Author URI: http://wuerzblog.de/
Description: Gibt die Artikel an, die vor einem Jahr oder einer beliebigen Zeitspanne veröffentlicht wurden.
*/
function ayb_posts_init() {
	if ( !function_exists('register_sidebar_widget') )
	return;

	function ayb_posts($para=Array()) {
		if (preg_match("/sidebar/i",$para["name"])) $ayb_posts_is_widget=true;

		//print_r( $para);
		global $wpdb;
		if ($ayb_posts_is_widget) extract($para);

		$options = get_option("ayb_posts");
			if ( !is_array($options) ) {
				$options = array('title'=>'Vor exakt einem Jahr');
			}
		if ($ayb_posts_is_widget) {
			$para="";
			foreach ($options as $key => $val) {
				$para.="$key=$val&";
		}
		//echo $para;
		}

	$title=$options["title"];
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


	$q="SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_status='publish' AND post_password='' AND YEAR(post_date_gmt)=YEAR(NOW()- INTERVAL $dyear YEAR) AND MONTH(post_date_gmt)=MONTH(NOW()- INTERVAL $dmonth MONTH) AND DAYOFMONTH(post_date_gmt)=DAYOFMONTH(NOW()- INTERVAL $dday DAY) ORDER BY post_date_gmt";
	$q="SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_status='publish' AND post_password='' AND YEAR(post_date_gmt)=".$datum['year']." AND MONTH(post_date_gmt)=".$datum['mon']." AND DAYOFMONTH(post_date_gmt)=".$datum['mday']." ORDER BY post_date_gmt";
	//echo $q;
	$result = $wpdb->get_results($q, OBJECT);
	//print_r($result);

	//Ausgabe für's Widget
	if ($ayb_posts_is_widget) {
	echo $before_widget;
    echo $before_title . $title . $after_title."<ul>";
    }

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
			echo $before.$pdate.'<a href="'.$plink.'" class="ayb_link">'.$ptitle.'</a></span>'.$after."\r";
			}
		} else {

			echo $before.$pdate.'<span class="ayb_notfound">'.$notfound.'</span>'.$after."\r";
		}
	if($ayb_posts_is_widget) {
		echo "</ul>".$after_widget;
	}

	}

	function ayb_posts_widget_control() {
		$options = get_option("ayb_posts");
		if ( !is_array($options) ) {
			/*$options = array('title'=>'Vor einem Jahr');
			$options = array('day'=>0);
			$options = array('month'=>0);
			$options = array('year'=>1);
			$options = array('showdate'=>1);
			$options = array('dateformat'=>'d.m.Y');
			$options = array('notfound'=>'Kein Beitrag an diesem Tag');
			*/
			$options = array('title'=>'Vor einem Jahr', 'day'=>0, 'month'=>0, 'year'=>1, 'showdate'=>1, 'dateformat'=>'d.m.Y', 'notfound'=>'Kein Beitrag an diesem Tag');

		}
		$title=$options['title'];
		$day=$options['day'];
		$month=$options['month'];
		$year=$options['year'];
		$showdate=$options["showdate"];
		$dateformat=$options["dateformat"];
		$notfound=$options["notfound"];

		if ( $_POST['ayb_posts_submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['ayb_posts_title']));
			$options["day"]=strip_tags(stripslashes($_POST['ayb_posts_day']));
			$options["month"]=strip_tags(stripslashes($_POST['ayb_posts_month']));
			$options["year"]=strip_tags(stripslashes($_POST['ayb_posts_year']));
			$options["showdate"]=strip_tags(stripslashes($_POST['ayb_posts_showdate']));
			$options["dateformat"]=strip_tags(stripslashes($_POST['ayb_posts_dateformat']));
			$options["notfound"]=strip_tags(stripslashes($_POST['ayb_posts_notfound']));
			update_option('ayb_posts', $options);
		}

		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);

		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo '<p style="text-align:right;"><label for="ayb_posts_title">' . __('Title:') . ' <input style="width: 200px;" id="ayb_posts_title" name="ayb_posts_title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_day">' . __('Tage zuvor:') . ' <input style="width: 30px;" id="ayb_posts_day" name="ayb_posts_day" type="text" value="'.$day.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_month">' . __('Monate zuvor:') . ' <input style="width: 30px;" id="ayb_posts_month" name="ayb_posts_month" type="text" value="'.$month.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_year">' . __('Jahre zuvor:') . ' <input style="width: 30px;" id="ayb_posts_year" name="ayb_posts_year" type="text" value="'.$year.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_showdate">' . __('Zeige Datum an:') . ' <input style="width: 15px;" id="ayb_posts_showdate" name="ayb_posts_showdate" type="checkbox" value="1"'.(($showdate==0)?'':'checked').' /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_dateformat">' . __('Datumsformat:') . ' <input style="width: 30px;" id="ayb_posts_dateformat" name="ayb_posts_dateformat" type="text" value="'.$dateformat.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_notfound">' . __('Text wenn kein gefundener Beitrag:') . ' <input style="width: 200px;" id="ayb_posts_notfound" name="ayb_posts_notfound" type="text" value="'.$notfound.'" /></label></p>';
		echo '<input type="hidden" id="ayb_posts_submit" name="ayb_posts_submit" value="1" />';

	}

	register_widget_control("A Year Before","ayb_posts_widget_control",200,300);
	register_sidebar_widget('A Year Before','ayb_posts');
}
//
add_action('widgets_init', 'ayb_posts_init');
?>
