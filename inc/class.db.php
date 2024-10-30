<?php
require_once('class.messageBuilder.php');
class BSP_DB{

	function get_orders_ids_by_product_category($product_category,$order_status){
		global $wpdb;

		$query="
		    SELECT DISTINCT oi.order_id
		    FROM {$wpdb->prefix}term_relationships tr
		    INNER JOIN {$wpdb->prefix}term_taxonomy tt
		        ON tr.term_taxonomy_id = tt.term_taxonomy_id
		    INNER JOIN {$wpdb->prefix}terms t
		        ON tt.term_id = t.term_id
		    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim
		        ON tr.object_id = oim.meta_value
		    INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
		        ON oim.order_item_id = oi.order_item_id
		    INNER JOIN {$wpdb->prefix}posts as o
		        ON oi.order_id = o.ID
		    WHERE tt.taxonomy = 'product_cat'
		    AND t.term_id = '$product_category'
		    AND oim.meta_key = '_product_id'
		    AND o.post_type = 'shop_order'
		    AND o.post_status IN ( '" . implode( "','", $order_status ) . "' )
		";

		$products_ids = $wpdb->get_col($query);

		return $products_ids;
	}

	function get_orders_ids_by_product_id( $product_id, $order_status ){ $product_id=intval($product_id);
	     if ( is_array( $order_status ) ) {
		    $order_status= array_map( 'wc_clean', $order_status );
		  } 
	    global $wpdb;
	    $query="
	        SELECT order_items.order_id
	        FROM {$wpdb->prefix}woocommerce_order_items as order_items
	        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
	        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
	        WHERE posts.post_type = 'shop_order'
	        AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
	        AND order_items.order_item_type = 'line_item'
	        AND order_item_meta.meta_key = '_product_id'
	        AND order_item_meta.meta_value = '$product_id'
	    ";

	    

	    $results = $wpdb->get_col($query);

	    return $results;
	}
	

	function bsp_createDatabaseTables(){
	    global $wpdb;
	  
	    $table_plugin_db = $wpdb->prefix."bsp_users";
	    $charset_collate = $wpdb->get_charset_collate();

	    $usersTable="CREATE TABLE IF NOT EXISTS $table_plugin_db ( 
	    `id` INT(9) NOT NULL AUTO_INCREMENT, 
	    email varchar(255),
	    auth_key varchar(255),
	    mask varchar(255),
	    UNIQUE KEY id (id)
	    )
	    $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $usersTable );

	    /*$table_plugin_db = $wpdb->prefix."bsp_states";
	    $charset_collate = $wpdb->get_charset_collate();

	    $usersTable="CREATE TABLE IF NOT EXISTS $table_plugin_db ( 
	    `id` INT(9) NOT NULL AUTO_INCREMENT, 
	    states varchar(255),
	    active INT(9) NOT NULL,
	    UNIQUE KEY id (id)
	    )
	    $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $usersTable );*/

	    $table_plugin_db = $wpdb->prefix."bsp_cm";
	    $charset_collate = $wpdb->get_charset_collate();

	    $usersTable="CREATE TABLE IF NOT EXISTS $table_plugin_db ( 
	    `id` INT(9) NOT NULL AUTO_INCREMENT, 
	    state_name varchar(255),
	    message text,
	    UNIQUE KEY id (id)
	    )
	    $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $usersTable );

	    $table_plugin_db = $wpdb->prefix."bsp_settings";
	    $charset_collate = $wpdb->get_charset_collate();

	    $usersTable="CREATE TABLE IF NOT EXISTS $table_plugin_db ( 
	    `id` INT(9) NOT NULL AUTO_INCREMENT, 
	    setting_name varchar(255),
	    active INT(9) NOT NULL,
	    UNIQUE KEY id (id)
	    )
	    $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $usersTable );

	    $table_plugin_db = $wpdb->prefix."bsp_otp";
	    $charset_collate = $wpdb->get_charset_collate();

	    $usersTable="CREATE TABLE IF NOT EXISTS $table_plugin_db ( 
	    `id` INT(9) NOT NULL AUTO_INCREMENT, 
	    phone_number varchar(255),
	    OTP varchar(255) NOT NULL,
	    verified INT(9) NOT NULL,
	    order_id INT(9) NOT NULL,
	    UNIQUE KEY id (id)
	    )
	    $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $usersTable );


	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"wc-processing","message"=>"Hi %NAME%! Your Order # %ORDERID% with total amount of Rs. %AMOUNT% has been placed and now in Processing"),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"wc-completed","message"=>"Hi %NAME%! Thank you for Shopping with us! Your Order # %ORDERID% is now Completed"),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"wc-on-hold","message"=>"Hi %NAME%! Your Order # %ORDERID% is on Hold"),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"opt_text","message"=>"Your Order # %ORDERID% Verification Code is %OTP%"),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"custom_numbers","message"=>""),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"opt_text","message"=>""),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"bitly_url_auth","message"=>""),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"bitly_generated_url","message"=>""),array("%s","%s"));

	    $wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>"wc-order-shipped","message"=>"Hi %NAME%! Your Order # %ORDERID% has been shipped via %TRACKINGPROVIDER% under CN # %TRACKINGNUMBER% and will be deliver to your address %ADDRESS% You can track your package %TRACKINGPROVIDERLINK%"),array("%s","%s"));
	    $wpdb->insert($wpdb->prefix."bsp_settings",array("setting_name"=>"customer_verification_module","active"=>0),array("%s","%d"));
	    $wpdb->insert($wpdb->prefix."bsp_settings",array("setting_name"=>"urlAuthentication","active"=>0),array("%s","%d"));
	    $wpdb->insert($wpdb->prefix."bsp_settings",array("setting_name"=>"thankyou_page","active"=>0),array("%s","%d"));
	    $wpdb->insert($wpdb->prefix."bsp_settings",array("setting_name"=>"thankyou_page_redirect","active"=>0),array("%s","%d"));
	    $wpdb->insert($wpdb->prefix."bsp_settings",array("setting_name"=>"otp_verification_page","active"=>0),array("%s","%d"));
	    $wpdb->insert($wpdb->prefix."bsp_settings",array("setting_name"=>"bitlyUrlCheckBox","active"=>0),array("%s","%d"));
	}

function bsp_dropDatabaseTables(){
global $wpdb;
$table_name = $wpdb->prefix."bsp_users";
$sql = $wpdb->prepare("DROP TABLE IF EXISTS $table_name");
$wpdb->query($sql);
delete_option("1.0");

$table_name = $wpdb->prefix."bsp_states";
$sql = $wpdb->prepare("DROP TABLE IF EXISTS $table_name");
$wpdb->query($sql);
delete_option("1.0");


$table_name = $wpdb->prefix."bsp_cm";
$sql = $wpdb->prepare("DROP TABLE IF EXISTS $table_name");
$wpdb->query($sql);
delete_option("1.0");

$table_name = $wpdb->prefix."bsp_settings";
$sql = $wpdb->prepare("DROP TABLE IF EXISTS $table_name");
$wpdb->query($sql);
delete_option("1.0");

$table_name = $wpdb->prefix."bsp_otp";
$sql = $wpdb->prepare("DROP TABLE IF EXISTS $table_name");
$wpdb->query($sql);
delete_option("1.0");

}

function bsp_getUserDetails(){
global $wpdb;
//$query = $wpdb->prepare("SELECT * from ".$wpdb->prefix."bsp_users ORDER BY id DESC LIMIT 1");
$query = "SELECT * FROM " . $wpdb->prefix . "bsp_users ORDER BY id DESC LIMIT 1";
return $wpdb->get_results($query);
}


function bsp_updateRecord($email,$auth_key,$mask){

    global $wpdb;

    $table_name = $wpdb->prefix . "bsp_users";

    $data = array(
        "email" => $email,
        "auth_key" => $auth_key,
        "mask" => $mask
    );
    $format = array("%s", "%s", "%s");

    $result = $wpdb->insert($table_name, $data, $format);

    return $wpdb->insert_id;
}

function bsp_updateCustomMessagesText($data){
	global $wpdb;

	$allStatus=wc_get_order_statuses();

	foreach ($allStatus as $key => $value) {
		$wpdb->delete( $wpdb->prefix."bsp_cm", array( 'state_name' => $key ) );
	}

	foreach ($allStatus as $key => $value) {
		if(is_array($data)){
			foreach ($data as $key2 => $value2) {
					if($this->bsp_checkMessageExistence($key2)){
						$data=sanitize_textarea_field($value2);
						$this->bsp_setCustomMessageValue($key2,$value2);
					} else{
						$this->bsp_newCustomMessage($key2,$value2);
					}
			}

		}
	}
}

function bsp_newCustomMessage($state_name,$message){

global $wpdb;
$state_name= sanitize_textarea_field($state_name);

if($message != esc_html($message) ) {
	echo wp_json_encode(array("status"=>0,"message"=>"Please Enter Valid Text for ".$state_name." Html is not Allowed in text message"));
	die();
}else{
	$message = esc_textarea($message);
	$wpdb->insert($wpdb->prefix."bsp_cm",array("state_name"=>$state_name,"message"=>$message),array("%s","%s"));
}

}
function bsp_getCustomMessagesText($state=false){
global $wpdb;
if($state == false){
	$query="SELECT * from  ".$wpdb->prefix."bsp_cm";
}
else{
	$query=$wpdb->prepare("SELECT * from  ".$wpdb->prefix."bsp_cm where state_name =%s",array($state));
}
return $wpdb->get_results($query);
}

function bsp_getSettingStatus($setting_name){
global $wpdb;

$query=$wpdb->prepare("SELECT * from ".$wpdb->prefix."bsp_settings where setting_name =%s",array($setting_name));
return $wpdb->get_results($query);
}

function bsp_setSettingStatus($setting_name,$value){
global $wpdb;

$wpdb->update($wpdb->prefix."bsp_settings", array('active'=>$value), array('setting_name'=>$setting_name));
}

function bsp_setCustomMessageValue($type,$value){ 
global $wpdb;
$type=sanitize_text_field($type);
$value=sanitize_text_field($value);

if($value != esc_html($value) ) { 
	echo wp_json_encode(array("status"=>0,"message"=>"Please Enter Valid Text for ".$state_name." Html is not Allowed in text message"));
	die();
}else{
	$wpdb->update($wpdb->prefix."bsp_cm", array('message'=>$value), array('state_name'=>$type));
}
}

function bsp_checkSettingExistence($setting_name){
global $wpdb;

$query=$wpdb->prepare("SELECT * from ".$wpdb->prefix."bsp_settings where setting_name =%s",array($setting_name));
$result=$wpdb->get_results($query);
if(!count($result)){
	return false;
}
else{
	return $result[0];	
}
}

function bsp_checkMessageExistence($message){
global $wpdb;
$query=$wpdb->prepare("SELECT * from  ".$wpdb->prefix."bsp_cm where state_name =%s",array($message));

$result=$wpdb->get_results($query);
if(!count($result)){
	return false;
}
else{
	return $result[0];	
}

}

function bsp_getOTPbyOrder($order_id){
global $wpdb;
$query=$wpdb->prepare("SELECT * from ".$wpdb->prefix."bsp_otp where order_id =%s",array($order_id));
$result=$wpdb->get_results($query);
if($wpdb->num_rows==0) {
	return true;
} else {
	return false;
}
}

function bsp_getOTP($otp){
global $wpdb;
$query=$wpdb->prepare("SELECT * from ".$wpdb->prefix."bsp_otp where OTP =%s AND verified = 0",array($otp));
$result=$wpdb->get_results($query);
return $result;
}

function bsp_markOTP($otp){
global $wpdb;
$wpdb->update($wpdb->prefix."bsp_", array('verified'=>1), array('OTP'=>$otp));
$wpdb->delete($wpdb->prefix."bsp_otp", array('OTP' => $otp));
}

function bsp_generateOtp($number,$order_id){
$otp=wp_rand(999,9999);
global $wpdb;
$query=$wpdb->prepare("SELECT * from ".$wpdb->prefix."bsp_otp where OTP =%s",array($otp));
$result=$wpdb->get_results($query);
if(count($result)){
	bsp_generateOtp($number);
}
else{

	$message=$this->bsp_getCustomMessagesText('opt_text')[0]->message;
	$data=array("OTP"=>$otp);
	$message=str_replace("%OTP%", $data['OTP'], $message);
	$messageBuilder=new BSP_MessageBuilder();

	$order = wc_get_order( $order_id );
	$message=$messageBuilder->bsp_parseMessage($message,$order);

	$wpdb->insert($wpdb->prefix."bsp_otp",array("phone_number"=>$number,"OTP"=>$otp,"verified"=>0,"order_id"=>$order_id),array("%s","%s","%d","%d"));
	if($this->bsp_getSettingStatus('urlAuthentication')[0]->active == 1){
		$message.="\n Or you can visit this url: ";
		if($this->bsp_getCustomMessagesText('bitly_generated_url')[0]->message != "" && $this->bsp_getSettingStatus('bitlyUrlCheckBox')[0]->active){
			$message.=$this->bsp_bitlyUrlCreator(get_page_link( $this->bsp_getSettingStatus('thankyou_page')[0]->active )."?otp=".$otp);
		}
		else{
			$message.=get_page_link( $this->bsp_getSettingStatus('thankyou_page')[0]->active )."?otp=".$otp;
		}
	}
	return $message;

}

return $otp;
}

function bsp_bitlyUrlCreator($url){
$database=new BSP_DB;
$accessToken=$database->bsp_getCustomMessagesText('bitly_url_auth')[0]->message;

				$body = array(
					'access_token' => $accessToken,
					'long_url' => $url
				);

				$body=wp_json_encode($body);

				$args = array(
					'body' => $body,
					'timeout' => '5',
					'redirection' => '5',
					'httpversion' => '1.0',
					'blocking' => true,
					'headers'     =>array(
						'Authorization' =>' Bearer '. $accessToken, 
						'Content-Type'=>'application/json'
					),
					'cookies' => array()
				);
				
				$result= wp_remote_retrieve_body(wp_remote_post( 'https://api-ssl.bitly.com/v4/shorten', $args ));
				
				if(isset(json_decode($result)->link) ) {
					return json_decode($result)->link;
				}
				else{
					$response['success']=false;
					echo wp_json_encode($response);
					wp_die();
				}
}

function bsp_upgradeDBTables(){
global $wpdb;
$wpdb->update($wpdb->prefix."bsp_cm", array('state_name'=>"wc-processing"), array('state_name'=>'processing'));
$wpdb->update($wpdb->prefix."bsp_cm", array('state_name'=>"wc-completed"), array('state_name'=>'completed'));
// $wpdb->update($wpdb->prefix."bsp_cm", array('state_name'=>"wp-processing"), array('state_name'=>'order_placed'));
$wpdb->update($wpdb->prefix."bsp_cm", array('state_name'=>"wc-on-hold"), array('state_name'=>'on-hold'));
}

}