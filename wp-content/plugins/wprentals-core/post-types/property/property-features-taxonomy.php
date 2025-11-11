<?php
/** MILLDONE
 * Property Features Admin Fields
 * src: post-types\property\property-features-admin.php
 * Handles the management of custom fields for the 'property_features' taxonomy. This includes adding, editing, and saving custom fields for terms.
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
 * - This file should be included as part of the WPRentals theme. It adds custom fields to the 'property_features' taxonomy for better management and extends the admin fields.
 */

// Add actions to add/edit form fields and save custom fields for 'property_features'.
add_action( 'property_features_edit_form_fields', 'wpestate_property_features_callback_function', 10, 2 );
add_action( 'property_features_add_form_fields', 'wpestate_property_features_callback_add_function', 10, 2 );
add_action( 'created_property_features', 'wpestate_property_features_save_extra_fields_callback', 10, 2 );
add_action( 'edited_property_features', 'wpestate_property_features_save_extra_fields_callback', 10, 2 );

if ( ! function_exists( 'wpestate_property_features_save_extra_fields_callback' ) ):
    /**
     * Saves custom fields for the 'property_features' taxonomy.
     *
     * @param int $term_id The ID of the term being saved.
     */
    function wpestate_property_features_save_extra_fields_callback( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id" );
            $term_meta = is_array( $term_meta ) ? $term_meta : array();
            $cat_keys = array_keys( $_POST['term_meta'] );
            $allowed_html = array();

            foreach ( $cat_keys as $key ) {
                $key = sanitize_key( $key );
                if ( isset( $_POST['term_meta'][ $key ] ) ) {
                    $term_meta[ $key ] = wp_kses( $_POST['term_meta'][ $key ], $allowed_html );
                }
            }

            // Save the updated term meta.
            update_option( "taxonomy_$t_id", $term_meta );
        }
    }
endif;

if ( ! function_exists( 'wpestate_property_features_callback_add_function' ) ):
    /**
     * Adds custom fields when creating a new 'property_features' term.
     *
     * @param object $tag The term object being created.
     */
    function wpestate_property_features_callback_add_function( $tag ) {
        // Retrieve default meta fields.
        $category_featured_image = '';
        $category_attach_id = '';
        ?>
        <div class="form-field">
            <label for="term_meta[category_featured_image]">
                <?php esc_html_e( 'SVG ICON - SVG ONLY!', 'wprentals-core' ); ?> -
                <a target="_blank" href="https://help.wprentals.org/article/how-to-add-icons-to-features-and-amenities/">
                    <?php esc_html_e( 'Video Tutorial', 'wprentals-core' ); ?>
                </a>
            </label>
            <input id="category_featured_image" type="text" size="36" class="wpestate_landing_upload" name="term_meta[category_featured_image]" value="<?php echo esc_attr( $category_featured_image ); ?>" />
            <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload SVG', 'wprentals-core' ); ?>" />
            <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="<?php echo esc_attr( $category_attach_id ); ?>" />
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_features">
        <?php
    }
endif;

if ( ! function_exists( 'wpestate_property_features_callback_function' ) ):
    /**
     * Adds custom fields when editing an existing 'property_features' term.
     *
     * @param object $tag The term object being edited.
     */
    function wpestate_property_features_callback_function( $tag ) {
        // Retrieve existing meta fields if available.
        $t_id = is_object( $tag ) ? $tag->term_id : '';
        $term_meta = get_option( "taxonomy_$t_id" );
        $category_featured_image = isset( $term_meta['category_featured_image'] ) ? esc_attr( $term_meta['category_featured_image'] ) : '';
        $category_attach_id = isset( $term_meta['category_attach_id'] ) ? esc_attr( $term_meta['category_attach_id'] ) : '';
        ?>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="category_featured_image">
                            <?php esc_html_e( 'SVG ICON - SVG ONLY!', 'wprentals-core' ); ?> -
                            <a target="_blank" href="https://help.wprentals.org/article/how-to-add-icons-to-features-and-amenities/">
                                <?php esc_html_e( 'Video Tutorial', 'wprentals-core' ); ?>
                            </a>
                        </label>
                    </th>
                    <td>
                        <input id="category_featured_image" type="text" class="wpestate_landing_upload" size="36" name="term_meta[category_featured_image]" value="<?php echo $category_featured_image; ?>" />
                        <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload SVG', 'wprentals-core' ); ?>" />
                        <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="<?php echo $category_attach_id; ?>" />
                    </td>
                </tr>
                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_features">
            </tbody>
        </table>
        <?php
    }
endif;
?>
