<?php global $the_date_title ?>
	</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
			<?php if ( is_category() ) { ?>
			<li><p>You are currently browsing the archives for the <strong><?php single_cat_title(); ?></strong> topic.</p></li>
			<?php } ?>
			<?php if ( is_search() ) { ?>
			<li><p>You have searched the Team News Network archives for <strong>'<?php the_search_query() ?>'</strong>.</p></li>
			<?php } ?>
			<?php if ( is_active_sidebar( 'sidebar-posts' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-posts' ); ?>
			<?php endif; ?>
		<?php if ( is_active_sidebar( 'sidebar-common' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-common' ); ?>
		<?php endif; ?>
		</ul>
	</div><!-- end of #subcontent -->
