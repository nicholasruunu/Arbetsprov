<?php
	
	// Sätter upp sökmotorer
	$config['search_engines'] = array(
		'google' => array(
			// Ändrade från google.com till google.se eftersom min browser (FF 4.0) ändrade resultaten till det automatiskt
			// medans cURL kom förbi, antog att era gör detsamma så ändrade för att få konsekventa resultat
			'url' => 'http://www.google.se/search?as_qdr=all&num=20&q=',
			'regex' => '/<h3\sclass="?r"?><a href="(?P<url>http:\/\/[^"]+?)".*?>(?P<title>[^"\n\r\t]+?)<\/a><\/h3>(?:<span\sclass="?std\snobr"?>.*?<\/span>)?(?:<div\sclass="?s"?>(<div\sclass="?f"?>.*?<\/div>)?(?P<text>.*?)(<br>.*?)?)?<span\sclass="?f"?>/m'
		),
		'bing' => array(
			'url' => 'http://www.bing.com/search?q=',
			'regex' => '/<div\sclass="?sb_tlst"?><h3><a.+?href="(?P<url>http:\/\/[^"]+?)".*?>(?P<title>[^\n\r\t]+?)<\/a>.*?(?:<p>(?P<text>.*?)<\/p>)?<div\sclass="sb_meta">/m'
		),
		'yahoo' => array(
			'url' => 'http://search.yahoo.com/search?n=20&p=',
			'regex' => '/<div\sclass="?res"?><div>(?:\s<span\sclass="?ext"?>.*?<\/span>\s)?<h3><a.+?href="(?P<url>http:\/\/[^"]+?)".*?>(?P<title>[^\n\r\t]+?)<\/a>.*?(?:<div\sclass="?(sm-abs|abstr)"?>(?P<text>.*?)<\/div>.*?)?<span\sclass="?url"?>/m'
		),
	);