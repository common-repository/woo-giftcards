<table style="border:1px dashed #000;border-radius: 8px;width: 100%;">
  <tbody>
    <tr style="vertical-align: middle;">
      <td style="width: 50%;">
        <img width="50" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../imgs/gift-icon.png' ); ?>">
      </td>
      <td style="width: 50%;">
        <h1 style="margin: 0;float: right;"><?php echo wc_price( $gift_amount ); ?></h1>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <?php echo '<strong>'. __( 'From: ', 'woo-giftcards' ) . '</strong>'. esc_html($sender_name); ?>
        <br>
        <?php echo '<strong>'. __( 'To: ', 'woo-giftcards' ) . '</strong>'. esc_html($to_name); ?>
        <br>
        <?php echo '<strong>'. __( 'Message: ', 'woo-giftcards' ) . '</strong>'. esc_textarea($gift_message); ?>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="padding-top: 0;">
        <?php echo '<h3 style="text-align:center;">'. __( 'Gift Card Code: ', 'woo-giftcards' ) .'<strong style="color:#000;">'.esc_html($code).'</strong></h3>'; ?>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center;padding-bottom: 24px;">
        <a style="background-color: #000;color: #fff;padding: 10px 20px;text-decoration: none;cursor: pointer;" href="<?php echo esc_url( $redeem_url ); ?>"><?php _e( 'REDEEM NOW', 'woo-giftcards' ); ?></a>
      </td>
    </tr>
  </tbody>
</table>