<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	    exit();
global $wpdb;		

delete_option( 'isms_contact_account_settings' );

$isms_contact_form = $wpdb->prefix. "isms_contact_form" ;
$isms_contact_sent = $wpdb->prefix. "isms_contact_sent" ;
$isms_contact_form_meta = $wpdb->prefix. "isms_contact_form_meta";
$isms_contact_form_message= $wpdb->prefix. "isms_contact_form_message";
$isms_contact_form_fields = $wpdb->prefix. "isms_contact_form_fields";

$wpdb->query("DROP TABLE `".$isms_contact_form."`");
$wpdb->query("DROP TABLE `".$isms_contact_sent."`");
$wpdb->query("DROP TABLE `".$isms_contact_form_meta."`");
$wpdb->query("DROP TABLE `".$isms_contact_form_message."`");
$wpdb->query("DROP TABLE `".$isms_contact_form_fields."`");
