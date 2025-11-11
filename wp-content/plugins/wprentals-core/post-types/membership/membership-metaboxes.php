<?php 

/**
 * Membership Metaboxes
 *
 * Custom metabox fields for membership packages.
 *
 * @package WPRentals
 * @subpackage Membership
 */




/**
 * Add package metaboxes
 *
 * @function wpestate_add_pack_metaboxes
 */

if( !function_exists('wpestate_add_pack_metaboxes') ):
    function wpestate_add_pack_metaboxes() {
        add_meta_box(  'estate_membership-sectionid',  esc_html__(  'Package Details', 'wprentals-core' ),'membership_package','membership_package' ,'normal','default'
    );
}
endif; // end   wpestate_add_pack_metaboxes

/**
 * Package metabox display
 *
 * @function membership_package
 * @param object $post Post object
 */

if( !function_exists('membership_package') ):
    function membership_package( $post ) {
	    wp_nonce_field( plugin_basename( __FILE__ ), 'estate_pack_noncename' );
	    global $post;
                $unlimited_days     =   esc_html(get_post_meta($post->ID, 'mem_days_unl', true));
                $unlimited_lists    =   esc_html(get_post_meta($post->ID, 'mem_list_unl', true));
                $billing_periods    =   array('Day','Week','Month','Year');

                $billng_saved       =   esc_html(get_post_meta($post->ID, 'biling_period', true));
                $billing_select     =   '<select name="biling_period" width="200px" id="billing_period">';
                foreach($billing_periods as $period){
                    $billing_select.='<option value="'.$period.'" ';
                    if($billng_saved==$period){
                         $billing_select.=' selected="selected" ';
                    }
                    $billing_select.='>'.$period.'</option>';
                }
                $billing_select.='</select>';

                $check_unlimited_lists='';
                if($unlimited_lists==1){
                    $check_unlimited_lists=' checked="checked"  ';
                }


                $visible_array=array('yes','no');
                $visible_saved=get_post_meta($post->ID, 'pack_visible', true);
                $visible_select='<select id="pack_visible" name="pack_visible">';

                foreach($visible_array as $option){
                    $visible_select.='<option value="'.$option.'" ';
                    if($visible_saved==$option){
                        $visible_select.=' selected="selected" ';
                    }
                    $visible_select.='>'.$option.'</option>';
                }
                $visible_select.='</select>';




                print'
                <p class="meta-options">
                    <label for="biling_period">'.esc_html__( 'Billing Time Unit :','wprentals-core').'</label><br />
                    '.$billing_select.'
                </p>

                <p class="meta-options">
                    <label for="billing_freq">'.esc_html__( 'Bill every x units','wprentals-core').' </label><br />
                    <input type="text" id="billing_freq" size="58" name="billing_freq" value="'.  intval(get_post_meta($post->ID, 'billing_freq', true)).'">
                </p>

                <p class="meta-options">
                    <label for="pack_listings">'.esc_html__( 'How many listings are included?','wprentals-core').'</label><br />
                    <input type="text" id="pack_listings" size="58" name="pack_listings" value="'.  esc_html(get_post_meta($post->ID, 'pack_listings', true)).'">

                    <input type="hidden" name="mem_list_unl" value=""/>
                    <input type="checkbox"  id="mem_list_unl" name="mem_list_unl" value="1" '.$check_unlimited_lists.'  />
                    <label for="mem_list_unl">'.esc_html__( 'Unlimited listings ?','wprentals-core').'</label>
                </p>

                <p class="meta-options">
                    <label for="pack_featured_listings">'.esc_html__( 'How many Featured listings are included?','wprentals-core').'</label><br />
                    <input type="text" id="pack_featured_listings" size="58" name="pack_featured_listings" value="'.  esc_html(get_post_meta($post->ID, 'pack_featured_listings', true)).'">
                </p>


                <p class="meta-options">
                    <label for="pack_price">'.esc_html__( 'Package Price in ','wprentals-core'). ' ' . wpestate_curency_submission_pick().'</label><br />
                    <input type="text" id="pack_price" size="58" name="pack_price" value="'.  esc_html(get_post_meta($post->ID, 'pack_price', true)).'">
		</p>

                <p class="meta-options">
                    <label for="pack_visible">'.esc_html__( 'Is visible? ','wprentals-core').'</label><br />
                    '.$visible_select.'
		</p>

                <p class="meta-options">
                    <label for="pack_stripe_id">Package Stripe id (ex:gold-pack) </label><br>
                    <input type="text" id="pack_stripe_id" size="58" name="pack_stripe_id" value="'.esc_html(get_post_meta($post->ID, 'pack_stripe_id', true)).'">
                </p>
         ';
    }
endif; // end   membership_package