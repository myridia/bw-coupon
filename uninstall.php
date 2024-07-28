<?php
/**
 * BWC Uninstall
 *
 * Uninstalling BWC options.
 *
 * @author      BWC
 * @package     BWC/Uninstaller
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) )
{
  exit;
}

global $wpdb;

delete_option( 'bwc_hide_amount' );
delete_option( 'bwc_show_logo' );
delete_option( 'bwc_logo' );
delete_option( 'bwc_send' );
delete_option( 'bwc_bg_color_header' );
delete_option( 'bwc_bg_color_footer' );
delete_option( 'bwc_bg_color_title' );
delete_option( 'bwc_info_paragraph_type' );
delete_option( 'bwc_info_paragraph' );
delete_option( 'bwc_info_footer' );
delete_option( 'bwc_title_type' );
delete_option( 'bwc_title_h' );
delete_option( 'bwc_subject' );
delete_option( 'bwc_email_message' );

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bwc" );

// Clear any cached data that has been removed.
wp_cache_flush();
