<?php
namespace wp_isms_contact\includes;
defined('ABSPATH') or die( 'Access Forbidden!' );

class iSMSContact {

    private $admin_contact_options;
    public $isms_contact_process;

    function __construct() {
        add_action('admin_menu', array($this,'isms_contact_hook_to_menu') );
        add_action('admin_init', array( $this, 'isms_contact_init' ) );
        add_action("admin_enqueue_scripts", array($this,"isms_contact_scripts_and_style"));
        add_action('wp_enqueue_scripts', array($this,"isms_contact_public_scripts_and_style"));

        $this->admin_contact_options = get_option( 'isms_contact_account_settings' );

        $this->isms_contact_process = new \wp_isms_contact\includes\iSMSContactProcess();
        $this->isms_contact_captcha = new \wp_isms_contact\includes\iSMSContactCaptcha();

        add_action( 'wp_ajax_get_contact_list', array($this, 'get_form_list') );
        add_action( 'wp_ajax_nopriv_get_contact_list', array($this, 'get_form_list') );

        add_action( 'wp_ajax_get_mail_sent_list', array($this, 'get_mail_sent_list') );
        add_action( 'wp_ajax_nopriv_get_mail_sent_list', array($this, 'get_mail_sent_list') );

        add_action( 'wp_ajax_add_form', array($this, 'add_form') );
        add_action( 'wp_ajax_nopriv_add_form', array($this, 'add_form') );

        add_action( 'wp_ajax_update_form', array($this, 'update_form') );
        add_action( 'wp_ajax_nopriv_update_form', array($this, 'update_form') );

        add_action( 'init', array($this,'register_shortcodes'));

        add_action( 'wp_ajax_send_email', array($this, 'send_email') );
        add_action( 'wp_ajax_nopriv_send_email', array($this, 'send_email') );

    }

    function send_email(){
        $error = false;
        $response = array();

        //Mail settings
        $form_id        = sanitize_text_field(filter_var($_POST['form-id'], FILTER_SANITIZE_NUMBER_INT));
        $addedfields    = sanitize_text_field($_POST['added_fields']);
        $mail_to        = sanitize_text_field($_POST['mail_to']);
        $mail_from      = sanitize_email(filter_var($_POST['mail_from'], FILTER_SANITIZE_EMAIL));
        $mail_header    = sanitize_text_field($_POST['mail_header']);
        $mail_subject   = sanitize_text_field($_POST['mail_subject']);
        $mail_body      = sanitize_textarea_field($_POST['mail_body']);
        $html_format    = 'Content-Type: text/html; charset=UTF-8';
        if($_POST['html_format'] == 0) {
            $html_format = "Content-Type: text/plain; charset=UTF-8";
        }
        $attachment = "";

        //Defaults
        $name           = sanitize_text_field($_POST['fname'])." ".sanitize_text_field($_POST['lname']);
        $email          = sanitize_email(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        $subject        = sanitize_text_field($_POST['subject']);
        $message        = sanitize_textarea_field($_POST['message']);
        $country_code   = sanitize_text_field($_POST['isms_contact_country_code']);
        $mobilefield    = sanitize_text_field($_POST['mobilefield']);
        $mobile         = sanitize_text_field($_POST['isms_contact_mobilefield_hidden']);
        

        $fields         = explode(",", $addedfields);
        $fields_array   = array();
        $post_field     = array();

        $form_messages = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM_MESSAGE,'form_id',$form_id);
        $form_fields = $this->isms_contact_process->get_db_data_id(ISMS_CONTACT_FORM_FIELDS,'form_id',$form_id);
        
        foreach($form_fields as $field){
          
            if($field->field_type == 'tel') {
                
                if($_POST[$field->field_name] != "") {
                    $postfield = sanitize_text_field( $_POST[$field->field_name] );
                    if(!preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $postfield)) {
                        $response['message'] =  "Error: ".$postfield;
                        $error = true;
                    }
                }
            }else if($field->field_type == 'url') {
                if($_POST[$field->field_name] != "") {
                     $postfield = sanitize_text_field( $_POST[$field->field_name] );
                    if (!filter_var($postfield, FILTER_VALIDATE_URL)) {
                        $response['message'] = $form_messages->url_invalid;
                        $error = true;
                    } 
                }
            }else if($field->field_type == 'file') {
                if (!empty($_FILES[$field->field_name]["name"])) {
                    $target_dir = wp_upload_dir();
                    if ( ! function_exists( 'wp_handle_upload' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    }
                    $upload_overrides = array(
                        'test_form' => false
                    );
                
                    $allowed = explode(",",$field->field_accept);
                    
                    $filename = $_FILES[$field->field_name]['name'];
                  
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $response['message'] = $form_messages->upload_file_type_error;
                        $error = true;
                    }else {
                        if ($_FILES[$field->field_name]["size"] > $field->field_limit) {
                            $response['message'] = $form_messages->upload_file_too_large;
                            $error = true;
                        }else {

                            $movefile = wp_handle_upload($_FILES[$field->field_name], $upload_overrides );
         
                            if ( $movefile && ! isset( $movefile['error'] ) ) {
                              $attachment = $movefile;
                            } else {
                                $response['message'] = $form_messages->upload_php_error;
                                $error = true;
                            }  
                        }
                    }
                }
            }
            
        }       

        if(!is_numeric($mobilefield)) {
            $response['message'] = $form_messages->mobile_invalid;
            $error = true;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = $form_messages->email_invalid;
            $error = true;
        } 
      
        if(sanitize_text_field($_POST['captcha-input']) != sanitize_text_field($_POST['session-captcha'])){
            $response['message'] = "Invalid Captcha";
            $error = true;
        }

        if(!$error){
            foreach ($fields as $key => $field) {
                array_push($fields_array,'[isms_'.$field.']');
                array_push($post_field, sanitize_text_field($_POST[$field]));
            }
          

            $sent = array(
                'form_id'   => $form_id,
                'mobile'    => $mobile,
                'name'      => $name,
                'email'     => $email,
                'subject'   => $subject,
                'message'   => $message,
                'date'      =>date("Y-m-d h:i:sa")
            );
           
            $headers=array(
                $html_format,
                'From: '.$mail_from,
                $mail_header
                
                
            );
            $msubject = $this->format_mail($fields_array,$post_field,$mail_subject);
            $mbody = $this->format_mail($fields_array,$post_field,$mail_body);
            $mheaders = $this->format_mail($fields_array,$post_field,$headers);
            
            if(wp_mail( $mail_to, $msubject, $mbody, $mheaders,$attachment )){
                $save_data = $this->isms_contact_process->save_data($sent,ISMS_CONTACT_SENT);
                
                if($save_data) {
                    $response['status'] = $save_data;
                    $response['message'] = $form_messages->sent_successfully;
                    
                    if($this->admin_contact_options['enable-sms'] == 'yes'){
                        $params = array(
                            'dstno' => $this->admin_contact_options['ismscontactphone'],
                            'msg' => $message
                        );
                        
                        $result = $this->isms_contact_process->send_notification($params);
                        $response_code = explode("=",str_replace('"', "",$result['body']));
                    }
                    wp_send_json($response);
                }  
            }else {
                $response['status'] ="Failed to send";
                $response['message'] = $form_messages->failed_to_send;
            }
            
        }else {
             wp_send_json($response);
        }

        
    }

    function get_messages($id,$field) {
        return $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM_MESSAGE,'form_id',$id);

    }
   
    function register_shortcodes(){
        add_shortcode('isms-contact-form', array($this,'isms_contact_form_function'));
        add_shortcode('isms-field', array($this,'isms_field_function'));
    }

    function isms_field_function($atts, $content = null){
        extract(shortcode_atts(array(
            'ty'    => null, //input type
            'fr'    => null, //required field
            'fn'    => null, //field name
            'dv'    => null, //default value
            'ph'    => null, //place holder
            'fid'   => null, //field ID
            'fc'    => null, //field Class
            'fl'    => null, //field label
            'ffa'   => null, //file accept
            'nmin'  => null, //number min
            'nmax'  => null //number max
        ), $atts));

        ob_start();
        
        $req = "";

        if($fr){
            $req = "required";
        }
        
        $createfield = '<p><input type="'.$ty.'" '.$req.' name="'.$fn.'" id="'.$fid.'" class="'.$fc.'" ';
     
        if($ty == "textarea"){

            if($ph != null && $ph == $dv) {
                $createfield ='<textarea name="'.$fn.'" '.$req.' placeholder="'.$ph.'" id="'.$fid.'" class="'.$fc.'"></textarea>'; 
            }else {
                $createfield ='<textarea name="'.$fn.'" '.$req.' id="'.$fid.'" class="'.$fc.'">'.$dv.'</textarea>'; 
            }
           
        }else{

            if($ty != 'file' && $ty != 'submit' && $ty != 'checkbox' && $ty != 'radio'){

                if($ph != null && $ph == $dv) {
                    $createfield .='placeholder="'.$ph.'" ';
                }else {
                    $createfield .='value="'.$dv.'" ';
                }
            }
            
            if($ty == 'file'){
                $createfield .='accept="'.$ffa.'" ';
            }

            if($ty == 'submit'){
                $createfield .='value="'.$fl.'" ';  
            }

            if($ty == 'number'){
                if($ph != null && $ph == $dv) {
                    $createfield .='min="'.$nmin.'" max="'.$nmax.'" ';
                }else {
                   $createfield .='min="'.$nmin.'" max="'.$nmax.'" ';
                }  
            }

            if($ty == 'checkbox' || $ty == "radio"){
                $createfield .= "><label for='".$fn."'class='checkandradiolabel'>".$fl."</label>";  

            }else {
                $createfield .= '>';
            }   
        }

        echo $createfield;
        $output_string = ob_get_contents();

        ob_end_clean();
        return $output_string;
    }
    function get_captcha(){
       $captcha_code = $this->isms_contact_captcha->getCaptchaCode(6);

       $this->isms_contact_captcha->setSession('captcha_code', $captcha_code);

      // $imageData = $this->isms_contact_captcha->createCaptchaImage($captcha_code);

      //$this->isms_contact_captcha->renderCaptchaImage($imageData);
        
    }
    function isms_contact_form_function($atts, $content = null){
        extract(shortcode_atts(array(
          'id'      => null,
          'title'   => null, 
        ), $atts));
        
        ob_start();
        $this->get_captcha();

        $form_data = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM,'id',$id);
        $form_meta_data = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM_META,'form_id',$id);
        
        echo '<input type="hidden" name="generated-captcha" id="generated-captcha" value="'.$this->isms_contact_captcha->getSession('captcha_code').'"/><form id="isms-contact-form-'.$id.'" class="isms-contact-form" method="post" enctype="multipart/form-data"><input type="hidden" name="html_format" value="'.$form_meta_data->html_format.'"/><input type="hidden" name="form-id" value="'.$id.'"/><input type="hidden" name="action" value="send_email"><input type="hidden" name="added_fields" value="'.$form_meta_data->fields.'"><input type="hidden" name="mail_to" value="'.$form_meta_data->mail_to.'"><input type="hidden" name="mail_from" value="'.$form_meta_data->mail_from.'"><input type="hidden" name="mail_header" value="'.$form_meta_data->mail_additional_header.'"><input type="hidden" name="mail_subject" value="'.stripslashes(base64_decode($form_meta_data->mail_subject)).'"><input type="hidden" name="mail_body" value="'.stripslashes(base64_decode($form_meta_data->mail_body)).'">';

            echo do_shortcode(stripslashes(base64_decode($form_data->form_data)));
            
        echo '</form><div class="isms-response-holder isms-hidden"></div>';

        $output_string = ob_get_contents();
        ob_end_clean();

        return $output_string;
    }

    function add_form() {
		$error = false;
        $response = array();
		
        $title          = sanitize_text_field($_POST['title']);
        $form           = wp_kses_post($_POST['form-data']);
        $addedfields    = sanitize_text_field($_POST['addedfields']);
        $user           = wp_get_current_user();

        $mailto         = sanitize_text_field($_POST['email-to']);
        $mailfrom       = sanitize_email($_POST['email-from']);
        $mailsubject    = sanitize_text_field($_POST['email-subject']);
        $mailheaders    = sanitize_text_field($_POST['email-headers']);
        $mailbody       = sanitize_textarea_field($_POST['email-body']);
        $html_format    = 0;
        if($_POST['html-format'] != NULL) {
            $html_format    = 1;
        }
        
        
        if($title == ""){
            $title = "Untitled";
        }

        $form = array(
            'title'     => $title,
            'form_data' => base64_encode($form),
            'author'    => $user->user_login,      
            'date'      => date("Y-m-d h:i:sa")
        );

        $meta = array(
            'mail_to'       => $mailto,
            'mail_from'     => $mailfrom,
            'mail_subject'  => base64_encode($mailsubject),
            'mail_additional_header' => $mailheaders,
            'mail_body'     => base64_encode($mailbody),
            'fields'        => $addedfields,
            'html_format'   => $html_format
        );

        $messages = array (
            'mobile_invalid' => sanitize_text_field($_POST['mobile_invalid']),
            'sent_successfully' => sanitize_text_field($_POST['sent_successfully']),
            'failed_to_send' => sanitize_text_field($_POST['failed_to_send']),
            'referred_to_as_spam' => sanitize_text_field($_POST['referred_to_as_spam']),
            'upload_error' => sanitize_text_field($_POST['upload_error']),
            'upload_file_type_error' => sanitize_text_field($_POST['upload_file_type_error']),
            'upload_file_too_large' => sanitize_text_field($_POST['upload_file_too_large']),
            'upload_php_error' => sanitize_text_field($_POST['upload_php_error']),
            'tel_invalid' => sanitize_text_field($_POST['tel_invalid']),
        );
        
		if (!filter_var($mailfrom, FILTER_VALIDATE_EMAIL)) {
			 $response['message'] = "Invalid From data";
			 $response['status'] ="Error";
            	$error = true;
		}
       
        if(!$error){
			$save_data = $this->isms_contact_process->save_form($form,$meta,$messages);

			if($save_data) {
			   foreach($_POST['fields'] as $value) {
					$field_data = array (
						'form_id'       => $save_data,
						'field_type'    => sanitize_text_field($value['field_type']),
						'field_name'    => sanitize_text_field($value['field_name']),
						'field_min'     => sanitize_text_field($value['field_min']),
						'field_max'     => sanitize_text_field($value['field_max']),
						'field_accept'  => sanitize_text_field($value['field_accept']),
						'field_limit'   => sanitize_text_field($value['field_limit']),
						'is_required'   => sanitize_text_field($value['is_required']),
					);

					$this->isms_contact_process->save_data($field_data,ISMS_CONTACT_FORM_FIELDS);
				}
			   // wp_send_json($save_data);
				$response['status'] = $save_data;
				$response['message'] = $save_data;
				

			}else{
				$response['status'] = "Failed";
				$response['message'] = "Failed to create form";
				
			}
		}
		 wp_send_json($response);
    }

    function update_form() {
        global $wpdb;
		$error = false;
        $response = array();
		
        $title          = sanitize_text_field($_POST['title']);
        $form           = wp_kses_post($_POST['form-data']);
        $form_id        = sanitize_text_field(filter_var($_POST['form-id'], FILTER_SANITIZE_NUMBER_INT));
        $addedfields    = sanitize_text_field($_POST['addedfields']);

        $mailto         = sanitize_text_field($_POST['email-to']);
        $mailfrom       = sanitize_email($_POST['email-from']);
        $mailsubject    = sanitize_text_field($_POST['email-subject']);
        $mailheaders    = sanitize_text_field($_POST['email-headers']);
        $mailbody       = sanitize_textarea_field($_POST['email-body']);
        $html_format    = filter_var($_POST['html-format'], FILTER_SANITIZE_NUMBER_INT);
        
        if($title == ""){
            $title = "Untitled";
        }

        $form_data = array(
           'title'      => $title,
           'form_data'  =>base64_encode($form),     
           'shortcode'  => '[isms-contact-form id="'.$form_id.'" title="'.$title.'"]'
        );

        $meta = array(
            'mail_to'       => $mailto,
            'mail_from'     => $mailfrom,
            'mail_subject'  => base64_encode($mailsubject),
            'mail_additional_header' => $mailheaders,
            'mail_body'     => base64_encode($mailbody),
            'fields'        => $addedfields,
            'html_format'   => $html_format
        );

         $messages = array (
            'mobile_invalid' => sanitize_text_field($_POST['mobile_invalid']),
            'sent_successfully' => sanitize_text_field($_POST['sent_successfully']),
            'failed_to_send' => sanitize_text_field($_POST['failed_to_send']),
            'referred_to_as_spam' => sanitize_text_field($_POST['referred_to_as_spam']),
            'upload_error' => sanitize_text_field($_POST['upload_error']),
            'upload_file_type_error' => sanitize_text_field($_POST['upload_file_type_error']),
            'upload_file_too_large' => sanitize_text_field($_POST['upload_file_too_large']),
            'upload_php_error' => sanitize_text_field($_POST['upload_php_error']),
            'tel_invalid' => sanitize_text_field($_POST['tel_invalid']),
        );

		if (!filter_var($mailfrom, FILTER_VALIDATE_EMAIL)) {
			 $response['message'] = "Invalid From data";
			 $response['status'] ="Error";
            $error = true;
		}
       
        if(!$error){
			$update_form = $this->isms_contact_process->update_form($form_id,$form_data,$meta,$messages);

			if($update_form){
				$wpdb->delete(
					ISMS_CONTACT_FORM_FIELDS,
					[ 'form_id' => $form_id ],
					[ '%d' ]
				);
				foreach($_POST['fields'] as $value) {
					$field_data = array (
						'form_id'       => $form_id,
						'field_type'    => sanitize_text_field($value['field_type']),
						'field_name'    => sanitize_text_field($value['field_name']),
						'field_min'     => sanitize_text_field($value['field_min']),
						'field_max'     => sanitize_text_field($value['field_max']),
						'field_accept'  => sanitize_text_field(stripslashes($value['field_accept'])),
						'field_limit'   => sanitize_text_field($value['field_limit']),
						'is_required'   => sanitize_text_field($value['is_required']),
					);

					$this->isms_contact_process->save_data($field_data,ISMS_CONTACT_FORM_FIELDS);
				}
				//wp_send_json($update_form);
				$response['status'] = $update_form;
				$response['message'] = $update_form;
			}else{
			  //  wp_send_json("Faild to update.");
				$response['status'] = "Failed";
				$response['message'] = "Failed: No changes applied" ;
			}
		}
		wp_send_json($response);
    }
    
    function get_form_list() {
        $forms = $this->isms_contact_process->get_db_data(ISMS_CONTACT_FORM);
        $lst = array();

        if($forms) {
            foreach ($forms as $form) {
                $lst['data'][]  = array(
                    'title' => '<a open-ul="#form-list-'.$form->id.'" href="'.get_site_url().'/wp-admin/admin.php?page=isms-contact-update&id='.$form->id.'">'.$form->title.'</a>
                                <div class="form-list-action-holder"><ul class="form-list-action isms-hidden" id="form-list-'.$form->id.'"><li><a href="'.get_site_url().'/wp-admin/admin.php?page=isms-contact-update&id='.$form->id.'">Edit</a></li></ul></div>',
                    'shortcode' => $form->shortcode,
                    'author' => $form->author,
                    'date' => $this->isms_contact_process->time_elapsed_string($form->timestamp)
                );
            }
            wp_send_json($lst);

        }else{
            $lst['data'][]  = array(
                'title'     => "",
                'shortcode' => "",
                'author'    => "No data available",
                'date'      =>"",
            );
            wp_send_json($lst);
        }
    }

    function get_mail_sent_list() {
        $sents = $this->isms_contact_process->get_db_data(ISMS_CONTACT_SENT);
        $lst = array();

        if($sents) {
            foreach ($sents as $sent) {
                $lst['data'][]  = array(
                    'mail_subject' => $sent->mail_subject,
                    'mail_from' => $sent->mail_from,
                    'mail_to' => $sent->mail_to,
                    'body' => $sent->mail_body,
                    'mobile' => $sent->mobile,
                    'date' => $this->isms_contact_process->time_elapsed_string($sent->timestamp)
                );
            }
            wp_send_json($lst);

        }else{
            $lst['data'][]  = array(
                'mail_subject' => "",
                'mail_from' => "",
                'mail_to' => "No data available",
                'body' => "",
                'mobile' => "",
                'date' => ""
            );
            wp_send_json($lst);
        }
    }
    function isms_contact_init() {
        add_option('enable-sms','no');
        
        register_setting(
            'isms_contact_admin_settings', // Option group
            'isms_contact_account_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'contact_setting_section_id', // ID
            ' ', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-contact-setting-admin' // Page
        );

        add_settings_field(
            'sendid', // ID
            'Sender ID', // Title
            array( $this, 'sendid_callback' ), // Callback
            'my-contact-setting-admin', // Page
            'contact_setting_section_id' // Section
        );
        add_settings_field(
            'username', // ID
            'Username', // Title
            array( $this, 'username_callback' ), // Callback
            'my-contact-setting-admin', // Page
            'contact_setting_section_id' // Section
        );

        add_settings_field(
            'phone',
            'Admin Phone',
            array( $this, 'phone_callback' ),
            'my-contact-setting-admin',
            'contact_setting_section_id'
        );
        add_settings_field(
            'password',
            'Password',
            array( $this, 'password_callback' ),
            'my-contact-setting-admin',
            'contact_setting_section_id'
        );
        add_settings_field(
            'enable-sms',
            'Enable SMS',
            array( $this, 'enable_sms_callback' ),
            'my-contact-setting-admin',
            'contact_setting_section_id'
        );
        
    }

    function isms_contact_hook_to_menu() {
        add_menu_page(
            'iSMS Contact Form',
            'iSMS Contact',
            'manage_options',
            'isms-contact',
             array( $this, 'isms_contact_list' ),'',6
        );

        add_submenu_page(
            'isms-contact',
            'iSMS Add new form',
            'Add new',
            'manage_options',
            'isms-contact-new',
            array( $this, 'isms_contact_new' ),''
        );

        add_submenu_page(
            'isms-contact',
            'iSMS Sent Email',
            'Sent Mail',
            'manage_options',
            'isms-contact-sent',
            array( $this, 'isms_contact_sent' ),''
        );
        add_submenu_page(
            'isms-contact',
            'iSMS Contact Account Settings',
            'iSMS Account',
            'manage_options',
            'isms-contact-account',
            array( $this, 'isms_contact_account' ),''
        );

        add_submenu_page(
            'isms-contact',
            'iSMS Update form',
            '',
            'manage_options',
            'isms-contact-update',
            array( $this, 'isms_contact_update' ),''
        );
    }
    function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['sendid'] ) )
            $new_input['sendid'] = sanitize_text_field( $input['sendid'] );

        if( isset( $input['username'] ) )
            $new_input['username'] = sanitize_text_field( $input['username'] );

        if( isset( $input['ismscontactphone'] ) )
            $new_input['ismscontactphone'] = sanitize_text_field( $input['ismscontactphone'] );

        if( isset( $input['password'] ) )
            $new_input['password'] = sanitize_text_field( $input['password'] );

        if( isset( $input['enable-sms'] ) )
            $new_input['enable-sms'] = sanitize_text_field( $input['enable-sms'] );

        return $new_input;
    }

    function print_section_info() {
        print 'Enter your iSMS credentials';
        
    }
    

    function sendid_callback() {
        printf(
            '<input type="text" style="width: 210px" id="sendid" autocomplete="off" name="isms_contact_account_settings[sendid]" value="%s" required="required"/>',
            isset( $this->admin_contact_options['sendid'] ) ? esc_attr( $this->admin_contact_options['sendid']) : ''
        );
    }

    function username_callback() {
        printf(
            '<input type="text" style="width: 210px" id="username" autocomplete="off" name="isms_contact_account_settings[username]" value="%s" required="required"/>',
            isset( $this->admin_contact_options['username'] ) ? esc_attr( $this->admin_contact_options['username']) : ''
        );
    }

    function phone_callback() {
        printf(
            '<input type="text" style="width: 210px" id="ismscontactphone" autocomplete="off" name="isms_contact_account_settings[ismscontactphone]" value="%s" required="required"/>',
            isset( $this->admin_contact_options['ismscontactphone'] ) ? esc_attr( $this->admin_contact_options['ismscontactphone']) : ''
        );
    }

    function password_callback() {
        printf(
            '<input type="password" style="width: 210px" id="password" autocomplete="off" name="isms_contact_account_settings[password]" value="%s" required="required"/>',
            isset( $this->admin_contact_options['password'] ) ? esc_attr( $this->admin_contact_options['password']) : ''
        );
    }
    public function enable_sms_callback() {?>
        <input type="radio" id="enable-sms-yes" autocomplete="off" name="isms_contact_account_settings[enable-sms]" value="yes" <?php checked("yes" , $this->admin_contact_options['enable-sms']); ?> />Yes
        <input type="radio" id="enable-sms-no" autocomplete="off" name="isms_contact_account_settings[enable-sms]" value="no" <?php checked("no" , $this->admin_contact_options['enable-sms']); ?> />No

        <?php
    }

    function isms_contact_account() { ?>
        <div class="wrap">
            <h1>iSMS Account Settings</h1>
            <div class="isms-divider"></div>
            <?php
			$balance = $this->isms_contact_process->get_data('isms_balance');
			$expiration = $this->isms_contact_process->get_data('isms_expiry_date');
					
            if($this->admin_contact_options){ ?>
                <div>
                    <h3>Your credit balance: <?php echo str_replace('"', "", $balance['body']); ?></h3>
                    <h4>valid until <?php echo str_replace('"', "", $expiration['body']); ?> </h4>
                </div>
                <br/>
            <?php } ?>

            <form method="post" action="options.php">
                <?php
				
                // This prints out all hidden setting fields
                settings_fields( 'isms_contact_admin_settings' );
                do_settings_sections( 'my-contact-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }


    function isms_contact_list () { ?>
        <div class="isms-contact">
            <div class="row isms-contact">
                <div class="col-md-12 mt-3">
                    <div class="col-md-6">
                        <h3>Contact Forms  <a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=isms-contact-new" class="btn-primary" id="add-new-form">Add New</a></h3>
                    </div>
                    <div class="col-md-6 search-form-holder">
                        <form action="" method="post">                            
                            <input type="text" name="s" id="search" value="<?php the_search_query(); ?>" />
                            <button class="btn btn-primary">Search</button>
                        </form>  
                    </div>
                </div>
            </div> 
            <div class="isms-divider"></div>

            <div class="row">
                <div class="col-md-12">
                    <form method="post">
                        <?php 
                            $wp_list_table = new iSMSContactTableList(); 
                            $wp_list_table->prepare_items();
                            $wp_list_table->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>                   
    <?php
    
    }

    function isms_contact_new () {
        $this->isms_contact_template('add-new');
    }

    function isms_contact_update () {
        $this->isms_contact_template('update-form');
    }

    function isms_contact_sent () {?>
        <div class="isms-contact">
            <div class="row isms-contact">
                <div class="col-md-12 mt-3">
                    <div class="col-md-6">
                        <h3>
                        <?php if(isset($_REQUEST['formID'])){
                            $form = $this->isms_contact_process->get_db_row(ISMS_CONTACT_FORM,'id',(int) $_REQUEST['formID']);
                            esc_attr_e( $form->title);

                        } else {
                            esc_attr_e( 'Sent Mail');
                        } ?>       
                        </h3>
                    </div>
                    <div class="col-md-6 search-form-holder">
                        <form action="" method="post">                            
                            <input type="text" name="s" id="search" value="<?php the_search_query(); ?>" />
                            <button class="btn btn-primary">Search</button>
                        </form>  
                    </div>
                </div>
            </div> 
            <div class="isms-divider"></div>

            <div class="row">
                <div class="col-md-12">
                    <form method="post">
                        <?php 
                            $wp_list_table = new iSMSContactTableList(); 

                            $wp_list_table->prepare_items();
                            $wp_list_table->display();
                        ?>
                    </form>
                </div>
            </div>
        </div> 

    <?php }

    function isms_contact_scripts_and_style($hook){
        if($hook == 'toplevel_page_isms-contact' || $hook == 'isms-contact_page_isms-contact-new' || $hook == 'isms-contact_page_isms-contact-update' || $hook == 'isms-contact_page_isms-contact-sent' || $hook == 'isms-contact_page_isms-contact-account'){
            wp_enqueue_style("isms-contact-bootstrap", plugins_url('../assets/css/bootstrap.min.css', __FILE__));
            wp_enqueue_style("isms-contact-prefix", plugins_url('../assets/prefix/css/intlTelInput.css', __FILE__));
            wp_enqueue_style("isms-contact-style", plugins_url('../assets/css/ismscontactstyle.css', __FILE__));

            wp_enqueue_script("isms-contact-bootstrap",plugins_url('../assets/js/bootstrap.min.js', __FILE__));
            wp_enqueue_script("isms-contact-prefix-js", plugins_url('../assets/prefix/js/intlTelInput.js', __FILE__));
            wp_enqueue_script("isms-contact-js", plugins_url('../assets/js/ismscontact.js', __FILE__));
            
            wp_localize_script('isms-contact-js', 'ismscontactajaxurl', array("scriptismscontact" => admin_url('admin-ajax.php')));
            wp_localize_script('isms-contact-js', 'ismscontactScript', array(
                'pluginsUrl' => plugin_dir_url( __FILE__ ),
            ));
        }
    }

    function isms_contact_public_scripts_and_style($hook){
        wp_enqueue_style("isms-contact-prefix", plugins_url('../assets/prefix/css/intlTelInput.css', __FILE__));
        wp_enqueue_style("isms-contact-style", plugins_url('../assets/public/css/ismscontactstyle.css', __FILE__));
        wp_enqueue_script('jquery');
        wp_enqueue_script("isms-contact-prefix-js", plugins_url('../assets/prefix/js/intlTelInput.js', __FILE__));

        wp_enqueue_script("isms-contact-js", plugins_url('../assets/public/js/ismscontact.js', __FILE__));
        wp_localize_script( 'isms-contact-js', 'isms_contact_public_ajax', array( "ajaxurl" => admin_url('admin-ajax.php') ) );

        wp_localize_script('isms-contact-js', 'ismscontactScript', array(
            'pluginsUrl' => plugin_dir_url( __FILE__ ),
        ));
    }

    private function format_mail($fields,$replace_with,$str) {
        return str_replace($fields,$replace_with,$str);
    }
    
    private function isms_contact_template($file) {
        include(dirname(__FILE__) . '/'.$file.'.php');
    }
}

?>