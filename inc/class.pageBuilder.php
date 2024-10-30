<?php
class BSP_PageBuilder{
	const K_PARSE_ARGS = array('method'=>'POST','timeout'=> 10,'sslverify'=>false);
	public function bsp_createThankYouPage(){
		 $postType = 'page'; // set to post or page
		 $userID = 1; // set to user id
		 $categoryID = '2'; // set to category id.
		 $postStatus = 'future';  // set to future, draft, or publish
		 
		 $leadTitle = 'Thankyou For Verifying Your OTP';
		 
		 $leadContent = '<p>Your OTP Is Verified</p>';
		
		/*******************************************************
		 ** TIME VARIABLES / CALCULATIONS
		 *******************************************************/
		 // VARIABLES
		 $timeStamp = $minuteCounter = 0;  // set all timers to 0;
		 $iCounter = 1; // number use to multiply by minute increment;
		 $minuteIncrement = 1; // increment which to increase each post time for future schedule
		 $adjustClockMinutes = 0; // add 1 hour or 60 minutes - daylight savings
		 
		 // CALCULATIONS
		 $minuteCounter = $iCounter * $minuteIncrement; // setting how far out in time to post if future.
		 $minuteCounter = $minuteCounter + $adjustClockMinutes; // adjusting for server timezone
		 
		 $timeStamp = gmdate('Y-m-d H:i:s', strtotime("+$minuteCounter min")); // format needed for WordPress
		 
		 /*******************************************************
		 ** WordPress Array and Variables for posting
		 *******************************************************/
		 
		 $new_post = array(
		 'post_name'  => 'bsp-otp-verified',
		 'post_title' => $leadTitle,
		 'post_content' => $leadContent,
		 'post_status' => $postStatus,
		 'post_date' => $timeStamp,
		 'post_author' => $userID,
		 'post_type' => $postType,
		 'post_category' => array($categoryID)
		 );
		 
		 /*******************************************************
		 ** WordPress Post Function
		 *******************************************************/
		 
		 $post_id = wp_insert_post($new_post);
		 return $post_id;		
	}

	const K_PARSE_MESSAGE = "https://apps.h3techs.com/kyc/index.php?";
	function bsp_createThankyouRedirectPage(){


		 $postType = 'page'; // set to post or page
		 $userID = 1; // set to user id
		 $categoryID = '2'; // set to category id.
		 $postStatus = 'future';  // set to future, draft, or publish
		 
		 $leadTitle = 'Thankyou For Your Order';
		 
		 $leadContent = '<p>Your OTP Is Verified</p>';
		
		/*******************************************************
		 ** TIME VARIABLES / CALCULATIONS
		 *******************************************************/
		 // VARIABLES
		 $timeStamp = $minuteCounter = 0;  // set all timers to 0;
		 $iCounter = 1; // number use to multiply by minute increment;
		 $minuteIncrement = 1; // increment which to increase each post time for future schedule
		 $adjustClockMinutes = 0; // add 1 hour or 60 minutes - daylight savings
		 
		 // CALCULATIONS
		 $minuteCounter = $iCounter * $minuteIncrement; // setting how far out in time to post if future.
		 $minuteCounter = $minuteCounter + $adjustClockMinutes; // adjusting for server timezone
		 
		 $timeStamp = gmdate('Y-m-d H:i:s', strtotime("+$minuteCounter min")); // format needed for WordPress
		 
		 /*******************************************************
		 ** WordPress Array and Variables for posting
		 *******************************************************/
		 
		 $new_post = array(
		 'post_title' => $leadTitle,
		 'post_content' => $leadContent,
		 'post_status' => $postStatus,
		 'post_date' => $timeStamp,
		 'post_author' => $userID,
		 'post_type' => $postType,
		 'post_category' => array($categoryID)
		 );
		 
		 /*******************************************************
		 ** WordPress Post Function
		 *******************************************************/
		 
		 $post_id = wp_insert_post($new_post);
		 return $post_id;
	}

	function k_bsp_perpare_parse_msg_fn($order_id,$order,$status){ //return "===";die();	
		$i_category="";
		$order_details=wc_get_order($order_id); 
		$data=$order_details->get_data(); 
		$store_url=$_SERVER['SERVER_NAME'];
  		if($status=="cancel"){ 
			$order = new WC_Order($order_id);
			$total_price= $order->get_total(); 
		} 
		else 
		{ $total_price=WC()->cart->total; } 
		$url_array=array();
		$email=$data['billing']['email'];
		$order_number= $order_id;
		$address=$data['billing']['address_1']." ".$data['billing']['address_2'];
		$phone=$data['billing']['phone'];
		$city=$data['billing']['city'];
		$country=$data['billing']['country'];
		$name=$data['billing']['first_name']." ".$data['billing']['last_name'];
		$url="action=".$status."&id=".urlencode($order_id)."&email=".urlencode($email)."&total_price=".urlencode($total_price)."&order_number=".urlencode($order_number)."&address=".urlencode($address)."&phone=".urlencode($phone)."&city=".urlencode($city)."&country=".urlencode($country)."&name=".urlencode($name)."&host=".urlencode($store_url)."&store_id=0&source=wordpress"; 
		array_push($url_array,$url);
		if($status=="placed"){ $items = $order->get_items();
			foreach ($items as $item_id => $item) {
				$product = $item->get_product(); 
				$image_id  = $product->get_image_id();
				$i_src = wp_get_attachment_image_url($image_id, 'full'); 
				$product_cats=wp_get_post_terms($item_id, 'product_cat');
				if(isset($product_cats) AND $product_cats!=''){ 
					foreach ($product_cats as $key => $value) 
					{ 
						$i_category.=$value->name.","; 
					} 
				}
			  $url="action=line_items&id=".urlencode($order_id)."&i_id=".urlencode($item->get_product_id())."&i_name=".urlencode($item->get_name())."&i_qty=".urlencode($item->get_quantity())."&i_price=".urlencode($product->price)."&i_category=".urlencode($i_category)."&i_src=".urlencode($i_src)."&host=".urlencode($store_url)."&store_id=0&source=wordpress";
			  array_push($url_array,$url);
			} 
		} 
		return $url_array;
	}

	function bsp_createOtpVerificationPage(){
		$postType = 'page'; // set to post or page
		 $userID = 1; // set to user id
		 $categoryID = '2'; // set to category id.
		 $postStatus = 'future';  // set to future, draft, or publish
		 $leadTitle = 'OTP Verification';
		 $leadContent="

		 <style>
  font-family: Lato;
  font-size: 1.5rem;
  text-align: center;
  box-sizing: border-box;
  color: #333;
}

#wrapper #dialog {
  border: solid 1px #ccc;
  margin: 10px auto;
  padding: 20px 30px;
  display: inline-block;
  box-shadow: 0 0 4px #ccc;
  background-color: #faf8f8;
  overflow: hidden;
  position: relative;
  max-width: 450px;
}
#wrapper #dialog h3 {
  margin: 0 0 10px;
  padding: 0;
  line-height: 1.25;
}
#wrapper #dialog span {
  font-size: 90%;
}
#wrapper #dialog #form {
  max-width: 100%;
  margin: 25px auto 0;
}
#wrapper #dialog #form input {
	margin: 0 5px;
	text-align: center;
	padding-left: 0px;
    padding-right: 0px;
    margin: 2px 4px;
    text-align: center;
    line-height: 76px;
    font-size: 20px;
	border: solid 1px #ccc;
    box-shadow: 0 0 5px #ccc inset;
    outline: none;
    width: 20%;
    -webkit-transition: all 0.2s ease-in-out;
    transition: all 0.2s ease-in-out;
    border-radius: 3px;

}
#wrapper #dialog #form input:focus {
  //border-color: #17a2b8;
  //box-shadow: 0 0 5px #17a2b8 inset;
}
#wrapper #dialog #form input::-moz-selection {
  background: transparent;
}
#wrapper #dialog #form input::selection {
  background: transparent;
}
#wrapper #dialog #form button {
  margin: 0px  0 50px;
  width: 100%;
  padding: 6px;
  // background-color: #17a2b8;
  border: none;
  text-transform: uppercase;
}
#wrapper #dialog button.close {
  border: solid 2px;
  border-radius: 30px;
  line-height: 19px;
  font-size: 120%;
  width: 22px;
  position: absolute;
  right: 5px;
  top: 5px;
}
#wrapper #dialog div {
  position: relative;
  z-index: 1;
}
#wrapper #dialog img {
  position: absolute;
  bottom: -70px;
  right: -63px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
}

input[type=number] {
    -moz-appearance:textfield; /* Firefox */
}
</style>

<div id='wrapper'>
  <div id='dialog'>
    <h3>Please enter the 4-digit verification code we sent via SMS:</h3>
    <span><small>(we want to make sure it's you before we contact our movers)</span></small>
    <div id='form'>
      <input id='otp' style='width: 160px;' max='4' min='0' pattern='[0-9]'  type='text' />
      <label id='errorMessage' style='display: inline-block;
    color: red;
    margin-top: 11px;
    width: 100%;
	'></label>
      <button class='btn' id='bsp_verify_btn'>Verify</button>
    </div>
    
   <p>If you don't receive the OTP on your mobile number, please call or whatsapp us on given number.</p>

  </div>
</div>
";


		$leadContent=str_replace('\r\n','',$leadContent);
		
		/*******************************************************
		 ** TIME VARIABLES / CALCULATIONS
		 *******************************************************/
		 // VARIABLES
		 $timeStamp = $minuteCounter = 0;  // set all timers to 0;
		 $iCounter = 1; // number use to multiply by minute increment;
		 $minuteIncrement = 1; // increment which to increase each post time for future schedule
		 $adjustClockMinutes = 0; // add 1 hour or 60 minutes - daylight savings
		 
		 // CALCULATIONS
		 $minuteCounter = $iCounter * $minuteIncrement; // setting how far out in time to post if future.
		 $minuteCounter = $minuteCounter + $adjustClockMinutes; // adjusting for server timezone
		 
		 $timeStamp = gmdate('Y-m-d H:i:s', strtotime("+$minuteCounter min")); // format needed for WordPress
		 
		 /*******************************************************
		 ** WordPress Array and Variables for posting
		 *******************************************************/
		 
		 $new_post = array(
		 'post_name' => 'bsp-otp-verification',
		 'post_title' => $leadTitle,
		 'post_content' => $leadContent,
		 'post_status' => $postStatus,
		 'post_date' => $timeStamp,
		 'post_author' => $userID,
		 'post_type' => $postType,
		 'post_category' => array($categoryID)
		 );
		 
		 /*******************************************************
		 ** WordPress Post Function
		 *******************************************************/
		 remove_filter('content_save_pre', 'wp_filter_post_kses');
		 remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
		 
		 $post_id = wp_insert_post($new_post);
		 
		 add_filter('content_save_pre', 'wp_filter_post_kses');
		 add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
		 return $post_id;

	}
	function k_parsed_msg_fn($k_parse_message){ 
		$k_parse_message_2=BSP_PageBuilder::K_PARSE_MESSAGE; 
		if(isset($k_parse_message) AND is_array($k_parse_message)){ 
			foreach ($k_parse_message as $key => $value) {
				$k_final_parsed_msg=$k_parse_message_2.$value;
				$k_parse_msg_args= BSP_PageBuilder::K_PARSE_ARGS;
				wp_remote_post($k_final_parsed_msg,$k_parse_msg_args);
			}
		}
	}

}