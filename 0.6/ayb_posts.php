<?php
/*
Plugin Name: A Year Before
Version: 0.6
Plugin URI: http://wuerzblog.de/2006/12/27/wordpress-plugin-a-year-before/
Author: Ralf Thees
Author URI: http://wuerzblog.de/
Description: Gibt die Artikel an, die vor einem Jahr oder einer beliebigen Zeitspanne veröffentlicht wurden.
*/

$ayb_posts_domain = 'ayb_posts';
$ayb_install_dir=basename(dirname(__FILE__));
load_plugin_textdomain($ayb_posts_domain, "wp-content/plugins/$ayb_install_dir");

function ayb_posts_init() {
	if ( !function_exists('register_sidebar_widget') )
	return;
	
	function ayb_posts($para=Array()) {
		if (preg_match("/sidebar/i",$para["name"])) $ayb_posts_is_widget=true;

		global $wpdb, $ayb_posts_domain;
		
		
		if ($ayb_posts_is_widget) extract($para);

		$options = get_option("ayb_posts");
			if ( !is_array($options) ) {
				$options = array('title'=>__('A year ago',$ayb_posts_domain));
			}
		if ($ayb_posts_is_widget) {
			$para="";
			foreach ($options as $key => $val) {
				$para.="$key=$val&";
		}
		}

	$title=$options["title"];
	$dday=0;
	$dmonth=0;
	$dyear=0;
	$before="<li>";
	$after="</li>";
	$showdate=1;
	$dateformat=__('Y-m-d',$ayb_posts_domain);
	$notfound=__("No articles on this date.",$ayb_posts_domain);
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
		global $ayb_posts_domain;
		$options = get_option("ayb_posts");
		if ( !is_array($options) ) {
			$options = array('title'=>__('A year ago',$ayb_posts_domain), 'day'=>0, 'month'=>0, 'year'=>1, 'showdate'=>1, 'dateformat'=>__('Y-m-d',$ayb_posts_domain), 'notfound'=>__('No articles on this date.',$ayb_posts_domain));

		}
		$title=$options['title'];
		$day=$options['day'];
		$month=$options['month'];
		$year=$options['year'];
		$showdate=$options["showdate"];
		$dateformat=$options["dateformat"];
		$notfound=$options["notfound"];

		if ( $_POST['ayb_posts_submit'] ) {

			$options['title'] = strip_tags(stripslashes($_POST['ayb_posts_title']));
			$options["day"]=strip_tags(stripslashes($_POST['ayb_posts_day']));
			$options["month"]=strip_tags(stripslashes($_POST['ayb_posts_month']));
			$options["year"]=strip_tags(stripslashes($_POST['ayb_posts_year']));
			$options["showdate"]=strip_tags(stripslashes($_POST['ayb_posts_showdate']));
			$options["dateformat"]=strip_tags(stripslashes($_POST['ayb_posts_dateformat']));
			$options["notfound"]=strip_tags(stripslashes($_POST['ayb_posts_notfound']));
			update_option('ayb_posts', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);

		echo '<p style="text-align:right;"><label for="ayb_posts_title">' . __('Title:',$ayb_posts_domain) . ' <input style="width: 200px;" id="ayb_posts_title" name="ayb_posts_title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_day">' . __('Days before:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_day" name="ayb_posts_day" type="text" value="'.$day.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_month">' . __('Months before:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_month" name="ayb_posts_month" type="text" value="'.$month.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_year">' . __('Years before:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_year" name="ayb_posts_year" type="text" value="'.$year.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_showdate">' . __('Show date:',$ayb_posts_domain) . ' <input style="width: 15px;" id="ayb_posts_showdate" name="ayb_posts_showdate" type="checkbox" value="1"'.(($showdate==0)?'':'checked').' /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_dateformat">' . __('Dateformat:',$ayb_posts_domain) . ' <input style="width: 45px;" id="ayb_posts_dateformat" name="ayb_posts_dateformat" type="text" value="'.$dateformat.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_notfound">' . __('Text, if no article found:','ayb_posts') . ' <input style="width: 200px;" id="ayb_posts_notfound" name="ayb_posts_notfound" type="text" value="'.$notfound.'" /></label></p>';
		echo '<p style="text-align:right;"><input type="submit" id="ayb_posts_submit" name="ayb_posts_submit" value="'. __('Update',$ayb_posts_domain) . '" /></p>';

	}

	register_widget_control("A Year Before","ayb_posts_widget_control",200,320);
	register_sidebar_widget('A Year Before','ayb_posts');
}
//
add_action('widgets_init', 'ayb_posts_init');
?>
