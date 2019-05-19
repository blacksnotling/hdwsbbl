# HDWSBBL
Source code for the HDWSBBL website, team network (multisite), and BB7 sites (coming soon).

Instructions on how to set up and configure will come eventually!


## Hooks, Filters, and Customisation
The following hooks are available

### Templating Actions (use with add_action() )
* **bblm_template_before_posts** - Executes before if have_posts. can be used to add additional content tags to the page (since 1.7)
* **bblm_template_before_loop** - Executes before while have_posts, or single page once post validation has been made. can be used to add additional content tags to the items about to be displayed (since 1.7)
* **bblm_template_before_content** - Executes before each entry (after the while have_posts). can be used to add additional content tags to each item that is being displayed (since 1.7)

They also have three counterparts - they are in the same locations but after the if / while / posts. they use *after* rather than before

### Other Actions (use with add_action() )
* **bblm_post_submission** - Executes after the BBLM plugin has submitted something to the database (since 1.7)

### Other Filters (use with add_filter() )
* **is_bblm** - Apply other items to see if the page in question belongs to the BBLM plugin (for styling and other items) (since 1.7)
* **bblm_filter_post_types** - Add additional post types to be considered part of the BBLM system (for templating etc) (since 1.7)
