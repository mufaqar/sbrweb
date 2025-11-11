<?php

/**
 * Permission Callback for Creating a New Property via REST API
 *
 * This function ensures that the user is authenticated via a JWT token 
 * and has the necessary permissions to create a new property.
 *
 * @param WP_REST_Request $request The current REST request.
 * @return true|WP_Error True if the user has permission, or WP_Error if not.
 */
function wprentals_check_permissions_for_posting(WP_REST_Request $request) {
    // Verify the JWT token
    $userID = apply_filters('determine_current_user', null);
    if (!$userID) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    wp_set_current_user($userID);

    // Fetch the current user details
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;

    // Check if the user is logged in
    if (!$userID || !is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            esc_html__('You must be logged in to create a property.', 'wprentals-core'),
            ['status' => 403]
        );
    }

    // Check user capabilities for posting
    if (!current_user_can('publish_estate_properties')) {
        return new WP_Error(
            'rest_forbidden',
            esc_html__('You do not have permission to create a property.', 'wprentals-core'),
            ['status' => 403]
        );
    }

    $paid_submission_status    = esc_html ( wprentals_get_option('wp_estate_paid_submission','') );
    $user_number_listings      = intval(wpestate_get_current_user_listings($userID));
    if (  $paid_submission_status== 'membership' && $user_number_listings > 0) { // if user can submit
        return true;
    }else{
        return new WP_Error(
            'rest_forbidden',
            esc_html__('Your membership does not allow you to create a new property.', 'wprentals-core'),
            [
                'status' => 403,

            ]
        );
    }


    return true;
}




/**
 * Create a new property.
 * Handles the creation of a new property post with taxonomies and metadata.
 *
 * @param WP_REST_Request $request The REST API request containing the property data.
 * @return WP_REST_Response|
 */
function wprentals_create_property(WP_REST_Request $request) {
    // Retrieve mandatory fields from options
    $mandatory_fields = wprentals_get_option('wp_estate_mandatory_page_fields', '');
  
    $input_data = wprentals_parse_request_params($request);

    // Validate mandatory fields
    foreach ($mandatory_fields as $field) {
        if (empty($input_data[$field])) {
            return new WP_Error(
                'rest_missing_field',
                __('Missing mandatory field: ' . $field),
                ['status' => 400]
            );
        }
    }

    // Set current user
    $current_user = get_current_user_id();
  

    // Create the property post
    $post_id = wp_insert_post([
        'post_type'   => 'estate_property',
        'post_title'  => $input_data['title'],
        'post_status' => 'publish',
        'post_author' => $current_user
    ]);

    if (is_wp_error($post_id)) {
        return $post_id;
    }
    $paid_submission_status    = esc_html ( wprentals_get_option('wp_estate_paid_submission','') );
    // Update user listing count
    if( $paid_submission_status == 'membership'){ // update pack status
        wpestate_update_listing_no($current_user);
    }

    // Process taxonomies and meta fields
    foreach ($input_data as $key => $value) {
        if (taxonomy_exists($key)) {
            if (is_array($value)) {
                wp_set_object_terms($post_id, $value, $key);
            }
        } else {
            if ($key === 'extra_pay_options') {
                wprentals_process_extra_pay_options($post_id, $value);
            } elseif ($key === 'beds_options') {
                wprentals_process_beds_options($post_id, $value);
            } elseif ($key === 'custom_fields') {
                wprentals_process_custom_fields($post_id, $value);
            } elseif ($key === 'ical_feeds') {
                wprentals_process_ical_feeds($post_id, $value);
            } elseif ($key === 'custom_price') {
                wprentals_process_custom_price($post_id, $value);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }
    }

    // Process images
    if (!empty($input_data['images']) && is_array($input_data['images'])) {
        wprentals_process_images($post_id, $input_data['images']);
    }

    // Return success response
    return rest_ensure_response([
        'status'       => 'success',
        'property_id'  => $post_id,
    ]);
}

/**
 * Process extra payment options and save them as post meta.
 *
 * @param int $post_id The ID of the property post.
 * @param array $extra_pay_options The extra payment options data.
 */
function wprentals_process_extra_pay_options($post_id, $extra_pay_options) {
    $extra_pay_values = [];
    if (is_array($extra_pay_options)) {
        foreach ($extra_pay_options as $pay_option) {
            $option = explode('|', $pay_option);
            $extra_pay_values[] = $option;
        }
    }
    update_post_meta($post_id, 'extra_pay_options', $extra_pay_values);
}

/**
 * Process beds options and save them as post meta.
 *
 * @param int $post_id The ID of the property post.
 * @param array $beds_options The bed options data.
 */
function wprentals_process_beds_options($post_id, $beds_options) {
    $bed_types = esc_html(wprentals_get_option('wp_estate_bed_list', ''));
    $bed_types_array = explode(',', $bed_types);
    $beds_options_array = [];

    $i = 0;
    while ($i < count($beds_options)) {
        $index_bed_options = $i % count($bed_types_array);
        $beds_options_array[trim(wpestate_convert_cyrilic($bed_types_array[$index_bed_options]))][] = intval($beds_options[$i]);
        $i++;
    }

    update_post_meta($post_id, 'property_bedrooms_details', $beds_options_array);
}

/**
 * Process custom fields and save them as post meta.
 *
 * @param int $post_id The ID of the property post.
 * @param array $custom_fields The array of custom field data containing slugs and values.
 */
function wprentals_process_custom_fields($post_id, $custom_fields) {
    if (is_array($custom_fields)) {
        foreach ($custom_fields as $field) {
            if (isset($field['slug'], $field['value'])) {
                update_post_meta($post_id, $field['slug'], sanitize_text_field($field['value']));
            }
        }
    }
}

/**
 * Process iCal feeds and save them as post meta.
 *
 * @param int $post_id The ID of the property post.
 * @param array $ical_feeds The array of iCal feed data containing URLs and names.
 */
function wprentals_process_ical_feeds($post_id, $ical_feeds) {
    $all_feeds = [];
    if (is_array($ical_feeds)) {
        foreach ($ical_feeds as $feed) {
            if (!empty($feed['feed']) && !empty($feed['name'])) {
                $tmp_feed_array = [
                    'feed' => esc_url_raw($feed['feed']),
                    'name' => sanitize_text_field($feed['name']),
                ];
                $all_feeds[] = $tmp_feed_array;
            }
        }
    }

    if (!empty($all_feeds)) {
        update_post_meta($post_id, 'property_icalendar_import_multi', $all_feeds);
        if (function_exists('wpestate_import_calendar_feed_listing_global')) {
            wpestate_import_calendar_feed_listing_global($post_id);
        }
    }
}

/**
 * Process custom prices and save them as post meta.
 *
 * @param int $post_id The ID of the property post.
 * @param array $custom_price The custom price array containing date ranges and details.
 */
function wprentals_process_custom_price($post_id, $custom_price) {
    $price_array = [];
    $mega_details_array = [];

    if (is_array($custom_price)) {
        foreach ($custom_price as $price_entry) {
            if (isset($price_entry['date_range'], $price_entry['price'], $price_entry['details'])) {
                // Convert dates to timestamps
                $from_date = strtotime($price_entry['date_range']['from']);
                $to_date = strtotime($price_entry['date_range']['to']);

                // Validate date range
                if ($from_date && $to_date && $from_date <= $to_date) {
                    // Populate prices and details for each day in the range
                    while ($from_date <= $to_date) {
                        if (!empty($price_entry['price'])) {
                            $price_array[$from_date] = floatval($price_entry['price']);
                        }
                        $mega_details_array[$from_date] = $price_entry['details'];
                        $from_date = strtotime('+1 day', $from_date);
                    }
                }
            }
        }
    }

    // Clean old data (older than 30 days)
    $threshold = time() - (30 * 24 * 60 * 60); // 30 days ago
    foreach ($price_array as $timestamp => $price) {
        if ($timestamp < $threshold) {
            unset($price_array[$timestamp], $mega_details_array[$timestamp]);
        }
    }

    // Save to post meta
    update_post_meta($post_id, 'custom_price', $price_array);
    update_post_meta($post_id, 'mega_details', $mega_details_array);
}




/**
 * Process images and save them as attachments in WordPress.
 *
 * @param int $post_id The ID of the property post.
 * @param array $images The array of image data containing IDs and URLs.
 */
function wprentals_process_images($post_id, $images) {
    $featured_image_set = false;
    foreach ($images as $image) {
        if (!empty($image['id']) && !empty($image['url'])) {
            // Validate that the file is an image
            $file_info = pathinfo($image['url']);
            $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($file_info['extension']), $valid_extensions)) {
                continue;
            }

            // Download the image to the WordPress uploads directory
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($image['url']);
            $filename = basename($image['url']);

            if ($image_data) {
                $file_path = $upload_dir['path'] . '/' . $filename;
                file_put_contents($file_path, $image_data);

                // Prepare the file for attachment
                $wp_filetype = wp_check_filetype($filename, null);
                if (!in_array($wp_filetype['ext'], $valid_extensions)) {
                    unlink($file_path); // Remove invalid files
                    continue;
                }

                $attachment = [
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name($filename),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                // Insert the attachment
                $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);

                // Generate attachment metadata and update
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
                wp_update_attachment_metadata($attachment_id, $attach_data);

                // Set the featured image if not set
                if (!$featured_image_set) {
                    set_post_thumbnail($post_id, $attachment_id);
                    $featured_image_set = true;
                }
            }
        }
    }
}
