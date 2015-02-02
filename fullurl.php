<?php

function rel2absURL($relURL, $base)
{
	if(parse_url($relURL, PHP_URL_SCHEME) != '')
		return $relURL;

	if($relURL[0]=='#' || $relURL[0]=='?')
		return $base.$relURL;

	extract(parse_url($base));
	$path = preg_replace('#/[^/]*$#', '', $path);

	if($relURL[0] == '/')
		$path = '';

	$abs = "$host$path/$relURL";
	$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');

	for($n=1; $n>0;$abs=preg_replace($re,'/', $abs,-1,$n)){}

	$abs=str_replace("../","",$abs);
		
	return $scheme.'://'.$abs;
}

function perfect_url($aTag_URL,$host_URL)
{
	$bp=parse_url($host_URL);

	if(!isset($bp['path']))
	{
		$bp['path'] = "";
	}
	if(($bp['path']!="/" && $bp['path']!="") || $bp['path']=='')
	{
		if($bp['scheme']==""){$scheme="http";}else{$scheme=$bp['scheme'];}
		$host_URL=$scheme."://".$bp['host']."/";
	}


	if(substr($aTag_URL,0,2)=="//")
	{
		$$aTag_URL="http:". $aTag_URL;
	}
	if(substr($aTag_URL,0,4)!="http")
	{
		$aTag_URL=rel2absURL($aTag_URL,$host_URL);
	}

	return $aTag_URL;
}
