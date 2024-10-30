<?php
require_once("class.db.php");
require_once("class.pageBuilder.php");
require_once("class.smsAPI.php");
class BSP_AjaxHandler{
	function bsp_sendMassMarketingMessage(){
		$numbers=sanitize_text_field($_REQUEST['numbers']);
		$message=sanitize_text_field($_REQUEST['message']);
		$premium=sanitize_text_field($_REQUEST['premium']);
		$db = new BSP_DB();
		$userDetails=$db->bsp_getUserDetails();

		$smsApi = new BSP_SMSAPI($userDetails[0]->email,$userDetails[0]->auth_key,$userDetails[0]->mask);
		$response=$smsApi->mass_marketing_sms($numbers,$message,$userDetails[0]->mask,$premium);

		echo wp_json_encode($response);
		wp_die();
	}
	// wc_clean() – Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	function wc_clean( $var ) {
	  if ( is_array( $var ) ) {
	    return array_map( 'wc_clean', $var );
	  } else {
	    return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	  }
	}
	
	public function bsp_getMassMarketingData(){
		$allOrdersId=array();
		$products=wc_clean( $_REQUEST['products'] );
		$categories=wc_clean( $_REQUEST['categories'] );
		$orderStatus=wc_clean( $_REQUEST['order-status'] );

		if( !is_array($products) && !is_array($categories) ){
			$response['success']=false;
			$response['message']='Please select any products or product categories';
			echo wp_json_encode($response);
			wp_die();
		}
		
		if( !is_array($orderStatus) ){
			$response['success']=false;
			$response['message']='Please select order status';
			echo wp_json_encode($response);
			wp_die();
		} else {
			foreach ($orderStatus as $key => $value) {
				$orderStatus[$key]=sanitize_text_field($orderStatus[$key]);
			}
		}

		$db = new BSP_DB();
		foreach ($products as $key => $value) {
			$value=sanitize_text_field($value);
			$orders=$db->get_orders_ids_by_product_id($value,$orderStatus);

			foreach ($orders as $orderkey => $orderid) {
				array_push($allOrdersId,$orderid);
			}
		}

		foreach ($categories as $key => $value) {
			//$value=sanitize_text_field($value);
			$orders=$db->get_orders_ids_by_product_category($value,$orderStatus);
			foreach ($orders as $orderkey => $orderid) {
				array_push($allOrdersId,$orderid);
			}
		}

		$phoneNumbers=$this->get_phone_numbers_via_order_id($allOrdersId);
		$phoneNumbers=$this->getDetailsFromNumbers($phoneNumbers);

		if($phoneNumbers['unique']){
			$phoneNumbers['success']=true;
		} else{
			$phoneNumbers['success']=false;
			$phoneNumbers['message']='We were unable to find numbers with this searching criteria';
		}
		
		$phoneNumbers=wp_json_encode($phoneNumbers);
		echo esc_html($phoneNumbers);
		wp_die();

	}

	function validateNumbersFormat($phoneNumbers){
		$validatedNumbers=array();
		$validNetworkTypes=array("30","31","32","33","34","35");
		foreach ($phoneNumbers as $key => $value) {
			$value = substr($value, -10);
			if(strlen($value) == 10){
				$network= substr($value, 0, 2);
				if(array_search($network, $validNetworkTypes)){
					array_push($validatedNumbers, $value);
				}
			}
		}
		return $validatedNumbers;
	}


	function getDetailsFromNumbers($phoneNumbers){
		$phoneNumbers=$this->validateNumbersFormat($phoneNumbers);
		$before = count($phoneNumbers);
		//echo $before;die();
		$phoneNumbers=array_unique($phoneNumbers);
		$phoneNumbers=$this->validateNumbersFormat($phoneNumbers);
		$after=count($phoneNumbers);
		$duplicates=$before-$after;
		$phoneNumbers=array_values($phoneNumbers);
		return array("duplicates"=>$duplicates,"unique"=>$after,"numbers"=>$phoneNumbers);

	}


	function get_phone_numbers_via_order_id($allOrdersId){//print_r($allOrdersId);die();
			$phoneNumberList=array();
			foreach ($allOrdersId as $key => $order_id) {
				$order = wc_get_order( $order_id );
				$order_data = $order->get_data(); // The Order data
				array_push($phoneNumberList, $order_data['billing']['phone']);
			}
			return $phoneNumberList;
	}

	public function bsp_customerVerificationForm(){ 

		$db = new BSP_DB();
		$response=array();
	    $response['success']=true;
	    $response['message']="Information Updated Successfully";
	    $validator = new BSP_Validator();
	    $validation=$validator->bsp_validateCustomerVerificationFormDetails($_REQUEST);
	    $pageBuilder= new BSP_PageBuilder();
	    if($validation){

			if(count($db->bsp_getUserDetails())){

		    	if(isset($_REQUEST['customerVerificationModule']) && $_REQUEST['customerVerificationModule'] == "on"){
		    		if($_REQUEST['opt_text']!=""){
		    			$opt_text = sanitize_text_field($_REQUEST["opt_text"]);
		    			$db->bsp_setSettingStatus('customer_verification_module',1);
		    			$db->bsp_setCustomMessageValue('opt_text',$opt_text);
		    		} else{
		    			$response['success']=false;
		    			$response['message']="Opt Text Cannot Remain Empty";
		    		}
		    	} else {
		    		$db->bsp_setSettingStatus('customer_verification_module',0);
		    	}
		    	//url authentication module

		    	if(isset($_REQUEST['urlAuthentication']) && $_REQUEST['urlAuthentication'] == "on"){
		    				    		
		    		$pageExistence=$db->bsp_checkSettingExistence('thankyou_page');

					if($pageExistence->active == 0){
						//means page not created
						
						$pageid=$pageBuilder->bsp_createThankYouPage();
						$db->bsp_setSettingStatus('thankyou_page',$pageid);
					}

					$db->bsp_setSettingStatus('urlAuthentication',1);
		    	} else {
		    		$db->bsp_setSettingStatus('urlAuthentication',0);
		    	}

		    	//bitly authentication tab
		    	if(isset($_REQUEST['bitlyUrlCheckBox']) && $_REQUEST['bitlyUrlCheckBox'] == "on"){

		    		if($_REQUEST['accessToken']!=""){
		    			$db->bsp_setSettingStatus('bitlyUrlCheckBox',1);
		    			$accessToken=sanitize_text_field($data['accessToken']);

		    			$body = array(
						    'access_token' => $accessToken,
						    'longUrl' => get_page_link($db->bsp_getSettingStatus('thankyou_page')[0]->active)
						);

						$args = array(
					        'body' => $body,
						    'timeout' => '5',
						    'redirection' => '5',
						    'httpversion' => '1.0',
						    'blocking' => true,
						    'headers' => array(),
						    'cookies' => array()
					    );

						$result= wp_remote_retrieve_body(wp_remote_post( 'https://api-ssl.bitly.com/v3/shorten', $args ));
						
						if(json_decode($result)->status_code == 200){
							$db->bsp_setCustomMessageValue('bitly_url_auth',$accessToken);
							$db->bsp_setCustomMessageValue('bitly_generated_url',json_decode($result)->data->url);
							
						}
		    			if(json_decode($result)->status_code == 500){
		    				$response['success']=false;
		    				$response['message']=json_decode($result)->status_txt;
		    				echo wp_json_encode($response);
							wp_die();
		    			}
		    		} else {
		    			$response['success']=false;
		    			$response['message']="Please Provide With Access Token";
		    		}
		    	} else {
		    		$db->bsp_setSettingStatus('bitlyUrlCheckBox',0);
		    	}
		    } else {
		    	$response['success']=false;
		    	$response['message']="Please Enter Your Credentials First";
		    }

	    } //close of validation condition
		echo wp_json_encode($response);
		wp_die();
	}

	function bsp_optVerification(){

		$response['success']=false;
		$response['message']="Please Enter Valid OTP";
		$db = new BSP_DB();
		$optCode = sanitize_text_field($_REQUEST["optCode"]);
		$otp=$db->bsp_getOTP($optCode);
		if(count($otp)){
			$db->bsp_markOTP($optCode);
			
			$response['success']=true;
			$response['message']="Thank You For Verifying";
			//$response['url']=get_page_link($db->bsp_getSettingStatus('thankyou_page')[0]->active);	
			$order = wc_get_order( $otp[0]->order_id );
			$response['url']=$order->get_checkout_order_received_url();
			
			if($order->get_payment_method()=="cod"){
				$order->update_status( 'processing', 'OTP Verified' );
				echo wp_json_encode($response);
		        wp_die();
			}elseif($order->get_payment_method()=="bacs"){
				$order->update_status( 'pending', 'OTP Verified and Waiting for Payment' );
				echo wp_json_encode($response);
		        wp_die();
			}else{
				$order->update_status( 'on-hold', 'OTP Verified and Order is On Hold' );
				echo wp_json_encode($response);
		        wp_die();
			}
		}

		echo wp_json_encode($response);
		wp_die();
	}

	function bsp_CheckoptVerification(){
		$db = new BSP_DB();
		$orderId = sanitize_text_field($_REQUEST["orderId"]);
		$result = $db->bsp_getOTPbyOrder($orderId);
		//$response['rows'] = $result;
		if($result=='true') {
			$order = wc_get_order($orderId);
			$response['url']=$order->get_checkout_order_received_url();
			$response['success']=true;
		} else {
			$response['success']=false;
		}

		echo wp_json_encode($response);
		wp_die();
	}

	public function bsp_customNumbers(){
		$db = new BSP_DB();
		$response['success']=false;
		$response['message']="Please Enter Numbers Seperated By Commas";

		if(isset($_REQUEST['custom_numbers']) && $_REQUEST['custom_numbers'] != ""){
			if(preg_match('/^\d(?:,\d)*$/', $posted_value)){

			}
			$numbers = explode(',',$_REQUEST['custom_numbers']);
			$valid = true;

			foreach($numbers as $key=>$value) {

			    if(!ctype_digit($value) || strlen($value) != 12) {
			        $valid = false;
			        break;
			    }
			}
			if($valid){
				$db->bsp_setCustomMessageValue("custom_numbers",$_REQUEST['custom_numbers']);
				$response['success']=true;
				$response['message']="Messages Will Also Be Sent To These Numbers";
			}
		}

		if(isset($_REQUEST['custom_numbers']) && $_REQUEST['custom_numbers'] == ""){
				$db->bsp_setCustomMessageValue("custom_numbers","");
				$response['success']=true;
				$response['message']="Messages Will Also Be Sent To These Numbers";

		}
		echo wp_json_encode($response);
		wp_die();

	}
}
?>