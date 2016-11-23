<?php
/*
Template Name: Team Roster
*/
/*
*	Filename: bbtm.view.roster.php
*	Version: 1.0
*	Description: .Big blank page sutible for rosters or any other large displays. RSV Online stuff is hard coaded
*/
/* -- Change History --
20091125 - 1.0 - Initial creation of file using the bblm roster page as a template.
20100914 - 1.1 - updated header and footer to bring it in line with the rest of the team theme
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
<style type="text/css">
html * {
	margin:0;
	padding:0;
}
body {
	font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
	font-size: 0.8em;
}
h1, div, table, p {
	margin-bottom: 0.8em
}
h1 {
	font-size: 1.6em;
}
table {
	margin-left: auto;
	margin-right: auto;
	background-color: #fff;
	border: 1px solid #000;
	border-collapse: collapse;
}
table td {
	text-align: center;
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000000;
	border-width: 1px;
	padding: 0.3em;
}
table th {
	border-bottom: 4px solid #000;
	background-color: #548ac3;
	color: #fff;
	font-weight: bold;
	text-align: center;
}
table th.tbl_enchance, table th.tbl_title {
	border-left-style: dotted;
	border-bottom-style: solid;
	border-color: #000000;
	border-width: 1px;
}
.tbl_stat {
	width:25px;
}
.tbl_skills {
	width:300px;
	text-align:left;
	font-size: smaller;
}
.tbl_name {
	width:200px;
}
.tbl_value {
	width:50px;
	text-align:right;
}
#footer {
	color: #666;
}
a, a:link, a:visited {
	color: #3366FF;
	text-decoration: none;
}

a:hover, a:active {
	color: #D7651B;
	text-decoration: underline;
}
</style>
</head>
<body>

<div id="wrapper">
	<div id="pagecontent">
		<div id="maincontent">

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

		<div id="breadcrumb">
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of <?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a> &raquo; <?php the_title(); ?></p>
		</div>

		<h1><?php the_title(); ?></h1>
		<?php the_content('Read the rest of this entry &raquo;'); ?>


		<?php endwhile;?>
	<?php endif; ?>

		</div> <!-- End of #maincontent -->
	</div> <!-- End of #pagecontent -->
		<div id="footer">
			<div id="footertext">
				<p>Unique content is &copy; 2006 - present.</p>
				<p>Blood Bowl concept and miniatures are &copy; Games Workshop LTD used without permission.</p>
			</div>
<?php
	$bbtn_footer_options = get_option('bbtnn_child_options');
	$teamnamesql = "SELECT T.t_name AS teamname FROM ".$bbtn_footer_options['bb_tbl_pre']."team T WHERE T.t_id = ".$bbtn_footer_options['t_id'];
	if ($tname = $wpdb->get_row($teamnamesql)) {
		$tname = $tname->teamname;
	}
?>
			<div id="footerimg">
				<p>In association with: <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/slysports.gif" alt="Sly Sports Logo" /><br/>
<?php
	if (isset($tname)) {
?>
	<strong><?php print($tname); ?></strong> are proudly part of the <a href="http://www.hdwsbbl.co.uk">HDWSBBL</a>.
<?
	}
?>
 Site powered by <a href="http://www.wordpress.org/" title="WordPress">WordPress</a></p>
							</div>
		</div> <!-- End of #footer -->
</div> <!-- End of #pagecontent -->
</div><!-- End of #wrapper -->
		<?php wp_footer(); ?>
</body>
</html>
