<?php
class BSP_Validator{
	function bsp_validateUserCredentials(){
		if(isset($_REQUEST["email"]) && isset($_REQUEST['auth_key']) && isset($_REQUEST['mask']) && $_REQUEST["email"] != '' && $_REQUEST["auth_key"] != '' && $_REQUEST["mask"] != ''){
			return true;
		}
		else{
			return false;
		}
	}


	function bsp_validateUserCredentials2(){
		if(isset($_REQUEST["email"]) && isset($_REQUEST['auth_key']) && $_REQUEST["email"] != '' && $_REQUEST["auth_key"] != ''){
			return true;
		}
		else{
			return false;
		}
	}

	function bsp_validateUserProvidedCustomNumbers($data){
		if(isset($data['custom_numbers'])){

			if($data['custom_numbers'] != ""){
			foreach (explode(",",$data['custom_numbers']) as $key => $value) {
				if(strlen($value) ==12 && $value[0] != '+'){
					if(!is_numeric($value)){
						return "false";
					}
				}
				else{
					return "false";	
				}
					}
			}
		}
		return "true";
	}
	function bsp_validateCustomerVerificationFormDetails($data){
		return true;
	}
}
