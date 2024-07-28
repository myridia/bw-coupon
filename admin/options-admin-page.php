<?php
/**
 * BWC Options Admin Page
 *
 * Sets up the options plugin.
 *
 * @author      BWC
 * @package     BWC/Options Admin Page
 */

?>

<?php
if ( ! empty( $_GET['coupon_id'] ) ):
  $coupon_id =  sanitize_text_field($_GET['coupon_id']);
  $r = bwc_send_email($coupon_id);
  if($r):
      echo __('Successfully Send Coupon PDF','bw-coupon') . ' ' . $coupon_id;
  endif;

else:
?>

<div class="wrap">
	<div id="bwc">
		<div id="gs-middle">
			<?php require_once BWC_DIR . 'admin/includes/gs-middle-options-page.php'; ?>
		</div>
	</div>
</div>

<?php
endif;

?>