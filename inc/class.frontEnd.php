<?php
class BSP_FrontEnd{

    function settingPageMain($allcategories,$allProducts,$allStatus){
?>
<style type="text/css">
/*custom font*/
@import url(https://fonts.googleapis.com/css?family=Montserrat);

/*basic reset*/
* {
    margin: 0;
    padding: 0;
}

html {
    height: 100%;
    background-color: #f1f1f1;
}

body {
    background: #f1f1f1;
    color: #444;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
    font-size: 13px;
    line-height: 1.4em;
    min-width: 600px;
    background: transparent;
}

/*form styles*/
#msform {
    text-align: center;
    position: relative;
    margin-top: 30px;
}

#msform fieldset {
    background: white;
    border: 0 none;
    border-radius: 0px;
    box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
    padding: 20px 30px;
    box-sizing: border-box;
    width: 80%;
    margin: 0 10%;

    /*stacking fieldsets above each other*/
    position: relative;
}

/*Hide all except first fieldset*/
#msform fieldset:not(:first-of-type) {
    display: none;
}

/*inputs*/
/*#msform input, #msform textarea {
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 0px;
    margin-bottom: 10px;
    width: 100%;
    box-sizing: border-box;
    font-family: montserrat;
    color: #2C3E50;
    font-size: 13px;
}
*/

#msform input, #msform textarea {
    font-family: montserrat;
    border-radius: 0px;
    padding: 15px;
}

/*
#msform input:focus, #msform textarea:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: 1px solid #ee0979;
    outline-width: 0;
    transition: All 0.5s ease-in;
    -webkit-transition: All 0.5s ease-in;
    -moz-transition: All 0.5s ease-in;
    -o-transition: All 0.5s ease-in;
}*/

/*buttons*/
#msform .action-button {
    width: 100px;
    background: #337ab7;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px;
    margin-top: 40px;
}

#msform .action-button:hover, #msform .action-button:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #337ab7;
}

#msform .action-button-previous {
    width: 100px;
    background: #337ab7;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px;
}

#msform .action-button-previous:hover, #msform .action-button-previous:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #C5C5F1;
}

/*headings*/
.fs-title {
    font-size: 18px;
    text-transform: uppercase;
    color: #2C3E50;
    margin-bottom: 10px;
    letter-spacing: 2px;
    font-weight: bold;
}

.fs-subtitle {
    font-weight: normal;
    font-size: 13px;
    color: #666;
    margin-bottom: 20px;
}

/*progressbar*/
#progressbar {
    margin-bottom: 30px;
    overflow: hidden;
    /*CSS counters to number the steps*/
    counter-reset: step;
}

#progressbar li {
    list-style-type: none;
    color: black;
    text-transform: uppercase;
    font-size: 9px;
    width: 33.33%;
    float: left;
    position: relative;
    letter-spacing: 1px;
}

#progressbar li:before {
    content: counter(step);
    counter-increment: step;
    width: 24px;
    height: 24px;
    line-height: 26px;
    display: block;
    font-size: 12px;
    color: #333;
    background: white;
    border-radius: 25px;
    margin: 0 auto 10px auto;
}

/*progressbar connectors*/
#progressbar li:after {
    content: '';
    width: 100%;
    height: 2px;
    background: white;
    position: absolute;
    left: -50%;
    top: 9px;
    z-index: -1; /*put it behind the numbers*/
}

#progressbar li:first-child:after {
    /*connector not needed before the first step*/
    content: none;
}

/*marking active/completed steps green*/
/*The number of the step and the connector before it = green*/
#progressbar li.active:before, #progressbar li.active:after {
    background: #337ab7;
    color: white;
}


/* Not relevant to this form */
.dme_link {
    margin-top: 30px;
    text-align: center;
}
.dme_link a {
    background: #FFF;
    font-weight: bold;
    color: #337ab7;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 5px 25px;
    font-size: 12px;
}

.dme_link a:hover, .dme_link a:focus {
    background: #C5C5F1;
    text-decoration: none;
}

#msgbody{
    font-size: 14px;
}

#thirdWizard h4{
    margin-bottom: 19px;
}

.wizardSetting{
    margin-left: 0px !important;
    margin-right: 0px !important;
}


.swal2-content{
    font-size: 14px;
    margin-top: 15px;
}

#h4s{
    font-size: 15px;
}
#ss{
        font-size: 11px;
}


#required{
    color: red;
    font-size: 12px;
    font-style: italic;
    
}
        </style>
<!-- MultiStep Form -->

<div class="row wizardSetting">
    <div class="col-md-10 col-md-offset-1">
        <form id="msform">
            <!-- progressbar -->
            <ul id="progressbar">
                <li class="active">Select Products/Categories To Market</li>
                <li>Message Body</li>
                <li>Summary</li>
            </ul>
            <!-- fieldsets -->
            <fieldset>
                <h2 class="fs-title">Select Items</h2>
                <h3 class="fs-subtitle">Please Select Customers By Product Categories Or Products Sold</h3>

                <?php

                // echo "<pre>";
                // print_r($allStatus);
                //print_r($allcategories);
                // echo "</pre>";
                // die();
                ?>

                <br>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <label style="width: 100%;text-align: left;margin-bottom: 15px">Please select status of orders that you want to filter out* <span id="required">(required)</span></label>
                      <select class="mySelect for" multiple="multiple" id="order-status" autocomplete="off" name="order-status" style="width: 100%">
                        <?php
                            foreach ($allStatus as $key => $value) {
                                echo '<option value="'.esc_html( $key ).'">'.esc_html( $value ).'</option>';
                            }
                        ?>

                      </select>
                    </div>
                </div>

                <br>
                <br>
                <br>
                
                <div class="row">
                    <div class="col-md-12">
                        <label>Select By Products Categories</label>
                      <select class="mySelect for" multiple="multiple" id="categories" autocomplete="off" name="categories" style="width: 100%">
                        <?php
                            foreach ($allcategories as $key => $value) {
                                echo '<option value="'.esc_html( $value['id'] ).'">'.esc_html( $value['name'] ).'</option>';
                            }
                        ?>

                      </select>
                    </div>
                </div>

                <br>
                <br>
                <br>

                <div class="row">
                    <div class="col-md-12">
                        <label>Select By Product Name</label>
                      <select class="mySelect for" id="products" name="products" multiple="multiple" autocomplete="off" style="width: 100%">
                        <?php
                            foreach ($allProducts as $key => $value) {
                                echo '<option value="'.esc_html( $value['id'] ).'">'.esc_html( $value['name'] ).'</option>';
                            }
                        ?>

                      </select>
                    </div>
                </div>

                <input type="button" name="next" class="next action-button" value="Next"/>
            </fieldset>
            <fieldset id="processData">
                <h2 class="fs-title">Set Message Body</h2>
                <h3 class="fs-subtitle">Please Select Your Message To Be Sent</h3>

                <div class="row">
                    <div class="col-md-2">
                        <label>Your Message:</label>
                    </div>
                    <div class="col-md-7">
                        <textarea class="form-control m-b-5 popshow" name="userMessage" placeholder="Type SMS" rows="7" id="userMessage"  dir=""></textarea>
                                <div id="smscounter" class="btn btn-sm btn-accent col-md-12">
                                    <i class="popshow" id="smscounterstatuss">
                                        <span class="length">323</span> Character, 
                                        <span class="messages">3</span> SMS , 
                                        <span class="remaining">136</span> Remaining characters,
                                        <span class="smstypes">Normal SMS</span>
                                    </i>
                                </div>
                    </div>
                </div>
                <script type="text/javascript">
                    jQuery("#userMessage").countSms('#smscounterstatuss', '#smscounter', 'btn btn-accent', 'btn-smscount');
                </script>

                <input type="button" name="previous" class="previous action-button-previous" value="Previous"/>
                <input type="button" name="next" class="next action-button" value="Next"/>
            </fieldset>
            <fieldset id="sendData">
                <h2 class="fs-title">Summary</h2>
                <h3 class="fs-subtitle">Please verify all things and hit submit to shoot sms</h3>
                
                <div class="row" id="thirdWizard" style="text-align: left">

                    <h4 id="h4s">Repeated Numbers: <span><b id="repeatedNumbers"></b><span id="ss"> (excluded)</span></span></h4>
                    <h4 id="h4s">Unique Numbers: <span><b id="uniqueNumbers"></b><span id="ss"> (included)</span></span></h4>
                    <h4 id="h4s">Message Body: </h4>
                    <div id="msgAbbu"> <span id="msgbody">This is the sample message that will occur here that the user has enter in the step 2</span></div>
                </div>
                <input type="button" name="previous" class="previous action-button-previous" value="Previous"/>
                <input type="submit" name="submit" class="submit action-button" value="Submit"/>
            </fieldset>
        </form>
        <!-- link to designify.me code snippets -->
   
        <!-- /.link to designify.me code snippets -->
    </div>
</div>
<!-- /.MultiStep Form -->

<script type="text/javascript">
    var placeholder = "select";
    jQuery(".mySelect").select2({
        placeholder: placeholder,
        allowClear: false,
        minimumResultsForSearch: 5
    });
</script>
        <?php
    }
    function bsp_navbar(){
    ?>
        <div class="wrap">  
        <div id="icon-themes" class="icon32"></div>  
        <?php settings_errors(); ?>  
        <?php
            if(isset($_GET[ 'tab' ]) AND $_GET[ 'tab' ]!='' ){
              $active_tab=sanitize_text_field($_GET[ 'tab' ]); $active_tab=esc_url_raw($active_tab); 
            }else{
              $active_tab = 'front_page_options';
            } 
        ?>  

        <div class="nav-tab-wrapper">  
            <a href="?page=branded-sms-pakistan-adminpanel&tab=gateway_credentials_tab" class="nav-tab  
            <?php  if(isset($_GET[ 'page' ]) AND $_GET[ 'page' ]!='' ){ $page=sanitize_text_field($_GET[ 'page' ]);
                $page=esc_url_raw($page);
                }
            if($page== "http://branded-sms-pakistan-adminpanel" && !isset($_GET[ 'tab' ])){
              echo "nav-tab-active";
            }
            echo $active_tab == 'http://gateway_credentials_tab' ? 'nav-tab-active' : ''; 

            ?>">Configuration</a>  
            <a href="?page=branded-sms-pakistan-adminpanel&tab=messages_text_tab" class="nav-tab <?php echo $active_tab == 'http://messages_text_tab' ? 'nav-tab-active' : ''; ?>">Message Text</a>
            <a href="?page=branded-sms-pakistan-adminpanel&tab=order_verification_tab" class="nav-tab <?php echo $active_tab == 'http://order_verification_tab' ? 'nav-tab-active' : ''; ?>">Order Verification</a> 
            <a href="?page=branded-sms-pakistan-marketing" class="nav-tab">Bulk Marketing Campaign</a> 
            <a href="?page=branded-sms-pakistan-adminpanel-contact" class="nav-tab <?php echo $active_tab == 'http://support_tab' ? 'nav-tab-active' : ''; ?>">Support</a>  
        </div>
    <?php 
    }

	function bsp_adminPanelIndexPage($userDetails,$availableMasks=False){

         if ( is_array( $availableMasks)) {
            $availableMasks= array_map( 'wc_clean', $availableMasks );
          } else {
            $availableMasks=is_scalar( $availableMasks ) ? sanitize_text_field( $availableMasks ) : $availableMasks;
          }
        $this->bsp_navbar();

        if($availableMasks && $userDetails[0]->mask){
            if(is_array($availableMasks) AND array_search($userDetails[0]->mask,$availableMasks) === false){
                if( !get_transient( 'fx-admin-notice-test' ) ){
                set_transient( "fx-admin-notice-test",true, 500 );
                }
            }
            else{
                if( get_transient( 'fx-admin-notice-test' ) ){
                    delete_transient( 'fx-admin-notice-test' );
                }
            }
        }
        if( get_transient( 'fx-admin-notice-test' ) ){
            ?>

            <div class="notice-error notice is-dismissible">
                <p>Your Mask Name Has Been expired. Please Visit <a href="https://brandedsmspakistan.com/">Branded SMS Pakistan</a></p>
            </div>

            <?php
        }
?>

<div>
    <div class="row rowSaeKarna" style="margin-top: 20px">
    </div>
    <div class="row rowSaeKarna">
            <div class="col-md-2">
                <label> Select Service:</label>
            </div>

            <div class="col-md-5">
                <select style="width: 100%; max-width: none">
                  <option selected value="brandedSmsPakistan">Branded SMS Pakistan - www.brandedsmspakistan.com</option>
                  
                </select>
            </div>
    </div>

    <div class="row rowSaeKarna" style="margin-top: 20px">
    	<div class="col-md-2">
    		<label>Email:</label>
    	</div>
    	<div class="col-md-5">
    		<input style="" required class="form-control" id="email" type="email" placeholder="Please Enter Your Email Address" value="<?php if(isset($userDetails[0]->email)){ echo esc_html($userDetails[0]->email); }?>" />	
    	</div>
    </div>
    
    <div   class="row rowSaeKarna" style="margin-top: 20px">
    	<div class="col-md-2">
    		<label>API Key:</label>
    	</div>
    	<div class="col-md-5">
    		<input style="" required class="form-control" id="apiKey" type="text" placeholder="Please Enter Your API Key" value="<?php if(isset($userDetails[0]->auth_key)){ echo esc_html($userDetails[0]->auth_key); }?>"/>	
    	</div>
		<p style="margin-left: 18%;width: 40%;margin-top: 40px">You can obtain API Key from Dashboard > Developers > <a href="https://secure.h3techs.com/sms/developers/key/" target="_blank" title="Click Here for API Key">API Key</a>.
		<br><b><i>OR</i></b>
		<br>You can register your account for free @ <a href="https://secure.h3techs.com/sms/account/signup" target="_blank">Branded SMS Pakistan</a></p>
		
    </div>

    <div class="row rowSaeKarna" style="margin-top: 0px">
        <div class="col-md-7" style="display:flex;justify-content: flex-end;">
            <button type="button" class="button button-primary mb-2 " id="submitCredentials2" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">Verify Account</button>
        </div>
    </div>


    <div class="row rowSaeKarna">
            <div class="col-md-2">
                <label>Select Mask Name:</label>
            </div>

            <div class="col-md-5">
                <select disabled id="availableMasks" style="width: 100%;max-width: none">
                      <?php

                      if($availableMasks){

                        if(isset($userDetails[0]->mask)){
							$mask_active=1;
                            if(array_search($userDetails[0]->mask,$availableMasks) !== false){
                                foreach ($availableMasks as $key => $value) {

                                   if($value == $userDetails[0]->mask){ $value=sanitize_text_field($value);
                                       ?>
                                        <option selected="selected" value="<?php echo esc_html($value);?>"><?php echo esc_html($value);?></option>
                                       <?php
                                   } else {
                                        $value=sanitize_text_field($value); ?> 
                                        <option value="<?php echo esc_html($value);?>"><?php echo esc_html($value);?></option> 
                                        <?php }
                                }

                            } else {
                                if( !get_transient( 'fx-admin-notice-test' ) ){
                                    set_transient( "fx-admin-notice-test",true, 500 );
                                }
                                echo "<option></option>";
                            }
                        }
                      } else {
                        echo "<option></option>";
						$mask_active=0;
                      }
                      ?>
                </select>
				<p style="margin-left: 6px;margin-top: 4px">Please verify account to obtain available mask</p>
            </div>

    </div>

</div>

    <div class="row rowSaeKarna" style="margin-top: 0px">
    	<div class="col-md-7" style="display:flex;justify-content: flex-end;">
			<button type="button" class="button button-primary mb-2 " id="submitCredentials" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">Save</button>
    	</div>
    	
    </div>

    <h3>Advance Configurations</h3>

    <div class="row rowSaeKarna" style="margin-top: 20px">
        <div class="col-md-2">
            <label>Send Message To Admin</label>
        </div>
        <div class="col-md-5">
            <input style="width:100%" required class="form-control" id="custom_numbers" type="text" placeholder="923151231015,923151231016 (Optional)" value="<?php if(isset($userDetails[1])){echo $userDetails[1];}?>" /> 
			<small>You can send all message copies to admin if you put any number above.</small>
        </div>
    </div>

    <div class="row rowSaeKarna" style="margin-top: 20px">
        <div class="col-md-7" style="display:flex;justify-content: flex-end;">
            <button type="button" class="button button-primary mb-2 " id="advanceOptions" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">Save</button>
        </div>
    </div>

<script type="text/javascript">
jQuery('#advanceOptions')
    .click(function () {
        var btn = jQuery(this)
        btn.button('loading')
        jQuery("#advanceOptions").text('Loading');

       var data={
        'action': 'saveCustomNumbers',
        'custom_numbers':jQuery("#custom_numbers").val(),
        }
        jQuery.post(ajaxurl, data, function(response) {
            var responseabc=JSON.parse(response);
            if(responseabc.success){
                Swal.fire(
                  'Success!',
                  responseabc.message,
                  'success'
                );
            }
            else{
                Swal.fire({
                  type: 'error',
                  title: 'Invalid Credentials',
                  text: responseabc.message
                });
            }
            jQuery("#advanceOptions").text('Save');
        });  
});

jQuery('#submitCredentials')
    .click(function () {
       
    	var btn = jQuery(this)
        btn.button('loading')
        jQuery("#submitCredentials").text('Loading');

        var data={
		'action': 'userDetails',
		'email':jQuery("#email").val(),
		'auth_key':jQuery("#apiKey").val(),
        'mask':jQuery("#availableMasks").val(),
		}
		jQuery.post(ajaxurl, data, function(response) {
			var responseabc=JSON.parse(response);
			if(responseabc.success){
				Swal.fire(
				  'Success!',
				  responseabc.message,
				  'success'
				);
			} else {
				Swal.fire({
				  type: 'error',
				  title: 'Invalid Credentials',
				  text: responseabc.message
				});

			}
			jQuery("#submitCredentials").text('Save');
		});	 
    });

jQuery('#submitCredentials2')
    .click(function () { 
       
        var btn = jQuery(this)
        btn.button('loading')
        jQuery("#submitCredentials2").text('Loading');

       var data={
        'action': 'getUserMasks',
        'email':jQuery("#email").val(),
        'auth_key':jQuery("#apiKey").val(),
        }
        jQuery.post(ajaxurl, data, function(response) {
            var responseabc=JSON.parse(response);

            if(responseabc.success){
                Swal.fire(
                  'Please Select Masking',
                  'account verified, please select masking to activate message service',
                  'success'
                );
                jQuery('#availableMasks').empty();
                for (var i = responseabc.message.length - 1; i >= 0; i--) {
                    console.log(responseabc.message[i]);
                    jQuery('#availableMasks').append(`<option value="`+responseabc.message[i]+`">`+responseabc.message[i]+`</option>`); 
                }
                 jQuery('#availableMasks').removeAttr("disabled");
                console.log(responseabc.message);
            }
            else{
                Swal.fire({
                  type: 'error',
                  title: 'Invalid Credentials',
                  text: responseabc.message
                });
            }
            jQuery("#submitCredentials2").text('Verify');
        });  
    });

</script>

<?php
	}
public function bsp_settingsPage($data,$otpModule){
$info=array();
foreach ($data as $key => $value) {

 $info[$value->state_name]=$value->message;    
}

?>

    <div class="row rowSaeKarna">
        <div class="containerBody" style="background-color: #f1f1f1; padding:25px 25px 25px 25px">
        <form id="customMessagesForm">

            <div class="row rowSaeKarna">
                <div class="col-md-2">
                    <label>Order on-hold message:</label>
                </div>
                <div class="col-md-5">
                   <textarea id="on-hold"  name="on-hold" cols="49" rows="5"><?php if(isset($info["on-hold"])){echo $info["on-hold"];} ?></textarea>
                </div>    
            </div>

            <div class="row rowSaeKarna">
                <div class="col-md-2">
                    <label>Order processing message:</label>
                </div>
                <div class="col-md-5">
                   <textarea id="processing" placeholder="Thank you for shopping with us! Your order No. %ORDERID% is now: Processing." name="processing" cols="49" rows="5"><?php if(isset($info["processing"])){echo $info["processing"];} ?></textarea>
                </div>    
            </div>


            <div class="row rowSaeKarna">
                <div class="col-md-2">
                    <label>Order completed message:</label>
                </div>
                <div class="col-md-5">
                   <textarea id="completed" placeholder="Thank you for shopping with us! Your order No. %ORDERID% is now: Completed." name="completed" cols="49" rows="5"><?php if(isset($info["completed"])){echo $info["completed"];} ?></textarea>
                </div>    
            </div>

            <div class="row rowSaeKarna">
                <div class="col-md-2">
                    <label>Order placed message w/o otp:  </label>
                </div>
                <div class="col-md-5">
                   <textarea <?php if($otpModule){ echo 'disabled'; }?> id="on-hold" placeholder="Your order No. %ORDERID% is received. Thank you for shopping with us!" name="order_placed" cols="49" rows="5"><?php if(isset($info["order_placed"])){echo $info["order_placed"];} ?></textarea>
                </div>    
            </div>


            <div class="row rowSaeKarna">
                <div class="col-md-2">
                    <label>Messages To Be Sent To Other Numbers</label>
                </div>
                <div class="col-md-5">
                   <textarea id="custom_numbers" name="custom_numbers" cols="49" rows="5" placeholder="eg: 923151231016"><?php if(isset($info["custom_numbers"])){echo $info["custom_numbers"];} ?></textarea>
                </div>
            </div>


            <div class="row rowSaeKarna">
                <div class="col-md-2">
                    <span style="font-size: 13px"><b>Variables You Can Use In Your Messages:</b></span>
                </div>
                <div class="col-md-5">
                    <span><b>%NAME% %EMAIL% %ORDERID% %PHONE%</b></span>
                </div>
            </div>


            <div class="row rowSaeKarna">
                <div class="col-md-7" style="display:flex;justify-content: flex-end;">
                    <button type="button" class="button button-primary mb-2 " id="submitCustomMessage" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">Save</button>
                </div>
            </div>

        </form>

        </div>
        
    </div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.js-example-basic-multiple').select2();
    });

    jQuery('#submitCustomMessage')
        .click(function () {
           
            var btn = jQuery(this)
            btn.button('loading')
            
            jQuery.post(ajaxurl, jQuery("form[action='options.php']").serialize()+"&action=saveCustomMessages", function(response) {
                var responseabc=JSON.parse(response);
                 //console.log(responseabc.status);return false;
                if(responseabc.status){
                     Swal.fire({
                      type: 'error',
                      title: 'Error',
                      text: responseabc.message
                    });
                }
                if(responseabc.success){
                    Swal.fire(
                      'Success!',
                      responseabc.message,
                      'success'
                    );
                }
                else{

                    Swal.fire({
                      type: 'error',
                      title: 'Error',
                      text: responseabc.message
                    });
            }
                btn.button('reset');
            });  
        });
</script>

<?php
}
function bsp_customerVerificationTab($data){ 
    if(isset($data) AND $data!=''){ $data=array_map( 'wc_clean', $data );
    }
    $this->bsp_navbar();
?>
<form id="submitCustomerVerificationForm">
<div style="margin-top: 25px"></div>
<div class="row rowSaeKarna">
    <div class="col-md-4">
        <label ><b>Activate Customer / Order Verification System</b>
            <br>
        <small style="font-weight: normal">Enable to send a random one time pin to customer in order to verify mobile number.</small>
    </label>
    </div>
    <div class="col-md-3">
        <label class="switch">
          <input id="customerVerificationModelCheckBox" name="customerVerificationModule"  type="checkbox" 
          <?php
          if($data['customerVerificationModule']->active){
            echo "checked";
          }
          ?>
          >
          <span class="slider round"></span>
        </label>        
    </div>
</div>

<div id="CustomerVerificationModelDiv" style="<?php 
if(!$data['customerVerificationModule']->active){
            echo "display: none";
          }
?>">

    <div class="row rowSaeKarna">
        <div class="col-md-4">
            <label><b>Please Enter Your Opt Text</b></label>
        </div>
        <div class="col-md-5">
            <textarea id="opt_text" placeholder="" name="opt_text" cols="49" rows="5"><?php
                if(isset($data['opt_text']->message)){
                    echo esc_html($data['opt_text']->message);
                }
             ?></textarea>

             <p>Place <b>%OTP%</b>  in your message for OTP code</p>
        </div> 
    </div>

    <div class="row rowSaeKarna">
        <div class="col-md-4">
            <label class="myLabel"><b>Enable URL Authentication </b><br>

<small style="
    font-weight: normal;
">Enable to send URL via SMS for quick verification.<br>
    sample: https://domain.com/thankyou-for-verifying-your-otp/?otp=1234</small>
</label>
        </div>
        <div class="col-md-3">
            <label class="switch">
              <input type="checkbox" name="urlAuthentication" id="authenticationCheckBox" <?php if($data['urlAuthentication']->active){ echo "checked"; } ?>>
              <span class="slider round"></span>
            </label>        
        </div>
    </div>

    <div class="urlAuthenticationTab" style="<?php if(!$data['urlAuthentication']->active){ echo "display: none"; } ?>">

        <div class="row rowSaeKarna">
           
            <div class="col-md-4">
            </div>
            <?php
                if($data['thankyou_page']->active != 0){ ?>
                    <div class="col-md-3">
                        <label class="myLabel"><b>A Page Has Been Created For Url Verification</b> </label>
                        <p><a href="<?php echo esc_url( get_page_link( $data['thankyou_page']->active ) );?>">Thankyou Page</a></p>
                        
                    </div>
               <?php }
            ?>
        </div>

        <div class="row rowSaeKarna">
            <div class="col-md-4">
                <label ><b>Generate Bit.ly URL</b>

            <br>
        <small style="font-weight: normal">Enable to generate short link for OTP URL authentication</small>
    </label>
            </div>
            <div class="col-md-3">
                <label class="switch">
                  <input type="checkbox" name="bitlyUrlCheckBox" id="bitlyUrlCheckBox"

                  <?php if($data['bitlyUrlCheckBox']->active){ echo "checked"; } ?>>
                  <span class="slider round"></span>
                </label>        
            </div>
        </div>
        
        <div id="bitlyUrlTab" style="<?php if($data['bitlyUrlCheckBox']->active == 0){ echo "display:none"; } ?> ">
            <div class="col-md-4">
                
            </div>
            <div class="col-md-3">

                <div class="row rowSaeKarna">
                    <div class="col-md-12">
                        <p>Please create a free access token from bit.ly website by <a href="https://app.bitly.com/Bj9bbb3GrBb/bitlinks/?actions=accountMain&actions=profile&actions=accessToken" target="_blank">clicking here</a> </p>    
                    </div>
                </div>
                <div class="row rowSaeKarna">
                    <div class="col-md-6">
                        <label>Access Token:</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="accessToken" id="accessToken" value="<?php 
                        if( isset($data['bitly_url_auth']->message) AND $data['bitly_url_auth']->message!='' ){
                           $bsp_btly_msg= sanitize_text_field($data['bitly_url_auth']->message);
                           echo esc_html($bsp_btly_msg);
                        } 
                        ?>">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="row rowSaeKarna">
    <div class="col-md-7" style="display:flex;justify-content: flex-end;">
        <button type="button" class="button button-primary mb-2 " id="submitCustomerVerificationBtn">Save</button>
    </div>
</div>

</form>
<script type="text/javascript">
    jQuery('#submitCustomerVerificationBtn')
        .click(function () {
            var btn = jQuery(this)
            btn.button('loading')
            jQuery.post(ajaxurl, jQuery("form[action='options.php']").serialize()+"&action=bsp_customerVerificationForm", function(response) {
                var responseabc=JSON.parse(response);
                if(responseabc.success){
                    Swal.fire(
                      'Success!',
                      responseabc.message,
                      'success'
                    );
                }
                else{
                    Swal.fire({
                      type: 'error',
                      title: 'Error',
                      text: responseabc.message
                    });
                }
                btn.button('reset');
            });  
        });
        jQuery("#authenticationCheckBox").change(function() {
            if(this.checked) {
                //Do stuff
                jQuery(".urlAuthenticationTab").fadeIn();
            }
            else{
                jQuery(".urlAuthenticationTab").fadeOut();
            }
        });
        jQuery("#customerVerificationModelCheckBox").change(function() {
            if(this.checked) {
                //Do stuff
               jQuery("#CustomerVerificationModelDiv").fadeIn();
            }
            else{
                jQuery("#CustomerVerificationModelDiv").fadeOut();
            }
        });
        jQuery("#bitlyUrlCheckBox").change(function() {
            if(this.checked) {
                //Do stuff
               jQuery("#bitlyUrlTab").fadeIn();
            }
            else{
                jQuery("#bitlyUrlTab").fadeOut();
            }
        });
</script>
<?php
}
function bsp_otpVerificationHtml($order_id)
{ 
    $order_id=intval($order_id);
$html='

<p>An OPT has been sent on your phone please verify</p>
<input type="text" name="optCode" id="optCode">
<p id="errorMessage" style="display: none"></p>
<input type="text" style="display:none" name="orderID" value="'.$order_id.'" id="orderID">

<input class="wpcf7-form-control wpcf7-submit" type="submit" value="Verify" onclick="verifyOtp()">';

return $html;
}

function bsp_settingsPageNewhtml($data,$data2,$customerVerificationModuleIsOn,$firstPageData){
?>
<ul class="nav nav-tabs">
    <li ><a data-toggle="tab" href="#mainPage" class="nav-link active">Gateway Credentials</a></li>
    <li ><a data-toggle="tab" href="#customerVerification" class="nav-link ">Order Verification</a></li>
    <li class=""><a data-toggle="tab" href="#messagesPanel" class="nav-link ">Messages Text</a></li>
    <li class=""><a data-toggle="tab" href="#supportPanel" class="nav-link ">Support</a></li>

  </ul>

  <div class="tab-content">
    <div id="mainPage" class="tab-pane fade in active ">
    <?php
        $this->bsp_adminPanelIndexPage($firstPageData);
    ?>
    </div>
    <div id="customerVerification" class="tab-pane fade in ">
<?php 
$this->bsp_customerVerificationTab($data); ?>
    </div>
    <div id="messagesPanel" class="tab-pane fade in">
        <?php

        $this->bsp_settingsPage($data2,$customerVerificationModuleIsOn);
        ?>
    </div>
    <div id="supportPanel" class="tab-pane fade in">
        <p style="margin-top: 20px">For any kind of support or to report any error please write us at <a href="mailto:info@h3techs.com">info@h3techs.com</a></p>        
    </div>
  </div>
<?php } 

function bsp_newCustomMessageUI($allStatusType,$data2){
    $this->bsp_navbar();
     // wc_clean() – Clean variables using sanitize_text_field. Arrays are cleaned recursively.
    $data2=array_map( 'wc_clean', $data2 );
    foreach ($data2 as $key => $value) { 
        $info[$value->state_name]=$value->message;    
    }
?>
    <style type="text/css">
        input[type=checkbox]:checked::before {    
            width: 16px;
        }
        input[type=checkbox]:focus{
             outline:none;
        }
        .checkbox-inline, .radio-inline{
            padding-left: 6px;
        }
    </style>
<div class="row">
    <div class="col-md-7">
        <div class="card" style="max-width: 100%">
          <div class="card-body">
            <h4><b>Woocommerce Events</b></h4>
            <div id="formForCustomMessages">
                <div style="margin-top: 20px">
                    <?php 
                        // wc_clean() – Clean variables using sanitize_text_field. Arrays are cleaned recursively.
                        $allStatusType=array_map( 'wc_clean', $allStatusType );
                         foreach ($allStatusType as $key => $value): ?>
                        <?php 
                            // echo "<pre>";
                            // print_r($info);
                            // echo "</pre>";
                        ?>
                        <div class="row" style="margin-top: 2px">
                            <div class="col-md-12" id="CustomMessages">
                                <input 
                                    id="<?php echo esc_html($key) ?>12" 
                                    type="checkbox" 
                                    data-text-name="<?php echo esc_html($key) ?>" 
                                    value="" 
                                    style="margin-top: 0px;width: 15px;height: 13px;"
                                    <?php
                                    if(isset($info[$key])){
                                        echo "checked";
                                    }
                                    ?>
                                />
                                <label 
                                    for="<?php echo esc_html($key) ?>12" 
                                    class="checkbox-inline">Send SMS when order <b><?php echo esc_html( $key ); ?></b>
                                </label>
                                <div id="<?php echo esc_html($key) ?>" style="margin-top: 10px; <?php if(!isset($info[$key])){ echo "display: none"; } ?>">
                                    <textarea style="width: 100%;height: 100px;"><?php if(isset($info[$key])) { echo esc_html($info[$key]); } ?></textarea>    
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="row rowSaeKarna">
                <div class="col-md-12" style="display:flex;justify-content: flex-end;">
                    <button type="button" class="button button-primary mb-2 " id="submitCustomMessage" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">Save</button>
                </div>
            </div>

<script type="text/javascript">
    jQuery('#submitCustomMessage')
        .click(function () {
            var data = new Object();
            jQuery('#CustomMessages input:checked').each(function() {
                if(jQuery("#"+jQuery(this).attr("data-text-name")+" textarea").val() != ""){
                    data[jQuery(this).attr("data-text-name")]=jQuery("#"+jQuery(this).attr("data-text-name")+" textarea").val();
                }
            });

            data['action']="saveCustomMessages";
            console.log(data);
            var btn = jQuery(this)
            btn.button('loading')
            
            jQuery.post(ajaxurl, data, function(response) {
                var responseabc=JSON.parse(response);

                if(responseabc.success){
                    Swal.fire(
                      'Success!',
                      responseabc.message,
                      'success'
                    );
                }
                else{
                    // location.reload();
                    //jQuery("#formForCustomMessages").load(location.href + " #formForCustomMessages");
                    Swal.fire({
                      type: 'error',
                      title: 'Error',
                      text: responseabc.message
                    });
            
        
                }
                btn.button('reset');
            });  
        });
</script>

          </div>
        </div>
        
    </div>

    <div class="col-md-5">

        <div class="card" style="max-width: 100%">
            <div class="card-body">

                <p><b>Variables You Can Use In Your Messages:</b></p>
                <hr>
                <p><b>%NAME%</b> </p>

                <p>This will be replaced by the name of the customer</p>
                <hr>
                <p><b>%EMAIL%</b></p>
                <p>This will be replaced by the Email Address of the customer</p>
                <hr>
                <p><b>%ORDERID%</b></p>
                <p>This will be replaced by the Order Number of the customer</p>
                <hr>
                <p><b>%PHONE%</b></p>
                <p>This will be replaced by the Phone Number of the customer</p>

                <hr>
                <p><b>%CURRENCY%</b></p>
                <p>This will be replaced by the Currency of Order</p>

                <hr>
                <p><b>%AMOUNT%</b></p>
                <p>This will be replaced by the Total Amount of Order</p>

                <hr>
                <p><b>%ADDRESS%</b></p>
                <p>This will be replaced by the Address of the customer</p>

                <hr>
                <p><b>%CUSTOMER_NOTE%</b></p>
                <p>This will be replaced by the Customers Notes of the order</p>

                <hr>
                <p><b>%PRODUCT%</b></p>
                <p>This will be replaced by the Product name and quantity available in Order</p>

                <?php $provider_name = ""; $provider_available = 0; 
                if(is_plugin_active('trax-plugin-wordpress/trax-booking.php')) { 
                    $provider_name.="Trax Logistics | ";
                    $provider_available = 1;
                }
				if(is_plugin_active('postex/postex-woo.php.php')) { 
                    $provider_name.="PostEx | ";
                    $provider_available = 1;
                }
                if(is_plugin_active('swyft-logistics/index.php')) { 
                    $provider_name.="Swyft Logistics | ";
                    $provider_available = 1;
                }
                if(is_plugin_active('mnp-courier-1.5.7/mnp-courier.php')) { 
                    $provider_name.="M&P Courier | ";
                    $provider_available = 1;
                }
                if(is_plugin_active('leopards-courier/leopards-courier.php')) { 
                    $provider_name.="Leopards Courier";
                    $provider_available = 1;
                }
                if($provider_available==1) {
                ?>
                <hr>
                <p><b><?php echo $provider_name; ?></b></p>
                <hr>
                <p><b>%TRACKINGPROVIDER%</b></p>
                <p>This will be replaced by the shipment provider company name</p>

                <hr>
                <p><b>%TRACKINGNUMBER%</b></p>
                <p>This will be replaced by the tracking number provide by the user</p>

                <hr>
                <p><b>%TRACKINGPROVIDERLINK%</b></p>
                <p>This will be replaced by the tracking url of the tracking provider</p>
                <?php } ?>


                <?php
                    if(class_exists('wfsxc_Tracking_Addon_MetaBox')){
                ?>

                <hr>
                <p><b>%TRACKINGPROVIDER%</b></p>
                <p>This will be replaced by the shipment provider company name</p>

                <hr>
                <p><b>%TRACKINGNUMBER%</b></p>
                <p>This will be replaced by the tracking number provide by the user</p>

                <hr>
                <p><b>%SHIPMENTNOTE%</b></p>
                <p>This will be replaced by the notes that you have written with the order</p>

                <hr>
                <p><b>%TRACKINGPROVIDERLINK%</b></p>
                <p>This will be replaced by the tracking url of the tracking provider</p>

                
                <?php
                    }
                    else{
                ?>
                 <hr>
                 <p>Please install  <a href="https://wordpress.org/plugins/smart-shipment-tracking">Shipment Tracking Plugin</a> to automated shipment tracking</p>

                <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<script type = "text/javascript">
    jQuery('input[type="checkbox"]').click(function(){
        if(jQuery(this).prop("checked") == true){
                jQuery("#"+jQuery(this).attr("data-text-name")).fadeIn( "fast", function() {
                    // Animation complete.
                });                          
        }
        else if(jQuery(this).prop("checked") == false){        
                jQuery("#"+jQuery(this).attr("data-text-name")).fadeOut( "fast", function() {
                    // Animation complete.
                });
        }
    });
</script>
<?php
}
}