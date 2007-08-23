=== Plugin Name ===
Contributors: wuerzblog
Donate link: http://wuerzblog.de/
Tags: date, post, history, sidebar
Requires at least: 2.0.0
Tested up to: 2.2.1
Stable tag: 0.5.3

»A Year Before« shows a list of articles, which were written a certain time ago. So you can show in a history, what happend in your blog in the past.

== Description ==

With »A Year Before« you can show the titles of the articles, which were written a certain time ago. So you can show in a »historical corner«, what happend in your blog e.g. 30 days, 6 months or a year before.

== Installation ==

1. Download the plugin and put the file ayb_posts.php in the plugin-folder of your  Wordpress-installation.
2. Then activate the plugin.
3. In your template — e.g. the sidebar — you can insert the following PHP-code:

        <?php if (function_exists("ayb_posts")) { ?>
        <div class="box">
            <h2>Posts a year ago</h2>
            <ul>
                <?php ayb_posts(); ?>
            </ul>
        </div>
        <?php } ?>

== Configuration ==

You can pass some parameters in this scheme
parameter1=value1&parameter2=value2&parameter3=value3 ...

You can use the following parameters

* day : the number of days ago you want to show the articles.
* month : the number of month ago you want to show the articles.
* year : the number of years ago you want to show the articles.
* before : piece of HTML to insert before the title of the articles. Default `<li>`
* after: piece of HTML to insert after the title of the articles. Default `</li>`
* showdate: shows the date (showdate=1) before every title or not (showdate=0)
* dateformat : dateformat as used by PHP. Default ist the german shortform »d.m.y«
* notfound: the text the plugin will output, if no article is found on the defined date.

== Examples ==
`ayb_posts("day=30&before=&after=<br />&showdate=0");`
Shows the titles of the articles written 30 days ago without showing the date. The articles will not been showed as a HTML-list but simply seperated by a linebreak `<br />`.

`ayb_posts("month=6&day=14&notfound=Nothing blogged on this day.");`
The titles of the articles written half a year and two weeks before, also showing the date . If there was no article written on that day, the output will be »Nothing blogged on this day.«

If you like CSS, you can style the date with the class `ayb_date`, the link of the article with the class `ayb_link` and the notfound-message by using the class `ayb_notfound`.

== Changelog ==

0.5.3

* XHTML-Bugfix (unnecessary span)
* Bugfix PHP 5 Error with empty function-parameter

0.5.2

* Bugfix for more tolerant date-values (e.g. day > 364). Thanks to AlohaDan for hinting and testing.

0.5.1

* Adjustment for MySQL-versions older than MySQL 4.1.1

0.5

* First public beta
