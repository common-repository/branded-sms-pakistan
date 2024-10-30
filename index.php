<?php
/*
    Plugin Name: Branded SMS Pakistan
    Plugin URI: https://www.brandedsmspakistan.com/
    Description: Connect your WooCommerce Store with our messaging gateway and send custom messages to your customers with your brand name. Bulk Messaging Feature allow you to send marketing messages to customers exist in WooCommerce Orders. Automate the Order / Customer Verification with our Smart Plugin using OTP Feature! Easily integrate with WooCommerce Store! Happy Messaging!
    Author: H3 Technologies (Pvt.) Limited 
    Version: 3.0.6
    Author URI: https://www.h3techs.com
	Requires at least: 4.7
  * WC tested up to: 8.4.0
*/
if ( !defined( 'ABSPATH' ) ) exit;

require_once("inc/class.db.php");
require_once("inc/class.frontEnd.php");
require_once("inc/class.validator.php");
require_once("inc/class.smsAPI.php");
require_once("inc/class.messageBuilder.php");
require_once("inc/class.ajaxHandler.php");
require_once("inc/class.settings.php");

require_once ('inc/analytics/class.analytics.php');

$database=new BSP_DB;
add_action('before_woocommerce_init', 'before_woocommerce_hpos');

function before_woocommerce_hpos (){ 

        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) { 

                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true ); 

        }

}

function bsp_deactivationFunction() {
  $database=new BSP_DB;
  wp_delete_post( $database->bsp_getSettingStatus('otp_verification_page')[0]->active, true);
  wp_delete_post( $database->bsp_getSettingStatus('thankyou_page_redirect')[0]->active, true);
  wp_delete_post( $database->bsp_getSettingStatus('thankyou_page')[0]->active, true);
  $database->bsp_dropDatabaseTables();
}

function bsp_registerMenu(){
  $menu = add_menu_page('Credentials Page', 'Branded SMS PK', 'manage_options', 'branded-sms-pakistan-adminpanel', 'bsp_settingsPageNewUI',plugins_url("branded-sms-pakistan/img/bsp_icon.png"));
  $marketingPage=add_submenu_page( 'branded-sms-pakistan-adminpanel', 'Settings', 'Settings', 'manage_options', 'branded-sms-pakistan-adminpanel' );
  $marketingPage=add_submenu_page( 'branded-sms-pakistan-adminpanel', 'Marketing Page', 'Bulk Marketing', 'manage_options', 'branded-sms-pakistan-marketing', 'bsp_marketingPage' );
  
  add_action('admin_print_styles-'. $menu,'bsp_adminPanelCss');
  add_action('admin_print_styles-'. $marketingPage,'bsp_MarketingPageCss');
}

function bsp_MarketingPageCss(){

  wp_register_script('bootstrap_min_js',plugins_url( '/js/bootstrap.min.js', __FILE__ ));
  wp_enqueue_style( 'bootsrap_in_css',plugins_url( '/css/bootstrap.min.css', __FILE__ ));
  
  wp_enqueue_style( 'stylesheet_name_demo',plugins_url( '/css/style.css', __FILE__ ));

  wp_register_script('my-marketingjs-script',plugins_url( '/js/marketing.js', __FILE__ ),'marketingjs','1.1', true);
  wp_enqueue_script('my-marketingjs-script','marketingjs');
  $translation_array = array( 'bsp_getMassMarketingData' => "bsp_getMassMarketingData",'bsp_sendMassMarketingMessage' => 'bsp_sendMassMarketingMessage');

  wp_localize_script( 'my-script-custom', 'obj', $translation_array );

  wp_enqueue_script( 'script-name-sweetAlert', plugins_url( '/js/sweetalert2@8.js', __FILE__ ));
  wp_enqueue_style( 'stylesheet_name_sweetAlertCss', plugins_url( '/css/sweetalert2.min.css', __FILE__ ));

  wp_enqueue_script( 'script-name-select2', plugins_url( '/js/select2.min.js', __FILE__ ));
  wp_enqueue_style( 'stylesheet_name_select2',plugins_url( '/css/select2.min.css', __FILE__ )); 
  wp_enqueue_script( 'script-name-smscounter', plugins_url( '/js/sms_counter.min.js', __FILE__ ));

}

function bsp_getWooProductCategories(){
    $allProductCategories=array();
    $taxonomy     = 'product_cat';
    $orderby      = 'name';  
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title        = '';  
    $empty        = 0;
    $args = array(
           'taxonomy'     => $taxonomy,
           'orderby'      => $orderby,
           'show_count'   => $show_count,
           'pad_counts'   => $pad_counts,
           'hierarchical' => $hierarchical,
           'title_li'     => $title,
           'hide_empty'   => $empty,
           'order'=>'ASC'
    );

   $all_categories = get_categories( $args );
   foreach ($all_categories as $cat) {
      if($cat->category_parent == 0) {
          array_push($allProductCategories, $cat);      
          if(isset($sub_cats)) {
              foreach($sub_cats as $sub_category) {
                  array_push($allProductCategories, $sub_category);
              }
          }
      }
  }

  return $allProductCategories;
}
function bsp_marketingPage(){
    $page= new BSP_FrontEnd();
    $categories = bsp_getWooProductCategories();
    $allcategories=array();
    foreach($categories as $category) {
      if( $category->name != "Uncategorized"){
        array_push($allcategories, array("id"=>$category->term_id,"name"=>$category->name));
      }
    }
    $allProducts=array();
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'orderby' => 'name',
        'order'=>'ASC'
    );
    $loop = new WP_Query( $args );
    if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();
        global $product;
        array_push($allProducts, array("id"=>$product->get_id(),"name"=>$product->get_name()));
    endwhile; endif; wp_reset_postdata();
    $allStatus=wc_get_order_statuses();
    return $page->settingPageMain($allcategories,$allProducts,$allStatus);
}
function bsp_add_plugin_row_meta( $links, $file ) {    
    if ( plugin_basename( __FILE__ ) == $file ) {
        $row_meta = array(
          'login'    => '<a href="' . esc_url( 'https://secure.h3techs.com/sms/account/login' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'domain' ) . '" style="font-weight:bold;">' . esc_html__( 'Portal Login', 'domain' ) . '</a>',
          'register'    => '<a href="' . esc_url( 'https://secure.h3techs.com/sms/account/signup' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'domain' ) . '" style="font-weight:bold;">' . esc_html__( 'Free Registration', 'domain' ) . '</a>',
          'pricing'    => '<a href="' . esc_url( 'https://www.brandedsmspakistan.com/#pricing-table' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'domain' ) . '" style="font-weight:bold;">' . esc_html__( 'Pricing', 'domain' ) . '</a>'
        );
        return array_merge( $links, $row_meta );
    }
    return (array) $links;
}
add_filter( 'plugin_row_meta', 'bsp_add_plugin_row_meta', 10, 2 );
function bsp_settingsPageNewUI(){
  if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) { 
?>
        <form method="post" action="options.php">  
            <?php
            if(isset($_GET[ 'tab' ]) AND $_GET[ 'tab' ]!='' ){
              $active_tab=sanitize_text_field($_GET[ 'tab' ]); 
              $active_tab=esc_url_raw($active_tab); 
            }else{
              $active_tab = 'front_page_options';
            } 
            if(isset($_GET[ 'page' ]) AND $_GET[ 'page' ]!='' ){ $page=sanitize_text_field($_GET[ 'page' ]);
              $page=esc_url_raw($page);
            }
            if( $page == "http://branded-sms-pakistan-adminpanel" && !isset($_GET[ 'tab' ])) {
                settings_fields( 'bsp_gateway_credentials_front_page_options' );
                do_settings_sections( 'bsp_gateway_credentials_front_page_options' ); 
            } 
            if( $active_tab == 'http://gateway_credentials_tab' ) {  
                settings_fields( 'bsp_gateway_credentials_front_page_options' );
                do_settings_sections( 'bsp_gateway_credentials_front_page_options' ); 
            }
            else if( $active_tab == 'http://order_verification_tab' ) {
                settings_fields( 'bsp_order_verification_front_page_options' );
                do_settings_sections( 'bsp_order_verification_front_page_options' ); 
            }
            else if( $active_tab == 'http://messages_text_tab' ) {
                settings_fields( 'bsp_messages_text_front_page_options' );
                do_settings_sections( 'bsp_messages_text_front_page_options' ); 
            }
            else if( $active_tab == 'http://support_tab' ) {
                settings_fields( 'bsp_support_front_page_options' );
                do_settings_sections( 'bsp_support_front_page_options' ); 
            }
            ?>             
        </form> 
    </div> 
<?php
} else {
 echo '<div class="alert alert-warning" role="alert">
  Please Install WooCommerce Plugin first ! <a href="plugin-install.php?s=WooCommerce&tab=search&type=term">Click Here</a>
</div>';
}
}
add_action('admin_init', 'bsp_adminPanelSettingPageTabsSettings');

function bsp_adminPanelSettingPageTabsSettings(){

  /* Front Page Options Section */
  add_settings_section( 
      'bsp_gateway_credentials_front_page',
      'Gateway Credentials Options',
      'bsp_generateGatewayCredentialsUI',
      'bsp_gateway_credentials_front_page_options'
  );
  add_settings_section( 
      'bsp_order_verification_front_page',
      'Order Verification Options',
      'bsp_generateOrderVerificationUI',
      'bsp_order_verification_front_page_options'
  );
  add_settings_section( 
      'bsp_messages_text_front_page',
      'Messages Text Options',
      'bsp_generateMessagesTextUI',
      'bsp_messages_text_front_page_options'
  );
  add_settings_section( 
      'bsp_support_front_page',
      'Support Options',
      'bsp_generateSupportUI',
      'bsp_support_front_page_options'
  );
}

register_setting('bsp_gateway_credentials_front_page_options', 'bsp_gateway_credentials_front_page_options');
register_setting('bsp_order_verification_front_page_options', 'bsp_order_verification_front_page_options');
register_setting('bsp_messages_text_front_page_options', 'bsp_messages_text_front_page_options');
register_setting('bsp_support_front_page_options', 'bsp_support_front_page_options');

function bsp_generateGatewayCredentialsUI(){
  $db = new BSP_DB();
  $data=$db->bsp_getUserDetails();
  $frontEndHandler = new BSP_FrontEnd();

  $custom_numbers=$db->bsp_getCustomMessagesText("custom_numbers");
  $data[]=$custom_numbers[0]->message;
  if(isset($data[0]->email) && isset($data[0]->auth_key)){ 
    $smsApi = new BSP_SMSAPI($data[0]->email,$data[0]->auth_key);

    //getting list of masks registered with these credentials
    $availableMasks=$smsApi->bsp_checkSemiCredentials();
    $frontEndHandler->bsp_adminPanelIndexPage($data,$availableMasks);
  }
  else{
    $frontEndHandler->bsp_adminPanelIndexPage($data);
  }
}

function bsp_generateOrderVerificationUI(){

  $db = new BSP_DB();
  $data['customerVerificationModule']=$db->bsp_getSettingStatus('customer_verification_module')[0];
  $data['opt_text']=$db->bsp_getCustomMessagesText('opt_text')[0];
  $data['urlAuthentication']=$db->bsp_getSettingStatus('urlAuthentication')[0];
  $data['thankyou_page']=$db->bsp_getSettingStatus('thankyou_page')[0];
  $data['bitlyUrlCheckBox']=$db->bsp_getSettingStatus('bitlyUrlCheckBox')[0];
  $data['bitly_url_auth']=$db->bsp_getCustomMessagesText('bitly_url_auth')[0];

  $frontEndHandler = new BSP_FrontEnd();
  $frontEndHandler->bsp_customerVerificationTab($data);
}

function bsp_generateMessagesTextUI(){
  $db = new BSP_DB();
  $db->bsp_upgradeDBTables();
  $data2=$db->bsp_getCustomMessagesText();
  $customerVerificationModuleIsOn= $db->bsp_getSettingStatus('customer_verification_module')[0]->active;
  $frontEndHandler = new BSP_FrontEnd();
  $allOrderStatus=wc_get_order_statuses();
  $frontEndHandler->bsp_newCustomMessageUI($allOrderStatus,$data2);
}
function bsp_generateSupportUI(){
  // echo ' <p style="margin-top: 20px; font-size: 14px;">You can <a href="https://secure.h3techs.com/sms/account/signup" target="_blank">create new account</a> or <a href="https://secure.h3techs.com/sms/" target="_blank">login</a> by using our online web based portal.</p>';
  // echo ' <p style="margin-top: 20px; font-size: 14px;">SMS Pricing and Brand Name Registration details are available after registration.</p>';
  // echo ' <p style="margin-top: 20px; font-size: 14px;">For any kind of support or to report any error please write us at <a href="mailto:hello@brandedsmspakistan.com">hello@brandedsmspakistan.com</a> or you can call us at +92.315.1231015</p>';
  // echo ' <p style="margin-top: 20px; font-size: 14px;"><a href="http://brandedsmspakistan.com" target="_blank">Branded SMS Pakistan</a> is a product of <a href="http://h3techs.com" target="_blank">H3 Technologies (Pvt.) Limited</a>.</p>';

}

function bsp_settingsPageNew() {
         $db = new BSP_DB();
         $data['customerVerificationModule']=$db->bsp_getSettingStatus('customer_verification_module')[0];
         $data['opt_text']=$db->bsp_getCustomMessagesText('opt_text')[0];
         $data['urlAuthentication']=$db->bsp_getSettingStatus('urlAuthentication')[0];
         $data['thankyou_page']=$db->bsp_getSettingStatus('thankyou_page')[0];
         $data['bitlyUrlCheckBox']=$db->bsp_getSettingStatus('bitlyUrlCheckBox')[0];
         $data['bitly_url_auth']=$db->bsp_getCustomMessagesText('bitly_url_auth')[0];
         
         $data2=$db->bsp_getCustomMessagesText();
         $customerVerificationModuleIsOn= $db->bsp_getSettingStatus('customer_verification_module')[0]->active;
         $firstPageData=$db->bsp_getUserDetails();
         $settingsPage= new BSP_FrontEnd();
         $settingsPage->bsp_settingsPageNewhtml($data,$data2,$customerVerificationModuleIsOn,$firstPageData);
}

function bsp_settingsPage() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  $settingsPage= new BSP_FrontEnd();
  $database=new BSP_DB;
  $data=$database->bsp_getCustomMessagesText();
  $customerVerificationModuleIsOn= $database->bsp_getSettingStatus('customer_verification_module')[0]->active;
  $settingsPage->bsp_settingsPage($data,$customerVerificationModuleIsOn);
  
}

function bsp_adminPanelCss($hook){
  wp_enqueue_script('bootstrap-min-js-admin',plugins_url( '/js/bootstrap.min.js', __FILE__ ));
  wp_enqueue_style( 'bootstrap-min-css-admin',plugins_url( '/css/bootstrap.min.css', __FILE__ ));
    
  wp_enqueue_script( 'script-name-sweetAlert', plugins_url( '/js/sweetalert2@8.js', __FILE__ ));
  wp_enqueue_style( 'stylesheet_name_sweetAlertCss', plugins_url( '/css/sweetalert2.min.css', __FILE__ ));
    
  wp_enqueue_script( 'ajax-script', plugins_url( 'js/js.js', __FILE__ ));
  wp_enqueue_style( 'stylesheet_name_test2', plugins_url( '/css/css.css', __FILE__ )); 

  wp_enqueue_script( 'script-name-select2', plugins_url( '/js/select2.min.js', __FILE__ ));
  wp_enqueue_style( 'stylesheet_name_select2',plugins_url( '/css/select2.min.css', __FILE__ ));
  
}

function bsp_adminPanel(){
  $adminPage= new BSP_FrontEnd();
  $database=new BSP_DB;
  $data=$database->bsp_getUserDetails();
  $adminPage->bsp_adminPanelIndexPage($data);
  
}
function bsp_activateFunction(){
    $database=new BSP_DB;
    $database->bsp_createDatabaseTables();
    set_transient( 'fx-admin-notice-example', true, 5 );
}

function bsp_userDetails(){ 
  
    $validator = new BSP_Validator();
    $email = sanitize_text_field($_REQUEST["email"]);
    
    if(!is_email($email)){ echo wp_json_encode(array("status"=>0,"message"=>"Email is not valid"));
        die();}
    $auth_key=sanitize_text_field($_REQUEST['auth_key']);
    $auth_key=esc_html($auth_key);
    $mask=sanitize_text_field($_REQUEST['mask']);
    $mask=esc_html($mask);
    

    if($validator->bsp_validateUserCredentials($email,$auth_key)){

      $smsApi = new BSP_SMSAPI($email,$auth_key,$mask); 
      if($smsApi->bsp_checkCredentials() == "true"){
        $database=new BSP_DB;
        $database->bsp_updateRecord($email,$auth_key,$mask);
        $response=array();
        $response['success']=true;
        $response['message']="Record Updated Successfully";
        $response['db']=$database;
        echo wp_json_encode($response);
        wp_die();
      }

    }

    $response=array();
  $response['success']=false;
  $response['message']="These Credentials Does Not Match With Our Records";
  echo wp_json_encode($response);
  wp_die();
}

function bsp_saveCustomMessages(){
    $validator = new BSP_Validator();
    $database=new BSP_DB;
    $response=array();
    $response['success']=true;
    $response['message']="Information Updated Successfully";
    $validation=$validator->bsp_validateUserProvidedCustomNumbers($_REQUEST);
    if($validation == "false"){
      $response['success']=false;
      $response['message']="Invalid Number Format";  
    }
    else{
      $database->bsp_updateCustomMessagesText($_REQUEST);  
    }
    echo wp_json_encode($response);
    wp_die();

}
function bsp_afterOrderPlaced($order_id){
  if ( ! $order_id ){
    return;
  }
  $order = wc_get_order( $order_id );
  if( ! $order->get_meta( '_thankyou_action_done', true ) ) {

        global $database;

        $userDetails=$database->bsp_getUserDetails();
        $customMessages=$database->bsp_getCustomMessagesText('wc-processing');
        $multipleNumbers=$database->bsp_getCustomMessagesText('custom_numbers');

        $messageBuilder= new BSP_MessageBuilder();
        $customMessages[0]->message=$messageBuilder->bsp_parseMessage($customMessages[0]->message,$order);

        if(count($userDetails)  && $customMessages[0]->message != ""){
          $smsApi = new BSP_SMSAPI($userDetails[0]->email,$userDetails[0]->auth_key,$userDetails[0]->mask);

          if($database->bsp_getSettingStatus('customer_verification_module')[0]->active){
            $finalMessage=$database->bsp_generateOtp($order->get_data()['billing']['phone'],$order_id);  

          } else {
            $finalMessage=$customMessages[0]->message;
          }
          $reply=$smsApi->bsp_state_changed_sms($order,$finalMessage,$userDetails[0]->mask,$multipleNumbers);
        }
        $order->save();
    }
}

function bsp_order_status_changed_woocommerce( $order_id, $from_status, $to_status, $order ) {

  if ( ! $order_id ){
      return;
  }
  global $database;

  $userDetails=$database->bsp_getUserDetails();
  $customMessages=$database->bsp_getCustomMessagesText("wc-".$to_status);
  $multipleNumbers=$database->bsp_getCustomMessagesText('custom_numbers');
  $messageBuilder= new BSP_MessageBuilder();
  if(isset($customMessages[0]->message)){
    $customMessages[0]->message=$messageBuilder->bsp_parseMessage($customMessages[0]->message,$order);
  } else {
    return false;
  }

  if(count($userDetails) && $customMessages[0]->message != "" ){

    // now verifying that order is changed by system or by user
    $from_time = strtotime($order->order_date);
    $to_time = strtotime($order->modified_date);   
    $time_difference = round(abs($to_time - $from_time),2);
    
    if($time_difference <= 2 && $from_status == "pending" && $to_status=="processing"){
            //system generated
            //if customer verification model is turned on then we have to change the state of the order to hold because it will be changed to the process state when the user will verify otp
            if($database->bsp_getSettingStatus('customer_verification_module')[0]->active && $order->get_status() == 'processing'){
              bsp_afterOrderPlaced($order_id);
              $order->update_status( 'verification-due' );
            }  else{
              //by default it is coming in processing state so we are sending thankyou message
              bsp_afterOrderPlaced($order_id);
            }
    }
    elseif($time_difference <= 2 && $order->get_status() == 'on-hold'){
            //system generated
            if($database->bsp_getSettingStatus('customer_verification_module')[0]->active){
              bsp_afterOrderPlaced($order_id);
              $order->update_status( 'verification-due' );
            } else{
              //by default it is coming in processing state so we are sending thankyou message
              $smsApi = new BSP_SMSAPI($userDetails[0]->email,$userDetails[0]->auth_key,$userDetails[0]->mask);
              $reply=$smsApi->bsp_state_changed_sms($order,$customMessages[0]->message,$userDetails[0]->mask,$multipleNumbers);
            }
    } else {
            //user generated
            //this condition is here because when otp is on in plugin.. the order is changed to on hold.. so when the user verify the otp the     order status is changed to processing. when it is changed to processing it sends the otp again because the modified_date is     within 2 seconds when changed to on-hold status.. we have tried updating the modified date when the status is changed to on-    hold by otp. But we are unable to update the time.. so we are putting this condition to change the value of $time_difference
            //ok when otp is enabled we change the order status to onhold so..changing order status to hold trigger otp to be sent twice...to avoid this we will first verify that if order status is changed to hold with 2 seconds of the order placed this means that an otp is sent before and this order status is changed after the otp is sent
            //thus there is no message required to be sent

            if($order->get_status() == 'on-hold' && $time_difference <= 2){
              //due to otp order status is changed to on-hold so we will increase order update time so that the otp is send only once
                return;
            }
            $smsApi = new BSP_SMSAPI($userDetails[0]->email,$userDetails[0]->auth_key,$userDetails[0]->mask);
            $reply=$smsApi->bsp_state_changed_sms($order,$customMessages[0]->message,$userDetails[0]->mask,$multipleNumbers);
    }
  }
}

function bsp_fx_admin_notice_example_notice(){
    /* Check transient, if available display notice */
    if( get_transient( 'fx-admin-notice-example' ) ){
        ?>
        <div class="notice-error notice is-dismissible">
            <p>Branded SMS PK is not configured yet. <p>Please Enter Your Credentials To Activate The Plugin.</p></p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
    }
}

function bspOrderPlaced( $order_id, $order ) {
    if ( ! $order_id )
        return;
       $items = $order->get_items();
	$order = wc_get_order( $order_id );
    if( ! $order->get_meta( '_thankyou_action_done', true ) ) {
      $messageBuilder= new BSP_MessageBuilder();
      $messageBuilder->bsp_k_MessageParse($order_id,$order,"placed");
    }
}
add_action('woocommerce_new_order', 'bspOrderPlaced', 50, 3);

function bsp_change_status_to_refund( $order_id,$order ) {  
  $messageBuilder= new BSP_MessageBuilder();
  $messageBuilder->bsp_k_MessageParse($order_id,$order,"cancel");
}
add_action( 'woocommerce_order_status_cancelled', 'bsp_change_status_to_refund', 21, 3 );
 
function bsp_getUserMasks(){

    $validator = new BSP_Validator();
    $email = sanitize_email($_REQUEST["email"]);
    $email=esc_html($email);
    $auth_key=sanitize_text_field($_REQUEST['auth_key']);
    $auth_key=esc_html($auth_key);

    if($validator->bsp_validateUserCredentials2()){

      $smsApi = new BSP_SMSAPI($email,$auth_key); 
      $allMasks=$smsApi->bsp_checkSemiCredentials();
        if($allMasks != "false"){
          $response['success']=true;
          $response['message']=$allMasks;
          echo wp_json_encode($response);
          wp_die();
        }
        // $database=new BSP_DB;
        // $database->bsp_updateRecord($email,$auth_key,$mask);
    }

  $response=array();
  $response['success']=false;
  $response['message']="These Credentials Does Not Match With Our Records";
  echo wp_json_encode($response);
  wp_die();

}

register_activation_hook(__FILE__,'bsp_activateFunction');
register_deactivation_hook( __FILE__, 'bsp_deactivationFunction' );
//ajax calls
$BSP_AjaxHandler = new BSP_AjaxHandler();

add_action( 'wp_ajax_saveCustomMessages', 'bsp_saveCustomMessages' );
add_action( 'wp_ajax_userDetails', 'bsp_userDetails' );


add_action( 'wp_ajax_getUserMasks', 'bsp_getUserMasks' );
add_action( 'wp_ajax_saveCustomNumbers', array($BSP_AjaxHandler, 'bsp_customNumbers') );

add_action( 'wp_ajax_bsp_customerVerificationForm',array($BSP_AjaxHandler, 'bsp_customerVerificationForm'));

add_action( 'wp_ajax_bsp_getMassMarketingData',array($BSP_AjaxHandler, 'bsp_getMassMarketingData'));
add_action( 'wp_ajax_bsp_sendMassMarketingMessage',array($BSP_AjaxHandler, 'bsp_sendMassMarketingMessage'));

function bsp_script_enqueuer() {
  // Register the JS file with a unique handle, file location, and an array of dependencies
   wp_register_script( "bspPlugin", plugin_dir_url(__FILE__).'js/js.js', array('jquery') );

   
   // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
   wp_localize_script( 'bspPlugin', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   
   // enqueue jQuery library and the script you registered above
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'bspPlugin' );

   $settings = new BSP_Settings;
   $settings->bsp_customerVerificationModel();

}
$otp_active_check="";
if(isset($database->bsp_getSettingStatus('customer_verification_module')[0]))
{
	$otp_active_check=$database->bsp_getSettingStatus('customer_verification_module')[0];
	if($otp_active_check->active) { add_action( 'init', 'bsp_script_enqueuer' ); }
}
add_action('admin_menu', 'bsp_registerMenu');
add_action('woocommerce_order_status_changed', 'bsp_order_status_changed_woocommerce', 10, 4);
add_action( 'admin_notices', 'bsp_fx_admin_notice_example_notice' );

// Register new status
function bsp_register_verfication_pending_status() {
    register_post_status( 'wc-verification-due', array(
        'label'                     => 'Verfication Pending',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Verfication Pending (%s)', 'Verfication Pending (%s)' )
    ) );
}
add_action( 'init', 'bsp_register_verfication_pending_status' );

// Add to list of WC Order statuses
function bsp_add_awaiting_shipment_to_order_statuses( $order_statuses ) {
    $new_order_statuses = array();
    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {
      $new_order_statuses[ $key ] = $status;
      if ( 'wc-processing' === $key ) {
          $new_order_statuses['wc-verification-due'] = 'Verfication Pending';
      }
    }
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'bsp_add_awaiting_shipment_to_order_statuses' );

add_action( 'upgrader_process_complete', function( $upgrader_object, $options ) {

  $db = new BSP_DB();
  $db->bsp_upgradeDBTables();

}, 10, 2 );

$m=array(
    array(
        "page"=>"contact",
        "position"=>"submenu",
        "show"=>true
    ),
    array(
        "page"=>"support",
        "position"=>"submenu",
        "show"=>true
    )

);

$analytics = new bsp_analytics("Branded SMS Pakistan", "branded-sms-pakistan-adminpanel", "branded-sms-pakistan/index.php","3.0.5",$m , "branded-sms-pakistan" );

?>