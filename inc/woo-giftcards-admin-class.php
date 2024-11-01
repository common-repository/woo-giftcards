<?php

    // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_GiftCards_Admin{

	public function __construct(){
		if ( defined( 'WOO_GIFTCARDS_VERSION' ) ) {
			$this->version = WOO_GIFTCARDS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'woo_giftcard_admin_scripts' ) );
		add_action( 'admin_menu', array( $this, 'woo_giftcards_admin_menu' ) );
		add_action( 'wp_ajax_test_email', array( $this, 'callback_test_email' ) );
		add_action( 'wp_ajax_nopriv_test_email', array( $this, 'callback_test_email' ) );
	}

	public function woo_giftcard_admin_scripts( $hook ){
  		wp_enqueue_script( 'woo_giftcard_admin_js', plugin_dir_url( __FILE__ ) . '../js/admin.js', array('jquery'), $this->version, false );
		if( $hook == 'toplevel_page_woo-giftcards' ) {
			wp_enqueue_style( 'woo_giftcard_admin_css', plugin_dir_url( __FILE__ ) . '../css/admin.css', '', $this->version );
		} 
	}

	public function woo_giftcards_admin_menu(){
		add_menu_page(
			__( 'Gift Cards for WooCommerce', 'woo-giftcards' ),
			__( 'Woo Gift Cards', 'woo-giftcards' ),
			'manage_options',
			'woo-giftcards',
			array( $this, 'woo_giftcards_admin_menu_callback' ),
			'dashicons-admin-generic',
			59
		);
	}

	public function woo_giftcards_admin_menu_callback(){

		echo '<h1>'. esc_html(get_admin_page_title()) .'</h1><h3>'. esc_html($this->version) .'</h3>';

		echo '<div class="woo-giftcard-admin-settings">';
		$this->woo_giftcards_email_settings();
		echo '</div>';

	}


	public function woo_giftcards_email_settings(){ ?>
		<h2><?php _e( 'Documentation', 'woo-giftcards' ); ?></h2>
		<p>
			<a href="https://ahmadshyk.com/woocommerce-gift-cards-documentation/" target="_blank"><?php _e( 'Click Here ' ) ?></a> <?php _e( 'to know how Gift Cards for WooCommerce plugin works.' ) ?>
		</p>
		<h2><?php _e( 'Test Email', 'woo-giftcards' ); ?></h2>
		<form name="woo-giftcard-test-email">
			<label class="woo-giftcard-setting-label"><?php _e( 'Enter email address where you want to receive test email', 'woo-giftcards' ); ?></label>
			<input type="email" class="woo-giftcard-admin-textfield" id="woo-giftcard-test-input" required="required">
			<button type="button" onclick="test_email()" id="woo-giftcard-test-email" class="test-email button-primary"><?php _e( 'Send Email', 'woo-giftcards' ); ?></button>
			<span id="test-email-status"></span>
		</form>

		<form name="woo-giftcards-email-settings">
			<h2><?php _e( 'Email Template Settings', 'woo-giftcards' ); ?></h2>


			<div class="giftcard-pt-field">
				<p>
					<?php _e( 'Gift Cards for WooCommerce plugin uses default WooCommerce Email Template. To change sender options and other colors changes', 'woo-giftcards' ); ?>
				</p>
				&nbsp;
				<h3>
					<a href="admin.php?page=wc-settings&tab=email"><?php _e( 'Click here' ) ?></a>
				</h3>
			</div>

			<h2><?php _e( 'General', 'woo-giftcards' ); ?></h2>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Code Prefix', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>

			<h2><?php _e( 'Frontend Labels & Messages', 'woo-giftcards' ); ?></h2>
			<p><?php _e( 'Note: If your site is multilingual, you\'ll need to leave these fields empty and translate built-in strings', 'woo-giftcards' ); ?></p>
			<br>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Recipient Name Label', 'woo-giftcards' ); ?>
				</label>
				<input type="text" name="recipient-name-label" class="pro-only" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Recipient Name Description', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="recipient-name-desc" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Recipient Email Label', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="recipient-email-label" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Recipient Email Description', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="recipient-email-desc" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Message Label', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="recipient-msg-label" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Message Description', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="recipient-msg-desc" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Name or Email Field Empty Text', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="form-validation-text" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Name and Email Numbers not Equal Text', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="form-validation-mismatch" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Form Invalid Email Text', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="form-validation-email" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Shop Page Button Text For Giftcard', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" readonly="readonly" name="woo-giftcard-shop-button" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>

			<h2><?php _e( 'Email Content Settings', 'woo-giftcards' ); ?></h2>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Gift Icon URL', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" name="woo-giftcard-email-gift-icon" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Subject', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" name="woo-giftcard-email-subject" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Heading', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" name="woo-giftcard-email-heading" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Text before Coupon', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" name="woo-giftcard-email-text-before" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Coupon Content', 'woo-giftcards' ); ?>
				</label>
				<textarea class="pro-only" name="woo-giftcard-email-coupon" readonly="readonly"><?php _e( 'Available in pro version only', 'woo-giftcards' ); ?></textarea>
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Redeem Button Text', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" name="woo-giftcard-email-redeem-button-text" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>
			<div class="giftcard-pt-field">
				<label class="woo-giftcard-setting-label">
					<?php _e( 'Giftcard Email Text after Coupon', 'woo-giftcards' ); ?>
				</label>
				<input type="text" class="pro-only" name="woo-giftcard-email-text-after" readonly="readonly" value="<?php _e( 'Available in pro version only', 'woo-giftcards' ); ?>">
			</div>

			<h2><?php _e( 'Shortcode to check remaining gift card amount <span style="color:red;">(<a href="https://ahmadshyk.com/item/woocommerce-gift-cards-pro/" target="_blank">Pro</a> Version Only)</span>', 'woo-giftcards-pro' ); ?></h2>
			<div class="giftcard-pt-field">
				<p>
					<?php _e( 'Use the below shortcode anywhere to check remaining amount on giftcard. This works if previous order total was less than the giftcard amount.', 'woo-giftcards-pro' ); ?>
				</p>
				<strong>[woo-giftcard-remaining not-found="Gift Card Invalid" label="Enter Gift Card Code" button="Submit" success="Remaining amount is {amount}"]</strong>
				&nbsp;
			</div>
		</form>
		<div class="pro-info">
			<h3>
				<strong>Gift Cards for WooCommerce Pro</strong> also offers:
			</h3>
			<ul>
				<li>Add Giftcards as variable products.</li>
				<li>An option to send multiple gift cards to multiple recipients.</li>
				<li>An option to set gift card expiry after purchase.</li>
				<li>An option to allow free shipping or not when gift card is use.</li>
				<li>Multiple time Gift Card Code usage (If the redeem order is less than the gift card amount)</li>
			</ul>
			<h2>
				Send an email to <a href="mailto:a.hassan@ahmadshyk.com">a.hassan@ahmadshyk.com</a> to know more about <a href="https://ahmadshyk.com/item/woocommerce-gift-cards-pro/" target="_blank">Pro</a> Version.
			</h2>
		</div>
		<?php 
	}
	public function woo_giftcards_email_test( $to, $to_name, $sender_name, $subject, $gift_message, $gift_amount, $code ){
		include_once plugin_dir_path( __FILE__ ) . 'woo-giftcards-email.php';
	}

	public function callback_test_email(){
		$to             = sanitize_email( $_REQUEST['to_email'] );
		$to_name        = __( 'John', 'woo-giftcards' );
		$sender_name    = __( 'Ahmad', 'woo-giftcards' );
		$gift_message   = __( 'Happy Birthday Dear! Here\'s a small gift for you, please accept', 'woo-giftcards' );
		$gift_amount    = 500;
		$code           = '5962-5155-782f-7735';
		$subject        = $sender_name . __( ' sent you a Gift!', 'woo-giftcards' );
		$mail           = $this->woo_giftcards_email_test( $to, $to_name, $sender_name, $subject, $gift_message, $gift_amount, $code );
	}

}