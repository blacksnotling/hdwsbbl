<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * race_rules taxonomy custom fields
 *
 * Defines the custom fields that are availible on the race_rules custom Taxonomy
 * For front end functions reloated to the Taxonomy see the includes/post-types directory
 *
 * @class 		BBLM_Admin_TAX_Race
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/Admin/TAX
 * @version   1.0
 */
if ( ! class_exists( 'BBLM_Admin_TAX_Race' ) ) {

class BBLM_Admin_TAX_Race {

  public function __construct() {

    add_action( 'race_rules_add_form_fields', array ( $this, 'add_star_mapping' ), 10, 2 );
    add_action( 'created_race_rules', array ( $this, 'save_category_image' ), 10, 2 );
    add_action( 'race_rules_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
    add_action( 'edited_race_rules', array ( $this, 'updated_category_image' ), 10, 2 );

  }

 /*
  * Add a form field in the new race_rules page
 */
 public function add_category_image ( $taxonomy ) { ?>
   <div class="form-field term-group">
     <label for="category-image-id"><?php _e('Image', 'hero-theme'); ?></label>
     <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
     <div id="category-image-wrapper"></div>
     <p>
       <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'hero-theme' ); ?>" />
       <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'hero-theme' ); ?>" />
    </p>
   </div>
 <?php
 }

 /*
  * Save the form field
  * @since 1.0.0
 */
 public function save_category_image ( $term_id, $tt_id ) {
   if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
     $image = $_POST['category-image-id'];
     add_term_meta( $term_id, 'category-image-id', $image, true );
   }
 }

 /*
  * Edit the form field
  * @since 1.0.0
 */
 public function update_category_image ( $term, $taxonomy ) { ?>
   <tr class="form-field term-group-wrap">
     <th scope="row">
       <label for="category-image-id"><?php _e( 'Image', 'hero-theme' ); ?></label>
     </th>
     <td>
       <?php $image_id = get_term_meta ( $term -> term_id, 'category-image-id', true ); ?>
       <input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo $image_id; ?>">
       <div id="category-image-wrapper">
         <?php if ( $image_id ) { ?>
           <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
         <?php } ?>
       </div>
       <p>
         <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'hero-theme' ); ?>" />
         <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'hero-theme' ); ?>" />
       </p>
     </td>
   </tr>
 <?php
 }

/*
 * Update the form field value
 * @since 1.0.0
 */
 public function updated_category_image ( $term_id, $tt_id ) {
   if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
     $image = $_POST['category-image-id'];
     update_term_meta ( $term_id, 'category-image-id', $image );
   } else {
     update_term_meta ( $term_id, 'category-image-id', '' );
   }
 }


  }
}


new BBLM_Admin_TAX_Race();
?>
