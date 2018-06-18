<?php
/**
 * Checkout Form
 *
 * This is an overridden copy of the woocommerce/templates/checkout/form-checkout.php file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// check the WooCommerce MultiStep Checkout options
$options = get_option('wmsc_options');
require_once 'settings-array.php';
if ( !is_array($options) || count($options) === 0 ) {
    $defaults = get_wmsc_settings();
    $options = array();
    foreach($defaults as $_key => $_value ) {
        $options[$_key] = $_value['value'];
    }
} 
$options = array_map('stripslashes', $options);

extract($options);

if ( !$show_shipping_step ) $unite_billing_shipping = false;

/*
$unite_billing_shipping = false;
$unite_order_payment = false;
$show_shipping_step = true;
$show_back_to_cart_button = true;
*/

// check the WooCommerce options
$show_login_step = ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) ? false : true;
$stop_at_login = ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) ? true : false;
$checkout_url = apply_filters( 'woocommerce_get_checkout_url', version_compare( '2.5', WC()->version, '<=' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url() );

// show the tabs
include_once('form-tabs.php');

?>

<div style="clear: both;"></div>

<div class="wpmc-steps-wrapper">

<?php wc_print_notices(); ?>

<div id="checkout_coupon" class="woocommerce_checkout_coupon" style="display: none;">
	<?php do_action( 'wpmc-woocommerce_checkout_coupon_form', $checkout ); ?>
</div>
<?php if( $show_login_step ) : ?>
	<!-- Step login -->
	<div class="wpmc-step-item wpmc-step-login">
			<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
			<div id="checkout_login" class="woocommerce_checkout_login wp-multi-step-checkout-step">
			<?php
			woocommerce_login_form(
				array(
					'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer, please proceed to the Billing &amp; Shipping section.', 'woocommerce' ),
					'redirect' => wc_get_page_permalink( 'checkout' ),
					'hidden'   => false,
				)
			);
			?>
			</div>
			<?php
			if ( $stop_at_login ) {
				echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
			}
			?>
	</div>
<?php endif; ?>

<?php if ( $stop_at_login ) { echo '</div>'; return; } ?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $checkout_url ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

			<!-- Step Billing -->
			<div class="wpmc-step-item wpmc-step-billing">
                <?php if ( !$show_login_step ) do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			<?php if ( ! $unite_billing_shipping) : ?></div><?php endif; ?>

			<!-- Step Shipping -->
			<?php if ( $show_shipping_step ) : ?>
				<?php if ( ! $unite_billing_shipping) : ?><div class="wpmc-step-item wpmc-step-shipping"><?php endif; ?>
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<!-- Step Review Order -->
	<div class="wpmc-step-item wpmc-step-review">
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
		<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		<?php do_action( 'wpmc-woocommerce_order_review' ); ?>

		<!-- Step 5: Payment info -->
	  <?php if ( ! $unite_order_payment ) : ?></div><div class="wpmc-step-item wpmc-step-payment"><?php endif; ?>
      <h3 id="payment_heading"><?php _e( 'Payment', 'woocommerce' ); ?></h3>
      <?php do_action( 'wpmc-woocommerce_checkout_payment' ); ?>
			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>

<?php include_once('form-buttons.php'); ?>
