<?php
require_once('class.db.php');
require_once('class.smsAPI.php');
require_once('class.ajaxHandler.php');

class BSP_Settings{

function bsp_customerVerificationModel(){

	$BSP_DB = new BSP_DB();
	$data=$BSP_DB->bsp_getSettingStatus('customer_verification_module')[0];
	//if customer verification model is turned on in database then add actions related to that model
	if($data->active){
		$BSP_AjaxHandler = new BSP_AjaxHandler();
		add_action("wp_ajax_bsp_optVerification", array($BSP_AjaxHandler, 'bsp_optVerification'));
		add_action( 'wp_ajax_nopriv_bsp_optVerification',array($BSP_AjaxHandler, 'bsp_optVerification'));
		add_action("wp_ajax_bsp_CheckoptVerification", array($BSP_AjaxHandler, 'bsp_CheckoptVerification'));
		add_action( 'wp_ajax_nopriv_bsp_CheckoptVerification',array($BSP_AjaxHandler, 'bsp_CheckoptVerification'));

		// if($BSP_DB->bsp_getSettingStatus('thankyou_page_redirect')[0]->active == 0){
		// 	$pageBuilder= new BSP_PageBuilder();
		// 	$pageid=$pageBuilder->bsp_createThankyouRedirectPage();
		// 	$BSP_DB->bsp_setSettingStatus('thankyou_page_redirect',$pageid);	
		// }

		if($BSP_DB->bsp_getSettingStatus('otp_verification_page')[0]->active == 0){
			$pageBuilder= new BSP_PageBuilder();
			$pageid=$pageBuilder->bsp_createOtpVerificationPage();
			$BSP_DB->bsp_setSettingStatus('otp_verification_page',$pageid);	
		}
		add_action( 'woocommerce_thankyou', array($this, 'bsp_bbloomer_redirectcustom'));
	}
}

function bsp_phoneNumberVerification($order_id ) {
	if (!$order_id )
		return;
	// Allow code execution only once
	$order = wc_get_order( $order_id );
	if( !$order->get_meta('_thankyou_action_done', true ) ) {
		$frontEnd= new BSP_FrontEnd();
		$frontEnd->bsp_otpVerificationHtml($order_id);
	} 
}

function bsp_otpGeneration($posted){
	$database=new BSP_DB;
	$otpMessage=$database->bsp_generateOtp($posted['billing_phone']);
	$smsApi=new BSP_SMSAPI($database->bsp_getUserDetails()[0]->email,$database->bsp_getUserDetails()[0]->auth_key,$database->bsp_getUserDetails()[0]->mask);
	$smsApi->bsp_sendOTP($posted['billing_phone'],$otpMessage);

}

function bsp_bbloomer_redirectcustom( $order_id ){
	$order = wc_get_order( $order_id );
	$BSP_DB = new BSP_DB();
	$url=get_page_link( $BSP_DB->bsp_getSettingStatus('otp_verification_page')[0]->active )."?bsporderid=".$order_id;
	//echo $url; die();
	//echo $order->status; die();
	if ($order->status == 'verification-due' || $order->status == 'wc-verification-due') {
		if($url != wp_get_referer()) {
		wp_safe_redirect( $url );
		exit;
		}
	}
}
}