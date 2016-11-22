<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head profile="http://gmpg.org/xfn/11">
<title><?php wp_title('-','true','right'); ?> HDWSBBL Team News Network</title>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/includes/jquery.js"></script>
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.ico" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?0910" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/print.css" type="text/css" media="print" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
</head>
<body>
<?php
	//Begin the custom wrapper class for the front page
	if ($ismainpage) {
?>
<div id="main-wrapper">
<?php
	}
	else {
?>
<div id="wrapper">
<?php
	}
?>
	<div id="tagline"><p>The latest team news, in their own words</p></div>
	<div id="header" onclick="location.href='<?php echo get_option('home'); ?>';" style="cursor: pointer;">
		<h1><a href="<?php echo get_option('home'); ?>" title="Go to the main page of <?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
		<p><?php bloginfo('description'); ?></p>
	</div>
	<div id="navcontainer">
		<ul id="navigation">
			<li><a href="<?php echo get_option('home'); ?>/blog/" title="Read the latest updates to the Team News Network">Blog</a></li>
			<li><a href="<?php echo get_option('home'); ?>/network/" title="Vire the sites thatare part of the Team News Network">News Network</a></li>
			<li><a href="<?php echo get_option('home'); ?>/about/" title="About the Team News Network">About</a></li>
		</ul>
	</div>


	<div id="pagecontent">
		<div id="maincontent">
