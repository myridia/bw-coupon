<?php
/**
 * BWC Options Admin Page Settings
 *
 * Sets up the options plugin.
 *
 * @author      BWC
 * @package     BWC/Options Admin Page Settings
 */

if ( ! empty( $_POST['submit'] ) ):
  /* Santitize */
  $bwc_send           = isset( $_POST['bwc_send'] )          ? sanitize_text_field( $_POST['bwc_send'] )    : null;
  $bwc_subject        = isset( $_POST['bwc_subject'] )       ? sanitize_text_field( $_POST['bwc_subject'] ) : null ;
  $bwc_email_message  = isset( $_POST['bwc_email_message'] ) ? wp_kses_post( $_POST['bwc_email_message'] )  : null ;
  $bwc_cc_mail        = isset( $_POST['bwc_cc_mail'] )       ? sanitize_text_field( $_POST['bwc_cc_mail'] ) : null ;
  $bwc_bcc_mail       = isset( $_POST['bwc_bcc_mail'] )      ? sanitize_text_field( $_POST['bwc_bcc_mail'] ) : null ;


  /* Validate */
  if($bwc_subject && $bwc_email_message ):
    update_option( 'bwc_send', $bwc_send );
    update_option( 'bwc_subject', $bwc_subject );
    update_option( 'bwc_email_message', $bwc_email_message );
    update_option( 'bwc_bcc_mail', $bwc_bcc_mail );
    update_option( 'bwc_cc_mail', $bwc_cc_mail );

    print '<div class="message">';
    print '<div class="updated inline updated"><p><strong>';
    print __( 'Your settings have been saved.','bw-coupon' );
    print '</strong></p>';
    print '</div>';
  else:
    print '<div class="message">';
    print '<div class="updated inline updated"><p><strong>';
    print __( 'Missing input!','bw-coupon' );
    print '</strong></p>';
    print '</div>';
  endif;
endif;


if ( ! empty( $_POST['send_test_email'] ) ):

  /* Santitize */
  $bwc_send           = isset( $_POST['bwc_send'] )          ? sanitize_text_field( $_POST['bwc_send'] )    : null;
  $bwc_subject        = isset( $_POST['bwc_subject'] )       ? sanitize_text_field( $_POST['bwc_subject'] ) : null ;
  $bwc_email_message  = isset( $_POST['bwc_email_message'] ) ? wp_kses_post( $_POST['bwc_email_message'] )  : null ;
  $test_mail       = isset( $_POST['test_mail'] )      ? sanitize_text_field( $_POST['test_mail'] ) : null ;
  $bwc_cc_mail        = isset( $_POST['bwc_cc_mail'] )       ? sanitize_text_field( $_POST['bwc_cc_mail'] ) : null ;
  $bwc_bcc_mail       = isset( $_POST['bwc_bcc_mail'] )      ? sanitize_text_field( $_POST['bwc_bcc_mail'] ) : null ;


  /* Validate */
  if($bwc_subject && $bwc_email_message ):
    update_option( 'bwc_send', $bwc_send );
    update_option( 'bwc_subject', $bwc_subject );
    update_option( 'bwc_email_message', $bwc_email_message );
    update_option( 'bwc_cc_mail', $bwc_cc_mail );
    update_option( 'bwc_bcc_mail', $bwc_bcc_mail );


    print '<div class="message">';
    print '<div class="updated inline updated"><p><strong>';
    print __( 'Your settings have been saved.','bw-coupon' );
    print '</strong></p>';
    print '</div>';

    $r = bwc_send_test_email($test_mail);

    if($r):
      $send_email = __( 'Test Email has been send to ','bw-coupon' ) . $test_mail ;
    else:
      $send_email = __( 'Test Email failed to send','bw-coupon' ) ;
    endif;

    print '<div class="message">';
    print '<div class="updated inline updated"><p><strong>';
    echo $send_email;
    print '</strong></p>';
    print '</div>';
  else:
    print '<div class="message">';
    print '<div class="updated inline updated"><p><strong>';
    print __( 'Missing input!','bw-coupon' );
    print '</strong></p>';
    print '</div>';
  endif;
endif;



$bwc_send           = get_option( 'bwc_send' );
$bwc_subject        = get_option( 'bwc_subject' );
$bwc_email_message  = get_option( 'bwc_email_message' );
$bwc_cc_mail        = get_option( 'bwc_cc_mail' );
$bwc_bcc_mail       = get_option( 'bwc_bcc_mail' );


$settings_tinymce = [
  '_content_editor_dfw'  => 1,
  'drag_drop_upload'     => true,
  'tabfocus_elements'    => 'content-html,save-post',
  'editor_height'        => 150,
  'tinymce'              => [
  'resize'               => false,
  'wp_autoresize_on'     => 1,
  'add_unload_trigger'   => false,
  ],
  'media_buttons'        => false,
  'quicktags'     => array("buttons"=>"link,strong,code,del,block,more,ins,em,li,ol,ul,close"),
];
?>

<div class="container">
	<form name="bwc_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ); ?>">
		<div class="wgc-box basic-information">
			<div class="header medium">
				<span class="step">1 - </span><div class="title"><?php echo  __( 'Basic configuration:', "bw-coupon" ); ?>:</div>
			</div>
			<div class="wgc-box-body">
				<table class="form-table">
					<tr>
						<th>
							<?php echo __( 'Generate Coupons', "bw-coupon" ); ?>:
						</th>
						<td>
							<select name="bwc_send" id="bwc_send">
								<?php
									$order_status = array(
										0 => __( 'Generate coupons mannually', "bw-coupon" ),
										1 => __( 'Generate coupons automatically on complete order status', "bw-coupon" ),
										2 => __( 'Generate coupons automatically on processing order status', "bw-coupon" ),
									);
									foreach ( $order_status as $key => $order ) {
										if ( isset( $bwc_send ) && $bwc_send == $key ) {
											$selected = 'selected';
										} else {
											$selected = false;
										}
										print '<option value="' . $key . '" ' . $selected . '>' . $order . '</option>';
									}
								?>
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="wgc-box">
			<div class="header medium">
				<span class="step">2 - </span><div class="title"><?php esc_html_e( 'Email configuration:', "bw-coupon" ); ?></div>
			</div>
			<div class="wgc-box-body">
				<table class="form-table">

			        <tr>
						<th>
							<label for="bwc_cc_mail" title="<?php esc_html_e( 'Send copy email', "bw-coupon" ); ?>" ><?php esc_html_e( 'Cc', "bw-coupon" ); ?>:</label>
						</th>
						<td>
							<input type="text" id="bwc_cc_mail" name="bwc_cc_mail" value="<?php echo $bwc_cc_mail ?>" /><br/>                           
                        </td>

					</tr>

			        <tr>
						<th>
							<label for="bwc_bcc_mail" title="<?php esc_html_e( 'Send hidden copy email', "bw-coupon" ); ?>" ><?php esc_html_e( 'Bcc', "bw-coupon" ); ?>:</label>
						</th>
						<td>
							<input type="text" id="bwc_bcc_mail" name="bwc_bcc_mail" value="<?php echo $bwc_bcc_mail ?>" /><br/>                           
						</td>
					</tr>

					<tr>
						<th>
							<label for="bwc_subject"><?php esc_html_e( 'Subject:', "bw-coupon" ); ?>:</label>
						</th>
						<td>
							<input type="text" id="bwc_subject" name="bwc_subject" value="<?php echo $bwc_subject; ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="bwc_email_message"><?php esc_html_e( 'Message:', "bw-coupon" ); ?>:</label>
						</th>
						<td>
							<?php wp_editor( $bwc_email_message, 'bwc_email_message', ['wp_editor'=>'','textarea_rows'=> '20'] ); ?>
						</td>
					</tr>



			        <tr>
						<th>
							<label for="test_mail"><?php esc_html_e( 'Test Mail', "bw-coupon" ); ?>:</label>
						</th>
						<td>
							<input type="text" id="test_mail" name="test_mail" value="<?php echo get_option('admin_email') ?>" /><br/>                           
            				<input type="submit" class="button button-primary" name="send_test_email" id="send_test_email" value="<?php esc_html_e( 'Send test e-mail', "bw-coupon" ); ?>" />
						</td>
					</tr>

				</table>
			</div>
		</div>

		<div class="block">
			<p class="submit">
				<input type="submit" class="button button-primary" name="submit" id="submit" value="<?php esc_html_e( 'Save options', "bw-coupon" ); ?>" />
			</p>
		</div>
	</form>
</div>



