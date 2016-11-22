		</div><!-- end of #maincontent -->
	</div><!-- end of #maincontent_wrapper -->
	<div id="subcontent">
		<ul>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-posts') ) : ?>
				<li id="archives" class="widget-container">
				<h3 class="widget-title">Archive</h3>
				<ul>
					<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</li>
		<?php endif; ?>
		</ul>
	</div><!-- end of #subcontent -->