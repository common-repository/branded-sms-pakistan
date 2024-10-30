<?php
class BSP_SMSAPI{

	private $email;
	private $auth_key;
	private $mask;

	function __construct($email,$auth_key,$mask=false){
		$this->email = $email;
		$this->auth_key = $auth_key;
		if($mask){
			$this->mask = $mask;
		}
	}

	function validateNumber($number){
		$number=preg_replace("/[^0-9,.]/", "", $number);
		$number=substr($number, -10);
		return $number;
	}

	function validateMultipleNumber($numbers){
		$numbers=explode(",", $numbers);
		$validatedNumbers=array();

		foreach ($numbers as $key => $number) {
			$number=preg_replace("/[^0-9,.]/", "", $number);
			$number=substr($number, -10);
			array_push($validatedNumbers, $number);
			
		}

		return implode(",92", $validatedNumbers);
	}

	function bsp_checkCredentials(){

		$data = "email=".$this->email."&key=".$this->auth_key."&mask=".$this->mask;
		$url='https://secure.h3techs.com/sms/api/mask?'.$data;
		$result = wp_remote_retrieve_body( wp_remote_get( $url ) );

		if(json_decode($result)->sms->code != "200"){
			return "false";
		}
		else{
			return "true";
		}
	}

	function bsp_checkSemiCredentials(){

		$data = "email=".$this->email."&key=".$this->auth_key."&type=getMasks";
		$url='https://secure.h3techs.com/sms/api/mask?'.$data;
		$result = wp_remote_retrieve_body( wp_remote_get( $url ) );
		if(json_decode($result)->sms->code != "200"){
			return "false";
		}
		else{
			return json_decode($result)->sms->response;
		}
	}

	function bsp_orderPlaced($number){
		$mask = "H3 TEST SMS";
		$to = $this->validateNumber($number);
		$message = "Order Placed";

		$mask = urlencode($mask);
		$message = urlencode($message);

		$body = array(
		    'email' => $this->email,
		    'key' => $this->auth_key,
		    'mask' => $this->mask,
		    'to' => '92'.$to,
		    'message' => $message
		);

		$result =$this->bsp_postRequest($body);

		if(json_decode($result)->sms->code == 000){
			return true;
		} else {
			return false;
		}
	}

	function bsp_sendOTP($number,$message){
			$mask = $this->mask;
			$to = $this->validateNumber($number);

			$mask = urlencode($mask);
			$message = urlencode($message);
			
			$body = array(
			    'email' => $this->email,
			    'key' => $this->auth_key,
			    'mask' => $mask,
			    'to' => '92'.$to,
			    'message' => $message
			);

			$result =$this->bsp_postRequest($body);

			if(json_decode($result)->sms->code == 000){
				
				return true;
			} else {
				return false;
			}
	}

	function bsp_postRequest($body){
		$args = array(
	        'body' => $body,
		    'timeout' => '5',
		    'redirection' => '5',
		    'httpversion' => '1.0',
		    'blocking' => true,
		    'headers' => array(),
		    'cookies' => array()
	    );
		return wp_remote_retrieve_body(wp_remote_post( 'https://secure.h3techs.com/sms/api/send', $args ));
	}

	function bsp_state_changed_sms($order,$message,$mask,$multipleNumbers=false){
		$number = $order->get_data()['billing']['phone'];
		$message = urlencode($message);
		$mask = urlencode($mask);

		if(($multipleNumbers != false || count($multipleNumbers) != 0) && $multipleNumbers[0]->message != ""){

			foreach (explode(",",$multipleNumbers[0]->message) as $key => $value) {	
				
				$mask = $mask;
				$to = $this->validateNumber($value);

				$body = array(
				    'email' => $this->email,
				    'key' => $this->auth_key,
				    'mask' => $mask,
				    'to' => '92'.$to,
				    'message' => $message
				);

				$result =$this->bsp_postRequest($body);

				if(json_decode($result)->sms->code == 000){	
				} else {
				}
			}
		}

		$mask = $mask;
		$to = $this->validateNumber($number);
		
		$body = array(
			'email' => $this->email,
			'key' => $this->auth_key,
			'mask' => $mask,
			'to' => '92'.$to,
			'message' => $message
		);

		$result =$this->bsp_postRequest($body);


		if(json_decode($result)->sms->code == 000){
			
			return true;
		} else {
			return false;
		}
	}

	

	function bsp_checkPremium(){
		$data = "email=".$this->email."&key=".$this->auth_key."&type=getMasks";
		$url='https://secure.h3techs.com/sms/api/premium_verify?'.$data;
		$result = wp_remote_retrieve_body( wp_remote_get( $url ) );

		$response['success']=false;


		if(json_decode($result)->sms->code != "200"){
			$response['heading']='PURCHASE PREMIUM PLUGIN TO ACCESS THIS FEATURE';
			$response['message']= json_decode($result)->sms->response;
		}
		else{
			$response['success']=true;
			$response['message']= json_decode($result)->sms->response;
		}


		return $response;


	}

	function mass_marketing_sms($number,$message,$mask,$premium,$multipleNumbers=false){
			$isPremium=$this->bsp_checkPremium();
			
			if($isPremium['success']){

				$numbers=explode(",",$number);
				
				if(count($numbers) > 1){
					$to = $this->validateMultipleNumber($number);

					$body = array(
					    'email' => $this->email,
					    'key' => $this->auth_key,
					    'mask' => $mask,
					    'to' => '92'.$to,
					    'message' => $message,
					    'multiple' => "1",
					    'premium' => $premium
					);
				} else {

					$to = $this->validateNumber($number);

					$body = array(
					    'email' => $this->email,
					    'key' => $this->auth_key,
					    'mask' => $mask,
					    'to' => '92'.$to,
					    'message' => $message,
					    'premium' => $premium
					);
				}
				$mask = $mask;
				$mask = urlencode($mask);
				$result =$this->bsp_postRequest($body);

				if(json_decode($result)->sms->code == 000){
					return array("success"=>true);
				} else {
					return array("success"=>false,"message"=>json_decode($result)->sms->response);
				}

			} else {
				return $isPremium;
			}
			
	}

}