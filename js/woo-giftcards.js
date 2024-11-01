-jQuery('.buy-giftcard').on( 'click', function() {
    var giftcard_id = jQuery(this).attr('giftcard-id');
    jQuery('.giftcard-container-' + giftcard_id).slideToggle('slow');
});

jQuery( document ).ready( function() {
  var coupon_input = jQuery( '.checkout_coupon' );
  if( coupon_input != null ){
    jQuery( '.checkout_coupon p:first-child'  ).html( 'If you have a coupon or gift card code, please apply it below.' );
    jQuery( '#coupon_code' ).attr( 'placeholder', 'Coupon or Gift Card Code' );
    jQuery( 'button[name="apply_coupon"]' ).html( 'Apply Code' );
  }
});