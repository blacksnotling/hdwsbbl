<?php global $the_date_title ?>
	<?php get_sidebar('content'); ?>
	</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
			<?php if ( is_category() ) { ?>
			<li><p>You are currently browsing the archives for the <strong><?php single_cat_title(); ?></strong> topic.</p></li>
			<?php } ?>
			<?php if ( is_search() ) { ?>
			<li><p>You have searched the HDWSBBL weblog archives for <strong>'<?php the_search_query() ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</p></li>
			<?php } ?>
			<?php
			/*	If the content is part of the WarZone then display the warzone sidebar, else display the
			*	normal sidebar area. The "common" sidebar is always displayed.
			*/
			if ( is_category( 'warzone' ) || is_page('warzone') || ( in_category( 'warzone' ) && is_single() ) ) { ?>
				<?php if ( !dynamic_sidebar('sidebar-warzone') ) : ?>
				<li><h2 class="widgettitle">Archive</h2>
				  <ul>
				   <?php wp_get_archives( 'type=monthly' ); ?>
				  </ul>
				</li>
			<?php endif; ?>
			<?php } else { ?>
			<?php if ( !dynamic_sidebar('sidebar-posts') ) : ?>
				<li><h2 class="widgettitle">Archive</h2>
				  <ul>
				   <?php wp_get_archives( 'type=monthly' ); ?>
				  </ul>
				</li>
			<?php endif; ?>
			<?php } ?>
			<?php if ( !dynamic_sidebar('sidebar-common') ) : ?>
				<li><h2 class="widgettitle">Search</h2>
				  <ul>
				   <li><?php get_search_form(); ?></li>
				  </ul>
				</li>
			<?php endif; ?>
		</ul>
	</div><!-- end of #subcontent -->
