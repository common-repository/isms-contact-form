<?php
namespace wp_isms_contact\includes;

defined('ABSPATH') or die( 'Access Forbidden!' );

class iSMSContactProcess {
    private $endpoint;
    private $options;
    private $username;
    private $password;
    private $prefix;

    function __construct() {
        $this->options = get_option( 'isms_contact_account_settings' );
        $this->endpoint = 'https://www.isms.com.my/RESTAPI.php';
		
        $this->username = $this->options['username'];
        $this->password = $this->options['password'];
    }

    function send_notification($params) {
        $data = array (
            'sendid' => $this->options['sendid'],
            'dstno' => $params['dstno'],
            'msg' => $params['msg'],
            'type' => '1',
            'agreedterm' =>  'YES',
            'method' => 'isms_send_all_id'
        );

        $payload = json_encode($data);
		$args = array(
			'body' => $payload,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode("$this->username:$this->password"),
			),
			'cookies' => array()
		);
		$response = wp_remote_post($this->endpoint, $args);
		return $response;

        
    }

    function get_data($method) {
        $data = array (
            'method' => $method
        );

		$args = array(
			'body' => json_encode($data),
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode("$this->username:$this->password"),
			),
			'cookies' => array()
		);
		$response = wp_remote_post($this->endpoint, $args);
		return $response;
    }

    function get_db_data($table) {
        global $wpdb;
        return $wpdb->get_results('SELECT * FROM '.$table.'', OBJECT );
    }

    function get_db_data_id($table,$uid,$id) {
        global $wpdb;
        return $wpdb->get_results('SELECT * FROM '.$table.' WHERE '.$uid.' = '.$id.'', OBJECT );
    }

    function get_db_row($table,$uid,$id) {
        global $wpdb;
        return $wpdb->get_row('SELECT * FROM '.$table.' WHERE '.$uid.' = '.$id.'', OBJECT );
    }

    function save_data ($data,$table){
        global $wpdb;
        if ( $wpdb->insert( $table,$data)){
            return $wpdb->insert_id;
        }
    }

    function save_form($form,$meta,$messages){
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        /*start main form*/
        $form_query = $wpdb->insert(ISMS_CONTACT_FORM,$form);
        $formid     = $wpdb->insert_id;
        /*end main form*/

        /*start added_shortcode*/
        $formdata   = $this->get_db_row(ISMS_CONTACT_FORM,'id',$formid);
        $formtitle  = $formdata->title;

        $shortcode  = array(
            'shortcode' => '[isms-contact-form id="'.$formid.'" title="'.$formtitle.'"]'
        );

        $shortcode_query = $this->update_data('id',$formid,ISMS_CONTACT_FORM,$shortcode);
        /*end added_shortcode*/

        /*start form meta*/
        $form_meta      = $wpdb->insert(ISMS_CONTACT_FORM_META,$meta);
        $form_meta_id   = $wpdb->insert_id;

        $form_meta_update = $this->update_data('id',$form_meta_id,ISMS_CONTACT_FORM_META,array('form_id' => $formid ));    
        /*end form meta*/  

        /*start form messages*/
        $form_message      = $wpdb->insert(ISMS_CONTACT_FORM_MESSAGE,$messages);
        $form_message_id   = $wpdb->insert_id;

        $form_message_update = $this->update_data('id',$form_message_id,ISMS_CONTACT_FORM_MESSAGE,array('form_id' => $formid ));    
        /*end form messages*/

        if($form_query && $shortcode_query && $form_meta && $form_meta_update && $form_message && $form_message_update) {
            $wpdb->query('COMMIT'); 
            return $formid;
        }else {
            $wpdb->query('ROLLBACK');
        }
    }

    function update_form($id,$form,$meta,$messages){
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        $form_update = $this->update_data('id',$id,ISMS_CONTACT_FORM,$form); 
        $meta_update = $this->update_data('form_id',$id,ISMS_CONTACT_FORM_META,$meta); 
        $messages_update = $this->update_data('form_id',$id,ISMS_CONTACT_FORM_MESSAGE,$messages); 

        if($form_update || $meta_update || $messages_update) {
            $wpdb->query('COMMIT'); 
            return $id;
        }else {
            $wpdb->query('ROLLBACK');
        }
    }

    function update_data($uid,$id,$table,$data){
        global $wpdb;
        $update = $wpdb->update($table,
            $data,
            array($uid => $id),
            array(
                '%s'
            ),
            array( '%d' )
        );

        if($update){
            return true;
        }else {
            return false;
        }
    }

    function time_elapsed_string($date) {
        if(empty($date)) {
            return "No date provided";
        }

        $periods         = array("sec", "min", "hour", "day", "week", "month", "year", "decade");
        $lengths         = array("60","60","24","7","4.35","12","10");
        $now             = time();
        $unix_date       = strtotime($date);
        // check validity of date

        if(empty($unix_date)) {
            return "Bad date";
        }
        // is it future date or past date
        if($now > $unix_date) {
            $difference     = $now - $unix_date;
            $diff = $difference;
            $tense         = "ago";
        } else {
            $difference     = $unix_date - $now;
            $diff = $difference;
            $tense         = "from now";
        }

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $periods[$j].= "s";
        }

        if($diff > 500){
            return date("Y/m/d", strtotime($date));
        }else {
            return "$difference $periods[$j] {$tense}";
        }
    }
}

?>