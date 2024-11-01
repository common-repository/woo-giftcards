<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Gift Cards for WooCommerce
 * Plugin URI:        https://ahmadshyk.com/item/woocommerce-gift-cards-pro/
 * Description:       Sell giftcards on your WooCommerce website.
 * Version:           1.5.8
 * Author:            Ahmad Shyk
 * Author URI:        https://ahmadshyk.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-giftcards
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'WOO_GIFTCARDS_VERSION', '1.5.8' );

function woo_giftcards_admin_notice(){ ?>
	<div class="error">
		<p><?php _e( 'Gift Cards for WooCommerce Plugin is activated but not effective. It requires WooCommerce in order to work.', 'woo-giftcards' ); ?></p>
	</div>
	<?php	
}

require plugin_dir_path( __FILE__ ) . '/inc/woo-giftcards-class.php';

require plugin_dir_path( __FILE__ ) . '/inc/woo-giftcards-admin-class.php';

function woo_giftcards_run() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'woo_giftcards_admin_notice' );
	}
	else{
		new Woo_GiftCards_Admin();
		new Woo_GiftCards();
	}
}
add_action( 'plugins_loaded', 'woo_giftcards_run', 11 );

//Add settings link on plugin page
function woo_giftcards_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=woo-giftcards">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'woo_giftcards_settings_link' );