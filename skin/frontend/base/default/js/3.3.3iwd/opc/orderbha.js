$j = jQuery;

$j(document).ready(function(){
	$j('.login-trigger').click(function(e){
		e.preventDefault();
		$j('#modal-login').addClass('md-show');
	});
			
	$j(document).on('click','.md-modal .close', function(e){
		e.preventDefault();
		$j('.md-modal').removeClass('md-show');
	});
			
	$j(document).on('click', '.restore-account', function(e){
		e.preventDefault();
		$j('#login-form').hide();$j('#login-button-set').hide();
		//$j('#form-validate-email').fadeIn();$j('#forgotpassword-button-set').show();
	});
			
	$j('#login-button-set .btn').click(function(){
		$j('#login-form').submit();
	});
			
	/*$j('#forgotpassword-button-set .btn').click(function(){
		var form = $j('#form-validate-email').serializeArray();
		IWD.OPC.Checkout.showLoader();
		IWD.OPC.Checkout.xhr = $j.post(IWD.OPC.Checkout.config.baseUrl + 'onepage/json/forgotpassword',form, IWD.OPC.Login.prepareResponse,'json');
	});*/

	/*$j('#forgotpassword-button-set .back-link').click(function(e){
		e.preventDefault();
		$j('#form-validate-email').hide();$j('#forgotpassword-button-set').hide();
		$j('#login-form').fadeIn();$j('#login-button-set').show();
	});*/
});