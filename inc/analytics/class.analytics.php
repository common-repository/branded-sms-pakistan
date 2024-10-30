<?php
require_once ('class.analyticsAjax.php');
require_once('class.frontEnd.php');

if (class_exists('bsp_analytics')) {
    return;
}
class bsp_analytics {
    private $bsp_PLUGIN_NAME;
    private $bsp_PLUGIN_SLUG_NAME;
    private $bsp_PLUGIN_SLUG_NAME_WP;
    private $bsp_PLUGIN_BASEFILE;
    private $bsp_PLUGIN_DIR_PATH;
    private $bsp_OPTIN;
    private $bsp_PREFIX;
    private $bsp_MENU_POSITION;


    function get_bsp_PLUGIN_NAME(){
        return $this->bsp_PLUGIN_NAME;
    }

    function get_bsp_PLUGIN_SLUG_NAME(){
        return $this->bsp_PLUGIN_SLUG_NAME;
    }

    function get_bsp_PLUGIN_SLUG_NAME_WP(){
        return $this->bsp_PLUGIN_SLUG_NAME_WP;
    }

    function get_bsp_PLUGIN_BASEFILE(){
        return $this->bsp_PLUGIN_BASEFILE;
    }

    function get_bsp_PLUGIN_DIR_PATH(){
        return $this->bsp_PLUGIN_DIR_PATH;
    }

    function get_bsp_OPTIN(){
        return $this->bsp_OPTIN;
    }

    function get_bsp_PREFIX(){
        return $this->bsp_PREFIX;
    }

    function get_bsp_MENU_POSITION(){
        return $this->bsp_MENU_POSITION;
    }

    function bsp_define_constants($pluginName, $pluginSlug, $pluginFile, $pluginSlugWp,$plugin_version,$menuPosition) {
        $this->bsp_PLUGIN_NAME = $pluginName;
        $this->bsp_PLUGIN_SLUG_NAME = $pluginSlug;
        $this->bsp_PLUGIN_SLUG_NAME_WP = $pluginSlugWp;
        $this->bsp_PLUGIN_BASEFILE = $pluginFile;
        $this->bsp_PLUGIN_DIR_PATH=plugin_dir_path(__FILE__);
        $this->bsp_OPTIN=$pluginName.'_optin';
        $this->bsp_PREFIX=str_replace(" ","-",$pluginName);

        $this->bsp_MENU_POSITION=$menuPosition;

        $this->define('bsp_FEEDBACK_SERVER', "https://secure.h3techs.com/pluginsAnalytics/feedback.php");
        $this->define('bsp_ADVERTISEMENT_SERVER', "https://secure.h3techs.com/pluginsAnalytics/advertisement.php");
        $this->define('bsp_CONTACT_SERVER', "https://secure.h3techs.com/pluginsAnalytics/contact.php");
        $this->define('bsp_PLUGIN_VERSION', $plugin_version);

        new bsp_analyticsAjax($this);
    }

    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    function bsp_setItems($links, $file) {
        if ($file == $this->bsp_PLUGIN_BASEFILE) {
            $settings_link = "";
            if (get_option($this->bsp_OPTIN) == 'no') {
                $settings_link.= sprintf(esc_html__(' %1$s Opt In %2$s ', $this->bsp_PLUGIN_SLUG_NAME), '<a class="opt-out" href="' . admin_url('admin.php?page=' . $this->bsp_PLUGIN_SLUG_NAME . '-optin') . '">', '</a>');
            } elseif (get_option($this->bsp_OPTIN) == 'yes') {
                $settings_link.= sprintf(esc_html__(' %1$s Opt Out %2$s ', $this->bsp_PLUGIN_SLUG_NAME), '<a class="opt-out" href="' . admin_url('admin.php?page=' . $this->bsp_PLUGIN_SLUG_NAME . '-optin' . '&plugin_menu=no') . '">', '</a>');
            } else {
                 $settings_link.= sprintf(esc_html__(' %1$s Opt In %2$s ', $this->bsp_PLUGIN_SLUG_NAME), '<a class="opt-out" href="' . admin_url('admin.php?page=' . $this->bsp_PLUGIN_SLUG_NAME . '-optin') . '">', '</a>');
            }
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    function __construct($pluginName, $pluginSlug, $pluginFile,$plugin_version,$menuPosition , $pluginSlugWp = false ) {
        $this->bsp_define_constants($pluginName, $pluginSlug, $pluginFile, $pluginSlugWp,$plugin_version,$menuPosition);
        $this->bsp_hooks();
    }

    function bsp_deactive_modal() {
        global $pagenow;
        if ('plugins.php' !== $pagenow) {
            return;
        }

        if (function_exists('showContactModal')) {
            showContactModal($this->bsp_PLUGIN_NAME,$this->bsp_PLUGIN_SLUG_NAME_WP);
        } else {
            include $this->bsp_PLUGIN_DIR_PATH . 'deactivate_modal.php';
            showContactModal($this->bsp_PLUGIN_NAME,$this->bsp_PLUGIN_SLUG_NAME_WP);
        }
    }

    function bsp_attach_scripts($hook) {
        wp_register_script('my-script-custom', plugins_url('/js/custom.js', __FILE__));
        wp_enqueue_script('my-script-custom');
        $translation_array = array('pluginName' => $this->bsp_PLUGIN_NAME);
        //after wp_enqueue_script
        wp_localize_script('my-script-custom', 'obj', $translation_array);
    }

    function bsp_hooks() {
        add_action('plugin_action_links', array($this, 'bsp_setItems'), 10, 2);
        add_action('admin_footer', array($this, 'bsp_deactive_modal'));
        add_filter('plugin_row_meta', array($this, 'bsp_row_meta'), 10, 2);
        add_action('admin_init', array($this, 'bsp_redirect_optin'));
        add_action('admin_init', array($this, 'bsp_optin_checker'));
        add_action('admin_enqueue_scripts', array($this, 'bsp_attach_scripts'));
        add_action('admin_menu', array($this, 'bsp_create_optin_page'));
        add_action('admin_init', array($this, 'bsp_myplugin_pages'));
        add_action('admin_menu', array($this, 'bsp_register_plugin_pages'));
        register_deactivation_hook( $this->bsp_PLUGIN_BASEFILE , array( $this, 'bsp_deactivationFunction' ) );
    }

    function shell($label){
         $shell='<span style="margin-left:4px;font-size: 19px">&rdsh;</span><span style="margin-left:5px">'.$label.'</span>';
        return $shell;
    }

    function registerMenu($label,$slug,$menuPosition,$callback=false){
        $label=sanitize_text_field($label);
        $slug=sanitize_text_field($slug);
        // comment below because showing error
        //$menuPosition=array_map( 'wc_clean', $menuPosition );
        $frontEnd= new bsp_views($this);

        if($menuPosition['position'] =='optionPage' && $menuPosition['show'] ==true){
            if($callback){
                $menu=add_options_page($label, $this->shell($label), 'manage_options', $slug,  array($frontEnd, $callback) );
            } else {
                $menu=add_options_page($label, $this->shell($label), 'manage_options', $slug );
            }
        } else if($menuPosition['position'] =='submenu' && $menuPosition['show'] ==true){ 
            if($callback){
                $menu=add_submenu_page( $this->bsp_PLUGIN_SLUG_NAME, $label, $label, 'manage_options', $slug, array($frontEnd, $callback) );
            } else {
                $menu=add_submenu_page( $this->bsp_PLUGIN_SLUG_NAME, $label, $label, 'manage_options', $slug );
            }
        }
        return $menu;
    }

    function bsp_register_plugin_pages(){
        $contact=$this->registerMenu('Contact Us',$this->bsp_PLUGIN_SLUG_NAME.'-contact',$this->bsp_MENU_POSITION[0],'bsp_contactView');
        // $contact=add_options_page(  'Contact Form', 'Contact Us', 'manage_options', $this->bsp_PLUGIN_SLUG_NAME.'-contact', array($frontEnd, 'bsp_contactView') );
        // $support=add_options_page(  'Contact Form', 'Support Forum', 'manage_options', 'https://wordpress.org/support/plugin/'.$this->bsp_PLUGIN_SLUG_NAME_WP);
        $support=$this->registerMenu('Support Forum','https://wordpress.org/support/plugin/'.$this->bsp_PLUGIN_SLUG_NAME_WP,$this->bsp_MENU_POSITION[1]);
        add_action('admin_print_styles-'. $contact,array($this, 'bsp_attachFilesToContactForm'));
    }

    function bsp_attachFilesToContactForm(){
        wp_enqueue_style( 'stylesheet_name_contact',plugins_url( '/css/custom.css', __FILE__ ));
        wp_enqueue_script( 'script-name-sweetAlert', plugins_url( '/js/sweetalert2@8.js', __FILE__ ));
        wp_enqueue_style( 'stylesheet_name_sweetAlertCss', plugins_url( '/css/sweetalert2.min.css', __FILE__ ));
    }

    function bsp_deactivationFunction(){
        if(get_option($this->bsp_OPTIN)){
            delete_option($this->bsp_OPTIN);
        }
    }

    function bsp_optin_checker() {

        if (isset($_REQUEST['page']) AND $_REQUEST['page']!='' ) { $page=sanitize_text_field($_REQUEST['page']);
        //$page=esc_html($page);
            if ($page == $this->bsp_PLUGIN_SLUG_NAME) {
                if (!get_option($this->bsp_OPTIN)) {
                    $url = admin_url('admin.php?page=' . $this->bsp_PLUGIN_SLUG_NAME . '-optin');
                    wp_redirect($url);
                }
            }
        }
    }

    function bsp_create_optin_page() {
        add_submenu_page('branded-sms-pakistan-adminpanel-optin', __('Activate', $this->bsp_PLUGIN_SLUG_NAME), __('Activate', $this->bsp_PLUGIN_SLUG_NAME), 'manage_options', $this->bsp_PLUGIN_SLUG_NAME . '-optin', array($this, 'bsp_optin_page'));
    }

    function bsp_optin_page() {
        include 'create_optin_page.php';
        showOptinPage($this->bsp_PLUGIN_SLUG_NAME,$this->bsp_PLUGIN_SLUG_NAME_WP,$this->bsp_PLUGIN_NAME,$this->bsp_PREFIX);
    }

    function bsp_get_settings_page() {
        $bsp__setting = get_option($this->bsp_PLUGIN_SLUG_NAME . '_setting', array());
        if (!is_array($bsp__setting) && empty($bsp__setting)) {
            $bsp__setting = array();
        }
        $page = array_key_exists($this->bsp_PLUGIN_SLUG_NAME . '_page', $bsp__setting) ? wc_get_post($bsp__setting[$this->bsp_PLUGIN_SLUG_NAME . '_page']) : false;
        return $page;
    }

    function bsp_myplugin_pages($value) {
        global $pagenow;
        if(isset($_REQUEST['page']) AND $_REQUEST['page']!='' ){
           $page= sanitize_text_field($_REQUEST['page']); //$page=esc_html($page);
        } else {
            $page=false;
        }
        //$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : false);
        if ($pagenow == 'admin.php' && $page == $this->bsp_PLUGIN_SLUG_NAME . '-optin') {
            $default_login_press_redirect = $this->bsp_PLUGIN_SLUG_NAME;
            if (isset($_GET['redirect-page'])) {
                $default_login_press_redirect = sanitize_text_field(wp_unslash($_GET['redirect-page']));
            }
            if (isset($_POST)) {
                if (isset($_POST[$this->bsp_PREFIX.'-submit-optout'])) {
                    update_option($this->bsp_OPTIN, 'no');
                    wp_redirect('admin.php?page=' . $default_login_press_redirect);
                } elseif (isset($_POST[$this->bsp_PREFIX.'-submit-optin'])) {
                    update_option($this->bsp_OPTIN, 'yes');
                    wp_redirect('admin.php?page=' . $default_login_press_redirect);
                }
            }

            if (isset($_GET['plugin_menu']) AND $_GET['plugin_menu']!='' ) { $plugin_menu=sanitize_text_field($_GET['plugin_menu']);
                if ($plugin_menu == 'no') {
                    update_option($this->bsp_OPTIN, 'no');
                    wp_redirect('admin.php?page=' . $default_login_press_redirect);
                } elseif ($plugin_menu == 'yes') {
                    update_option($this->bsp_OPTIN, 'yes');
                    wp_redirect('admin.php?page=' . $default_login_press_redirect);
                }
            }
        }
    }

    function bsp_check_settings_page() {
        // Retrieve the bsp_ admin page option, that was created during the activation process.
        $option = $this->bsp_get_settings_page();
        // Retrieve the status of the page, if the option is available.
        if ($option) {
            $page = wc_get_post($option);
            $status = $page->post_status;
        } else {
            $status = '';
        }
        // Check the status of the page. Let's fix it, if the page is missing or in the trash.
        if (empty($status) || 'trash' === $status) {
            //new bsp__Page_Create();
        }
    }

    function bsp_redirect_optin() {
        if (isset($_POST[$this->bsp_PREFIX.'-submit-optout'])) {
            update_option($this->bsp_OPTIN, 'no');
            $this->bsp_send_data(array('action' => 'Skip',));
        } elseif (isset($_POST[$this->bsp_PREFIX.'-submit-optin'])) {
            update_option($this->bsp_OPTIN, 'yes');
            $fields = array('action' => 'Activate','plugin_version'=>bsp_PLUGIN_VERSION);
            $this->bsp_send_data($fields);
        }
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR')) $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED')) $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR')) $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED')) $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR')) $ipaddress = getenv('REMOTE_ADDR');
        else $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/OPR/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        } elseif (preg_match('/Edge/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Trident/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }
        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array('userAgent' => $u_agent, 'name' => $bname, 'version' => $version, 'platform' => $platform, 'pattern' => $pattern);
    }

    function bsp_send_data($args) {
        $cuurent_user = wp_get_current_user();
        $browser = $this->getBrowser();
        $fields = array('email' => get_option('admin_email'), 'website' => get_site_url(), 'action' => '', 'reason' => '', 'reason_detail' => '', 'display_name' => $cuurent_user->display_name, 'blog_language' => get_bloginfo('language'), 'wordpress_version' => get_bloginfo('version'), 'php_version' => PHP_VERSION, 'plugin_name' => $this->bsp_PLUGIN_NAME, 'wordpress_timezone' => date_default_timezone_get(), 'ip_address' => $this->get_client_ip(), 'browser' => $browser['name'] . '/' . $browser['version'] . '/' . $browser['platform']);
        $args = array_merge($fields, $args);
        $response = wp_remote_post(bsp_FEEDBACK_SERVER, array('method' => 'POST', 'timeout' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => $args,));
    }

    public function bsp_row_meta($meta_fields, $file) {
        if ($file != $this->bsp_PLUGIN_BASEFILE) {
            return $meta_fields;
        }
        echo "<style>.bsp_-rate-stars { display: inline-block; color: #ffb900; position: relative; top: 3px; }.bsp_-rate-stars svg{ fill:#ffb900; } .bsp_-rate-stars svg:hover{ fill:#ffb900 } .bsp_-rate-stars svg:hover ~ svg{ fill:none; } </style>";
        $plugin_rate = "https://wordpress.org/support/plugin/" . $this->bsp_PLUGIN_SLUG_NAME_WP . "/reviews/?rate=5#new-post";
        $plugin_filter = "https://wordpress.org/support/plugin/" . $this->bsp_PLUGIN_SLUG_NAME_WP . "/reviews/?filter=5";
        $svg_xmlns = "https://www.w3.org/2000/svg";
        $svg_icon = '';
        for ($i = 0;$i < 5;$i++) {
            $svg_icon.= "<svg xmlns='" . esc_url($svg_xmlns) . "' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>";
        }
        // Set icon for thumbsup.
        $meta_fields[] = '<a href="' . esc_url($plugin_filter) . '" target="_blank"><span class="dashicons dashicons-thumbs-up"></span>' . __('Vote!', $this->bsp_PLUGIN_SLUG_NAME_WP) . '</a>';
        // Set icon for 5-star reviews. v1.1.22
        $meta_fields[] = "<a href='" . esc_url($plugin_rate) . "' target='_blank' title='" . esc_html__('Rate', $this->bsp_PLUGIN_SLUG_NAME_WP) . "'><i class='bsp_-rate-stars'>" . $svg_icon . "</i></a>";
        return $meta_fields;
    }

}
?>