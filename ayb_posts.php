<?php
/*
Plugin Name: A Year Before
Version: 0.7beta1
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
	$ayb_posts_is_widget=false;
	register_widget_control("A Year Before","ayb_posts_widget_control",200,320);
	register_sidebar_widget('A Year Before','ayb_posts');
}
	
	function ayb_posts($ayb_para=Array()) {
	if ( is_array($ayb_para) && sizeof($ayb_para)>0)  $ayb_posts_is_widget=true;

		global $wpdb, $ayb_posts_domain;
		
		if ($ayb_posts_is_widget) {
			extract($ayb_para);

			$options = get_option("ayb_posts");
			if ( !is_array($options) ) {
				$options = array('title'=>__('A year ago',$ayb_posts_domain));
			}
			$ayb_para="";
			foreach ($options as $key => $val) {
				$ayb_para.="$key=$val&";
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
	$range=0;
	$ayb_parameter = explode('&', $ayb_para);
	$i = 0;
	while ($i < count($ayb_parameter)) {
	$b = split('=', $ayb_parameter[$i]);
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
		case "range":
			$range=urldecode($b[1]);
			break;
	}

	$i++;
	}
	if ($dday==0 && $dmonth==0 && $dyear==0) {
		$dyear=1;
	}

	$datum  = getdate(mktime(0, 0, 0, date("m")-$dmonth, date("d")-$dday, date("Y")-$dyear));
	$range_date1=date("Y-m-d",mktime(0, 0, 0, date("m")-$dmonth, date("d")-$dday, date("Y")-$dyear)).' 00:00:00';
	$range_date2=date("Y-m-d",mktime(0, 0, 0, date("m")-$dmonth, date("d")-$dday+$range, date("Y")-$dyear)).' 23:59:59';

	$q="SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_status='publish' AND post_password='' AND YEAR(post_date_gmt)=".$datum['year']." AND MONTH(post_date_gmt)=".$datum['mon']." AND DAYOFMONTH(post_date_gmt)=".$datum['mday']." ORDER BY post_date_gmt";
	$q="SELECT ID, post_title, post_date FROM $wpdb->posts WHERE post_status='publish' AND post_password='' AND (post_date >= '".$range_date1."' AND post_date <= '".$range_date2."') ORDER BY post_date DESC";	
echo $q;	
	$result = $wpdb->get_results($q, OBJECT);
	//print_r($result);

	//Ausgabe für's Widget
	if ($ayb_posts_is_widget) {
	echo $before_widget;
    echo $before_title . $title . $after_title."<ul>";
    }

	if ($showdate) {
		$post_date=$result[0]->post_date;
		$ts_post_date=mktime(0,0,0,substr($post_date,5,2),substr($post_date,8,2),substr($post_date,1,4));
		$pdate='<span class="ayb_date">'.date($dateformat,$ts_post_date)."</span> ";

	} else {
		$pdate='';
		}
	if ($result) {
	$post_date=$result[0]->post_date;
	$ts_post_date=mktime(0,0,0,substr($post_date,5,2),substr($post_date,8,2),substr($post_date,1,4));
	$ts_date_old=$ts_post_date;
		foreach ($result as $post)
			{
			$post_date=$post->post_date;
			$ts_post_date=mktime(0,0,0,substr($post_date,5,2),substr($post_date,8,2),substr($post_date,1,4));
			$pdate='<span class="ayb_date">'.date($dateformat,$ts_post_date)."</span> ";
			if ($ts_post_date !=$ts_date_old) {
				break;
				} else {
					$ts_date_old=$ts_post_date;
				}
			$plink = get_permalink($post->ID);
			$ptitle= $post->post_title;
			echo $before.$pdate.'<a href="'.$plink.'" class="ayb_link">'.$ptitle.'</a></span>'.$after."\r";
		
				
			}
		} else {
$pdate='<span class="ayb_date">'.date($dateformat,mktime(0, 0, 0, date("m")-$dmonth  , date("d")-$dday, date("Y")-$dyear))."</span> ";
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
			$options = array('title'=>__('A year ago',$ayb_posts_domain), 'day'=>0, 'month'=>0, 'year'=>1, 'showdate'=>1, 'dateformat'=>__('Y-m-d',$ayb_posts_domain), 'notfound'=>__('No articles on this date.',$ayb_posts_domain),'range'=>0);

		}
		$title=$options['title'];
		$day=$options['day'];
		$month=$options['month'];
		$year=$options['year'];
		$showdate=$options["showdate"];
		$dateformat=$options["dateformat"];
		$notfound=$options["notfound"];
		$range=$options["range"];

		if ( $_POST['ayb_posts_submit'] ) {

			$options['title'] = strip_tags(stripslashes($_POST['ayb_posts_title']));
			$options["day"]=strip_tags(stripslashes($_POST['ayb_posts_day']));
			$options["month"]=strip_tags(stripslashes($_POST['ayb_posts_month']));
			$options["year"]=strip_tags(stripslashes($_POST['ayb_posts_year']));
			$options["showdate"]=strip_tags(stripslashes($_POST['ayb_posts_showdate']));
			$options["dateformat"]=strip_tags(stripslashes($_POST['ayb_posts_dateformat']));
			$options["notfound"]=strip_tags(stripslashes($_POST['ayb_posts_notfound']));
			$options["range"]=strip_tags(stripslashes($_POST['ayb_posts_range']));
			update_option('ayb_posts', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);

		echo '<p style="text-align:right;"><label for="ayb_posts_title">' . __('Title:',$ayb_posts_domain) . ' <input style="width: 200px;" id="ayb_posts_title" name="ayb_posts_title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_day">' . __('Days before:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_day" name="ayb_posts_day" type="text" value="'.$day.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_month">' . __('Months before:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_month" name="ayb_posts_month" type="text" value="'.$month.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_year">' . __('Years before:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_year" name="ayb_posts_year" type="text" value="'.$year.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_range">' . __('Lookup-range:',$ayb_posts_domain) . ' <input style="width: 30px;" id="ayb_posts_year" name="ayb_posts_range" type="text" value="'.$range.'" /></label></p>';		
		echo '<p style="text-align:right;"><label for="ayb_posts_showdate">' . __('Show date:',$ayb_posts_domain) . ' <input style="width: 15px;" id="ayb_posts_showdate" name="ayb_posts_showdate" type="checkbox" value="1"'.(($showdate==0)?'':'checked').' /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_dateformat">' . __('Dateformat:',$ayb_posts_domain) . ' <input style="width: 45px;" id="ayb_posts_dateformat" name="ayb_posts_dateformat" type="text" value="'.$dateformat.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ayb_posts_notfound">' . __('Text, if no article found:','ayb_posts') . ' <input style="width: 200px;" id="ayb_posts_notfound" name="ayb_posts_notfound" type="text" value="'.$notfound.'" /></label></p>';
		echo '<p style="text-align:right;"><input type="submit" id="ayb_posts_submit" name="ayb_posts_submit" value="'. __('Update',$ayb_posts_domain) . '" /></p>';

	}

	
//
add_action('widgets_init', 'ayb_posts_init');
?>