<?php
/** MILLDONE
 * Property Action Category Taxonomy
 * src:post-types\property\property-action-taxonomy.php
 * Handles the property action category taxonomy in WPRentals. Includes functions for
 * managing taxonomy terms including callbacks for term add/edit forms and term saving.
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
 * - This file should be included as part of the WPRentals theme. It adds extra fields to the "property_action_category" taxonomy and handles saving of these fields when a category is created or edited.
 */

// Add actions to edit/add form fields and save extra fields for the property_action_category taxonomy.
add_action( 'property_action_category_edit_form_fields', 'wpestate_property_action_category_callback_function', 10, 2 );
add_action( 'property_action_category_add_form_fields', 'wpestate_property_action_category_callback_add_function', 10, 2 );
add_action( 'created_property_action_category', 'wpestate_property_action_category_save_extra_fields_callback', 10, 2 );
add_action( 'edited_property_action_category', 'wpestate_property_action_category_save_extra_fields_callback', 10, 2 );

// Check if the function does not already exist to avoid redeclaration.
if ( ! function_exists( 'wpestate_property_action_category_callback_function' ) ):
    /**
     * Displays custom fields for editing an existing property_action_category term.
     *
     * @param object $tag The term object being edited.
     *
     * This function outputs additional fields for the 'property_action_category' taxonomy edit form, allowing users to set a page ID, featured image, and tagline.
     */
    function wpestate_property_action_category_callback_function( $tag ) {
        // Retrieve saved meta fields if available.
        $t_id = is_object( $tag ) ? $tag->term_id : '';
        $term_meta = get_option( "taxonomy_$t_id" );
        $pagetax = isset( $term_meta['pagetax'] ) ? esc_attr( $term_meta['pagetax'] ) : '';
        $category_featured_image = isset( $term_meta['category_featured_image'] ) ? esc_url( $term_meta['category_featured_image'] ) : '';
        $category_tagline = isset( $term_meta['category_tagline'] ) ? esc_html( stripslashes( $term_meta['category_tagline'] ) ) : '';
        $category_attach_id = isset( $term_meta['category_attach_id'] ) ? esc_attr( $term_meta['category_attach_id'] ) : '';

        // Display the form fields for editing.
        ?>
        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th scope="row" valign="top"><label for="term_meta[pagetax]"><?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?></label></th>
                    <td>
                        <input type="text" name="term_meta[pagetax]" class="postform" value="<?php echo $pagetax; ?>">
                        <p class="description"><?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="category_featured_image"><?php esc_html_e( 'Featured Image', 'wprentals-core' ); ?></label></th>
                    <td>
                        <input id="category_featured_image" type="text" class="wpestate_landing_upload postform" size="36" name="term_meta[category_featured_image]" value="<?php echo $category_featured_image; ?>">
                        <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload Image', 'wprentals-core' ); ?>">
                        <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="<?php echo $category_attach_id; ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="term_meta[category_tagline]"><?php esc_html_e( 'Category Tagline', 'wprentals-core' ); ?></label></th>
                    <td>
                        <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="<?php echo $category_tagline; ?>">
                    </td>
                </tr>
                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_action_category">
            </tbody>
        </table>
        <?php
    }
endif;

if ( ! function_exists( 'wpestate_property_action_category_callback_add_function' ) ):
    /**
     * Displays custom fields when creating a new property_action_category term.
     *
     * @param object $tag The term object being created.
     *
     * This function outputs additional fields for the 'property_action_category' taxonomy add form, allowing users to set a page ID, featured image, and tagline.
     */
    function wpestate_property_action_category_callback_add_function( $tag ) {
        // Retrieve saved meta fields if available.
        $pagetax = '';
        $category_featured_image = '';
        $category_tagline = '';
        $category_attach_id = '';

        // Display the form fields for adding a new term.
        ?>
        <div class="form-field">
            <label for="term_meta[pagetax]"><?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?></label>
            <input type="text" name="term_meta[pagetax]" class="postform" value="<?php echo $pagetax; ?>">
        </div>
        <div class="form-field">
            <label for="term_meta[category_featured_image]"><?php esc_html_e( 'Featured Image', 'wprentals-core' ); ?></label>
            <input id="category_featured_image" type="text" class="wpestate_landing_upload" size="36" name="term_meta[category_featured_image]" value="<?php echo $category_featured_image; ?>">
            <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload Image', 'wprentals-core' ); ?>">
            <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="<?php echo $category_attach_id; ?>">
        </div>
        <div class="form-field">
            <label for="term_meta[category_tagline]"><?php esc_html_e( 'Category Tagline', 'wprentals-core' ); ?></label>
            <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="<?php echo $category_tagline; ?>">
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_action_category">
        <?php
    }
endif;








if ( ! function_exists( 'wpestate_property_action_category_save_extra_fields_callback' ) ):
    /**
     * Saves custom fields for the property_action_category taxonomy.
     *
     * @param int $term_id The ID of the term being saved.
     *
     * This function saves the additional fields set for the 'property_action_category' taxonomy, including the page ID, featured image, and tagline.
     * It uses `wp_kses_post()` to sanitize the input values, ensuring only the allowed HTML is saved.
     */
    function wpestate_property_action_category_save_extra_fields_callback( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id" );
            $cat_keys = array_keys( $_POST['term_meta'] );
         

            foreach ( $cat_keys as $key ) {
                $key = sanitize_key( $key );
                if ( isset( $_POST['term_meta'][ $key ] ) ) {
                    $term_meta[ $key ] = wp_kses_post( $_POST['term_meta'][ $key ] );
                }
            }

            update_option( "taxonomy_$t_id", $term_meta );
        }
    }
endif;
?>
