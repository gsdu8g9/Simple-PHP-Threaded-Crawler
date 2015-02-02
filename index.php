<?php
/*
 * SIMPLE PHP THREADED WEB CRAWLER
 * 
 * Requirements:
 * PHP 5+
 * pthreads: http://php.net/manual/en/book.pthreads.php
 * simple HTML DOM parser: http://simplehtmldom.sourceforge.net/
 * 
 * Using php  
 * @author Ivan Vulovic <vulovic@gmail.com>
 * @version alfa
 * 
 * Special thanks to designer of threads for PHP
 * Joe Watkins (krakjoe)
 * https://github.com/krakjoe
 * 
 * and designers of simple_html_dom libary
 * http://simplehtmldom.sourceforge.net/
 * 
 */
include('./simple_html_dom.php'); // "Simple HTML DOM Parser"
include('./thread.php');
include('./link.php');
include('./fullurl.php'); // for making nice absolute url

set_time_limit(0);

// Website to crawl
$url="http://www.example.com";
/* 
 * $trigger = every page needs to contain this part of code in url
 * it prevents crawler to check links outside website
*/
$trigger="example.com";


/*
 * crawling function
 */
function crawl_site($website_url,$trigger)
{	

	$link = new Link();
	
	if(!strpos($website_url,$trigger)) return false;

	$html = file_get_html($website_url);
	
	// from now we can process data from current webpage ($html is simple_html_dom object containing html)
	// in this case, we wil get all links from current page and put them in db
	
	foreach($html->find("a") as $linkTag)
	{
		$url=trim($linkTag->href);
		$enurl=urlencode($url);
		$found_urls = array();
		
		if($url!='' && substr($url,0,4)!="mail" && substr($url,0,4)!="java" && array_key_exists($enurl,$found_urls)==0)
		{
			$found_urls[$enurl]=1;
			$url = explode("#", $url);
			$url = $url['0'];

			if(strpos($url,$trigger) && $link->check($url) == false)
			{
				 $link->insert($url);
			}
		}		
	}

	$html->clear();
	$link->setChecked($website_url);
	
	return;
}


function main($website_url,$trigger)
{

	crawl_site($website_url, $trigger);
	
	$counter = new Counter();

	$jobs = array(
		new Process($website_url, $trigger, $counter),
		new Process($website_url, $trigger, $counter),
		new Process($website_url, $trigger, $counter),
		new Process($website_url, $trigger, $counter)
	);

	foreach ($jobs as $job)
	    $job->start();

	foreach ($jobs as $job)
		$job->join();
}


// lets crawl !
main($url,$trigger);


?>