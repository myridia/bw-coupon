<?php
/**
 * BWC Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types).
 *
 * @author      BWC
 * @package     BWC/Meta Boxes
 */

if ( ! defined( 'ABSPATH' )):
  exit;
endif;

/**
 * BWC_Data Class.
 */
class BWC_Data {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post Post object.
     * https://docs.woocommerce.com/wc-apidocs/function-woocommerce_wp_textarea_input.html 
	 */


	public static function output( $post ) {
        load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  //Load the WP set language 
		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

		$giftcoupon                 = get_post_meta( $post->ID, 'giftcoupon' );
		$giftcoupon_html            = get_post_meta( $post->ID, 'giftcoupon_html' );
		$type                       = get_post_meta( $post->ID, 'discount_type' );
		$amount                     = get_post_meta( $post->ID, 'coupon_amount' );
		$individual_use             = get_post_meta( $post->ID, 'individual_use' );
		$product_ids                = get_post_meta( $post->ID, 'product_ids' );
		$exclude_product_ids        = get_post_meta( $post->ID, 'exclude_product_ids' );
		$usage_limit                = get_post_meta( $post->ID, 'usage_limit' );
		$usage_limit_per_user       = get_post_meta( $post->ID, 'usage_limit_per_user' );
		$limit_usage_to_x_items     = get_post_meta( $post->ID, 'limit_usage_to_x_items' );
        $timeout_after_purchase     = get_post_meta( $post->ID, 'timeout_after_purchase' );
        $couponcode_with_order_no   = get_post_meta( $post->ID, 'couponcode_with_order_no' );
		$apply_before_tax           = get_post_meta( $post->ID, 'apply_before_tax' );
		$free_shipping              = get_post_meta( $post->ID, 'free_shipping' );
		$exclude_sale_items         = get_post_meta( $post->ID, 'exclude_sale_items' );
		$product_categories         = get_post_meta( $post->ID, 'product_categories' );
		$exclude_product_categories = get_post_meta( $post->ID, 'exclude_product_categories' );
		$minimum_amount             = get_post_meta( $post->ID, 'minimum_amount' );
		$maximum_amount             = get_post_meta( $post->ID, 'maximum_amount' );
		$customer_email             = get_post_meta( $post->ID, 'customer_email' );

		$giftcoupon                 = reset( $giftcoupon );
		$giftcoupon_html            = reset( $giftcoupon_html );
		$type                       = reset( $type );
		$amount                     = reset( $amount );
		$individual_use             = reset( $individual_use );
		$product_ids                = reset( $product_ids );
		$exclude_product_ids        = reset( $exclude_product_ids );
		$usage_limit                = reset( $usage_limit );
		$usage_limit_per_user       = reset( $usage_limit_per_user );
		$limit_usage_to_x_items     = reset( $limit_usage_to_x_items );
		$timeout_after_purchase     = reset( $timeout_after_purchase );
		$couponcode_with_order_no   = reset( $couponcode_with_order_no );

		$apply_before_tax           = reset( $apply_before_tax );
		$free_shipping              = reset( $free_shipping );
		$exclude_sale_items         = reset( $exclude_sale_items );
		$product_categories         = reset( $product_categories );
		$exclude_product_categories = reset( $exclude_product_categories );
		$minimum_amount             = reset( $minimum_amount );
		$maximum_amount             = reset( $maximum_amount );
		$customer_email             = reset( $customer_email );

		if ( ! empty( $product_ids ) ):
			$product_ids = explode( ',', $product_ids );
		endif;

		if ( ! empty( $exclude_product_ids ) ):
			$exclude_product_ids = explode( ',', $exclude_product_ids );
		endif;

		?>
        <div class="bwc_product_as_coupon panel-wrap coupon_data">
        <?php 
				// Gift Coupon.
				woocommerce_wp_checkbox( array(
					'id'          => 'giftcoupon',
					'label'       => '',
					'description'       => __( 'Product is a coupon', "bw-coupon" )
				) );

        ?>
        </div>
		<div id="coupon_options" class="panel-wrap coupon_data bwc_coupon_options">
			<div class="wc-tabs-back"></div>
			<ul class="coupon_data_tabs wc-tabs" style="display:none;">
				<?php
				$coupon_data_tabs = apply_filters( 'woocommerce_coupon_data_tabs', array(
					'general'           => array(
						'label'  => __( 'General', 'woocommerce' ),
						'target' => 'general_coupon_data',
						'class'  => 'general_coupon_data',
					),
					'usage_restriction' => array(
						'label'  => __( 'Usage restriction', 'woocommerce' ),
						'target' => 'usage_restriction_coupon_data',
						'class'  => '',
					),
					'usage_limit'       => array(
						'label'  => __( 'Usage limits', 'woocommerce' ),
						'target' => 'usage_limit_coupon_data',
						'class'  => '',
					),
				) );
				foreach ( $coupon_data_tabs as $key => $tab ) {
					?>
					<li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
						<a href="#<?php echo $tab['target']; ?>"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
					</li>
				<?php
				}
				?>
			</ul>
			<div id="general_coupon_data" class="panel woocommerce_options_panel">
			<?php


				// Amount.
				woocommerce_wp_text_input( array(
					'id'          => 'coupon_amount',
					'label'       => __( 'Coupon amount', 'woocommerce' ),
					'placeholder' => wc_format_localized_price( 0 ),
					'description' => __( 'Value of the coupon.', 'woocommerce' ),
					'data_type'   => 'price',
					'desc_tip'    => true,
				) );



				// Free Shipping.
				if ( wc_shipping_enabled() ) {
					woocommerce_wp_checkbox( array(
						'id'          => 'free_shipping',
						'label'       => __( 'Allow free shipping', 'woocommerce' ),
						'description' => sprintf( __( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/' ),
					) );
				}

				// Timeout after purchase .
				woocommerce_wp_select( array(
					'id'                => 'timeout_after_purchase',
					'value'             => $timeout_after_purchase,
                    'options'           => bwc_get_timeout_after_purschase_options(),
					'label'             => __( 'Timeout after purchase', 'bw-coupon' ),
					'placeholder'       => '',
					'description'       => __( 'How long will the Coupon be valid after the customer purchase it.', 'bw-coupon' ),
					'desc_tip'    => true,
				) );


				// Coupon Code With Order No
				woocommerce_wp_checkbox( array(
					'id'          => 'couponcode_with_order_no',
					'label'       => __( 'Coupon incl Order No ', "bw-coupon" ),
					'description' => __( 'Coupon Code will include the Order Number', "bw-coupon" ),
					'desc_tip'    => true,
				) );

			?></div>
			<div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel"><?php
				echo '<div class="options_group">';

				// minimum spend.
				woocommerce_wp_text_input( array(
					'id'          => 'minimum_amount',
					'label'       => __( 'Minimum spend', 'woocommerce' ),
					'placeholder' => __( 'No minimum', 'woocommerce' ),
					'description' => __( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'woocommerce' ),
					'data_type'   => 'price',
					'desc_tip'    => true,
				) );

				// maximum spend.
				woocommerce_wp_text_input( array(
					'id'          => 'maximum_amount',
					'label'       => __( 'Maximum spend', 'woocommerce' ),
					'placeholder' => __( 'No maximum', 'woocommerce' ),
					'description' => __( 'This field allows you to set the maximum spend (subtotal) allowed when using the coupon.', 'woocommerce' ),
					'data_type'   => 'price',
					'desc_tip'    => true,
				) );

				// Individual use.
				woocommerce_wp_checkbox( array(
					'id'          => 'individual_use',
					'label'       => __( 'Individual use only', 'woocommerce' ),
					'description' => __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woocommerce' ),
				) );

				// Exclude Sale Products.
				woocommerce_wp_checkbox( array(
					'id'          => 'exclude_sale_items',
					'label'       => __( 'Exclude sale items', 'woocommerce' ),
					'description' => __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'woocommerce' ),
				) );
				echo '</div><div class="options_group">';

				// Product ids.
				?>
				<p class="form-field"><label><?php esc_html_e( 'Products', 'woocommerce' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations">
					<?php
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					?>
				</select> <?php echo wc_help_tip( __( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?></p>
				<?php

				// Exclude Product ids.
				?>
				<p class="form-field"><label><?php esc_html_e( 'Exclude products', 'woocommerce' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="exclude_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations">
					<?php
						$product_ids = $exclude_product_ids ? $exclude_product_ids != false : [];
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					?>
				</select> <?php echo wc_help_tip( __( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?></p>
				<?php
				echo '</div><div class="options_group">';

				// Categories.
				?>
				<p class="form-field"><label for="product_categories"><?php esc_html_e( 'Product categories', 'woocommerce' ); ?></label>
				<select id="product_categories" name="product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any category', 'woocommerce' ); ?>">
					<?php
						$category_ids = $product_categories ? $product_categories != false: [];
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

						if ( $categories ) {
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
							}
						}
					?>
				</select> <?php echo wc_help_tip( __( 'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?></p>
				<?php

				// Exclude Categories.
				?>
				<p class="form-field"><label for="exclude_product_categories"><?php esc_html_e( 'Exclude categories', 'woocommerce' ); ?></label>
				<select id="exclude_product_categories" name="exclude_product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
					<?php
						$category_ids = $exclude_product_categories ? $exclude_product_categories !=  false : [] ;
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
						if ( $categories ) {
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
							}
						}
					?>
				</select> <?php echo wc_help_tip( __( 'Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?></p>
				<?php
				echo '</div><div class="options_group">';

				// Customers
				woocommerce_wp_text_input( array(
					'id'                => 'customer_email',
					'label'             => __( 'Allowed emails', 'woocommerce' ),
					'placeholder'       => __( 'No restrictions', 'woocommerce' ),
					'description'       => __( 'Whitelist of billing emails to check against when an order is placed. Separate email addresses with commas. You can also use an asterisk (*) to match parts of an email. For example "*@gmail.com" would match all gmail addresses.', 'woocommerce' ),
					'value'             => implode( ', ', (array) $customer_email),
					'desc_tip'          => true,
					'type'              => 'email',
					'class'             => '',
					'custom_attributes' => array(
						'multiple'  => 'multiple',
					),
				) );
				echo '</div>';
			?></div>
			<div id="usage_limit_coupon_data" class="panel woocommerce_options_panel"><?php
				echo '<div class="options_group">';

				// Usage limit per coupons.
				woocommerce_wp_text_input( array(
					'id'                => 'usage_limit',
					'label'             => __( 'Usage limit per coupon', 'woocommerce' ),
					'placeholder'       => esc_attr__( 'Unlimited usage', 'woocommerce' ),
					'description'       => __( 'How many times this coupon can be used before it is void.', 'woocommerce' ),
					'type'              => 'number',
					'desc_tip'          => true,
					'class'             => 'short',
					'custom_attributes' => array(
						'step'  => 1,
						'min' => 0,
					),
					'value' => $usage_limit ? $usage_limit : '',
				) );

				// Usage limit per product.
				woocommerce_wp_text_input( array(
					'id'                => 'limit_usage_to_x_items',
					'label'             => __( 'Limit usage to X items', 'woocommerce' ),
					'placeholder'       => esc_attr__( 'Apply to all qualifying items in cart', 'woocommerce' ),
					'description'       => __( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'woocommerce' ),
					'desc_tip'          => true,
					'class'             => 'short',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'  => 1,
						'min' => 0,
					),
					'value' => $limit_usage_to_x_items ? $limit_usage_to_x_items : '',
				) );

				// Usage limit per users.
				woocommerce_wp_text_input( array(
					'id'                => 'usage_limit_per_user',
					'label'             => __( 'Usage limit per user', 'woocommerce' ),
					'placeholder'       => esc_attr__( 'Unlimited usage', 'woocommerce' ),
					'description'       => __( 'How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users.', 'woocommerce' ),
					'desc_tip'          => true,
					'class'             => 'short',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'  => 1,
						'min' => 0,
					),
					'value' => $usage_limit_per_user ? $usage_limit_per_user : '',
				) );
				echo '</div>';
			?></div>
			<div class="clear"></div>
<div id="coupon_design" data-domain="<?php echo get_home_url(); ?>" >
<label for="giftcoupon_html" style="font-size:20px;">Coupon Design:</label><br/>
<?php wp_editor($giftcoupon_html,'giftcoupon_html',['wp_editor'=>'','textarea_rows'=> '20']); ?>
</div>

		</div>



		<?php

	}






	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post )
    {
        $allowed_html = wp_kses_allowed_html( 'post' );
		$giftcoupon                 = sanitize_text_field( isset( $_POST['giftcoupon'] ) ? 'yes' : 'no' );
		$couponcode_with_order_no   = sanitize_text_field( isset( $_POST['couponcode_with_order_no'] ) ? 'yes' : 'no' );

        add_filter( 'safe_style_css', 'bwc_add_css_attributes' );
		$giftcoupon_html            = isset( $_POST['giftcoupon_html'] ) ? wp_kses( $_POST['giftcoupon_html'] , $allowed_html )  : '' ;
        add_filter( 'safe_style_css', 'bwc_remove_css_attributes' );

		$type                       = wc_clean( $_POST['discount_type'] );
		$amount                     = wc_format_decimal( $_POST['coupon_amount'] );
		$usage_limit                = empty( $_POST['usage_limit'] ) ? '' : absint( $_POST['usage_limit'] );
		$usage_limit_per_user       = empty( $_POST['usage_limit_per_user'] ) ? '' : absint( $_POST['usage_limit_per_user'] );
		$limit_usage_to_x_items     = empty( $_POST['limit_usage_to_x_items'] ) ? '' : absint( $_POST['limit_usage_to_x_items'] );
		$individual_use             = sanitize_text_field( isset( $_POST['individual_use'] ) ? 'yes' : 'no' );
        $timeout_after_purchase     = wc_clean( $_POST['timeout_after_purchase'] );



		$apply_before_tax           = sanitize_text_field( isset( $_POST['apply_before_tax'] ) ? 'yes' : 'no' );
		$free_shipping              = sanitize_text_field( isset( $_POST['free_shipping'] ) ? 'yes' : 'no' );
		$exclude_sale_items         = sanitize_text_field( isset( $_POST['exclude_sale_items'] ) ? 'yes' : 'no' );
		$minimum_amount             = wc_format_decimal( $_POST['minimum_amount'] );
		$maximum_amount             = wc_format_decimal( $_POST['maximum_amount'] );
		$customer_email             = array_filter( array_map( 'trim', explode( ',', wc_clean( $_POST['customer_email'] ) ) ) );
		$product_ids                = isset( $_POST['product_ids'] )                ? implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_ids'] ) ) ) : '';
		$exclude_product_ids        = isset( $_POST['exclude_product_ids'] )        ? implode( ',', array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) ) : '';
		$product_categories         = isset( $_POST['product_categories'] )         ? (array) $_POST['product_categories'] : array();
		$exclude_product_categories = isset( $_POST['exclude_product_categories'] ) ? (array) $_POST['exclude_product_categories'] : array();

		update_post_meta( $post_id, 'giftcoupon', $giftcoupon );
		update_post_meta( $post_id, 'couponcode_with_order_no', $couponcode_with_order_no );
		update_post_meta( $post_id, 'giftcoupon_html', $giftcoupon_html );
		update_post_meta( $post_id, 'discount_type', $type );
		update_post_meta( $post_id, 'coupon_amount', $amount );
		update_post_meta( $post_id, 'individual_use', $individual_use );
		update_post_meta( $post_id, 'product_ids', $product_ids );
		update_post_meta( $post_id, 'exclude_product_ids', $exclude_product_ids );
		update_post_meta( $post_id, 'usage_limit', $usage_limit );
		update_post_meta( $post_id, 'usage_limit_per_user', $usage_limit_per_user );
		update_post_meta( $post_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );
		update_post_meta( $post_id, 'expiry_date', $expiry_date );
		update_post_meta( $post_id, 'timeout_after_purchase', $timeout_after_purchase );
		update_post_meta( $post_id, 'apply_before_tax', $apply_before_tax );
		update_post_meta( $post_id, 'free_shipping', $free_shipping );
		update_post_meta( $post_id, 'exclude_sale_items', $exclude_sale_items );
		update_post_meta( $post_id, 'product_categories', $product_categories );
		update_post_meta( $post_id, 'exclude_product_categories', $exclude_product_categories );
		update_post_meta( $post_id, 'minimum_amount', $minimum_amount );
		update_post_meta( $post_id, 'maximum_amount', $maximum_amount );
		update_post_meta( $post_id, 'customer_email', $customer_email );
	}

}
