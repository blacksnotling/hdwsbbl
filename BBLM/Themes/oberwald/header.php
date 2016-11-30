<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="verify-v1" content="J9bPx/TvWuo23XUXc0nYCJFSmgUPTSk08c1uZQRsOjw=" />
<script type="text/javascript" src="<?php echo esc_url( get_template_directory_uri() ); ?>/includes/jquery.js"></script>
<link rel="shortcut icon" href="<?php echo esc_url( get_template_directory_uri() ); ?>/favicon.ico" />
<link href='http://fonts.googleapis.com/css?family=Graduate' rel='stylesheet' type='text/css'>
<?php if (is_home()) { ?>
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/main.css?0910" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo esc_url( get_template_directory_uri() ); ?>/includes/ui.tabs.js"></script>
<script type="text/javascript" src="<?php echo esc_url( get_template_directory_uri() ); ?>/includes/jquery.newsticker.js"></script>
<script type="text/javascript">
 var $j = jQuery.noConflict();
          $j(document).ready(function(){
			$j('#main-tabs > ul').tabs({ fx: { opacity: 'toggle', duration: 300 }});
			$j('#twitterfeed div.aktt_tweets ul').newsticker();
		});
</script>

<?php
	} //end of if is_home
	else {
?>
<script type="text/javascript" src="<?php echo esc_url( get_template_directory_uri() ); ?>/includes/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	//JS Code to expand / collapase the stats and recent match tables.
	//before we begin the activation, we should stip the tr_alt class from the table (it can be styled from the tablesorter program
	$("table.sortable tr.tbl_alt").removeClass('tbl_alt');

	//Table Sorting

	//Append some text to the sorted table
	$("table.sortable").before("<p>The below table is sortable. Click a table column to sort by it.</p>");

    // extend the default setting to always include the zebra widget.
    $.tablesorter.defaults.widgets = ['zebra'];
    // extend the default setting to always sort on the first column
//    $.tablesorter.defaults.sortList = [[0,0]];
    // call the tablesorter plugin
    $("table.sortable").tablesorter();

	//gather all the apropiate tables into an array
	$("table.expandable").each(function(i){
		//remember to add 1 to the length as the tbl header counts as a row
		if ($(this).find("tr").length > 11) {
			//if it qualifies, hide the over-run and append a link
			$(this).find("tr.tb_hide").hide();
			//Append a link to the bottom of the table with hidden rows
			$(this).after("<p class='showtable'><a href='' title='Show or hide more of the above entries'>Show / Hide more entries &gt; &gt;</a></p>");
		}

	});

	//assign a onClick event to the generated links
	$("p.showtable a").click(function(event){
		//finds the table above and toggles the marked rows
		$(this).parents("p").prev(".expandable").find("tr.tb_hide").toggle();
		// Stop the link click from doing its normal thing
		return false;
	 });

	//Now the match comments code
	//play with new coach comments
	//insert a new TD on each table
	var c = $("#recentmatches tr:first td").length;
	$("#recentmatches thead tr:first").append("<th>Comments</th>");
	$("#recentmatches tbody tr:even(0)").append("<td class='showcomment'><a href=''>view/hide</a></td>");
	$("#recentmatches tbody tr:odd(0)").append("<td>&nbsp;</td>");

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
	} //end of if NOT is_home
	if ($iswarzonepage) { ?>
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/warzone.css?0909" type="text/css" media="screen" />
<?php
	}else { ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?0909" type="text/css" media="screen" />
<?php
	} //end of if else cat 13 (warzone) ?>
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/print.css" type="text/css" media="print" />
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

	if(is_home()){ ?>
<div id="home-wrapper">
<?php	}else if ($ismainpage) {
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
	<div id="tagline"><p>Causing Havoc in the old world since 2506</p></div>
	<div id="header" onclick="location.href='<?php echo home_url(); ?>';" style="cursor: pointer;">
		<h1><a href="<?php echo home_url(); ?>" title="Go to the main page of <?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
		<p><?php bloginfo('description'); ?></p>
	</div>
	<div id="navcontainer">
		<ul id="navigation">
			<li><a href="<?php echo home_url(); ?>/news/" title="Visit the News Section">News</a></li>
			<li><a href="<?php echo home_url(); ?>/warzone/" title="Visit the Warzone Section">Warzone</a></li>
			<li><a href="<?php echo home_url(); ?>/teams/" title="View the teams of the HDWSBBL">Teams</a></li>
			<li><a href="<?php echo home_url(); ?>/competitions/" title="View the gruling competitions">Competitions</a></li>
			<li><a href="<?php echo home_url(); ?>/matches/" title="All the results">Results</a></li>
			<li><a href="<?php echo home_url(); ?>/stats/" title="All the Statistics">Stats</a></li>
			<li><a href="<?php echo home_url(); ?>/fixtures/" title="View the upcoming Matches">Fixtures</a></li>
			<li><a href="<?php echo home_url(); ?>/about/" title="About the HDWSBBL">About</a></li>
		</ul>
	</div>
<!--  GC -->
	<div id="pagecontent">
		<div id="maincontent">
      <?php 	if (is_home()) {
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
