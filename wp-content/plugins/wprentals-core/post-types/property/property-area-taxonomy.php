<?php
/** MILLDONE
 * Property Area Admin Columns
 * src: post-types\property\property-area-admin-columns.php
 * Handles the management of custom fields for the 'property_area' taxonomy. This includes adding custom fields during creation/editing of terms, displaying them, and saving their data.
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
 * - This file should be included as part of the WPRentals theme. It adds custom fields to the 'property_area' taxonomy for better management and extends the admin columns.
 */

// Add actions to add/edit form fields and save custom fields for 'property_area'.
add_action( 'property_area_edit_form_fields', 'wpestate_property_area_callback_function', 10, 2 );
add_action( 'property_area_add_form_fields', 'wpestate_property_area_callback_add_function', 10, 2 );
add_action( 'created_property_area', 'wpestate_property_area_save_extra_fields_callback', 10, 2 );
add_action( 'edited_property_area', 'wpestate_property_area_save_extra_fields_callback', 10, 2 );

// Add custom columns to the 'property_area' taxonomy.
add_filter('manage_edit-property_area_columns', 'wprentals_custom_columns_property_area');
add_filter('manage_property_area_custom_column', 'wprentals_custom_columns_content_taxonomy', 10, 3);

if ( ! function_exists( 'wprentals_custom_columns_property_area' ) ):
    /**
     * Adds custom columns to the 'property_area' taxonomy admin list.
     *
     * @param array $new_columns The existing columns.
     * @return array Modified columns with new custom fields.
     */
    function wprentals_custom_columns_property_area( $new_columns ) {
        $new_columns = array(
            'cb'            => '<input type="checkbox" />',
            'name'          => esc_html__( 'Name', 'wprentals-core' ),
            'city'          => esc_html__( 'City', 'wprentals-core' ),
            'slug'          => esc_html__( 'Slug', 'wprentals-core' ),
            'posts'         => esc_html__( 'Posts', 'wprentals-core' ),
            'id'            => esc_html__( 'ID', 'wprentals-core' ),
        );
        return $new_columns;
    }
endif;

if ( ! function_exists( 'wprentals_custom_columns_content_taxonomy' ) ):
    /**
     * Populates the custom columns for the 'property_area' taxonomy in the admin list.
     *
     * @param string $out The output of the column.
     * @param string $column_name The name of the column being populated.
     * @param int $term_id The ID of the current term.
     */
    function wprentals_custom_columns_content_taxonomy( $out, $column_name, $term_id ) {
        if ( 'city' == $column_name ) {
            $term_meta = get_option( "taxonomy_$term_id" );
            if ( isset( $term_meta['cityparent'] ) ) {
                echo esc_html( $term_meta['cityparent'] );
            }
        }
        if ( 'id' == $column_name ) {
            echo esc_html( $term_id );
        }
    }
endif;

if ( ! function_exists( 'wpestate_property_area_callback_add_function' ) ):
    /**
     * Adds custom fields when creating a new 'property_area' term.
     *
     * @param object $tag The term object being created.
     */
    function wpestate_property_area_callback_add_function( $tag ) {
        // Retrieve existing meta fields if available.
        $cityparent = wpestate_get_all_cities();
        $pagetax = '';
        $category_featured_image = '';
        $category_tagline = '';
        $category_attach_id = '';

        ?>
        <div class="form-field">
            <label for="term_meta[cityparent]"> <?php esc_html_e( 'Which city has this area', 'wprentals-core' ); ?> </label>
            <select name="term_meta[cityparent]" class="postform">
                <?php echo wp_kses( $cityparent, array( 'option' => array( 'value' => array(), 'selected' => array() ) ) ); ?>
            </select>
        </div>
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
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_area">
        <?php
    }
endif;






if ( ! function_exists( 'wpestate_property_area_callback_function' ) ):
    /**
     * Adds custom fields when editing an existing 'property_area' term.
     *
     * @param object $tag The term object being edited.
     */
    function wpestate_property_area_callback_function( $tag ) {
        // Retrieve existing meta fields if available.
        $t_id = is_object( $tag ) ? $tag->term_id : '';
        $term_meta = get_option( "taxonomy_$t_id" );
        $cityparent = isset( $term_meta['cityparent'] ) ? $term_meta['cityparent'] : '';
        $pagetax = isset( $term_meta['pagetax'] ) ? $term_meta['pagetax'] : '';
        $category_featured_image = isset( $term_meta['category_featured_image'] ) ? $term_meta['category_featured_image'] : '';
        $category_tagline = isset( $term_meta['category_tagline'] ) ? stripslashes( $term_meta['category_tagline'] ) : '';
        $category_attach_id = isset( $term_meta['category_attach_id'] ) ? $term_meta['category_attach_id'] : '';
        $cityparent = wpestate_get_all_cities( $cityparent );

        ?>
        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th scope="row" valign="top"><label for="term_meta[cityparent]"> <?php esc_html_e( 'Which city has this area', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <select name="term_meta[cityparent]" class="postform">
                            <?php echo wp_kses( $cityparent, array( 'option' => array( 'value' => array(), 'selected' => array() ) ) ); ?>
                        </select>
                        <p class="description"> <?php esc_html_e( 'City that has this area', 'wprentals-core' ); ?> </p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label for="term_meta[pagetax]"> <?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <input type="text" name="term_meta[pagetax]" class="postform" value="<?php echo esc_attr( $pagetax ); ?>">
                        <p class="description"> <?php esc_html_e( 'Page ID for this term', 'wprentals-core' ); ?> </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="logo_image"> <?php esc_html_e( 'Featured Image', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <input id="category_featured_image" type="text" class="wpestate_landing_upload" size="36" name="term_meta[category_featured_image]" value="<?php echo esc_attr( $category_featured_image ); ?>" />
                        <input id="category_featured_image_button" type="button" class="upload_button button category_featured_image_button" value="<?php esc_html_e( 'Upload Image', 'wprentals-core' ); ?>" />
                        <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="<?php echo esc_attr( $category_attach_id ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="term_meta[category_tagline]"> <?php esc_html_e( 'Category Tagline', 'wprentals-core' ); ?> </label></th>
                    <td>
                        <input id="category_tagline" type="text" size="36" name="term_meta[category_tagline]" value="<?php echo esc_attr( $category_tagline ); ?>">
                    </td>
                </tr>
                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_area">
            </tbody>
        </table>
        <?php
    }
endif;




if ( ! function_exists( 'wpestate_get_all_cities' ) ):
    /**
     * Generates a list of all cities for the 'property_city' taxonomy.
     *
     * @param string $selected The selected city (if any).
     * @return string HTML for the cities dropdown options.
     */
    function wpestate_get_all_cities( $selected = '' ) {
        $taxonomy = 'property_city';
        $args = array( 'hide_empty' => false );
        $tax_terms = get_terms( $taxonomy, $args );
        $select_city = '';

        foreach ( $tax_terms as $tax_term ) {
            $select_city .= '<option value="' . esc_attr( $tax_term->name ) . '"';
            if ( $tax_term->name == $selected ) {
                $select_city .= ' selected="selected"';
            }
            $select_city .= '>' . esc_html( $tax_term->name ) . '</option>';
        }
        return $select_city;
    }
endif;





if ( ! function_exists( 'wpestate_property_area_save_extra_fields_callback' ) ):
    /**
     * Saves custom fields for the 'property_area' taxonomy.
     *
     * @param int $term_id The ID of the term being saved.
     */
    function wpestate_property_area_save_extra_fields_callback( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id" );
            $term_meta = is_array( $term_meta ) ? $term_meta : array();
            $cat_keys = array_keys( $_POST['term_meta'] );
 

            foreach ( $cat_keys as $key ) {
                $key = sanitize_key( $key );
                if ( isset( $_POST['term_meta'][ $key ] ) ) {
                    $term_meta[ $key ] = wp_kses_post( $_POST['term_meta'][ $key ], $allowed_html );
                }
            }

            // Save the updated term meta.
            update_option( "taxonomy_$t_id", $term_meta );
        }
    }
endif;
?>
