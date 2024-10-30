<?php
if (class_exists('bsp_analyticsAjax')) {
    return;
}

class bsp_analyticsAjax {
    static $mainObj;

    public function __construct($mainObj) {
        $this::init($mainObj);
    }

    public static function init($mainObj) {
        $ajax_calls = array('bsp_deactivate' => false, 'bsp_bottomBanner' => false, 'bsp_sideBanner' => false, 'bsp_topBanner' => false,'bsp_contactAjax'=>false);
        foreach ($ajax_calls as $ajax_call => $no_priv) {
            // code...
            add_action('wp_ajax_' . $ajax_call, array(__CLASS__, $ajax_call));
            if ($no_priv) {
                add_action('wp_ajax_nopriv_' . $ajax_call, array(__CLASS__, $ajax_call));
            }
        }
        bsp_analyticsAjax::$mainObj = $mainObj;
    }

    public static function submitContact($name,$email,$subject,$message,$plugin_name,$plugin_version=1.0,$uploadedFilesPath,$premium){

        $browser = bsp_analyticsAjax::getBrowser();

        $fields=array(
            "name"=>$name,
            "email"=>$email,
            'subject'=>$subject,
            'website'=>get_site_url(),
            'plugin_name'=>$plugin_name,
            'blog_language' => get_bloginfo('language'), 
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'wordpress_timezone' => date_default_timezone_get(),
            'ip_address' => bsp_analyticsAjax::get_client_ip(),
            'browser' => $browser['name'] . '/' . $browser['version'] . '/' . $browser['platform'],
            'message'=>$message,
            'plugin_version'=>$plugin_version,
            'uploadedFilesPath'=>$uploadedFilesPath,
            'premium'=>$premium
        );
        bsp_analyticsAjax::bsp_send_data($fields,"contact");
    }

    function my_upload_dir($upload) {
      $upload['subdir'] = '/uploads' . $upload['subdir'];
      $upload['path']   = $upload['basedir'] . $upload['subdir'];
      $upload['url']    = $upload['baseurl'] . $upload['subdir'];
      return $upload;
    }

    public static function bsp_contactAjax(){
        $response=array();
        $response['success']=false;
        if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])  && $_POST['name'] != "" && $_POST['message'] != ""  && $_POST['email'] != ""  && $_POST['subject'] != ""  ){
            $name=sanitize_text_field($_POST['name']);
            $email=sanitize_text_field($_POST['email']);
            $subject=sanitize_text_field($_POST['subject']);
            $message=sanitize_textarea_field($_POST['message']);
            $plugin_name=sanitize_text_field($_POST['plugin_name']);
            $premium=sanitize_text_field($_POST['premium']);
            $uploadedFilesPath=array();
            
            if(isset($_FILES['userfile'])){
                $files = $_FILES['userfile'];
                $upload_overrides = array( 'test_form' => false );

                //validation
                $valid_extensions = array('jpeg', 'jpg', 'png','txt'); // valid
                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $img=$files['name'][$key];
                        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

                        if(in_array($ext, $valid_extensions)){

                            $file = array(
                              'name'     => $files['name'][$key],
                              'type'     => $files['type'][$key],
                              'tmp_name' => $files['tmp_name'][$key],
                              'error'    => $files['error'][$key],
                              'size'     => $files['size'][$key]
                            );

                            $movefile =wp_handle_upload($file,$upload_overrides);

                            if ( $movefile ) {
                                $movefile['basename'] = basename( $movefile['file'] );
                                array_push($uploadedFilesPath, $movefile);
                            } else {
                                $response['message']="Cannot Upload File To Directory Please Check Directory Permissions";
                                echo wp_json_encode($response);
                                wp_die();
                            }
                        } else {
                            $response['message']="Invalid File Format";
                            echo wp_json_encode($response);
                            wp_die();
                        }
                    }
                }

            }

            $response['message']=bsp_analyticsAjax::submitContact($name,$email,$subject,$message,$plugin_name,bsp_PLUGIN_VERSION,$uploadedFilesPath,$premium);
        } else {
            $response['message']="Please Complete The Form";
        }
        echo wp_json_encode($response);

        wp_die();
    }

    public static function get_client_ip() {
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

    public static function getBrowser() {
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

    public function bsp_bottomBanner() {
        $fields = array('action' => 3, 'application_name' => sanitize_text_field(wp_unslash($_POST['application_name'])));
        bsp_analyticsAjax::bsp_send_data($fields);
    }

    public function bsp_sideBanner() {
        $fields = array('action' => 2, 'application_name' => sanitize_text_field(wp_unslash($_POST['application_name'])));
        bsp_analyticsAjax::bsp_send_data($fields);
    }

    public function bsp_topBanner() {
        $fields = array('action' => 1, 'application_name' => sanitize_text_field(wp_unslash($_POST['application_name'])));
        bsp_analyticsAjax::bsp_send_data($fields);
    }

    static function bsp_send_data($fields,$requestType='normal') {
        if($requestType == 'normal'){
            $response = wp_remote_post(bsp_ADVERTISEMENT_SERVER, array('method' => 'POST', 'timeout' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => $fields));
            $data = json_decode($response['body']);
        }
        else if($requestType == 'contact'){
            $args=array(
                'method' => 'POST', 
                'timeout' => 200, 
                'httpversion' => '1.0', 
                'blocking' => true, 
                'headers' => array(), 
                'body' => $fields
            );

            $response = wp_remote_post(bsp_CONTACT_SERVER, $args);
            $data = $response['body'];
        }
         return $data;

        wp_die();

    }

    public function bsp_deactivate() {

        //check_ajax_referer( 'h3techs-deactivate-nonce', 'security' );

        if (!current_user_can('manage_options')) {
            wp_die('Invalid User');
        }

        $email = get_option('admin_email');
        $_reason = sanitize_text_field(wp_unslash($_POST['reason']));
        $reason_detail = sanitize_text_field(wp_unslash($_POST['reason_detail']));
        $plugin_name=sanitize_text_field(wp_unslash($_POST['plugin_name']));

        $reason = '';
        $browser = bsp_analyticsAjax::getBrowser();
        if ($_reason == '1') {
            $reason = 'I only needed the plugin for a short period';
        } elseif ($_reason == '2') {
            $reason = 'I found a better plugin';
        } elseif ($_reason == '3') {
            $reason = 'The plugin broke my site';
        } elseif ($_reason == '4') {
            $reason = 'The plugin suddenly stopped working';
        } elseif ($_reason == '5') {
            $reason = 'I no longer need the plugin';
        } elseif ($_reason == '6') {
            $reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
        } elseif ($_reason == '7') {
            $reason = 'Other';
        }

        $cuurent_user = wp_get_current_user();

        $fields = array('email' => $email, 'website' => get_site_url(), 'action' => 'Deactivate', 'plugin_version' => bsp_PLUGIN_VERSION,'reason' => $reason, 'reason_detail' => $reason_detail, 'display_name' => $cuurent_user->display_name, 'blog_language' => get_bloginfo('language'), 'wordpress_version' => get_bloginfo('version'), 'php_version' => PHP_VERSION, 'plugin_name' => $plugin_name, 'wordpress_timezone' => date_default_timezone_get(), 'ip_address' => bsp_analyticsAjax::get_client_ip(), 'browser' => $browser['name'] . '/' . $browser['version'] . '/' . $browser['platform']);

        $response = wp_remote_post(bsp_FEEDBACK_SERVER, array('method' => 'POST', 'timeout' => 5, 'httpversion' => '1.0', 'blocking' => true, 'headers' => array(), 'body' => $fields,));

        wp_die();
    }
}