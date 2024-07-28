<?php
/**
 * BWC Functions
 *
 * Sets functions of the plugin
 *
 * @author      BWC
 * @package     BWC/Functions
 */

if ( ! defined( 'ABSPATH' ) )
{
  exit;
}

/**
 * Get results of coupons by user.
 *
 * @param int $user_id Current user profile ID.
 * @return array
 */
function bwc_get_coupons_user( $user_id )
{
  global $wpdb;
  if ( ! $user_id ):
		return;
  endif;
  return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bwc LEFT JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}bwc.id_coupon =  {$wpdb->prefix}posts.ID WHERE id_user={$user_id} ORDER BY id_coupon DESC" );
}



/**
 * Get product reference of a coupon
 *
 * @param int $id_coupon Coupon ID.
 */
function bwc_get_product_reference_coupon( $id_coupon )
{
  $product_reference = get_post_meta( $id_coupon, 'product_reference' );
  if ( !empty( $product_reference ) ):
    return reset( $product_reference );
  endif;
  return;
}


/**
 * Check coupons by order.
 *
 * @param mixed $order_id WC Order ID.
 * @return array
 */
function bwc_check_order_coupons( $order_id )
{
  global $wpdb;
  if ( !$order_id || !$wpdb ):
    return;
  endif;
  return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bwc WHERE id_order=" . $order_id );
}


/**
 * Lookup for a order by coupon id 
 *
 * @param mixed $coupon_id WC Order ID.
 * @return object
 */
function bwc_get_coupon( $coupon_id )
{
  global $wpdb;
  if ( !$coupon_id || !$wpdb ):
    return;
  endif;
  $r =  $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bwc WHERE id_coupon=" . $coupon_id . " LIMIT 1");
  return reset($r);
}


/**
 * Lookup for a order by coupon id 
 *
 * @param mixed $coupon_id WC Order ID.
 * @return object
 */
function bwc_update_send_by_email( $coupon_id )
{
  global $wpdb;
  if ( !$coupon_id || !$wpdb ):
    return;
  endif;
  $sql = "UPDATE {$wpdb->prefix}bwc SET send_by_email = send_by_email+1, send_date = CURRENT_TIMESTAMP  WHERE id_coupon=\"{$coupon_id}\"";
  $r =  $wpdb->query($sql);
  return TRUE;
}


/**
 * Proccess to generate coupons.
 *
 * @param int $order_id Order ID.
 */
function bwc_process_coupons_generation( $order_id )
{
  if ( isset( $order_id ) ):
    $post_ids = array_map( 'intval', array( $order_id ) );
  endif;
  if ( empty( $post_ids ) ):
    return;
  endif;
  return bwc_register_coupons( $post_ids );
}


/**
 * Proccess to generate coupons.
 *
 * @param int $order_id Order ID.
 * @param int $order_line_id Order Line ID.
 * @param int $qty qty of the order line
 */
function bwc_coupon_fulfilled($order_id,$order_line_id,$qty)
{
  global $wpdb;    
  $sql = "
  SELECT count(m.post_id) AS c 
  FROM `wp_postmeta` AS m
  LEFT JOIN wp_posts  AS p
  ON m.post_id =  p.id 
  WHERE m.meta_key IN ('order_id','order_line_id')
  AND m.meta_value IN ('{$order_id}','{$order_line_id}')
  AND p.post_type = 'shop_coupon'
  ";

  $r = $wpdb->get_var($sql);
  if($r == (2 * $qty)):
    return TRUE;
  else:
    return FALSE;
  endif;
}

/**
 * Register New Coupons.
 *
 * @param array $post_ids Posts IDs.
 */
function bwc_register_coupons( $post_ids )
{
  global $wpdb;
  bwc_logging( '...bwc_register_coupons fnc');  
  if ( !$wpdb ):
    return false;
  endif;

  $generated_coupon = 0;
  $emails_to_send = [];
  foreach ( $post_ids as $post_id ):
    $coupons = bwc_check_order_coupons_count( $post_id ); /* General check if coupon order exist */
	if ( $coupons['count'] > 0 ):
      bwc_logging( '...coupons count:  ' . $coupons['count']);  
      $order  = new WC_Order( $post_id );
      $items  = $order->get_items();
      
   	  //Only get items what are not yet generated.
   	  $items  = bwc_get_items_to_generate( $items, $post_id); //set the qty for the coupons to generate

      foreach ( $items as $key => $item ):
  	    $coupons_mail = array();
		$product_id   = isset( $item['product_id'] ) ? $item['product_id'] : NULL;

		if ( !empty( $product_id ) ):
		  $giftcoupon = bwc_is_coupon( $product_id );

		  if ( $giftcoupon == 'yes' && $item['qty'] > 0 ):

            for ( $i=1; $i <= $item['qty']; $i++ ):
              //bwc_coupon_exists($post_id,$key,$item['qty']);

              if( bwc_coupon_fulfilled($post_id,$key,$item['qty']) == FALSE):

                bwc_logging("...create coupon product id: " .$product_id . "  post id:" . $post_id ." key: " .$key  );
                $coupon_id = bwc_create_woocommerce_coupon( $product_id, $post_id, $key);

                if ( !empty( $coupon_id ) ):
                  $user_order = ( $order->user_id < 1 ) ? NULL : $order->user_id;
                  $db_table = $wpdb->prefix . 'bwc';

                  $wpdb->insert( $db_table,
                                 ['id_user'     => $user_order,
                                 'id_coupon'   => $coupon_id,
                                 'id_order'    => $post_id,
                                 'coupon_msg'  => $item->get_name(),
                                 'dompdf'      => bwc_parse_html($product_id,$coupon_id)
                                 ],
                                 ['%s','%s','%s','%s','%s']
                  );

                  $emails_to_send[] =  $coupon_id; //add coupon ids for sending 
  			    endif;
              endif;
			endfor;
		  endif;
		endif;
      endforeach;
    endif;
  endforeach;;
  
  bwc_send_emails( $emails_to_send ); 
  //  var_dump($emails_to_send);
  return $generated_coupon;
}


function bwc_has_open_coupons( $order_id )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  $order   = new WC_Order( $order_id );
  $items   = $order->get_items();
  $coupons = [];
  if ( !empty( $items ) ):
    foreach ( $items as $item ):
	  $is_coupon = get_post_meta( $item['product_id'], 'giftcoupon' );
      if(is_array($is_coupon)):
        if( reset($is_coupon) == 'yes'):
          echo __("Open Coupons",'bw-coupon') . "<br/>";
        endif;
      endif;
    endforeach;
  endif;

}
/**
 * Check coupons count by order.
 *
 * @param int $order_id WC Order ID.
 * @return array
 */
function bwc_check_order_coupons_count( $order_id ) {
  $order   = new WC_Order( $order_id );
  $items   = $order->get_items();
  $coupons = ['count'=>0,];

  if ( !empty( $items ) ):
    foreach ( $items as $item ):
	  $product_type = get_post_meta( $item['product_id'], 'giftcoupon' );

	  if ( !empty( $product_type ) ):
        $giftcoupon = reset( $product_type );
	    if ( $giftcoupon == 'yes' && $item['qty'] > 0 ):
	      $coupons['count'] += $item['qty'];
	    endif;
	  endif;

	endforeach;

  endif;

  return $coupons;
}



/**
 * Function to filter coupons no already generated.
 *
 * @param array  $tiems Product order items.
 * @param int    $post_id Order ID.
 * @param array  $coupons_generated Coupons already generated.
 * @return array
 */
function bwc_get_items_to_generate ( $items, $post_id)
{
  
  $coupons_generated = bwc_check_order_coupons( $post_id ); 

  if ( !empty( $coupons_generated ) ):
    // Get product ID from generated coupons.
	$products_generated = array();
	foreach ( $coupons_generated as $coupon ):
      $products_generated[] = bwc_get_product_reference_coupon( $coupon->id_coupon );
	endforeach;

	if ( !empty( $products_generated ) ):
      $products_generated = array_count_values( $products_generated );
	endif;		

    // Get ID of gift coupons products.
	$items_purchased = array();
	foreach ( $items as $key => $item ):
      $product_id = isset( $item['product_id'] ) ? $item['product_id'] : NULL;
	  if ( ! empty( $product_id ) ):
        $giftcoupon = bwc_is_coupon( $product_id );
		if ( $giftcoupon == 'yes' && $item['qty'] > 0 ):
		  $items_purchased[$key][$product_id] = $item['qty'];
		endif;
	  endif;
	endforeach;

	// Remove from items if coupons are already generated.
	foreach ( $items_purchased as $key => $item ):
	  $product_key = array_keys( $item );
      $product_key = reset( $product_key );
	  $product     = reset( $item );
	  if ( !empty( $product_key ) && !empty( $product ) ):
	    if ( isset( $products_generated[$product_key] ) ):
		   $items[$key]['qty'] = $product - $products_generated[$product_key];
		endif;
	  endif;
	endforeach;

  endif;
  return $items;
}


/**
 * Get product if is gift coupon
 *
 * @param int $id_product Product ID.
 */
function bwc_is_coupon( $id_product )
{
  $product_type = get_post_meta( $id_product, 'giftcoupon' );
  if ( !empty( $product_type ) ):
    return reset( $product_type );
  endif;
  return;
}



function set_expire_date($period)
{
  $a = explode('_',$period);
  $amount = $a[0];
  $type   = $a[1];

  if($type == 'years' || $type == 'year'):
    $date = date('Y-m-d', strtotime('+'.$amount.' years'));
  elseif($type == 'months' || $type == 'month'):
    $date = date('Y-m-d', strtotime('+'.$amount.' months'));
  elseif($type == 'days' || $type == 'day'):
    $date = date('Y-m-d', strtotime('+'.$amount.' days'));
  else:
    $date = date('Y-m-d');
  endif;
  return $date;
}


/**
 * Create a Woocommerce Coupon from the Product Meta data
 *
 * @param int product_id WC Product ID.
 * @return int coupon id
 */

function bwc_create_woocommerce_coupon( $product_id, $order_id, $order_line_id )
{
  //var_dump($order_id);
  //exit;
  $data = bwc_get_coupon_product_data($product_id);
  $coupon_code  = bwc_create_code( $order_id, $data['couponcode_with_order_no'] );
  $coupon = ['post_title'   => $coupon_code,
             'post_excerpt' => 'Discount coupon',
             'post_status'  => 'publish',
             'post_author'  => 1,
             'post_type'    => 'shop_coupon'
  ];
  $coupon_id   = wp_insert_post( $coupon );


  $data['expiry_date'] = set_expire_date($data['timeout_after_purchase']);
  $data['order_id'] = $order_id;
  $data['order_line_id'] = $order_line_id;
  bwc_update_coupon($coupon_id,$data);
  return $coupon_id;
}

/**
 * Helper function to generate a random coupon code.
 *
 * @param int $post_id Post ID.
 */
function bwc_create_code( $order_id, $incl_ord )
{
  $str = 'abcdefghijklmnopqrstuvwxyz012345678901234567891';
  $shuffled = str_shuffle( $str );

  if( $incl_ord):
    $shuffled = (string)$order_id . '-' . substr( $shuffled , 1, 4);
  else:
    $shuffled = substr( $shuffled , 1, 8);
  endif;

  $code = mb_strtoupper( $shuffled );
  return $code;
}


function bwc_get_coupon_product_data( $product_id )
{
    $type                       = get_post_meta( $product_id, 'discount_type' );
    $amount                     = get_post_meta( $product_id, 'coupon_amount' );
    $individual_use             = get_post_meta( $product_id, 'individual_use' );
    $product_ids                = get_post_meta( $product_id, 'product_ids' );
    $exclude_product_ids        = get_post_meta( $product_id, 'exclude_product_ids' );
    $usage_limit                = get_post_meta( $product_id, 'usage_limit' );
    $usage_limit_per_user       = get_post_meta( $product_id, 'usage_limit_per_user' );
    $limit_usage_to_x_items     = get_post_meta( $product_id, 'limit_usage_to_x_items' );
    $expiry_date                = get_post_meta( $product_id, 'expiry_date' );
    $timeout_after_purchase     = get_post_meta( $product_id, 'timeout_after_purchase' );
    $couponcode_with_order_no   = get_post_meta( $product_id, 'couponcode_with_order_no' );


    $apply_before_tax           = get_post_meta( $product_id, 'apply_before_tax' );
    $free_shipping              = get_post_meta( $product_id, 'free_shipping' );
    $exclude_sale_items         = get_post_meta( $product_id, 'exclude_sale_items' );
    $product_categories         = get_post_meta( $product_id, 'product_categories' );
    $exclude_product_categories = get_post_meta( $product_id, 'exclude_product_categories' );
    $minimum_amount             = get_post_meta( $product_id, 'minimum_amount' );
    $maximum_amount             = get_post_meta( $product_id, 'maximum_amount' );
    $customer_email             = get_post_meta( $product_id, 'customer_email' );
    

    $data['type']                       = reset( $type );
    $data['amount']                     = reset( $amount );
    $data['individual_use']             = reset( $individual_use );
    $data['product_ids']                = reset( $product_ids );
    $data['exclude_product_ids']        = reset( $exclude_product_ids );
    $data['usage_limit']                = reset( $usage_limit );
    $data['usage_limit_per_user']       = reset( $usage_limit_per_user );
    $data['limit_usage_to_x_items']     = reset( $limit_usage_to_x_items );
    $data['expiry_date']                = reset( $expiry_date );
    $data['timeout_after_purchase']     = reset( $timeout_after_purchase );
    $data['couponcode_with_order_no']   = reset( $couponcode_with_order_no );


    $data['apply_before_tax']           = reset( $apply_before_tax );
    $data['free_shipping']              = reset( $free_shipping );
    $data['exclude_sale_items']         = reset( $exclude_sale_items );
    $data['product_categories']         = reset( $product_categories );
    $data['exclude_product_categories'] = reset( $exclude_product_categories );
    $data['minimum_amount']             = reset( $minimum_amount );
    $data['maximum_amount']             = reset( $maximum_amount );
    $data['customer_email']             = reset( $customer_email );
    $data['product_reference']          = $product_id ;

    return $data;
}

function bwc_update_coupon( $coupon_id, $data )
{
    update_post_meta( $coupon_id, 'discount_type', $data['type'] );
    update_post_meta( $coupon_id, 'coupon_amount', $data['amount'] );
    update_post_meta( $coupon_id, 'individual_use', $data['individual_use'] );
    update_post_meta( $coupon_id, 'usage_limit', $data['usage_limit'] );
    update_post_meta( $coupon_id, 'usage_limit_per_user', $data['usage_limit_per_user'] );
    update_post_meta( $coupon_id, 'limit_usage_to_x_items', $data['limit_usage_to_x_items'] );
    update_post_meta( $coupon_id, 'expiry_date', $data['expiry_date'] );
    update_post_meta( $coupon_id, 'apply_before_tax', $data['apply_before_tax'] );
    update_post_meta( $coupon_id, 'free_shipping', $data['free_shipping'] );
    update_post_meta( $coupon_id, 'product_ids', $data['product_ids'] );
    update_post_meta( $coupon_id, 'exclude_product_ids', $data['exclude_product_ids'] );
    update_post_meta( $coupon_id, 'exclude_sale_items', $data['exclude_sale_items'] );
    update_post_meta( $coupon_id, 'product_categories', $data['product_categories'] );
    update_post_meta( $coupon_id, 'exclude_product_categories', $data['exclude_product_categories'] );
    update_post_meta( $coupon_id, 'minimum_amount', $data['minimum_amount'] );
    update_post_meta( $coupon_id, 'maximum_amount', $data['maximum_amount'] );
    update_post_meta( $coupon_id, 'customer_email', $data['customer_email'] );
    update_post_meta( $coupon_id, 'product_reference', $data['product_reference'] );
    update_post_meta( $coupon_id, 'order_id', $data['order_id'] );
    update_post_meta( $coupon_id, 'order_line_id', $data['order_line_id'] );

}
