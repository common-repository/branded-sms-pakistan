jQuery( document ).ready(function() {
    
    var plugin_name=obj.pluginName;
    


    if(jQuery("#submitContact").length){


      jQuery( "#submitContact" ).click(function() {

        var attachment="none";
        var formData = new FormData();
        
        formData.append("action", 'bsp_contactAjax');
        formData.append("name", jQuery( "#name" ).val());
        formData.append("email", jQuery( "#email" ).val());
        formData.append("subject", jQuery( "input[name='subject']:checked" ).val());
        formData.append("message", jQuery('#message').val());
        formData.append("plugin_name", jQuery('#plugin_name').val());
        formData.append("premium", jQuery( "#premium" ).val());
        if(jQuery('#attachment').val() != ""){

          var ins = document.getElementById('attachment').files.length;
          for (var x = 0; x < ins; x++) {
              formData.append("userfile[]", document.getElementById('attachment').files[x]);
          }

          console.log(jQuery('#attachment').prop('files'));

          // file =jQuery('#attachment').prop('files')[0];
          // formData.append("userfile", file);
        }


        jQuery(this).prop("disabled",true);
        jQuery("#submitContact span").text("Please wait");



         jQuery.ajax({
                            url: ajaxurl,
                            type: 'post',
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                                          jQuery("#submitContact").prop("disabled",false);

                                                          if(typeof(response) == "string"){
                                                            response=JSON.parse(response);  
                                                          }

                                                          jQuery("#submitContact span").html("Submit <sub>›</sub>");
                                          

                                                          console.log(response);
                                                          if(response.success){
                                                              Swal.fire(
                                                                'Success!',
                                                                response.message,
                                                                'success'
                                                              );

                                                              jQuery('#contact_form input').val('');
                                                              jQuery('#contact_form textarea').val('');
                                                              jQuery('#contact_form input:radio').removeAttr('checked');
                                                          }
                                                          else{
                                                              Swal.fire({
                                                                type: 'error',
                                                                title: 'Invalid Input',
                                                                text: response.message
                                                              });
                                                          }
                          
                            },  
                            error: function (response) {
                             console.log('error');
                            }

                        });



      });

    }

});



