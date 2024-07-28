<?php
/**
 * Plugin Name: BW Coupon
 * Description: This plugin lets WooCommerce customers purchase coupons as PDF ( Portable Document Format). You design the PDF with the WordPress Product HTML Editor. The Coupons are automatically generated and delivered to customers as PDF attachments 
 * Depends: WooCommerce
 * Version: 1.5.3
 * Requires PHP: 8.0
 * Author: Myridia.com Co., LTD.
 * Author URI: https://myridia.com/bw-coupon
 * Developer: Myridia.com Co., LTD.
 * Text Domain: bw-coupon 
 * Domain Path: /languages
 * WC requires at least: 8.9.1
 * WC tested up to: 8.9.1
 * WP requires at least: 6.5.2
 * WP tested up to: 6.6.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html 
 *
 * @package BW Coupon
 */

/**
 Code Style Standard:  https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/
*/
if ( ! defined( 'ABSPATH' ) ):
  exit;
endif;

define( 'BWC_VERSION', '1.5.3' );
define( 'BWC_DIR', plugin_dir_path( __FILE__ ) );
define( 'BWC_URL', plugin_dir_url( __FILE__ ) );
define( 'BWC_BASENAME', plugin_basename( __FILE__ ) );
require_once BWC_DIR . 'includes/class-bwc-data.php';
require_once BWC_DIR . 'includes/pdf.php';
require_once BWC_DIR . 'includes/coupons.php';
require_once BWC_DIR . 'includes/functions.php';
require_once BWC_DIR . 'includes/mail.php';


register_activation_hook( __FILE__, array( 'Bwc_Plugin', 'plugin_activation' ) );

class Bwc_Plugin
{
  public static function plugin_activation()
  {
    $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
    if ( in_array( $plugin_path, wp_get_active_and_valid_plugins() )):

  if ( ! function_exists( 'WC' ) ):
    throw new Exception( __( 'Please active first WooCommerce plugin.', "bw-coupon" ) );
  endif;

  global $wpdb;
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';

  $db_table = $wpdb->prefix . 'bwc';
  $collate = '';
  if ( $wpdb->has_cap( 'collation' ) ):
    $collate = $wpdb->get_charset_collate();
  endif;

  $sql = 'CREATE TABLE IF NOT EXISTS ' . $db_table . '(
		id_wgc INT(10) AUTO_INCREMENT PRIMARY KEY,
		id_user BIGINT(20) UNSIGNED NULL,
		id_coupon BIGINT(20) UNSIGNED NOT NULL,
		id_order BIGINT(20) UNSIGNED NOT NULL,
        coupon_msg text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
        dompdf mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
		send_by_email INT(10) DEFAULT 0,
        `send_date` TIMESTAMP NOT NULL DEFAULT 0,
		KEY woocomerce_key_coupon_generate_coupons (id_coupon),
		KEY woocomerce_key_order_generate_coupons (id_order),
		FOREIGN KEY (id_coupon) REFERENCES ' . $wpdb->prefix . 'posts(ID) ON DELETE CASCADE,
		FOREIGN KEY (id_order) REFERENCES ' . $wpdb->prefix . 'woocommerce_order_items(order_id) ON DELETE CASCADE
		) ' . $collate;

      $wpdb->query($sql);


    else:
      deactivate_plugins( plugin_basename( __FILE__ ) );
      $msg = '<div class="error"><p><strong>';
      $msg .=  sprintf( esc_html__( 'BW Coupons requires WooCommerce to run. You can download %s \
here.', 'wc-vendors' ), '<a href="' .  get_bloginfo( 'url' ) . '/wp-admin/plugin-install.php?s=Wo\
oCommerce&tab=search&type=term">WooCommerce</a><br/><a href="' .  get_bloginfo( 'url' ) . '/wp-ad\
min/plugins.php">Go Back</a>') ;
      $msg .=  '</strong></p></div>';
      wp_die( __($msg, 'my-plugin' ) );
    endif;
  }

}








register_deactivation_hook( __FILE__, 'bwc_deactivation' );
function bwc_deactivation()
{
  flush_rewrite_rules();
}


register_uninstall_hook( __FILE__, 'bwc_uninstall' );
function bwc_uninstall()
{
}



/**
 Hooks to allow special html css styles
*/
function bwc_add_css_attributes( $array )
{
   $array[] = 'top';
   $array[] = 'left';
   $array[] = 'position';
   return $array;
}


function bwc_remove_css_attributes( $array )
{
   $array[] = 'top';
   $array[] = 'left';
   $array[] = 'position';
   return $array;
}





