<?php
/*
Plugin Name: WP Category Image plugin
Description: Asocia una imagen a una categoría específica.
Version: dev-0.1
Author: Silvano Emanuel Roqués
Author URI: http://lode.uno/tejne
*/

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wpcip_category_image_field($taxonomies) {
         if (current_filter() == 'category_edit_form_fields') {
            $attachment_id = get_term_meta( $taxonomies->term_id, 'wpcip_image_attachment_id', true );
            $image_attributes = wp_get_attachment_image_src( $attachment_id );
			?>
			<tr class="form-field">
				<th valign="top" scope="row"><label for="wpcip_image" class="prfx-row-title"><?php _e( 'Category Image', 'wpcip' )?></label></th>
				<td>
			<?php
			if ( $image_attributes ) : ?>
				<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" style="margin-bottom:10px" /><br />
			<?php endif; ?>
				<input type="hidden" name="wpcip_image_attachment_id" id="wpcip_image_attachment_id" value="<?php if ( isset ( $category_image) ) echo $category_image; ?>" />
				<input type="button" id="wpcip-image-button" class="button" value="<?php _e( 'Choose or Upload an Image', 'wpcip' )?>" />
				</td>
			</tr>
    
		<?php } elseif (current_filter() == 'category_add_form_fields') {
		?> 
			<div class="form-field">
				<label for="wpcip_image_attachment_id" class="wpcip-row-title"><?php _e( 'Category Image', 'wpcip' )?></label>
				<input type="hidden" size="40" value="" id="wpcip_image_attachment_id" name="wpcip_image_attachment_id">
				<input type="button" id="wpcip-image-button" class="button" value="<?php _e( 'Choose or Upload an Image', 'wpcip' )?>" />
				<p class="description">
				</p>
			</div> 
      
        <?php
        } 
 }
    
add_action('category_edit_form_fields', 'wpcip_category_image_field', 10, 2);
add_action('category_add_form_fields', 'wpcip_category_image_field', 10, 2); 
    
function wpcip_save_category_image_field($term_id) {
	if ( isset( $_REQUEST['wpcip_image_attachment_id'] ) ) { 
		$category_image = $_REQUEST['wpcip_image_attachment_id']; 
		update_term_meta( $term_id, 'wpcip_image_attachment_id', $category_image );   
	}
}
add_action('edited_category', 'wpcip_save_category_image_field', 10, 2);
add_action('create_category', 'wpcip_save_category_image_field', 10, 2); 

function wpcip_enqueue_script() {
    global $typenow;
    if( $typenow == 'post' ) {
        wp_enqueue_media();
 
        // Registers and enqueues the required javascript.
        wp_register_script( 'meta-box-image', plugin_dir_url( __FILE__ ) . 'meta-box-image.js', array( 'jquery' ) );
        wp_localize_script( 'meta-box-image', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'wpcip' ),
                'button' => __( 'Use this image', 'wpcip' ),
            )
        );
        wp_enqueue_script( 'meta-box-image' );
    }
}
add_action( 'admin_enqueue_scripts', 'wpcip_enqueue_script' );

function wpcip_shortcode( $atts = [], $content = null) {
    if(is_category()){
		$term_id = get_queried_object()->term_id;
        $attachment_id = get_term_meta( $term_id, 'wpcip_image_attachment_id', true );
        $image_attributes = wp_get_attachment_image_src( $attachment_id );
		if($image_attributes){
			$src = $image_attributes[0];
			$width = $image_attributes[1];
			$height = $image_attributes[2];
			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true);
			$content = '<img src="'.$src.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" />';
			return $content;
		}
	}
}

add_shortcode('catimg', 'wpcip_shortcode');
