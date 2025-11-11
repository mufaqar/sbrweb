<?php
/** MILLDONE
 * Property Admin Columns
 * src: post-types\property\property-admin-columns.php
 * Handles custom columns for the 'estate_property' post type in the WordPress admin area. This includes adding, populating, and sorting custom columns.
 *
 * @package WPRentals Core
 * @subpackage Property
 * @since 4.0.0
 *
 * @dependencies
 * - WordPress post type functions
 * - WPRentals theme options and settings
 *
 * Usage:
 * - This file should be included as part of the WPRentals theme. It adds custom columns to the "estate_property" post type in the admin area, providing additional information about each property.
 */

// Add filter to modify columns for the 'estate_property' post type.
add_filter( 'manage_edit-estate_property_columns', 'wpestate_my_columns' );

if ( ! function_exists( 'wpestate_my_columns' ) ):
    /**
     * Modifies the columns displayed in the admin listing for 'estate_property'.
     *
     * @param array $columns The existing columns.
     * @return array Modified columns with additional custom fields.
     */
    function wpestate_my_columns( $columns ) {
        // Remove the comments column.
        unset( $columns['comments'] );

        // Create slices of columns to insert custom columns in specific positions.
        $slice = array_slice( $columns, 2, 2 );
        unset( $slice['comments'] );
        $splice = array_splice( $columns, 2 );

        // Add custom columns.
   
        $columns['estate_image'] = esc_html__( 'Image', 'wprentals-core' );
        $columns['estate_action'] = esc_html__( 'Action', 'wprentals-core' );
        $columns['estate_category'] = esc_html__( 'Category', 'wprentals-core' );
        $columns['estate_autor'] = esc_html__( 'User', 'wprentals-core' );
        $columns['estate_status'] = esc_html__( 'Status', 'wprentals-core' );
        $columns['estate_price'] = esc_html__( 'Price night/day/hour', 'wprentals-core' );
        $columns['estate_featured'] = esc_html__( 'Featured', 'wprentals-core' );

        return array_merge( $columns, array_reverse( $slice ) );
    }
endif;










// Add action to populate custom columns.
add_action( 'manage_posts_custom_column', 'wpestate_populate_columns' );

if ( ! function_exists( 'wpestate_populate_columns' ) ):
    /**
     * Populates the custom columns for 'estate_property' in the admin listing.
     *
     * @param string $column The name of the column being populated.
     */
    function wpestate_populate_columns( $column ) {
        $the_id = get_the_ID();

        if ( 'estate_id' == $column ) {
            echo esc_html( $the_id );
        }

        if ( 'estate_image' == $column ) {
            echo get_the_post_thumbnail( $the_id, 'wpestate_user_thumb' );
        }

        if ( 'estate_featured' == $column ) {
            $is_featured = intval( get_post_meta( $the_id, 'prop_featured', true ) ) === 1 ? 'Yes' : 'No';
            echo esc_html( $is_featured );
        }

        if ( 'estate_status' == $column ) {
            $estate_status = get_post_status( $the_id );
            echo $estate_status == 'publish' ? esc_html__( 'Published', 'wprentals-core' ) : esc_html( $estate_status );

            $pay_status = get_post_meta( $the_id, 'pay_status', true );
            if ( ! empty( $pay_status ) ) {
                echo ' | ' . esc_html( $pay_status );
            }
        }

        if ( 'estate_autor' == $column ) {
            $user_id = wpsestate_get_author( $the_id );
            $estate_autor = get_the_author_meta( 'display_name', $user_id );
            echo '<a href="' . esc_url( get_edit_user_link( $user_id ) ) . '">' . esc_html( $estate_autor ) . '</a>';
        }

        if ( 'estate_action' == $column ) {
            $estate_action = get_the_term_list( $the_id, 'property_action_category', '', ', ', '' );
            echo wp_kses_post( $estate_action );
        }
        elseif ( 'estate_category' == $column ) {
            $estate_category = get_the_term_list( $the_id, 'property_category', '', ', ', '' );
            echo wp_kses_post( $estate_category );
        }

        if ( 'estate_price' == $column ) {
            $wpestate_currency = esc_html( wprentals_get_option( 'wp_estate_currency_label_main', '' ) );
            $wpestate_where_currency = esc_html( wprentals_get_option( 'wp_estate_where_currency_symbol', '' ) );
            wpestate_show_price( $the_id, $wpestate_currency, $wpestate_where_currency, 0 );
        }
    }
endif;





// Add filter to make certain columns sortable.
add_filter( 'manage_edit-estate_property_sortable_columns', 'wprentals_sort_columns' );

if ( ! function_exists( 'wprentals_sort_columns' ) ):
    /**
     * Makes certain columns sortable in the 'estate_property' admin listing.
     *
     * @param array $columns The existing columns.
     * @return array Modified columns with sortable options.
     */
    function wprentals_sort_columns( $columns ) {
        $columns['estate_autor'] = 'estate_autor';
        $columns['estate_price'] = 'estate_price';
        return $columns;
    }
endif;






// Add filter to modify the orderby request for sorting custom columns.
add_filter( 'request', 'wprentals_sort_custom_columns' );

if ( ! function_exists( 'wprentals_sort_custom_columns' ) ):
    /**
     * Modifies the query to sort by custom columns in the 'estate_property' admin listing.
     *
     * @param array $vars The query variables.
     * @return array Modified query variables for custom sorting.
     */
    function wprentals_sort_custom_columns( $vars ) {
        if ( isset( $vars['orderby'] ) && 'estate_price' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => 'property_price',
                'orderby'  => 'meta_value_num'
            ) );
        }

        if ( isset( $vars['orderby'] ) && 'estate_autor' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'orderby' => 'author'
            ) );
        }

        return $vars;
    }
endif;
?>
