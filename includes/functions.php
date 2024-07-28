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

require_once BWC_DIR . 'lib/dompdf/vendor/autoload.php';
//require_once BWC_DIR . 'lib/dompdf/lib/html5lib/Parser.php';
//require_once BWC_DIR . 'lib/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
//require_once BWC_DIR . 'lib/dompdf/lib/php-svg-lib/src/autoload.php';
//require_once BWC_DIR . 'lib/dompdf/src/Autoloader.php';


function my_plugin_init() 
{
  add_action( 'plugins_loaded', 'my_plugin_init' );
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  //Load the WP set language 
}

add_filter( 'plugin_row_meta', 'bwc_row_meta', 10, 2 );
function bwc_row_meta( $links, $file )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  if ( strpos( $file, 'bwc.php' ) !== false ):
    $action_links = [
        '<a href="https://www.paypal.me/donate217">' . __( 'Donate', "bw-coupon" ) . '</a>',
    ];
   $links = array_merge( $links, $action_links );
  endif;
  return $links;
}

add_filter( 'plugin_action_links_' . BWC_BASENAME, 'bwc_action_links' );
function bwc_action_links( $links )
{
  $action_links = [
 'settings' => '<a href="' .admin_url( 'admin.php?page=bwc_options_page' ) . '" aria-label="' . esc_attr__( 'Settings', "bw-coupon" ). '">' .esc_html__( 'Settings', 'woocommerce' ) . '</a>',];
  return array_merge( $action_links, $links );
}

add_action( 'admin_menu', 'bwc_menu' );
function bwc_menu() //Add menu items
{ 
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
add_submenu_page(
 'woocommerce',
 __('BWC', 'bwc'),
 __('BWC '. __('Settings','bw-coupon'), 'bwc'),
 'manage_woocommerce',
 'bwc_options_page',
 'bwc_import_options_page'
 );
}


function bwc_import_options_page() // Helper function to include options_admin_page.
{ 
  require_once BWC_DIR . 'admin/options-admin-page.php';
}


/**
 * Enqueue scripts.
 */
add_action( 'admin_enqueue_scripts', 'bwc_scripts' );
function bwc_scripts()
{
  wp_enqueue_script( "BWC_scripts", BWC_URL . 'admin/js/bwc.js', [], false, true );
}



/**
 * Enqueue styles.
 */
add_action( 'admin_enqueue_scripts', 'bwc_plugin_styles' );
function bwc_plugin_styles()
{
  wp_register_style( 'bwc_css', BWC_URL . 'admin/css/styles.css' );
  wp_enqueue_style( 'bwc_css' );
}



/**
 * Render individual columns.
 *
 * @param array $columns Columns to render.
 * @return array
 */
add_filter( 'manage_edit-shop_order_columns', 'bwc_columns' );
function bwc_columns( $columns )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  $columns['coupons']  = __( 'Coupons', "bw-coupon" );
  return $columns;
}



/**
 * Render individual columns.
 *
 * @param array $column Column to render.
 */
add_action( 'manage_shop_order_posts_custom_column', 'bwc_render_columns' );
function bwc_render_columns( $column )
{
  global $post, $woocommerce, $wpdb;
  switch ( $column ):
  case 'coupons':
    $coupons_generated = bwc_check_order_coupons( $post->ID );
	if ( ! empty( $coupons_generated ) ):
      echo '<ul>';
	  foreach ( $coupons_generated as $coupon ):
   	    $preview = '<a title="preview PDF" onclick="return false;" href="javascript:" class="bwc_preview_pdf" ></a>';
        $id = '<a title="Coupon details" href="' . get_edit_post_link( $coupon->id_coupon ) . '">ID:' . $coupon->id_coupon . '</a>';
   	    $email = '<a title="email PDF" onclick="return false;" class="bwc_email_pdf" href="'.admin_url( "admin.php?page=bwc_options_page&coupon_id={$coupon->id_coupon}" ).'"></a>';
        echo '<li data-coupon="'. $coupon->id_coupon .'" data-domain="'. get_home_url() .'">'. $id . $preview . $email. '</li>';
      endforeach;
      echo "<ul>";
    else:
      bwc_has_open_coupons( $post->ID );
	endif;

	break;
  endswitch;
}



/**
 * Render individual columns for coupons.
 *
 * @param array $columns Columns to render.
 * @return array
 */
add_filter( 'manage_edit-shop_coupon_columns', 'bwc_coupon_columns' );
function bwc_coupon_columns( $columns )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  $columns['order_no']  = __( 'Order No.', "bw-coupon" );
  $columns['order_item']  = __( 'Order Item', "bw-coupon" );
  $columns['coupon_name']  = __( 'Item Name', "bw-coupon" );
  $columns['view_pdf']  = __( 'PDF', "bw-coupon" );
  return $columns;
}



/**
 * Render individual columns for coupons.
 *
 * @param array $column Column to render.
 */
add_action( 'manage_shop_coupon_posts_custom_column', 'bwc_render_coupon_columns' );
function bwc_render_coupon_columns( $column )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  global $post, $woocommerce, $wpdb;
  $coupon = bwc_get_coupon( $post->ID );
  if($coupon): /* check if the coupon is from BWC */
    $order  = new WC_Order( $coupon->id_order );
    $mailto = $order->get_billing_email();
    switch ( $column ):
      case 'coupon_name':
        echo $coupon->coupon_msg . "<br/>";
	    break;
      case 'email_send':
      echo "{$coupon->send_by_email}x<br/>";
	  break;
    case 'send_email':
      $html = '<input id="bwc_coupon_'.$coupon->id_coupon.'" class="bwc_send_email" type="email" name="mailto" value="'. $mailto . '" />';
      $html .= '<button class="bwc_send_email_btn" onclick="return false;"  data-coupon="'.$coupon->id_coupon.'"  data-domain="'. get_home_url() .'">' .__('Send','bw-coupon') . '</button>';
      echo $html;
	  break;
    case 'order_no':
      echo edit_post_link( __( '#'. get_post_meta( $post->ID, 'order_id', true ), 'textdomain' ), '<p>', '</p>', get_post_meta( $post->ID, 'order_id', true ), 'btn btn-primary btn-edit-post-link' );
	  break;
    case 'order_item':
      echo get_post_meta( $post->ID, 'order_line_id', true );
	  break;
    case 'view_pdf':
	  echo  '<a title="preview PDF" onclick="return false;" href="javascript:" class="bwc_pdf" data-coupon="'.$coupon->id_coupon.'"  data-domain="'. get_home_url() .'"  ></a>';
	  break;
    endswitch;
  endif;
  
}



/**
 * Set row actions.
 *
 * @param array   $actions Array of actions.
 * @param WP_Post $post Current post object.
 * @return array
 */
add_filter( 'post_row_actions', 'bwc_coupon_action_link', 10, 2 );
function bwc_coupon_action_link( $actions, $post )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  $post_type = 'shop_order';
  if ( $post->post_type == $post_type ):
    $coupons = bwc_check_order_coupons_count( $post->ID );
	$coupons_generated = bwc_check_order_coupons( $post->ID );

	if ( $coupons['count'] > 0 ):

	  if ( count( $coupons_generated ) < $coupons['count'] ):
	    $str_coupon = __( 'Generate coupons', "bw-coupon" );

		if ( $coupons['count'] == 1 ):
		  $str_coupon = __( 'Generate coupon', "bw-coupon" );
		endif;

		$sendback = admin_url( "edit.php?post_type=$post_type" );
		$sendback = add_query_arg( 'paged', 1, $sendback );
		$sendback = add_query_arg( 'wcgc_gc', array( $post->ID ), $sendback );
		$sendback = add_query_arg( array( 'ids' => $post->ID ), $sendback );
		$sendback = remove_query_arg( array( 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );
		$actions['generate_coupon'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $sendback ), esc_html( $str_coupon ) );
	  endif;
	endif;
  endif;
  return $actions;
}






/**
 * Update customer on edit order.
 *
 * @param int $post_id Post id.
 */
add_action( 'delete_post', 'bwc_coupon_delete' );
function bwc_user_delete( $user_id )
{
  global $wpdb;
  $wpdb->query( "DELETE FROM `{$wpdb->prefix}bwc` WHERE id_user = {$user_id}" );
}



/**
 * Update customer on edit order.
 *
 * @param int $post_id Post id.
 */
add_action( 'edit_post', 'bwc_order_update' );
function bwc_order_update( $post_id )
{
  global $post, $wpdb;
  if ( ! empty( $_POST ) && ! empty( $post ) ):
    if ( $post->post_type == 'shop_order' ):
	  $customer = isset( $_POST['customer_user'] ) ? sanitize_text_field( $_POST['customer_user'] ) : NULL;
	  if ( empty( $customer )  || $customer < 1):
	    $user_order = 'NULL';
	  else:
	    $user_order = $customer;
	  endif;
	  $wpdb->query( "UPDATE `{$wpdb->prefix}bwc` SET id_user = {$user_order} WHERE id_order = {$post_id}" );
     endif;
   endif;
}

/**
 * Delete coupons files.
 *
 * @param int $post_id Post id.
 */
add_action( 'delete_user', 'bwc_user_delete' );
function bwc_coupon_delete( $post_id )
{
  global $wpdb;
  $wpdb->hide_errors();
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';

  if ( ! empty( $post_id ) ):
    // Remove PDFs.
	$code = $wpdb->get_results( "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID={$post_id}" );
	if ( ! empty( $code ) ):
	  $code       = reset( $code );
	  $code       = $code->post_title;
	  $upload_dir = wp_upload_dir();
	  $pathupload = $upload_dir['basedir'] . '/bwc';
	  $file       = $pathupload . '/' . $code . '.pdf';
	  if ( is_file( $file ) ):
	    unlink( $file );
	  endif;
	endif;

		// Remove relationships db on removing post.
	$wpdb->query( "DELETE FROM `{$wpdb->prefix}bwc` WHERE id_coupon = {$post_id} OR id_order = {$post_id}" );
	endif;
}

// Save Coupon Meta Boxes on product details.
add_action( 'woocommerce_process_product_meta', 'BWC_Data::save', 10, 2 );

/**
 * Add WC Meta boxes.
 */
add_action( 'add_meta_boxes', 'bwc_product_add' );
function bwc_product_add() 
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  add_meta_box( 'product_details', __( 'BWC - Buy Woocommerce Coupons', "bw-coupon" ), 'bwc_call', 'product', 'normal', 'high' );
}

/**
 * Add BWC  Meta Box Coupon Data.
 *
 * @param WP_Post $post Post object.
 */
function bwc_call( $post )
{
  echo '<div id="woocommerce-coupon-data" class="postbox">';
  BWC_Data::output( $post );
  echo '</div>';
}


add_action('admin_print_scripts', 'bwc_admin_scripts');
function bwc_admin_scripts()
{
  wp_enqueue_script('media-upload');
  wp_enqueue_script('thickbox');
  wp_enqueue_script('jquery');
  wp_enqueue_media();
}





/**
 * Helper function to show the results of the generation.
 */
add_action( 'admin_notices', 'bwc_admin_notices' );
function bwc_admin_notices()
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  global $pagenow;
  if ( $pagenow == 'edit.php' && ! isset( $_GET['trashed'] ) ):
    $generated_coupon = 0;

  	if ( isset( $_REQUEST['generated_coupon'] ) && (int) $_REQUEST['generated_coupon'] ):
      $generated_coupon = (int) $_REQUEST['generated_coupon'];
	elseif ( isset( $_GET['generated_coupon'] ) && (int) $_GET['generated_coupon'] ):
	  $generated_coupon = (int) sanitize_text_field($_GET['generated_coupon']);
	endif;
	$str_coupon = __( 'Coupons generated', "bw-coupon" );

	if ( $generated_coupon == 1 ):
   	  $str_coupon = __( 'Coupon generated', "bw-coupon" );
	endif;

	if ( isset( $_REQUEST['generated_coupon'] ) || isset( $_GET['generated_coupon'] ) ):
  	  $message = sprintf( _n( '<b>%s</b> ' . $str_coupon, '<b>%s</b> ' . $str_coupon, $generated_coupon ), number_format_i18n( $generated_coupon ) );
	  echo "<div class=\"updated\"><p>{$message}</p></div>";
	endif;
  endif;
}




/**
 * Add 'Generate coupons' option to select filter.
 */
add_action( 'admin_footer', 'bwc_bulk' );
function bwc_bulk()
{
  global $post_type;
  if ( $post_type == 'shop_order' ):
  ?>
  <script type="text/javascript">
  jQuery(function() {
      jQuery('<option>').val('generate_coupon').text('<?php esc_html_e( 'Generate coupons', "bw-coupon" ); ?>').appendTo("select[name='action']");
      jQuery('<option>').val('generate_coupon').text('<?php esc_html_e( 'Generate coupons', "bw-coupon" ); ?>').appendTo("select[name='action2']");
  });
  </script>
	<?php
  endif;
}

/**
 * Add action of generated coupons on load edit.
 */
add_action( 'load-edit.php', 'bwc_bulk_action' );
function bwc_bulk_action() {
  global $typenow;

  $wp_list_table   = _get_list_table( 'WP_Posts_List_Table' );
  $action          = $wp_list_table->current_action();


  $allowed_actions = array( 'generate_coupon' );

  if ( ! in_array( $action, $allowed_actions ) ):
    return;
  endif;

  check_admin_referer( 'bulk-posts' );

  if ( isset( $_REQUEST['post'] ) ):
    $post_ids = array_map( 'intval', $_REQUEST['post'] );
  endif;

  if ( empty( $post_ids ) ):
    return;
  endif;

  $sendback = bwc_generate_sendback( $wp_list_table );

  switch ( $action )
  {
    case 'generate_coupon':
      $generated_coupon = bwc_register_coupons( $post_ids );
      $sendback = add_query_arg(['generated_coupon' => $generated_coupon,'ids' => join( ',', $post_ids ),],$sendback);
      break;
    default:
      return;
   }

	$sendback = remove_query_arg(['action',
                                  'action2',
                                  'tags_input',
                                  'post_author',
                                  'comment_status',
                                  'ping_status',
                                  '_status',
                                  'post',
                                  'bulk_edit',
                                  'post_view' ],
                                 $sendback );
	wp_redirect( $sendback );

	exit();
}

/**
 * Helper Loggin function
 *
 * @param string 
 */
function bwc_logging( $str, $type='info' )
{
  $logger = wc_get_logger();
  $context = array( 'source' => 'bwc' );
  $logger->info( $str, $context );
}


/**
 * Helper function to create de query arguments of the URL.
 *
 * @param object $wp_list_table Table list orders
 */
function bwc_generate_sendback( $wp_list_table )
{
  $sendback = remove_query_arg( array( 'generated_coupon', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
  if ( ! $sendback ):
    $sendback = admin_url( "edit.php?post_type=$post_type" );
  endif;
  $pagenum  = $wp_list_table->get_pagenum();
  $sendback = add_query_arg( 'paged', $pagenum, $sendback );
  return $sendback;
}


/**
 * Order status 'completed'.
 *
 * @param int $order_id Order ID.
 */
add_action( 'woocommerce_order_status_completed', 'bwc_automatically_send_completed' );
function bwc_automatically_send_completed( $order_id )
{
  $bwc_send = get_option( 'bwc_send' );
  if ( $bwc_send == 1 ):
    return bwc_process_coupons_generation( $order_id );
  else:
	return;
  endif;
}

/**
 * Order status 'processing'.
 *
 * @param int $order_id Order ID.
 */
add_action( 'woocommerce_order_status_processing', 'bwc_automatically_send_processing' );
function bwc_automatically_send_processing( $order_id )
{
  $bwc_send = get_option( 'bwc_send' );
  if ( $bwc_send == 2 ):
    return bwc_process_coupons_generation( $order_id );
  else:
    return;
  endif;
}



/**
 * Helper function to generate coupon and  send emails automatically after an order.
 *
 * @param int $order_id Order ID.
 */
add_action( 'woocommerce_thankyou', 'bwc_woocommerce_auto_complete_order' );
function bwc_woocommerce_auto_complete_order( $order_id )
{
  if ( ! $order_id ):
    return;
  endif;

  $order = wc_get_order( $order_id );
  $status_order  = $order->get_status();
  $bwc_send = get_option( 'bwc_send' );

  switch ( $bwc_send )
    {
      case 1: // Complete orders.
          $status_transform = 'completed';
          break;
      case 2: // Processing orders.
          $status_transform = 'processing';
          break;
	  default:
          $status_transform = '';
          break;
	}

	if ( $status_transform == $status_order ):
	  $post_ids = array_map( 'intval', array( $order_id ) );

	  if ( empty( $post_ids ) ):
	    return;
	  endif;

	  return bwc_register_coupons( $post_ids );
	endif;
}
