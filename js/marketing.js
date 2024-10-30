//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

function nextWizard(current_fs,next_fs){ 
	 if(animating) return false;
     animating = true;
    

    //activate next step on progressbar using the index of next_fs
    jQuery("#progressbar li").eq(jQuery("fieldset").index(next_fs)).addClass("active");
    
    //show the next fieldset
    next_fs.show(); 
    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, { 
        step: function(now, mx) {
            //as the opacity of current_fs reduces to 0 - stored in "now"
            //1. scale current_fs down to 80%
            scale = 1 - (1 - now) * 0.2;
            //2. bring next_fs from the right(50%)
            left = (now * 50)+"%";
            //3. increase opacity of next_fs to 1 as it moves in
            opacity = 1 - now;
            current_fs.css({
        'transform': 'scale('+scale+')',
        'position': 'absolute'
      });
            next_fs.css({'left': left, 'opacity': opacity});
        }, 
        duration: 800, 
        complete: function(){
            current_fs.hide();
            animating = false;
        }, 
        //this comes from the custom easing plugin
        easing: 'easeInOutBack'
    });

}

jQuery(".next").click(function(){ 
	current_fs = jQuery(this).parent();
    next_fs = jQuery(this).parent().next();
    // console.log(current_fs.attr('id'));
    
    // return false;	
    if(current_fs.attr('id') == "processData"){ 

    	acquireDetails( jQuery("#categories").val() , jQuery("#products").val(),current_fs,next_fs,jQuery("#order-status").val());

    }

    else{ 
    	nextWizard(current_fs,next_fs);
    }
    
    
});


function sendMassMessage(){


	jQuery(".action-button").val("Processing");
	jQuery(".action-button").css("background","#ccc");
	jQuery(".action-button-previous").css("background","#ccc");
	jQuery(".action-button").css("box-shadow","0 0 0 2px white, 0 0 0 3px #ccc");

	var data={
		'action': obj.bsp_sendMassMarketingMessage,
		'numbers':localStorage.getItem('numbers'),
		'message':localStorage.getItem('message')
		}
		// localStorage.removeItem('numbers');
		// localStorage.removeItem('message');

		jQuery.post(ajaxurl, data, function(response) {
			var responseabc=JSON.parse(response);
			
			jQuery(".action-button").val("Next");
			jQuery(".action-button").css("background","#337ab7");
			jQuery(".action-button").css("box-shadow","none");

			jQuery(".action-button-previous").css("background","#337ab7");


			if(responseabc.success){


				Swal.fire({
			            title: 'Success!',
			            text: responseabc.message,
			            type: "success"
			        }).then((result) => {
			        	location.reload();

					});

				// Swal.fire(
				//   'Success!',
				//   responseabc.message,
				//   'success'
				// );



				
			} else {
				// 
				

				if(responseabc.heading){

					Swal.fire({
					  type: 'error',
					  title: responseabc.heading,
					  text: responseabc.message,
					  html:responseabc.message,
					  customClass: 'swal-wide',
					});

				}

				else{

					Swal.fire({
					  type: 'error',
					  title: 'Something Is Wrong',
					  text: responseabc.message,
					  html:responseabc.message,
					  customClass: 'swal-wide',
					});

				}

			}
			jQuery("#submitCredentials").text('Save');
		});	 



}

jQuery(".previous").click(function(){
    if(animating) return false;
    animating = true;
    
    current_fs = jQuery(this).parent();
    previous_fs = jQuery(this).parent().prev();
    
    //de-activate current step on progressbar
    jQuery("#progressbar li").eq(jQuery("fieldset").index(current_fs)).removeClass("active");
    
    //show the previous fieldset
    previous_fs.show(); 
    //hide the current fieldset with style
    current_fs.animate({opacity: 0}, {
        step: function(now, mx) {
            //as the opacity of current_fs reduces to 0 - stored in "now"
            //1. scale previous_fs from 80% to 100%
            scale = 0.8 + (1 - now) * 0.2;
            //2. take current_fs to the right(50%) - from 0%
            left = ((1-now) * 50)+"%";
            //3. increase opacity of previous_fs to 1 as it moves in
            opacity = 1 - now;
            current_fs.css({'left': left});
            previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
        }, 
        duration: 800, 
        complete: function(){
            current_fs.hide();
            animating = false;
        }, 
        //this comes from the custom easing plugin
        easing: 'easeInOutBack'
    });
});

jQuery(".submit").click(function(e){
	e.preventDefault();
	sendMassMessage();
    return false;
})



function acquireDetails(categories,products,current_fs,next_fs,orderStatus){

	if(typeof premium == 'undefined'){
		premium=false;
	}


	jQuery(".action-button").val("Loading");
	jQuery(".action-button").css("background","#ccc");
	jQuery(".action-button-previous").css("background","#ccc");
	jQuery(".action-button").css("box-shadow","0 0 0 2px white, 0 0 0 3px #ccc");

	var data={
		'action': obj.bsp_getMassMarketingData,
		'categories':categories,
		'products':products,
		'order-status':orderStatus
		}
		jQuery.post(ajaxurl, data, function(response) {
			var responseabc=JSON.parse(response);
			
			jQuery(".action-button").val("Next");
			jQuery(".action-button").css("background","#337ab7");
			jQuery(".action-button").css("box-shadow","none");

			jQuery(".action-button-previous").css("background","#337ab7");


			if(responseabc.success){
				// Swal.fire(
				//   'Success!',
				//   responseabc.message,
				//   'success'
				// );

				console.log(responseabc);
				jQuery("#repeatedNumbers").text(responseabc.duplicates);
				localStorage.setItem('numbers', responseabc.numbers );
				localStorage.setItem('message', jQuery("#userMessage").val())

				jQuery("#uniqueNumbers").text(responseabc.unique);
				jQuery("#msgbody").html(jQuery("#userMessage").val().replace(/\n/g, "<br />"));




				nextWizard(current_fs,next_fs);
				
			} else {

				Swal.fire({
				  type: 'error',
				  title: 'Invalid Input Found',
				  text: responseabc.message
				});

			}
			jQuery("#submitCredentials").text('Save');
		});	 

}



// added 10-08-2021 from here


(function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define([ "jquery" ], factory );
	} else {

		// Browser globals
		factory( jQuery );
	}
} (function( $ ) {

$.ui = $.ui || {};

/******************************************************************************/
/*********************************** EASING ***********************************/
/******************************************************************************/

	( function() {

	// Based on easing equations from Robert Penner (http://www.robertpenner.com/easing)

	var baseEasings = {};


		$.extend( baseEasings, {
			Sine: function( p ) {
				return 1 - Math.cos( p * Math.PI / 2 );
			},
			Circ: function( p ) {
				return 1 - Math.sqrt( 1 - p * p );
			},
			Elastic: function( p ) {
				return p === 0 || p === 1 ? p :
					-Math.pow( 2, 8 * ( p - 1 ) ) * Math.sin( ( ( p - 1 ) * 80 - 7.5 ) * Math.PI / 15 );
			},
			Back: function( p ) {
				return p * p * ( 3 * p - 2 );
			},
			Bounce: function( p ) {
				var pow2,
					bounce = 4;

				while ( p < ( ( pow2 = Math.pow( 2, --bounce ) ) - 1 ) / 11 ) {}
				return 1 / Math.pow( 4, 3 - bounce ) - 7.5625 * Math.pow( ( pow2 * 3 - 2 ) / 22 - p, 2 );
			}
		} );

		$.each( baseEasings, function( name, easeIn ) {
			$.easing[ "easeIn" + name ] = easeIn;
			$.easing[ "easeOut" + name ] = function( p ) {
				return 1 - easeIn( 1 - p );
			};
			$.easing[ "easeInOut" + name ] = function( p ) {
				return p < 0.5 ?
					easeIn( p * 2 ) / 2 :
					1 - easeIn( p * -2 + 2 ) / 2;
			};
		} );

	} )();

}));