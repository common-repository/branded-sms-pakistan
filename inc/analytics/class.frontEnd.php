<?php
if (class_exists('bsp_views')) {
    return;
}

class bsp_views {
	private $mainObj;
	function __construct($mainObj){
		$this->mainObj=$mainObj;
	}

	function bsp_users_name( $user_id = "" ) {
		$user_info = $user_id ? new WP_User( $user_id ) : wp_get_current_user();
		if ( $user_info->first_name ) {
			if ( $user_info->last_name ) {
				return $user_info->first_name . ' ' . $user_info->last_name;
			}
			return $user_info->first_name;
		}
		return $user_info->display_name;
	}

	function bsp_contactView(){
		$current_user = wp_get_current_user();
		?>
		<style type="text/css">
			.subhead{
				    margin-top: 7px;
    				margin-bottom: 4px;
			}
		</style>

		<div id="contactMain">
			<section>
			<header id="header" class="card clearfix">
			<div class="product-header">
			<div class="product-icon">
			<!-- <img src="https://ps.w.org/<?php //echo $this->mainObj->get_bsp_PLUGIN_SLUG_NAME_WP(); ?>/assets/icon-256x256.png" alt=""> --> 
			<?php ?>
			<img src="<?php echo plugin_dir_url( $this->mainObj->get_bsp_PLUGIN_SLUG_NAME_WP() ).$this->mainObj->get_bsp_PLUGIN_SLUG_NAME_WP().'/img/icon-256x256.png'; ?>"  alt=''>
			</div>
			<div class="product-header-body">
			<h1 class="page-title">Have questions? We're happy to help!</h1>
			<h2 class="plugin-title"><?php _e($this->mainObj->get_bsp_PLUGIN_NAME() , $this->mainObj->get_bsp_PLUGIN_NAME()); ?></h2>
			<h3>We'll do our best to get back to you as soon as we can.</h3>
			</div>
			</div>
			</header>
			</section>
			<div class="rw-ui-section-container clearfix">

			<section>
			<div>
			<section id="widgets" class="card">
			<header><h3>Frequently Asked Questions</h3></header>
			<div id="faq">
			<ul class="clearfix">
			<li><p>All submitted data will not be saved and is used solely for the purposes of your support request. You will not be added to a mailing list, solicited without your permission, nor will your site be administered after this support case is closed.</p></li>

			</ul>
			</div>
			</section>

			<section id="contact_form" class="message embed wp-core-ui relative">
			<div>
			<fieldset>
			<input name="security" type="hidden" value="s_secure=6f84ae0ceeff69f6b04d8eed5ce84fc6&amp;s_ts=1581940909">
			<input name="install_id" type="hidden" value="">
			<label class="iconed-input name"><i class="name"></i><input  type="text" name="name" id="name" value="<?php echo esc_html( $this->bsp_users_name() )  ?>" placeholder="First and Last Name"></label>
			<label class="iconed-input"><i class="email"></i><input type="email" id="email" name="email" value="<?php echo esc_html( $current_user->user_email ); ?>" placeholder="Your Email Address"></label>

			<label class="iconed-input"><i class="premium"></i>
				<input type="text" id="premium" name="premium" value="" placeholder="Your Premium Key">
			</label>
			<label class="iconed-input module" style="display: none">

			<select id="context_plugin">
			<option value="5448" selected=""><?php _e($this->mainObj->get_bsp_PLUGIN_NAME() , $this->mainObj->get_bsp_PLUGIN_NAME()); ?></option>
			</select>
			</label>

			<input type="hidden" id="plugin_name" name="plugin_name" value="<?php echo $this->mainObj->get_bsp_PLUGIN_NAME(); ?>">
			<ul class="subjects iconed-input">
			<li><label><input type="radio" name="subject" value="Billing Issue"> Billing Issue</label></li>
			<li><label><input type="radio" name="subject" value="Feature Request"> Feature Request</label></li>
			<li><label><input type="radio" name="subject" value="Customization"> Customization</label></li>
			<li><label><input type="radio" name="subject" value="Pre Sale Question"> Pre-Sale Question</label></li>
			<li><label><input type="radio" name="subject" value="Press"> Press</label></li>
			<li><label><input type="radio" name="subject" value="Bug"> Bug</label></li>
			</ul>

			<p class="subhead">Message:</p>
			<textarea style="width:100%;height: 100px" id="message"></textarea>
			<p class="subhead">Attachments:</p>
			<input type="file" name="attachment[]" id="attachment" multiple>
			</fieldset>
			<div class="dynamic">
			<fieldset class="site" style="display: none">
			<div>
				<label class="iconed-input site"><i class="site"></i><input type="text" name="domain" value="" placeholder="Your Site Address (E.g http://my.address.com)"></label>
			</div>
			</fieldset>
			<fieldset class="message-box" style="display: none">
			<div>
			<label class="iconed-input site"><i class="category"></i><input type="text" name="summary" value="" placeholder="Summary (In 10 words or less, summarize your issue or question)"></label>
			<label id="contact_msg" class="iconed-input textarea">
			<i class="edit"></i>
			<textarea name="message" cols="44" rows="10" placeholder=""></textarea>
			</label>
			</div>
			</fieldset>
			<fieldset class="site" style="display: none">
			<div>
			<p style="margin-top: 10px;">If it's about a specific page on your site, please add the
			relevant link.</p>
			<label class="iconed-input site"><i class="site"></i><input type="text" name="link" value="" placeholder=""></label>
			</div>
			</fieldset>
			<fieldset class="expandable closed" style="display: none">
			<h4 class="title"><span>WordPress Login</span></h4>
			<div>
			<input type="hidden" name="wp_admin_url" value="">
			<label class="iconed-input admin-login"><i class="name"></i><input type="text" name="wp_admin_user" value="" placeholder="Username"></label>
			<label class="iconed-input admin-password"><i class="password"></i><input type="password" name="wp_admin_password" value="" placeholder="Password"></label>
			<p class="note">Instead of providing your primary admin account, create a new admin that can
			be disabled when the support case is closed.</p>
			</div>
			</fieldset>
			<fieldset class="expandable closed" style="display: none">
			<h4 class="title"><span>FTP Access</span></h4>
			<div>
			<label class="iconed-input ftp-host"><i class="site"></i><input type="text" name="ftp_host" value="" placeholder="FTP Host"></label>
			<label class="iconed-input ftp-login"><i class="name"></i><input type="text" name="ftp_user" value="" placeholder="FTP User"></label>
			<label class="iconed-input ftp-password"><i class="password"></i><input type="password" name="ftp_password" value="" placeholder="FTP Password"></label>
			<p class="note">Instead of providing your primary FTP account, create a new FTP user that
			can be disabled when the support case is closed.</p>
			</div>
			</fieldset>
			</div>
			<div class="message-sent">
			<p>Your message has been sent! We'll get back to you as soon as we can.</p>
			<h5>Be AWESOME and spread the word:</h5>
			</div>
			</div>
			<footer>
			<button id="submitContact" value="Send Message" class="primary large button-primary"><span>Send Message <sub>›</sub></span></button>
			<div class="social-buttons">
			<a href="https://twitter.com/intent/tweet?text=Awesome%20%23WordPress%20%23Plugin%21%20WordPress%20WooCommerce%20Sync%20for%20Google%20Sheet%20https%3A%2F%2Fwordpress.org%2Fplugins%2Fwp-woo-commerce-sync-for-g-sheet%2F" target="_blank">
			<button class="twitter"><i></i> Tweet</button>
			</a>
			<a href="https://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwp-woo-commerce-sync-for-g-sheet%2F" target="_blank">
			<button class="facebook"><i></i> Share</button>
			</a>
			<a href="mailto:?subject=Interesting%20WordPress%20Plugin&amp;body=Yo%21%20I%20think%20you%20will%20be%20interested%20to%20try%20WordPress%20WooCommerce%20Sync%20for%20Google%20Sheet%2C%20%0A%0AYou%20can%20download%20it%20from%20here%3A%0Ahttps%3A%2F%2Fwordpress.org%2Fplugins%2Fwp-woo-commerce-sync-for-g-sheet%2F">
			<button class="email"><i></i> Email to Friend</button>
			</a>
			</div>
			</footer>
			</section>
			</div>
			</section>
			</div>
			<input type="hidden" id="parent_url" value="">
	</div>

		<?php
	}

}