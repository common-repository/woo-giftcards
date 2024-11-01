function resend_email(){

  var to         = jQuery('#woo_giftcard_to_friend_name').val();
  var to_email   = jQuery('#woo_giftcard_to_friend_email').val();
  var from       = jQuery('#woo_giftcard_from_sender_name').val();
  var from_email = jQuery('#woo_giftcard_from_sender_email').val();
  var message    = jQuery('#woo_giftcard_message').val();
  var code       = jQuery('#title').val();
  var amount     = jQuery('#coupon_amount').val();
 // alert(amount);

 jQuery('#resend-email-status').text('Sending...');

 jQuery.ajax({
   url: 'admin-ajax.php',
   type: "POST",
   cache: false,
   data:{ 
    action: 'resend_email', 
    to: to,
    to_email: to_email,
    from: from,
    message: message,
    code: code,
    amount: amount
  },
  success:function(res){
    if( res == 'success' ){
   jQuery('#resend-email-status').text('Email Sent.');
 }
 else{
  jQuery('#resend-email-status').text('We found an error, please try again later.');
 }
 }
}); 

}

function test_email(){

var to_email = jQuery('#woo-giftcard-test-input').val();

 jQuery('#test-email-status').text('Sending...');

 jQuery.ajax({
   url: 'admin-ajax.php',
   type: "POST",
   cache: false,
   data:{ 
    action: 'test_email', 
    to_email: to_email
  },
  success:function(res){
    if( res == 'success' ){
      jQuery('#test-email-status').text('Email Sent.');
    }
    else{
      jQuery('#test-email-status').text('We found an error, please try again later.');
    }
 }
}); 

}