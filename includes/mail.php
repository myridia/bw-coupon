<?php
/**
 * BWC Mail
 *
 * Sets up the mail functionallity.
 *
 * @author      BWC
 * @package     BWC/Mail
 */

if ( ! defined( 'ABSPATH' ) )
{
  exit;
}


function bwc_send_emails( $coupon_ids )
{
    bwc_logging('...bwc_send_emails fn');
    foreach( $coupon_ids as $k => $i ):
        bwc_logging( '...send email pdf to ' . $i);        
        $r =   bwc_send_email($i); //enable later
    endforeach;
}


function bwc_send_email( $coupon_id, $mailto=FALSE )
{

  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  $coupon = bwc_get_coupon( $coupon_id );
  $order  = new WC_Order( $coupon->id_order );
  $title  = get_post_meta( $coupon->id_order , '_billing_title', true );


  $coupon_post = get_post( $coupon_id );
  $coupon_code        = $coupon_post->post_title;


  $order_id = trim(str_replace('#', '', $order->get_order_number()));
  $last_name = $order->get_billing_last_name();

  if($mailto == FALSE):
    $mailto = $order->get_billing_email();
  endif;

  $r = FALSE;

  if( $mailto ):
    //var_dump("xxxxxxxxxxxxxxxxxxxxxxxxxxxxx");      
    //var_dump($coupon_id );
    //var_dump("xxxxxxxxxxxxxxxxxxxxxxxxxxxxx");  
    $pdf = bwc_create_pdf( $coupon_id );

    $_path = tempnam(sys_get_temp_dir(), __('Coupon','bw-coupon'). "_{$coupon_code}__");
    rename($_path, $_path .= '.pdf');
    file_put_contents( $_path, $pdf );

    $subject  = ! empty( get_option( 'bwc_subject' ) ) ? get_option( 'bwc_subject' ) : __('Coupon','bw-coupon');
    $message  = ! empty( get_option( 'bwc_email_message' ) ) ? get_option( 'bwc_email_message' ) : __( 'Please download the PDF', "bw-coupon" );
    $cc  = ! empty( get_option( 'bwc_cc_mail' ) ) ? get_option( 'bwc_cc_mail' ) : FALSE;
    $bcc  = ! empty( get_option( 'bwc_bcc_mail' ) ) ? get_option( 'bwc_bcc_mail' ) : FALSE;
    $message = bwc_email_format( $message , ucfirst($last_name) , $title , $coupon->id_order );

    $mail_settings = ! empty( get_option( 'woocommerce_email_from_address' ) ) ? get_option( 'woocommerce_email_from_address' ) : get_bloginfo( 'admin_email' );
    $from_settings = ! empty( get_option( 'woocommerce_email_from_name' ) ) ? get_option( 'woocommerce_email_from_name' ) : get_bloginfo( 'name' );
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= "Content-Type: text/html\n";
    $headers .= 'From: ' . $from_settings . ' <' . $mail_settings . '>' . "\r\n" . 'Reply-To: ' . $mail_settings . '' . "\r\n";

    if($bcc):
      $headers .= 'Bcc: '. $bcc . "\r\n";
    endif;    

    if($cc):
      $headers .= 'Cc: '. $cc . "\r\n";
    endif;    

    $r = wp_mail( $mailto , $subject, $message, $headers, [$_path] );

    unlink( $_path );
    if($r):
      bwc_update_send_by_email( $coupon_id );
      return $mailto;
    else:
      return FALSE;
    endif;

  endif;

  return FALSE;
}

function bwc_email_format( $message, $customer=FALSE, $title=FALSE, $order_id = '' )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  //Load the WP set language 
  $message = nl2br( $message );
  if($customer):   // Set Customer
    if($title == 2):
      $customer = __( 'Dear Ms','bw-coupon' ) . ' ' . $customer;
    elseif($title == 1):
      $customer = __( 'Dear Mr' , 'bw-coupon' ) . ' ' . $customer;
    else:
      $customer = __( 'Hi' , 'bw-coupon' ) . ' ' . $customer;
    endif;
    $message = str_replace( '[customer]' , $customer , $message );
  else:
    $message = str_replace( '[customer]' , 'Liebe Frau Katz' , $message );
  endif;

  if( $order_id ):
    $order  = new WC_Order( $order_id );
    $message = str_replace( '[order_id]' , $order_id , $message );  
    $message = str_replace( '[order_date]' , date_i18n( get_option( 'date_format' ) ,$order->get_date_modified()->getOffsetTimestamp()) , $message );   
  else:
    $message = str_replace( '[order_id]' , '1234' ,$message);  
    $message = str_replace( '[order_date]' , date_i18n(get_option( 'date_format' ) , time() )  ,$message );  
  endif;


  return $message;
}


function bwc_send_test_email( $mailto=FALSE, $customer=FALSE, $title=False, $order_id='' )
{
  load_plugin_textdomain( 'bw-coupon', false, 'bw-coupon/languages' );  
  if( $mailto ):
    $subject  = ! empty( get_option( 'bwc_subject' ) ) ? get_option( 'bwc_subject' ) : __('Coupon','bw-coupon');
    $message  = ! empty( get_option( 'bwc_email_message' ) ) ? get_option( 'bwc_email_message' ) : '';
    $mail_settings = ! empty( get_option( 'woocommerce_email_from_address' ) ) ? get_option( 'woocommerce_email_from_address' ) : get_bloginfo( 'admin_email' );
    $from_settings = ! empty( get_option( 'woocommerce_email_from_name' ) ) ? get_option( 'woocommerce_email_from_name' ) : get_bloginfo( 'name' );
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= "Content-Type: text/html\n";
    $headers .= 'From: ' . $from_settings . ' <' . $mail_settings . '>' . "\r\n" . 'Reply-To: ' . $mail_settings . '' . "\r\n";
    $message = bwc_email_format($message, $customer, $title, $order_id);
    $cc  = ! empty( get_option( 'bwc_cc_mail' ) ) ? get_option( 'bwc_cc_mail' ) : FALSE;
    $bcc  = ! empty( get_option( 'bwc_bcc_mail' ) ) ? get_option( 'bwc_bcc_mail' ) : FALSE;

    if($bcc):
      $headers .= 'Bcc: '. $bcc . "\r\n";
    endif;    

    if($cc):
      $headers .= 'Cc: '. $cc . "\r\n";
    endif;    

    $r = wp_mail( $mailto , $subject, $message, $headers);
    return $r;
  endif;
}
