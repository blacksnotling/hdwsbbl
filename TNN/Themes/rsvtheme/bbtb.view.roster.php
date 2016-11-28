<?php
/*
Template Name: Team Roster
*/
/*
*	Filename: bbtm.view.roster.php
*	Description: .Big blank page sutible for rosters or any other large displays. RSV Online stuff is hard coaded
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php wp_title(); ?> - RSV Online</title>
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
			<p><a href="<?php echo home_url(); ?>" title="Back to the front of RSV Online">RSV Online</a> &raquo; <a href="<?php print(home_url()); ?>/team/" title="View the team information page">Team Information</a> &raquo; <?php the_title(); ?></p>
		</div>

		<h1><?php the_title(); ?></h1>
		<?php the_content('Read the rest of this entry &raquo;'); ?>


		<?php endwhile;?>
	<?php endif; ?>

		</div> <!-- End of #maincontent -->
	</div> <!-- End of #pagecontent -->
	<div id="footer">
				<p>Unique content is &copy; RSV Online 2007 - present.</p>
				<p>Blood Bowl concept and miniatures are &copy; Games Workshop LTD used without permission.</p>
				<div id="footerimg">
					<p>Ragnars Swift Velocity are proudly part of the <a href="http://www.hdwsbbl.co.uk">HDWSBBL</a>. Site powered by <a href="http://www.wordpress.org/" title="WordPress">WordPress</a></p>
				</div>
				<?php wp_footer(); ?>
	</div> <!-- End of #footer -->
</div> <!-- End of #wrapper -->
</body>
</html>
