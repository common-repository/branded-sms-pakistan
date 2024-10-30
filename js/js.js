function verifyOtp(){
	optCode = jQuery('#otp').val();
      
      jQuery('#bsp_verify_btn').text("Processing");
      jQuery('#bsp_verify_btn').prop( "disabled", true );

      jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {'action': "bsp_optVerification", 'optCode' : optCode},
         success: function(response) {
         	console.log(response);
            console.log(response.success);
            if(response.success) {
            	jQuery("#errorMessage").hide();
            	jQuery("#errorMessage").html(response.message);
              	jQuery("#errorMessage").fadeIn(response.message);
              	jQuery("#errorMessage").css( "color", "green");
              	jQuery('#bsp_verify_btn').text("Verify");
              	jQuery('#bsp_verify_btn').prop( "disabled", false );
               	// self.location ="http://www."+window.location.hostname;
              	self.location =response.url;
            }
            else {
            	jQuery("#errorMessage").hide();
              	jQuery("#errorMessage").html(response.message);
               	jQuery("#errorMessage").fadeIn(response.message);
               	jQuery("#errorMessage").css( "color", "red");
               	jQuery('#bsp_verify_btn').text("Verify");
              	jQuery('#bsp_verify_btn').prop( "disabled", false );
            }
         }
      });
}


jQuery( document ).ready(function() {
    jQuery( "#bsp_verify_btn" ).click(function() {
      verifyOtp();
   });
});


var url_string = window.location.href; //window.location.href
var url = new URL(url_string);
var c = url.searchParams.get("otp");
if(c){
   optCode = c;
      
      jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {action: "bsp_optVerification", optCode : optCode},
         success: function(response) {
            
            if(response.success) {
              //console.log(response);
              var message = response.message;
              var url = response.url;
              alert(message);
              self.location = url;
            } else {
              alert("Invalid OTP");

            }
         }
      });
}else{
  
}


var url_string = window.location.href; //window.location.href
var url = new URL(url_string);
var bsporderid = url.searchParams.get("bsporderid");
if(bsporderid){
   orderId = bsporderid;
setInterval(function(){ // interval function
      jQuery.ajax({
         type : "post",
         dataType : "json",
         url : myAjax.ajaxurl,
         data : {action: "bsp_CheckoptVerification", orderId: orderId },
         success: function(response) {
            //console.log(response);
            if(response.success) {
              var url = response.url;
              self.location = url;
            }
         }
      });
}, 3000);

}else{
  
}

