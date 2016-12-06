<?php global $the_date_title ?>
	<?php get_sidebar('content'); ?>
	</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
			<?php if ( is_category() && !is_category( 'warzone' )) { ?>
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
				<li class="sideinfo"><h2>About the Warzonews</h2>
					<p>The Warzone is the leagues's source for the latest team news a gossip. It presents a weekly update on the league and its happenings.</p>
					<p><a href="<?php echo esc_url( get_permalink( get_page_by_title( 'about' ) ) ); ?>#warzone" title="Read more about the Warzone">Read More about the Warzone</a></p>
				</li>
				<li><h2>Latest From the Warzone</h2>
					<?php
					//Grabs the last 6 entries from the Warzone and Displays them.
								$warzonerecentposts = array(
									'post_type' => 'post',
									'posts_per_page' => 6,
									'category__in' =>  get_cat_ID( 'warzone' ),
									'post__not_in' => get_option( 'sticky_posts' )
								);
								// The Query
								$the_query = new WP_Query( $warzonerecentposts );

								// The Loop
								if ( $the_query->have_posts() ) {
									print("<ul>\n");
									while ( $the_query->have_posts() ) {
										$the_query->the_post();
					?>
					<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></li>

						<?php

									} //end of while
									print("</ul>\n");
								} //end of have posts
					?>
				</li>
				<?php //load the warzone sidebar if it exists
				if ( !dynamic_sidebar('sidebar-warzone') ) : ?>
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
