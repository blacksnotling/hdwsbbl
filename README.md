# HDWSBBL
Source code for the HDWSBBL website, team network (multisite), and BB7 sites (coming soon).

Instructions on how to set up and configure will come eventually!


## Hooks, Filters, and Customisation (since 1.2)
Since 1.2 the following hooks are availible

###Templating Actions (use with add_action() )
* **bblm_template_before_posts** - executes before if have_posts. can be used to add additional content tags to the page
* **bblm_template_before_loop** - executes before while have_posts, or single page once post validation has been made. can be used to add additional content tags to the items about to be displayed
* **bblm_template_before_content** - executes before each entry (after the while have_posts). can be used to add additional content tags to each item that is being displayed

They also have three counterparts - they are in the same locations but after the if / while / posts. they use *after* rather than before

###Other Filters (use with add_filter() )
* **is_bblm** - apply other items to see if the page in question belongs to the BBLM plugin (for styling and other items)
* **bblm_filter_post_types** - Add additional post types to be considered part of the BBLM system (for templating etc)
