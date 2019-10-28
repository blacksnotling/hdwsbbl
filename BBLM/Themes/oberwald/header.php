<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="verify-v1" content="J9bPx/TvWuo23XUXc0nYCJFSmgUPTSk08c1uZQRsOjw=" />
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/jquery.js"></script>
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
<link href='http://fonts.googleapis.com/css?family=Graduate' rel='stylesheet' type='text/css'>
<?php if (is_front_page()) { ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/main.css?0909" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/ui.tabs.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/jquery.newsticker.js"></script>
<script type="text/javascript">
 var $j = jQuery.noConflict();
          $j(document).ready(function(){
			$j('#main-tabs > ul').tabs({ fx: { opacity: 'toggle', duration: 300 }});
			$j('#twitterfeed div.aktt_tweets ul').newsticker();
		});
</script>

<?php
	} //end of if is_front_page()
	else {
?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/includes/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//JS Code to expand / collapase the stats and recent match tables.
	//before we begin the activation, we should stip the tr_alt class from the table (it can be styled from the tablesorter program
	$("table.bblm_sortable tr.bblm_tbl_alt").removeClass('bblm_tbl_alt');

	//Table Sorting

	//Append some text to the sorted table
	$("table.bblm_sortable").before("<p>The below table is sortable. Click a table column to sort by it.</p>");

    // extend the default setting to always include the zebra widget.
    $.tablesorter.defaults.widgets = ['zebra'];
    // extend the default setting to always sort on the first column
//    $.tablesorter.defaults.sortList = [[0,0]];
    // call the tablesorter plugin
    $("table.bblm_sortable").tablesorter();

	//gather all the apropiate tables into an array
	$("table.bblm_expandable").each(function(i){
		//remember to add 1 to the length as the tbl header counts as a row
		if ($(this).find("tr").length > 11) {
			//if it qualifies, hide the over-run and append a link
			$(this).find("tr.bblm_tbl_hide").hide();
			//Append a link to the bottom of the table with hidden rows
			$(this).after("<p class='showtable'><a href='' title='Show or hide more of the above entries'>Show / Hide more entries &gt; &gt;</a></p>");
		}

	});

	//assign a onClick event to the generated links
	$("p.showtable a").click(function(event){
		//finds the table above and toggles the marked rows
		$(this).parents("p").prev(".bblm_expandable").find("tr.bblm_tbl_hide").toggle();
		// Stop the link click from doing its normal thing
		return false;
	 });

	//Now the match comments code
	//play with new coach comments
	//insert a new TD on each table
	var c = $("#bblm_recentmatches tr:first td").length;
	$("#bblm_recentmatches thead tr:first").append("<th>Comments</th>");
	$("#bblm_recentmatches tbody tr:even(0)").append("<td class='showcomment'><a href=''>view/hide</a></td>");
	$("#bblm_recentmatches tbody tr:odd(0)").append("<td>&nbsp;</td>");

	//assign a onClick event to the generated links
	$("td.showcomment").click(function(event){
		//finds the next row and toggles visibility
 		$(this).parent().next().toggle();
		// Stop the link click from doing its normal thing
		return false;
	 });


 });


</script>
<?php
	} //end of if NOT is_front_page()
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>?1503" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/print.css" type="text/css" media="print" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	if(is_front_page()){ ?>
<!-- <div id="home-wrapper"> -->
<div id="home-wrapper">
<?php
	}
	else {
?>
<div id="wrapper">
<?php
	}
?>
	<div id="tagline"><p>Causing Havoc in the old world since 2506</p></div>
	<div id="header" onclick="location.href='<?php echo home_url(); ?>';" style="cursor: pointer;">
		<h1><a href="<?php echo home_url(); ?>" title="Go to the main page of <?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
		<p><?php bloginfo('description'); ?></p>
	</div>
	<div id="navcontainer">
    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_id' => 'navigation' ) ); ?>
	</div>

	<div id="pagecontent">
		<div id="maincontent">
<?php 	if (is_front_page()) {
		}
		else {
			//Dont print the breadcrumbs on the home page
?>
		<div id="breadcrumb">
			<p><?php oberwald_breadcrumb(); ?></p>
		</div>
<?php
		}
?>
