<?php global $the_date_title ?>
	</div><!-- end of #maincontent -->
	<div id="subcontent">
		<ul>
			<?php if ( is_category() ) { ?>
			<li><p>You are currently browsing the archives for the <strong><?php single_cat_title(); ?></strong> topic.</p></li>
			<?php } ?>
			<?php if ( is_search() ) { ?>
			<li><p>You have searched the HDWSBBL weblog archives for <strong>'<?php the_search_query() ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</p></li>
			<?php } ?>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-posts') ) : ?>
			<li><h2 class="widgettitle">Opps</h2>
			  <ul>
			   <li>Something has gone wrong and you have lost your widget settings. better log in quick and fix it!</li>
			  </ul>
			</li>
		<?php endif; ?>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-common') ) : ?>
			<li><h2 class="widgettitle">Opps</h2>
			  <ul>
			   <li>Something has gone wrong and you have lost your widget settings. better log in quick and fix it!</li>
			  </ul>
			</li>
		<?php endif; ?>
		</ul>
	</div><!-- end of #subcontent -->