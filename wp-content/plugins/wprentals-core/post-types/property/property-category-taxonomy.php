<?php
/** MILLDONE
 * Property Category Admin Fields
 * src: post-types\property\property-category-admin.php
 * Handles the management of custom fields for the 'property_category' taxonomy. This includes adding, editing, and saving custom fields for terms.
 *
 * @package WPRentals Core
 * @subpackage Property
 * @since 4.0.0
 *
 * @dependencies
 * - WordPress taxonomy functions
 * - WPRentals theme options and settings
 *
 * Usage:
 * - This file should be included as part of the WPRentals theme. It adds custom fields to the 'property_category' taxonomy for better management and extends the admin fields.
 */

// Add actions to add/edit form fields and save custom fields for 'property_category'.
add_action('property_category_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_category_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_category', 'wpestate_property_category_save_extra_fields_callback', 10, 2);
add_action('edited_property_category', 'wpestate_property_category_save_extra_fields_callback', 10, 2);

if ( ! function_exists( 'wpestate_property_category_callback_function' ) ):
    /**
     * Adds custom fields when editing an existing 'property_category' term.
     *
     * @param object $tag The term object being edited.
     */
    function wpestate_property_category_callback_function( $tag ) {
        // Retrieve existing meta fields if available.
        $t_id = is_object( $tag ) ? $tag->term_id : '';
        $term_meta = get_option( "taxonomy_$t_id" );
        $pagetax = isset( $term_meta['pagetax'] ) ? esc_attr( $term_meta['pagetax'] ) : '';
        $category_featured_image = isset( $term_meta['category_featured_image'] ) ? esc_attr( $term_meta['category_featured_image'] ) : '';
        $category_tagline = isset( $term_meta['category_tagline'] ) ? esc_attr( stripslashes( $term_meta['category_tagline'] ) ) : '';
        $category_attach_id = isset( $term_meta['category_attach_id'] ) ? esc_attr( $term_meta['category_attach_id'] ) : '';
        ?>
        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th scope="row" valign="top"><label for="term_meta[pagetax]"> <?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <input type="text" name="term_meta[pagetax]" class="postform" value="<?php echo $pagetax; ?>">
                        <p class="description"> <?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?> </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="category_featured_image"> <?php esc_html_e( 'Featured Image', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <input id="category_featured_image" type="text" class="wpestate_landing_upload" size="36" name="term_meta[category_featured_image]" value="<?php echo $category_featured_image; ?>" />
                        <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload Image', 'wprentals-core' ); ?>" />
                        <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="<?php echo $category_attach_id; ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="term_meta[category_tagline]"> <?php esc_html_e( 'Category Tagline', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="<?php echo $category_tagline; ?>">
                    </td>
                </tr>
                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_category">
            </tbody>
        </table>
        <?php
    }
endif;







if ( ! function_exists( 'wpestate_property_category_callback_add_function' ) ):
    /**
     * Adds custom fields when creating a new 'property_category' term.
     *
     * @param object $tag The term object being created.
     */
    function wpestate_property_category_callback_add_function( $tag ) {
        // Retrieve default meta fields.
        $pagetax = '';
        $category_featured_image = '';
        $category_tagline = '';
        $category_attach_id = '';
        ?>
        <div class="form-field">
            <label for="term_meta[pagetax]"> <?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?> </label>
            <input type="text" name="term_meta[pagetax]" class="postform" value="<?php echo esc_attr( $pagetax ); ?>">
        </div>
        <div class="form-field">
            <label for="term_meta[category_featured_image]"> <?php esc_html_e( 'Featured Image', 'wprentals-core' ); ?> </label>
            <input id="category_featured_image" type="text" class="wpestate_landing_upload" size="36" name="term_meta[category_featured_image]" value="<?php echo esc_attr( $category_featured_image ); ?>" />
            <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload Image', 'wprentals-core' ); ?>" />
            <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="<?php echo esc_attr( $category_attach_id ); ?>" />
        </div>
        <div class="form-field">
            <label for="term_meta[category_tagline]"> <?php esc_html_e( 'Category Tagline', 'wprentals-core' ); ?> </label>
            <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="<?php echo esc_attr( $category_tagline ); ?>">
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_category">
        <?php
    }
endif;





if ( ! function_exists( 'wpestate_property_category_save_extra_fields_callback' ) ):
    /**
     * Saves custom fields for the 'property_category' taxonomy.
     *
     * @param int $term_id The ID of the term being saved.
     */
    function wpestate_property_category_save_extra_fields_callback( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id" );
            $term_meta = is_array( $term_meta ) ? $term_meta : array();
            $cat_keys = array_keys( $_POST['term_meta'] );
        

            foreach ( $cat_keys as $key ) {
                $key = sanitize_key( $key );
                if ( isset( $_POST['term_meta'][ $key ] ) ) {
                    $term_meta[ $key ] = wp_kses_post( $_POST['term_meta'][ $key ] );
                }
            }

            // Save the updated term meta.
            update_option( "taxonomy_$t_id", $term_meta );
        }
    }
endif;
?>
