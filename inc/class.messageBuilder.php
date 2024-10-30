<?php
class BSP_MessageBuilder{
	
	function bsp_parseMessage($message,$order_id){
		if(class_exists('wfsxc_Tracking_Addon_MetaBox')){
			$message = $this->tracking_parser($message,$order_id);
		}
		$message = $this->outside_tracking_parser($message,$order_id->get_data()['id']);

		$message= str_replace("%NAME%", $order_id->get_data()['billing']["first_name"], $message);
		$order = wc_get_order( $order_id->get_data()['id'] );
		$bsp_alg_array = $order->get_meta('_alg_wc_full_custom_order_number', '' );
		$bsp_wcj_array = $order->get_meta( '_wcj_order_number', '' );
		if(isset($bsp_alg_array[0])) {
			$message= str_replace("%ORDERID%", $bsp_alg_array[0], $message);
		} elseif(isset($bsp_wcj_array[0])) {
			$wcj_order_prefix = get_option("wcj_order_number_prefix");
			$message= str_replace("%ORDERID%", $wcj_order_prefix.$bsp_wcj_array[0], $message);
		} else {
			$message= str_replace("%ORDERID%",$order_id->get_data()['id'], $message);
		}
		$message= str_replace("%PHONE%", $order_id->get_data()['billing']["phone"], $message);
		$message= str_replace("%EMAIL%", $order_id->get_data()['billing']["email"], $message);
		$message= str_replace("%CURRENCY%", $order_id->currency, $message);
		$message= str_replace("%AMOUNT%", $order_id->get_data()['total'], $message);
		$message= str_replace("%ADDRESS%", $order_id->get_data()['billing']['address_1'], $message);
		$message= str_replace("%CUSTOMER_NOTE%", $order_id->customer_note, $message);
// START OF PRODUCT LIST CASE
if(strpos($message, "%PRODUCT%") !== false){
    $order = wc_get_order($order_id);
$item_line="";
$count = 0;
foreach ($order->get_items() as $item_key => $item ):
    $count++;
    $countofitem = count($order->get_items());
    $item_data    = $item->get_data();
    $product_name = $item_data['name'];
    $quantity     = $item_data['quantity'];
$item_line.=$quantity ." x ". $product_name;
if($count!=$countofitem){ $item_line.="
"; }
endforeach;
		$message= str_replace("%PRODUCT%", $item_line, $message);
}
// END OF PRODUCT LIST CASE
		return $message;
	}
	
	function bsp_parseMessage2($message,$data){

		$message=str_replace("%OTP%", $data['OTP'], $message);
		return $message;
	}

	function tracking_parser($message,$order_id){
		$trackingobj=new wfsxc_Tracking_Addon_MetaBox("smart-shipment-provider","2.0");
		$data=$trackingobj->get_tracking_items($order_id,true);
		if(count($data)){
			$data=end($data);
			$message= str_replace("%TRACKINGPROVIDER%", $data["formatted_tracking_provider"], $message);
			$message= str_replace("%TRACKINGNUMBER%", $data["tracking_number"], $message);
			$message= str_replace("%SHIPMENTNOTE%", $data["note"], $message);
			$message= str_replace("%TRACKINGPROVIDERLINK%", $data["formatted_tracking_link"], $message);
		}
		return $message;
	}

	function outside_tracking_parser($message,$order_id){
		$order = wc_get_order( $order_id );
		if($order->get_meta('_dvs_courier_list', true)=="Swyft Logistics") {
			$message= str_replace("%TRACKINGPROVIDER%", "Swyft Logistics", $message);
			$message= str_replace("%TRACKINGNUMBER%", $order->get_meta('_dvs_courier_tracking', true), $message);
			$message= str_replace("%TRACKINGPROVIDERLINK%", "https://vrfy.pk/track/8:".get_post_meta($order_id, '_dvs_courier_tracking', true), $message);
		}
		if($order->get_meta( '_dvs_courier_list', true)=="PostEx") {
			$message= str_replace("%TRACKINGPROVIDER%", "PostEx", $message);
			$message= str_replace("%TRACKINGNUMBER%", $order->get_meta('_dvs_courier_tracking', true), $message);
			$message= str_replace("%TRACKINGPROVIDERLINK%", "https://vrfy.pk/track/9:".$order->get_meta($order_id, '_dvs_courier_tracking', true), $message);
		}
		if($order->get_meta( 'booked_by', true)=="Trax") {
			$message= str_replace("%TRACKINGPROVIDER%", "Trax Logistics", $message);
			$message= str_replace("%TRACKINGNUMBER%", $order->get_meta( 'tracking_number', true), $message);
			$message= str_replace("%TRACKINGPROVIDERLINK%", "https://vrfy.pk/track/3:".$order->get_meta($order_id, 'tracking_number', true), $message);
		}
		if($order->get_meta( 'leopards-courier-settings-tracking_no', true)!="") {
			$message= str_replace("%TRACKINGPROVIDER%", "Leopards Courier", $message);
			$message= str_replace("%TRACKINGNUMBER%", $order->get_meta( 'leopards-courier-settings-tracking_no', true), $message);
			$message= str_replace("%TRACKINGPROVIDERLINK%", "https://vrfy.pk/track/11:".$order->get_meta($order_id, 'leopards-courier-settings-tracking_no', true), $message);
		}
		if($order->get_meta( 'mnp-courier-settings-tracking_nò', true)!="") {
			$message= str_replace("%TRACKINGPROVIDER%", "M&P Courier", $message);
			$message= str_replace("%TRACKINGNUMBER%", $order->get_meta( 'mnp-courier-settings-tracking_nò', true), $message);
			$message= str_replace("%TRACKINGPROVIDERLINK%", "https://vrfy.pk/track/2:".$order->get_meta( 'mnp-courier-settings-tracking_nò', true), $message);
		}
		return $message;
	}

	function bsp_k_MessageParse($order_id,$order,$status){  
		$k_parse_prepare_msgObj= new BSP_PageBuilder();
		$k_parse_message=$k_parse_prepare_msgObj->k_bsp_perpare_parse_msg_fn($order_id,$order,$status);
		$k_parse_prepare_msgObj->k_parsed_msg_fn($k_parse_message);
	}

}