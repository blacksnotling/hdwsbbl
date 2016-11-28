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
