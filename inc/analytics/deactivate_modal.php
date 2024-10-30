<?php

function showContactModal($Triad_Mark_PLUGIN_NAME,$Triad_Mark_PLUGIN_SLUG_NAME_WP){
  $Triad_Mark_deactivate_nonce = wp_create_nonce($Triad_Mark_PLUGIN_NAME.'-deactivate-nonce');
  $prefix=str_replace(" ","-",$Triad_Mark_PLUGIN_NAME); 
  $prefix=esc_html( $prefix );

?>
<style>
    .<?php echo $prefix ?>-hidden{

      overflow: hidden;
    }
    .<?php echo $prefix ?>-popup-overlay .<?php echo $prefix ?>-internal-message{
      margin: 3px 0 3px 22px;
      display: none;
    }
    .<?php echo $prefix ?>-reason-input{
      margin: 3px 0 3px 22px;
      display: none;
    }
    .<?php echo $prefix ?>-reason-input input[type="text"]{

      width: 100%;
      display: block;
    }
  .<?php echo $prefix ?>-popup-overlay{

    background: rgba(0,0,0, .8);
    position: fixed;
    top:0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 1000;
    overflow: auto;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .<?php echo $prefix ?>-popup-overlay.<?php echo $prefix ?>-active{
    opacity: 1;
    visibility: visible;
  }
  .<?php echo $prefix ?>-serveypanel{
    width: 600px;
    background: #fff;
    margin: 0 auto 0;
    border-radius: 3px;
  }
  .<?php echo $prefix ?>-popup-header{
    background: #f1f1f1;
    padding: 20px;
    border-bottom: 1px solid #ccc;
  }
  .<?php echo $prefix ?>-popup-header h2{
    margin: 0;
    text-transform: uppercase;
  }
  .<?php echo $prefix ?>-popup-body{
      padding: 10px 20px;
  }
  .<?php echo $prefix ?>-popup-footer{
    background: #f9f3f3;
    padding: 10px 20px;
    border-top: 1px solid #ccc;
  }
  .<?php echo $prefix ?>-popup-footer:after{

    content:"";
    display: table;
    clear: both;
  }
  .action-btns{
    float: right;
  }
  .<?php echo $prefix ?>-anonymous{

    display: none;
  }
  .attention, .error-message {
    color: red;
    font-weight: 600;
    display: none;
  }
  .<?php echo $prefix ?>-spinner{
    display: none;
  }
  .<?php echo $prefix ?>-spinner img{
    margin-top: 3px;
  }
  .<?php echo $prefix ?>-pro-message{
    padding-left: 24px;
    color: red;
    font-weight: 600;
    display: none;
  }
  .<?php echo $prefix ?>-popup-header{
    background: none;
        padding: 18px 15px;
    -webkit-box-shadow: 0 0 8px rgba(0,0,0,.1);
    box-shadow: 0 0 8px rgba(0,0,0,.1);
    border: 0;
}
.<?php echo $prefix ?>-popup-body h3{
    margin-top: 0;
    margin-bottom: 30px;
        font-weight: 700;
    font-size: 15px;
    color: #495157;
    line-height: 1.4;
    text-tranform: uppercase;
}
.<?php echo $prefix ?>-reason{
    font-size: 13px;
    color: #6d7882;
    margin-bottom: 15px;
}
.<?php echo $prefix ?>-reason input[type="radio"]{
margin-right: 15px;
}
.<?php echo $prefix ?>-popup-body{
padding: 30px 30px 0;

}
.<?php echo $prefix ?>-popup-footer{
background: none;
    border: 0;
    padding: 29px 39px 39px;
}
</style>



<style type="text/css">
  .col-md-4{
        width: 33.3333333%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
  }
</style>




<div class="<?php echo $prefix ?>-popup-overlay">
  <div class="<?php echo $prefix ?>-serveypanel">
    <form action="#" method="post" id="<?php echo $prefix ?>-deactivate-form">
    <div class="<?php echo $prefix ?>-popup-header">
      <h2><?php _e('Quick feedback about ' . $Triad_Mark_PLUGIN_NAME, $Triad_Mark_PLUGIN_NAME); ?></h2>
    </div>
    <div class="<?php echo $prefix ?>-popup-body">
      <h3><?php _e('If you have a moment, please let us know why you are deactivating:', $Triad_Mark_PLUGIN_NAME); ?></h3>
      <input type="hidden" class="Triad_Mark_deactivate_nonce" name="Triad_Mark_deactivate_nonce" value="<?php echo $Triad_Mark_deactivate_nonce; ?>">
      <ul id="<?php echo $prefix ?>-reason-list">
        <li class="<?php echo $prefix ?>-reason <?php echo $prefix ?>-reason-pro" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="pro">
            </span>
            <span><?php _e(" I upgraded to " . $Triad_Mark_PLUGIN_NAME . " Pro", $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-pro-message"><?php _e('No need to deactivate this ' . $Triad_Mark_PLUGIN_NAME . ' Core version. Pro version works as an add-on with Core version.', $Triad_Mark_PLUGIN_NAME); ?></div>
        </li>
        <li class="<?php echo $prefix ?>-reason" data-input-type="" data-input-placeholder="">
          <label>

            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="1">
            </span>
            <span><?php _e('I only needed the plugin for a short period', $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
        </li>
        <li class="<?php echo $prefix ?>-reason has-input" data-input-type="textfield">
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="2">
            </span>
            <span><?php _e('I found a better plugin', $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
          <div class="<?php echo $prefix ?>-reason-input"><span class="message error-message "><?php _e('Kindly tell us the Plugin name.', $Triad_Mark_PLUGIN_NAME); ?></span><input type="text" name="<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP?>better_plugin_h3" placeholder="What's the plugin's name?"></div>
        </li>
        <li class="<?php echo $prefix ?>-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="3">
            </span>
            <span><?php _e('The plugin broke my site', $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
        </li>
        <li class="<?php echo $prefix ?>-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="4">
            </span>
            <span><?php _e('The plugin suddenly stopped working', $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
        </li>
        <li class="<?php echo $prefix ?>-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="5">
            </span>
            <span><?php _e('I no longer need the plugin', $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
        </li>
        <li class="<?php echo $prefix ?>-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="6">
            </span>
            <span><?php _e("It's a temporary deactivation. I'm just debugging an issue.", $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
        </li>
        <li class="<?php echo $prefix ?>-reason has-input" data-input-type="textfield" >
          <label>
            <span>
              <input type="radio" name="<?php echo $prefix ?>-selected-reason" value="7">
            </span>
            <span><?php _e('Other', $Triad_Mark_PLUGIN_NAME); ?></span>
          </label>
          <div class="<?php echo $prefix ?>-internal-message"></div>
          <div class="<?php echo $prefix ?>-reason-input"><span class="message error-message "><?php _e('Kindly tell us the reason so we can improve.', $Triad_Mark_PLUGIN_NAME); ?></span><input type="text" name="<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP?>other_reason_h3" placeholder="Kindly tell us the reason so we can improve."></div>
        </li>
      </ul>
    </div>
    <div class="<?php echo $prefix ?>-popup-footer">
      <label class="<?php echo $prefix ?>-anonymous"><input type="checkbox" /><?php _e('Anonymous feedback', $Triad_Mark_PLUGIN_NAME); ?></label>
        <input type="button" class="button button-secondary button-skip <?php echo $prefix ?>-popup-skip-feedback" value="<?php _e('Skip & Deactivate', $Triad_Mark_PLUGIN_NAME); ?>" >
      <div class="action-btns">
        <span class="<?php echo $prefix ?>-spinner"><img src="<?php echo admin_url('/images/spinner.gif'); ?>" alt=""></span>
        <input type="submit" class="button button-secondary button-deactivate <?php echo $prefix ?>-popup-allow-deactivate" value="<?php _e('Submit & Deactivate', $Triad_Mark_PLUGIN_NAME); ?>" disabled="disabled">
        <a href="#" class="button button-primary <?php echo $prefix ?>-popup-button-close"><?php _e('Cancel', $Triad_Mark_PLUGIN_NAME); ?></a>

      </div>
    </div>
  </form>
    </div>
  </div>


  <script>
    (function( $ ) {

      jQuery(function() {

       
        // Code to fire when the DOM is ready apna.

        jQuery(document).on('click', 'tr[data-slug="<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP ?>"] .deactivate', function(e){
          e.preventDefault();
          
          $('.<?php echo $prefix ?>-popup-overlay').addClass('<?php echo $prefix ?>-active');
          $('body').addClass('<?php echo $prefix ?>-hidden');
        });
        $(document).on('click', '.<?php echo $prefix ?>-popup-button-close', function () {
          close_popup();
        });
        $(document).on('click', ".<?php echo $prefix ?>-serveypanel,tr[data-slug='<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP ?>'] .deactivate",function(e){
            e.stopPropagation();
        });

        $(document).click(function(){
          close_popup();
        });
        $('.<?php echo $prefix ?>-reason label').on('click', function(){
          if($(this).find('input[type="radio"]').is(':checked')){
            //$('.bsp-anonymous').show();
            $(this).next().next('.<?php echo $prefix ?>-reason-input').show().end().end().parent().siblings().find('.<?php echo $prefix ?>-reason-input').hide();
          }
        });
        $('input[type="radio"][name="<?php echo $prefix ?>-selected-reason"]').on('click', function(event) {
          $(".<?php echo $prefix ?>-popup-allow-deactivate").removeAttr('disabled');
          $(".<?php echo $prefix ?>-popup-skip-feedback").removeAttr('disabled');
          $('.message.error-message').hide();
          $('.<?php echo $prefix ?>-pro-message').hide();
        });

        $('.<?php echo $prefix ?>-reason-pro label').on('click', function(){
          if($(this).find('input[type="radio"]').is(':checked')){
            $(this).next('.<?php echo $prefix ?>-pro-message').show().end().end().parent().siblings().find('.<?php echo $prefix ?>-reason-input').hide();
            $(this).next('.<?php echo $prefix ?>-pro-message').show()
            $('.<?php echo $prefix ?>-popup-allow-deactivate').attr('disabled', 'disabled');
            $('.<?php echo $prefix ?>-popup-skip-feedback').attr('disabled', 'disabled');
          }
        });
        $(document).on('submit', '#<?php echo $prefix ?>-deactivate-form', function(event) {
          event.preventDefault();

          var _reason =  $('input[type="radio"][name="<?php echo $prefix ?>-selected-reason"]:checked').val();
          var _reason_details = '';

          var deactivate_nonce = $('.Triad_Mark_deactivate_nonce').val();

          if ( _reason == 2 ) {
            _reason_details = jQuery("input[type='text'][name='<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP?>better_plugin_h3']").val();
          } else if ( _reason == 7 ) {
            _reason_details = jQuery("input[type='text'][name='<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP?>other_reason_h3']").val();
          }



          if ( ( _reason == 7 || _reason == 2 ) && _reason_details == '' ) {
            $('.message.error-message').show();
            return ;
          }
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action        : 'Triad_Mark_deactivate',
              plugin_name        : '<?php echo $Triad_Mark_PLUGIN_NAME ?>',
              reason        : _reason,
              reason_detail : _reason_details,
              security      : deactivate_nonce
            },
            beforeSend: function(){
              $(".<?php echo $prefix ?>-spinner").show();
              $(".<?php echo $prefix ?>-popup-allow-deactivate").attr("disabled", "disabled");
            }
          })
          .done(function() {
            $(".<?php echo $prefix ?>-spinner").hide();
            $(".<?php echo $prefix ?>-popup-allow-deactivate").removeAttr("disabled");
            window.location.href =  $("tr[data-slug='<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP ?>'] .deactivate a").attr('href');
          });

        });

        $('.<?php echo $prefix ?>-popup-skip-feedback').on('click', function(e){
          // e.preventDefault();
          window.location.href =  $("tr[data-slug='<?php echo $Triad_Mark_PLUGIN_SLUG_NAME_WP ?>'] .deactivate a").attr('href');
        })

        function close_popup() {
          $('.<?php echo $prefix ?>-popup-overlay').removeClass('<?php echo $prefix ?>-active');
          $('#<?php echo $prefix ?>-deactivate-form').trigger("reset");
          $(".<?php echo $prefix ?>-popup-allow-deactivate").attr('disabled', 'disabled');
          $(".<?php echo $prefix ?>-reason-input").hide();
          $('body').removeClass('<?php echo $prefix ?>-hidden');
          $('.message.error-message').hide();
          $('.<?php echo $prefix ?>-pro-message').hide();
        }
        });

        })( jQuery ); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
  </script>



<?php
}
?>