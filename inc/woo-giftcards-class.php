<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Woo_GiftCards{

	public function __construct(){
		if ( defined( 'WOO_GIFTCARDS_VERSION' ) ) {
			$this->version = WOO_GIFTCARDS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		add_action( 'add_meta_boxes', array( $this, 'woo_giftcards_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'woo_giftcard_save' ) );
		add_filter( 'product_type_options', array( $this, 'woo_giftcard_option' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'woo_giftcard_option_save' ) );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'woo_giftcard_add_to_cart_button_link' ), 10, 2 );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woo_giftcard_single_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'woo_giftcards_scripts' ) );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'woo_giftcard_cart_item_name' ), 99, 3);
		add_action( 'woocommerce_after_order_notes', array( $this, 'woo_giftcard_checkout_fields' ) );
		if( !is_admin() ){
			add_filter( 'woocommerce_cart_needs_shipping', array( $this, 'woo_giftcard_no_shipping' ) );
		}
		add_action( 'woocommerce_checkout_process', array( $this, 'woo_giftcards_checkout_fields_validation' ) );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'woo_giftcards_checkout_fields_save' ), 20, 2 );
		add_action( 'woocommerce_thankyou', array( $this, 'woo_giftcard_order_processing' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'woo_giftcard_order_process' ) );
		add_filter( 'woocommerce_is_sold_individually', array( $this, 'woo_giftcard_include_single' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woo_giftcard_check_cart' ), 10, 3 );
		add_filter( 'woocommerce_checkout_coupon_message', array( $this, 'woo_giftcard_coupon_message_on_checkout' ) );
		add_action( 'wp_ajax_resend_email', array( $this, 'callback_resend_email' ) );
		add_action( 'wp_ajax_nopriv_resend_email', array( $this, 'callback_resend_email' ) );
		$shopCoupon = 'shop_coupon';
		add_filter( 'manage_edit-shop_coupon_columns', array( $this, 'woo_giftcard_new_coupon_column' ) );
		add_action( 'manage_posts_custom_column',  array( $this, 'woo_giftcard_coupon_column_data' ) );
		add_action( 'wp_footer', array( $this, 'woo_giftcards_get_coupon_code_to_session' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'woo_giftcards_add_discount_to_checkout' ), 10, 0 );
	}

	public function woo_giftcards_scripts(){
		wp_enqueue_style( 'woo-giftcards-css', plugins_url( '../css/woo-giftcards.css', __FILE__ ), '', $this->version );
		wp_enqueue_script( 'woo-giftcards-js', plugins_url( '../js/woo-giftcards.js', __FILE__ ), array( 'jquery' ), $this->version, true );
	} 

	public function woo_giftcards_add_meta_box() {
		add_meta_box(
			'woo_giftcards',
			__( 'Gift Card', 'woo-giftcards' ),
			array( $this, 'woo_giftcards_meta_html' ),
			'shop_coupon',
			'normal',
			'default'
		);
	}

	public function woo_giftcards_meta_html( $post ) {
		wp_nonce_field( '_woo_giftcard_nonce', 'woo_giftcard_nonce' ); ?>

		<p class="giftcard-pt-field">
			<label for="woo_giftcard_to_friend_name"><?php _e( 'To: Recipient Name', 'woo-giftcards' )[0]; ?></label><br>
			<input type="text" name="woo_giftcard_to_friend_name" id="woo_giftcard_to_friend_name" value="<?php echo esc_attr(get_post_meta( $post->ID, 'woo_giftcard_to_friend_name' )[0]); ?>">
		</p>    
		<p class="giftcard-pt-field">
			<label for="woo_giftcard_to_friend_email"><?php _e( 'To: Recipient Email', 'woo-giftcards' ); ?></label><br>
			<input type="text" name="woo_giftcard_to_friend_email" id="woo_giftcard_to_friend_email" value="<?php echo esc_attr(get_post_meta( $post->ID, 'woo_giftcard_to_friend_email' )[0]); ?>">
		</p>    
		<p class="giftcard-pt-field">
			<label for="woo_giftcard_from_sender_name"><?php _e( 'From: Sender Name', 'woo-giftcards' ); ?></label><br>
			<input type="text" name="woo_giftcard_from_sender_name" id="woo_giftcard_from_sender_name" value="<?php echo esc_attr(get_post_meta( $post->ID, 'woo_giftcard_from_sender_name' )[0]); ?>">
		</p>    
		<p class="giftcard-pt-field">
			<label for="woo_giftcard_from_sender_email"><?php _e( 'From: Sender Email', 'woo-giftcards' ); ?></label><br>
			<input type="text" name="woo_giftcard_from_sender_email" id="woo_giftcard_from_sender_email" value="<?php echo esc_attr(get_post_meta( $post->ID, 'woo_giftcard_from_sender_email' )[0]); ?>">
		</p>    
		<p class="giftcard-pt-field">
			<label for="woo_giftcard_message"><?php _e( 'Gift Card Message', 'woo-giftcards' ); ?></label><br>
			<textarea rows="3" name="woo_giftcard_message" id="woo_giftcard_message"><?php echo esc_attr(get_post_meta( $post->ID, 'woo_giftcard_message' )[0]); ?></textarea>
		</p>
		<p>
			<button type="button" onclick="resend_email()" id="woo-giftcard-resend-email" class="resend-email button-primary"><?php _e( 'Resend Email', 'woo-giftcards' ); ?></button>
			<h3 id="resend-email-status"></h3>
		</p>
		<input type="hidden" name="woo_giftcard_order_number">

		<?php
	}

	public function woo_giftcard_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['woo_giftcard_nonce'] ) || ! wp_verify_nonce( $_POST['woo_giftcard_nonce'], '_woo_giftcard_nonce' ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		if ( isset( $_POST['woo_giftcard_to_friend_name'] ) )
			update_post_meta( $post_id, 'woo_giftcard_to_friend_name', sanitize_text_field( $_POST['woo_giftcard_to_friend_name'] ) );
		if ( isset( $_POST['woo_giftcard_to_friend_email'] ) )
			update_post_meta( $post_id, 'woo_giftcard_to_friend_email', sanitize_email( $_POST['woo_giftcard_to_friend_email'] ) );
		if ( isset( $_POST['woo_giftcard_from_sender_name'] ) )
			update_post_meta( $post_id, 'woo_giftcard_from_sender_name', sanitize_text_field( $_POST['woo_giftcard_from_sender_name'] ) );
		if ( isset( $_POST['woo_giftcard_from_sender_email'] ) )
			update_post_meta( $post_id, 'woo_giftcard_from_sender_email', sanitize_email( $_POST['woo_giftcard_from_sender_email'] ) );
		if ( isset( $_POST['woo_giftcard_message'] ) )
			update_post_meta( $post_id, 'woo_giftcard_message', sanitize_textarea_field( $_POST['woo_giftcard_message'] ) );
	}

	public function woo_giftcard_option( $product_type_options ) {
		$product_type_options['woo_giftcard'] = array(
			'id'            => '_woo_giftcard',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Gift Card', 'woo-giftcards' ),
			'default'       => 'no'
		);

		return $product_type_options;
	}

	public function woo_giftcard_option_save( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$is_giftcard = isset( $_POST['_woo_giftcard'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_woo_giftcard', $is_giftcard );
	}

	public function woo_giftcard_add_to_cart_button_link( $link, $product ) {

		$is_gift_card = get_post_meta( $product->get_id(), '_woo_giftcard', true );
		if( $is_gift_card == 'yes' )
		{
			$link = sprintf( '<a href="%s" rel="nofollow" class="button">%s</a>',
				esc_url( $product->get_permalink() ),
				__( 'Buy Gift Card', 'woo-giftcards' ) 
			);
		}
		return $link;
	}

	public function woo_giftcard_single_form(){
		global $product;
		$is_gift_card = get_post_meta( $product->get_id(), '_woo_giftcard', true );
		if( !$is_gift_card || $is_gift_card == 'no' )
			return;
		$this->woo_giftcard_form();
	}

	public function woo_giftcard_form(){

		global $product;
		$is_gift_card = get_post_meta( $product->get_id(), '_woo_giftcard', true );
		if($is_gift_card == 'yes')
		{
			?>
			<div container-id="<?php echo esc_attr($product->get_id()); ?>" class="giftcard-container giftcard-container-<?php echo esc_attr($product->get_id()); ?>">
				<input type="hidden" name="giftcard-form-submit" value="1">
				<div class="giftcard-form-field">
					<label>
						<?php _e( "TO: RECIPIENT NAME *", 'woo-giftcards' ); ?>
					</label>
					<input type="text" name="giftcard-friend-name" class="giftcard-textfield" required="true">
				</div>
				<div class="giftcard-form-field">
					<label>
						<?php _e( "TO: RECIPIENT EMAIL *", 'woo-giftcards' ); ?>
					</label>
					<input type="email" name="giftcard-friend-email" class="giftcard-textfield" required="true">
				</div>
				<div class="giftcard-form-field">
					<label>
						<?php _e( 'MESSAGE *', 'woo-giftcards' ); ?>
					</label>
					<textarea rows="3" class="giftcard-textarea" name="giftcard-message"></textarea>
				</div>
			</div>
		<?php }
	}

	public function woo_giftcard_cart_item_name( $name, $cart_item, $cart_item_key ) {
		$product = $cart_item['data'];
		$is_gift_card = get_post_meta( $product->get_id(), '_woo_giftcard', true );
		if ( $is_gift_card == 'yes' ) {
			$name .= '</br><strong>To: </strong>'. WC()->session->get( 'giftcard-friend-name' );
			return $name; 
		} 
		return $name;
	}

	public function woo_giftcard_checkout_fields($checkout)
	{
		foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$cart_product = $values['data'];
			$product_ids[]   = $cart_product->get_id();
		}
		$giftcard = 0;
		foreach ( $product_ids as $product_id ) {
			$is_gift_card = get_post_meta( $product_id, '_woo_giftcard', true );
			if( $is_gift_card == 'yes' ){
				$giftcard = 1;
			}
		}
		if( $giftcard == 0 ){
			return;
		}

		echo '<div id="gift_checkout_field"><h2>' . __( 'Gift Card', 'woo-giftcards' ) . '</h2>';

		woocommerce_form_field('_giftcard_friend_name', array(
			'type'        => 'text',
			'class'       => array( 'form-row-wide' ),
			'required'    => true,
			'label'       => __( 'To: Recipient Name', 'woo-giftcards' ) ,
			'default'     => WC()->session->get( 'giftcard-friend-name' ),
		),
		$checkout->get_value('giftcard-friend-name'));

		woocommerce_form_field( '_giftcard_friend_email', array(
			'type'        => 'email',
			'label'       => __( 'To: Recipient Email', 'woo-giftcards' ) ,
			'class'       => array( 'form-row-wide' ),
			'required'    => true,
			'default'     => WC()->session->get( 'giftcard-friend-email' ),
		), $checkout->get_value('_friend_email') );

		woocommerce_form_field('_giftcard_message', array(
			'type'        => 'textarea',
			'class'       => array( 'form-row-wide' ),
			'required'    => false,
			'label'       => __( 'Gift Card Message', 'woo-giftcards' ) ,
			'default'     => WC()->session->get( 'giftcard-message' ),
		),
		$checkout->get_value('giftcard-friend-message'));

		echo '</div>';

	}

	public function woo_giftcards_checkout_fields_validation() {
		if( isset($_POST['_giftcard_friend_name']) && empty($_POST['_giftcard_friend_name']) )
			wc_add_notice( __( 'Please fill in the <strong>Recipient Name</strong> field.', 'woo-giftcards' ), 'error' );
		if( isset($_POST['_giftcard_friend_email']) && empty($_POST['_giftcard_friend_email']) )
			wc_add_notice( __( 'Please fill in the <strong>Recipient Email</strong> field.', 'woo-giftcards' ), 'error' );
		elseif( isset($_POST['_giftcard_friend_email']) && !is_email($_POST['_giftcard_friend_email'] ) ){
			wc_add_notice( __( 'Please enter a valid <strong>Recipient Email</strong>.', 'woo-giftcards' ), 'error' );
		}
	}

	public function woo_giftcards_checkout_fields_save( $order, $data ) {

		$friend_name       = sanitize_text_field( $_POST['_giftcard_friend_name'] );
		$friend_email      = sanitize_email( $_POST['_giftcard_friend_email'] );
		$giftcard_message  = sanitize_textarea_field( $_POST['_giftcard_message'] );

		if ( isset( $_POST['_giftcard_friend_name'] ) ) {
			$order->update_meta_data( '_giftcard_friend_name', $friend_name );
		}
		if ( isset( $_POST['_giftcard_friend_email'] ) ) {
			$order->update_meta_data( '_giftcard_friend_email', $friend_email );
		}
		if ( isset( $_POST['_giftcard_message'] ) ) {
			$order->update_meta_data( '_giftcard_message', $giftcard_message );
		}
	}

	public function woo_giftcard_no_shipping( $shipping ){
		if ( WC()->cart && !WC()->cart->is_empty() ) {
			foreach ( WC()->cart->get_cart() as $item ) {
				$product_id = $item['product_id'];
				$product = wc_get_product( $product_id );
				$is_gift_card = get_post_meta( $product_id, '_woo_giftcard', true );
				if( $is_gift_card != 'yes' && !$product->is_virtual() && !$product->is_downloadable() ){
					$shipping = true;
					break;
				}
				else{
					$shipping = false;
				}
			}
			return $shipping;
		}
	}

	public function woo_giftcard_order_processing( $order_id ){
		$order = wc_get_order( $order_id );
		if( !get_post_meta( $order_id, 'gift-card-generated', true ) ){
			if( $order->get_status() == 'processing' || $order->get_status() == 'completed' ){
				$this->woo_giftcard_coupon_generator( $order_id, $order );
			}
		}
	}

	public function woo_giftcard_order_process( $order_id ){
		$order = wc_get_order( $order_id );
		if( is_admin() && !get_post_meta( $order_id, 'gift-card-generated', true ) ){
			$this->woo_giftcard_coupon_generator( $order_id, $order );
		}
	}

	public function woo_giftcard_coupon_generator( $order_id, $order ) {

		$items = $order->get_items();

		foreach ( $order->get_items() as $item_key => $item ) {
			$is_gift_card = get_post_meta( $item->get_product_id(), '_woo_giftcard', true );

			if($is_gift_card == 'yes'){
				$token = base64_encode(openssl_random_pseudo_bytes(16));
				$token = bin2hex($token);
				$hyphen = chr(45);
				$uuid =  substr($token, 0, 4).$hyphen
				.substr($token, 4, 4).$hyphen
				.substr($token,8, 4).$hyphen
				.substr($token,12, 4);

				$gift_card = array(
					'post_title'    => $uuid,
					'post_status'   => 'publish',
					'post_type'     => 'shop_coupon',
				);
				$gift_card_id = wp_insert_post( $gift_card );

				$customer_name = $order->get_billing_first_name() . ' '. $order->get_billing_last_name();

				update_post_meta( $order_id, 'gift-card-generated', true );
				update_post_meta( $gift_card_id, 'woo_giftcard_to_friend_name', get_post_meta( $order_id, '_giftcard_friend_name' )[0] );
				update_post_meta( $gift_card_id, 'woo_giftcard_to_friend_email', get_post_meta( $order_id, '_giftcard_friend_email' )[0] );
				update_post_meta( $gift_card_id, 'woo_giftcard_from_sender_name', $customer_name );
				update_post_meta( $gift_card_id, 'woo_giftcard_from_sender_email', $order->get_billing_email() );
				update_post_meta( $gift_card_id, 'woo_giftcard_message', get_post_meta( $order_id, '_giftcard_message' )[0] );
				update_post_meta( $gift_card_id, 'discount_type', 'fixed_cart' );
				update_post_meta( $gift_card_id, 'coupon_amount', ( floatval( ( $item->get_total() ) + ( $item->get_total_tax() ) ) ) );
				update_post_meta( $gift_card_id, 'usage_limit', '1' );
				update_post_meta( $gift_card_id, 'woo_giftcard_order_number', $order_id );
				update_post_meta( $gift_card_id, 'apply_before_tax', 'no' );
				update_post_meta( $gift_card_id, 'free_shipping', 'yes' );
			}
		}

		$to           = get_post_meta( $gift_card_id, 'woo_giftcard_to_friend_email' )[0];
		$to_name      = get_post_meta( $gift_card_id, 'woo_giftcard_to_friend_name' )[0];
		$sender_name  = get_post_meta( $gift_card_id, 'woo_giftcard_from_sender_name' )[0];      
		$subject      = $sender_name . __( ' sent you a Gift!', 'woo-giftcards' );
		$gift_message = get_post_meta( $gift_card_id, 'woo_giftcard_message' )[0];
		$gift_amount  = get_post_meta( $gift_card_id, 'coupon_amount' )[0];
		$code         = get_the_title( $gift_card_id );
		$this->email_send( $to, $to_name, $sender_name, $subject, $gift_message, $gift_amount, $code );
	}

	public function woo_giftcard_include_single( $return, $product ) {
		$id = $product->get_id(); 
		$is_gift_card = get_post_meta( $id, '_woo_giftcard', true );
		if($is_gift_card == 'yes'){
			return true;
		}
		else{
			return false;
		}
	}


	public function woo_giftcard_check_cart( $passed, $product_id, $quantity) {
		$product_type = get_post_meta( $product_id, '_woo_giftcard', true );
		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item ){
			$is_gift_card = get_post_meta( $cart_item['product_id'], '_woo_giftcard', true );
			if( $is_gift_card && $product_type === true ){
				wc_add_notice( __( 'Only one Gift card is allowed in cart', 'woo-giftcards' ), 'notice' );
				return;
			}
		}
		if(isset($_POST['giftcard-form-submit'])){
			$friend_name  = sanitize_text_field($_POST['giftcard-friend-name']);
			$friend_email = sanitize_email($_POST['giftcard-friend-email']);
			$message      = sanitize_textarea_field($_POST['giftcard-message']);

			WC()->session->set('giftcard-friend-name', $friend_name);   
			WC()->session->set('giftcard-friend-email', $friend_email);
			WC()->session->set('giftcard-message', $message);
		}
		return $passed;
	}

	public function woo_giftcard_coupon_message_on_checkout() {
		return 'Have a Promo Code or Gift Card?' . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woo-giftcards' ) . '</a>';
	}

	public function callback_resend_email(){
		$to             = sanitize_email($_REQUEST['to_email']);
		$to_name        = sanitize_text_field($_REQUEST['to']);
		$sender_name    = sanitize_text_field($_REQUEST['from']);
		$gift_message   = sanitize_textarea_field($_REQUEST['message']);
		$gift_amount    = sanitize_text_field($_REQUEST['amount']);
		$code           = sanitize_text_field($_REQUEST['code']);
		$subject        = $sender_name . __( ' sent you a Gift!', 'woo-giftcards' );
		$mail = $this->email_send( $to, $to_name, $sender_name, $subject, $gift_message, $gift_amount, $code );
	}

	public function woo_giftcard_new_coupon_column( $columns ) {
		$columns[ 'order_number' ] = __( 'Order Number', 'woo-giftcards' );
		return $columns;
	}

	function woo_giftcard_coupon_column_data( $name ) {
		global $post;
		switch ($name) {
			case 'order_number':
			$order_number = get_post_meta($post->ID, 'woo_giftcard_order_number', true);
			echo esc_html($order_number);
		}
	}

	public function woo_giftcards_get_coupon_code_to_session(){
		if( isset($_GET['coupon_code']) ){
			$coupon_code = WC()->session->get('coupon_code');
			if(empty($coupon_code)){
				$coupon_code = sanitize_text_field( $_GET['coupon_code'] );
				WC()->session->set( 'coupon_code', $coupon_code );
			}
		}
	}

	public function woo_giftcards_add_discount_to_checkout( ) {
		$coupon_code = WC()->session->get('coupon_code');
		if ( ! empty( $coupon_code ) && ! WC()->cart->has_discount( $coupon_code ) ){
        WC()->cart->add_discount( $coupon_code ); // apply the coupon discount
        WC()->session->__unset('coupon_code'); // remove coupon code from session
    }
}

public function email_send( $to, $to_name, $sender_name, $subject, $gift_message, $gift_amount, $code ){
	include_once plugin_dir_path( __FILE__ ) . 'woo-giftcards-email.php';
}

}