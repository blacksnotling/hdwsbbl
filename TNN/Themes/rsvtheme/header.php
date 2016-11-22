<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
</head>
<body>
<?php
	if (function_exists(bbtn_header_bar_init)) {
		bbtn_header_bar_init();
	}


?>
<div id="wrapper">

	<div id="header">
		<h1><a href="<?php echo home_url( '/' ); ?>" title="Go to the main page of <?php bloginfo( 'name' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<p><?php bloginfo( 'description' ); ?></p>
	</div><!-- end of #header -->
	<div id="navcontainer">
		<ul id="navigation">
		<?php wp_nav_menu($args); ?>
<!--		<li><a href="#" title="Learn about the history of RSV">History</a></li>-->
<!--		<li><a href="http://rsv.hdwsbbl.co.uk/team/" title="Find out all the information you need">Team Info</a></li>
		<li><a href="http://rsv.hdwsbbl.co.uk/players/" title="Find out about the stars of RSV">The Players</a></li>
		<li><a href="http://rsv.hdwsbbl.co.uk/hall-of-records/" title="See the teams personal hall of records">Hall of Records</a></li>
		<li><a href="http://rsv.hdwsbbl.co.uk/coaches-corner/" title="Read the ramblings of our Head Coaches">Coaches Corner</a></li>
		<li><a href="http://rsv.hdwsbbl.co.uk/about/" title="About the team and the site" class="last">About</a></li> -->
		</ul>
	</div><!-- end of #navcontainer -->

	<div id="pagecontent">
	<div id="maincontent_wrapper">
		<div id="maincontent">
