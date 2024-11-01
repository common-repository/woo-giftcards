<?php ob_start();
$redeem_url    = site_url() .'/?coupon_code='. $code;
include_once plugin_dir_path( __FILE__ ) . 'woo-giftcards-email-template.php';
$message       = ob_get_clean();
$heading       = $sender_name . __( ' sent you a Gift!', 'woo-giftcards' );

// Get woocommerce mailer from instance
$mailer = WC()->mailer();
  // Wrap message using woocommerce html email template
$wrapped_message = $mailer->wrap_message($heading, $message);
  // Create new WC_Email instance
$wc_email = new WC_Email;
  // Style the wrapped message with woocommerce inline styles
$html_message = $wc_email->style_inline($wrapped_message);
  // Send the email using wordpress mail function
$headers[]     = 'Content-Type: text/html; charset=UTF-8';
$headers[]     = 'From: '. $wc_email->get_from_name() .' <'. $wc_email->get_from_address() .'>';
if( wp_mail( $to, $subject, $html_message, $headers ) ){
	if( defined('DOING_AJAX') && DOING_AJAX ){
	echo __( "success", 'woo-giftcards' );
	die();
}
}