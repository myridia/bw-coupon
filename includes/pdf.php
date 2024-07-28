<?php
/**
 * BWC Functions
 *
 * PDF related Functions
 *
 * @author      Veto
 * @package     BWC/PDF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Dompdf\Dompdf;
global $wpdb;

/**
 * Add place holder buttons to the wordpress editor in product edit/crate page 
 *
 * 
*/

add_action('admin_head', 'bwc_add_mce_button');
function bwc_add_mce_button() // Add Generate PDF Button to the Coupon Editor 
{
  if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ):
    return;
  endif;
  if ( 'true' == get_user_option( 'rich_editing' ) ):
    add_filter( 'mce_external_plugins', 'bwc_add_tinymce_plugin' );
    add_filter( 'mce_buttons', 'bwc_register_mce_button' );
  endif;
}

function bwc_register_mce_button( $buttons )
{
  array_push( $buttons, 'mce_button_html' );
  array_push( $buttons, 'mce_button_value' );
  array_push( $buttons, 'mce_button_code' );
  array_push( $buttons, 'mce_button_expire_date' );
  array_push( $buttons, 'mce_button_issue_date' );
  array_push( $buttons, 'mce_button_order_id' );
  array_push( $buttons, 'mce_button_order_date' );
  array_push( $buttons, 'mce_button_customer' );
  return $buttons;
}

function bwc_add_tinymce_plugin( $plugin_array )
{
  $plugin_array['mce_button'] = plugins_url('bw-coupon/admin/js/mce-button.js');
  return $plugin_array;
}


add_action( 'rest_api_init', 'bwc_register_route' );
function bwc_register_route()
{
  register_rest_route( 'bwc/foo', '/bar',[
      'methods' => 'GET',
      'callback' => 'bwc_get_foo',
      'permission_callback' => '__return_true' 
  ]);

  register_rest_route( 'bwc/v1', '/pdf/(?P<id>\d+)',[
      'methods' => 'GET',
      'callback' => 'bwc_get_pdf',
      'permission_callback' => '__return_true' 
  ]);
  register_rest_route( 'bwc/v1', '/send_pdf/(?P<id>\d+)',[
      'methods' => 'GET',
      'callback' => 'bwc_send_pdf',
      'permission_callback' => '__return_true' 
  ]);
  register_rest_route( 'bwc/v1', '/pdf',[
      'methods' => 'POST',
      'callback' => 'bwc_post_pdf',
      'permission_callback' => '__return_true' 
  ]);
}

function bwc_get_foo()
{
  $data = ['foo'=>'test'];
  return rest_ensure_response( $data );
}

function bwc_send_pdf($req)
{
  // http://127.0.0.1/wp-json/bwc/v1/send_pdf/3651
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  global $wpdb;
  $coupon_id = $req['id'];
  $mailto = is_email($req['email']) ? $req['email'] : FALSE;

  $r = bwc_send_email($coupon_id,$mailto);
  if( $r ):
    $data = ['mailto'=> $mailto ? $mailto : __('Customer email','bw-coupon'),
             'send'=>TRUE,
             'msg'=> __('Successfully send email to') . ' ' .  $r ];
  else:
    $data = ['send'=>FALSE, 'msg'=> __('Failed to send email') ];
  endif;   
  return rest_ensure_response( $data );
}

function bwc_post_pdf( $req )
{
  $p = $req->get_params();
  $html = decorate_html($p['id']);
  $pdf = bwc_html2pdf($html);
//  return rest_ensure_response( $pdf );
  $data = ['pdf'=>base64_encode($pdf)];
  return rest_ensure_response( $data );
}


function bwc_get_pdf($req)
{
  $coupon_id = $req['id'];
  $pdf = bwc_create_pdf($coupon_id);
  $data = ['pdf'=>base64_encode($pdf)];
  return rest_ensure_response( $data );
}

function bwc_create_pdf($coupon_id)
{
  global $wpdb;
  $row  = $wpdb->get_row( "SELECT `dompdf` FROM `{$wpdb->prefix}bwc` WHERE `id_coupon` = '{$coupon_id}' " );
  $html = decorate_html($row->dompdf);
  $pdf  = bwc_html2pdf($html);
  return $pdf;
}


function decorate_html( $_html )
{
  $html = "<html> 
<style>

html {
 margin-top:0;
}

@page {
 margin-top:0;
 }

body {
  color:#333;
  font-family: arial, helvetica, sans-serif;
  margin: 0px;
  positon:relative;
}

body img {
  margin-top: 0px;
}

</style>
    ";
  $html .= '<body>';
  $html .= $_html;
  $html .= '</body></html>';
  return $html;
}
 




function bwc_html2pdf($html) //enter html and getout the dom2pdf string 
{
  // replace relative image path with full URL, some WP settings have it setup this way
  $html = str_replace('src="/wp-content/uploads/', 'src="'. get_site_url()  .'/wp-content/uploads/', $html);

  $dompdf = new Dompdf();
  $contxt = stream_context_create([ 
      'ssl' => [ 
          'verify_peer' => FALSE, 
          'verify_peer_name' => FALSE,
          'allow_self_signed'=> TRUE 
      ] 
  ]);

  $dompdf->set_option('isRemoteEnabled', TRUE);
  $dompdf->set_option('isHtml5ParserEnabled', TRUE);
  $dompdf->setHttpContext($contxt);
  $dompdf->setPaper(array(0,0,590,841.89));
  $dompdf->loadHtml($html); 
  $dompdf->render();
  $_string = $dompdf->output();

  return $_string;
}

function bwc_get_timeout_after_purschase_options() // generate the option dropdown for the timout period of the coupon
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  $timeout = [];
  for($x=1;$x<=31;$x++)
  {
    $day = $x == 1 ? 'Day' : 'Days';
    $timeout["{$x}_day"] = "{$x} ".__($day,'bw-coupon');
  }

  for($x=1;$x<=12;$x++)
  {
    $month = $x == 1 ? 'Month' : 'Months';
    $timeout["{$x}_month"] = "{$x} ".__($month,'bw-coupon');
  }

  for($x=1;$x<=10;$x++)
  {
    $year = $x == 1 ? 'Year' : 'Years';
    $timeout["{$x}_year"] = "{$x} ".__($year,'bw-coupon');
  }
  return $timeout;
}


function bwc_parse_html($product_id,$coupon_id)
{
  $coupon_post = get_post( $coupon_id );
  $html        = get_post_meta( $product_id, 'giftcoupon_html', true );
  $coupon      = get_post($coupon_id);
  $code        = $coupon_post->post_title;
  $issue_date  = date_i18n(get_option( 'date_format' ) ,date('Y-m-d H:i:s'));
  $expiry_date = reset(get_post_meta($coupon_id,'expiry_date'));
  $expiry_date = date_i18n(get_option( 'date_format' ) , strtotime($expiry_date) ) ;
  $amount      = get_post_meta($coupon_id,'coupon_amount',true);
  $html = str_replace('[bwc_value]',$amount, $html);
  $html = str_replace('[bwc_code]',$code, $html);
  $html = str_replace('[bwc_expire_date]',$expiry_date, $html);
  $html = str_replace('[bwc_issue_date]',$issue_date, $html);
  return $html;
}


/**
 * Helper function to save pdfs
 *
 * @param string $body Body html to generate PDF.
 * @param string $pdf_name PDF Filename.
 */
function bwc_generate_pdf( $body, $pdf_name ) {
	$filename   = $pdf_name . '.pdf';
	$upload_dir = wp_upload_dir();
	$pathupload = $upload_dir['basedir'] . '/bwc';
	if ( wp_mkdir_p( $pathupload ) && ! file_exists( trailingslashit( $pathupload ) . '.htaccess' ) ) {
		$pdf_handle = fopen( trailingslashit( $pathupload ) . '.htaccess', 'w' );
		if ( $pdf_handle ) {
			fwrite( $pdf_handle, 'allow from all' );
			fclose( $pdf_handle );
		}
	}
	$dompdf = new Dompdf();
    $contxt = stream_context_create([ 
      'ssl' => [ 
          'verify_peer' => FALSE, 
          'verify_peer_name' => FALSE,
          'allow_self_signed'=> TRUE 
      ] 
    ]);

    $dompdf->setHttpContext($contxt);
	$dompdf->setPaper( 'A4', 'portrait' );
	$dompdf->set_option('isFontSubsettingEnabled', true);
    $dompdf->set_option('isRemoteEnabled', TRUE);
    $dompdf->set_option('isHtml5ParserEnabled', true);
	$dompdf->loadHtml( $body, get_bloginfo( 'charset' ) );
	$dompdf->render();
	$filepath = $pathupload . '/' . $filename;
	file_put_contents( $filepath, $dompdf->output() );
	return $filepath;
}
