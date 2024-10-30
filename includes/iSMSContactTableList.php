<?php
namespace wp_isms_contact\includes;
defined('ABSPATH') or die( 'Access Forbidden!' );

require_once(dirname(__FILE__) . '/WPListTable.php');

class iSMSContactTableList extends WP_List_Table {
  
  function __construct() {
    parent::__construct(
      array('ajax'     => false )
    );
  }

  public static function delete_form( $id ) {
    global $wpdb;

    if($_REQUEST['page'] == 'isms-contact'){
      $dbtable = ISMS_CONTACT_FORM;

    }else {
       $dbtable = ISMS_CONTACT_SENT;
    }

    $wpdb->delete(
        $dbtable,
        [ 'id' => $id ],
        [ '%d' ]
    );

    $wpdb->delete(
        ISMS_CONTACT_FORM_META,
        [ 'form_id' => $id ],
        [ '%d' ]
    );
	 $wpdb->delete(
        ISMS_CONTACT_FORM_MESSAGE,
        [ 'form_id' => $id ],
        [ '%d' ]
    );
	  $wpdb->delete(
        ISMS_CONTACT_FORM_FIELDS,
        [ 'form_id' => $id ],
        [ '%d' ]
    );
	  
  }

  public static function record_count() {
    global $wpdb;

    if($_REQUEST['page'] == 'isms-contact'){
      $dbtable = ISMS_CONTACT_FORM;

    }else {
      if(isset($_REQUEST['formID'])) {
        $dbtable = ISMS_CONTACT_SENT;

      }else{
        $dbtable = ISMS_CONTACT_FORM;
      }
    }

    $sql = "SELECT COUNT(*) FROM `".$dbtable."`";
    return $wpdb->get_var( $sql );
  }

  public function no_items() {
    if($_REQUEST['page'] == 'isms-contact'){
       _e( 'No form avaliable.');
    }else {
      _e( 'No email sent');
    }
  }

  public function column_default( $item, $column_name ) {
    if($_REQUEST['page'] == 'isms-contact'){
      switch ( $column_name ) {
        case 'id':
        case 'title':
        case 'shortcode':
        case 'author':
        case 'date':

        return $item[ $column_name ];
        default:

        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
      }

    }else {
      if(isset($_REQUEST['formID'])) {
        switch ( $column_name ) {
          case 'id':
          case 'name':
          case 'email':
          case 'mobile':
          case 'subject':
          case 'message':
          case 'date':

          return $item[ $column_name ];
          default:

          return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
      }else {
        switch ( $column_name ) {
          case 'id':
          case 'title':

          return $item[ $column_name ];

          default:
          return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
      } 
    }   
  }

  function column_title($item) {
    $actions = array();
    if($_REQUEST['page'] == "isms-contact"){
      $delete_nonce = wp_create_nonce( 'isms_delete_form' );
    
      $title = sprintf('<a href="?page=%s&id=%s"><strong>'.$item['title'].'</strong></a>','isms-contact-update',$item['id']);
      $actions = array(
          'edit'      => sprintf('<a href="?page=%s&id=%s">Edit</a>','isms-contact-update',$item['id']),
            //'delete'    => sprintf('<a href="?page=%s&action=%s&form=%s&_wpnonce=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id'],$delete_nonce),
      );
    }else {
       $title = sprintf('<a href="?page=%s&formID=%s"><strong>'.$item['title'].'</strong></a>','isms-contact-sent',$item['id']);
    }
    return sprintf('%1$s %2$s', $title, $this->row_actions($actions) );
  }

  function column_date($item) {
    return $this->time_elapsed_string($item['date']);
  }

  function column_count($item) {
    global $wpdb;
    $count = $wpdb->get_var( "SELECT COUNT(id) FROM ".ISMS_CONTACT_SENT." WHERE form_id = ".$item['id'] );
    return $count;
  }

  function column_cb( $item ) {
    return sprintf(
      '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
    );
  }

  function get_columns() {
    if($_REQUEST['page'] == "isms-contact"){
         $columns = [
          'cb'        => '<input type="checkbox" />',
          'title'     => 'Title',
          'shortcode' => 'Shortcode',
          'author'    => 'Author',
          'date'      => 'Date'
        ];  
    }else {

      if(isset($_REQUEST['formID'])) {
          $columns = [
            'cb'        => '<input type="checkbox" />',
            'name'      => 'Name',
            'email'     => 'Email',
            'mobile'    => 'Mobile',
            'subject'   => 'Subject',
            'message'   => 'Message',
            'date'      => 'Date'
          ];
      }else {
          $columns = [
          'title'      => 'Title',
          'count'     => 'Count'
        ];
      }
    }
    return $columns;
  }

  function usort_reorder( $a, $b ) {
	  
      // If no sort, default to title
      $orderby = ( ! empty( $_GET['orderby'] ) ) ? filter_var($_GET['orderby'], FILTER_SANITIZE_STRING): 'id';
      // If no order, default to asc
      $order = ( ! empty( $_GET['order'] ) ) ? filter_var($_GET['order'], FILTER_SANITIZE_STRING) : 'desc';
      // Determine sort order
      $result = strcmp( $a[$orderby], $b[$orderby] );
      // Send final sort direction to usort
      return ( $order === 'asc' ) ? $result : -$result;
  }
  
  function get_sortable_columns() {
      if($_GET['page'] == 'isms-contact'){
        $sortable_columns = array(
          'title'  => array('title',false),
          'date'  => array('date',false),
        );
      }else {
        if(isset($_REQUEST['formID'])) {
          $sortable_columns = array(
          'name'  => array('name',false),
          'date'  => array('date',false),
          );
        }else {
          $sortable_columns = array(
          'title'  => array('title',false)
          );
        }
      }
      return $sortable_columns;
  } 

  public function get_bulk_actions() {
      $actions = [
        'bulk-delete' => 'Delete'
      ];
      return $actions;
  }

  public function prepare_items() {
      global $wpdb;

      $dbtable = ISMS_CONTACT_FORM;
      $formID = "1 = 1";

      if($_REQUEST['page'] == 'isms-contact-sent'){
        if(isset($_REQUEST['formID']) && filter_var($_REQUEST['formID'], FILTER_SANITIZE_NUMBER_INT)) {
          $formID = "form_id = ".(int) $_REQUEST['formID'];
          $dbtable = ISMS_CONTACT_SENT;

        }else {
          $dbtable = ISMS_CONTACT_FORM;
        }    
      }

      $per_page     = $this->get_items_per_page( 'isms_form_per_page', 5 );
      $current_page = $this->get_pagenum();
     
      if ( 1 < $current_page ) {
        $offset = $per_page * ( $current_page - 1 );

      } else {
        $offset = 0;
      }

      $search = '';
      
      if ( ! empty( $_REQUEST['s'] ) ) {
        if($_REQUEST['page'] == 'isms-contact'){
          $search = "AND title LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%'";
        }else {
          if(isset($_REQUEST['formID'])) {
            $search = "AND name LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%' OR email LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%' OR subject LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%' OR mobile LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%'";
          }else {
            $search = "AND title LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%'";
          }
        }
      }   

      
      $items = $wpdb->get_results( "SELECT * FROM ".$dbtable." WHERE {$formID} {$search} " . $wpdb->prepare( "ORDER BY id DESC LIMIT %d OFFSET %d;", $per_page, $offset ),ARRAY_A);

      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable); 
      usort( $items, array( &$this, 'usort_reorder' ) );
      $count = $wpdb->get_var( "SELECT COUNT(id) FROM ".$dbtable." WHERE {$formID} {$search} " );
   
      $this->items = $items;

      $this->_column_headers = $this->get_column_info();
      /** Process bulk action */
      $this->process_bulk_action();

      $this->set_pagination_args( [
      'total_items' => $count, //WE have to calculate the total number of items
      'per_page'    => $per_page //WE have to determine how many items to show on a page
      ] );
  }

  public function process_bulk_action() {

    //Detect when a bulk action is being triggered...
    if ( 'delete' === $this->current_action() ) {

      // In our file that handles the request, verify the nonce.
      $nonce = esc_attr( $_REQUEST['_wpnonce'] );

      if ( ! wp_verify_nonce( $nonce, 'isms_delete_form' ) ) {
        die( 'Go get a life script kiddies' );
      }else {
      
        self::delete_form( absint( $_GET['form'] ) );

        $rdpage = 'isms-contact';
        if($_REQUEST['page'] == 'isms_contact-sent'){
            $rdpage = 'isms-contact-sent';
        }
        echo'<script> window.location="admin.php?page='.$rdpage.'"; </script> ';
        exit;
      }
    }
  // If the delete bulk action is triggered
    
    if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
         || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
    ) {

      $delete_ids = esc_sql( $_POST['bulk-delete'] );

      // loop over the array of record IDs and delete them
      foreach ( $delete_ids as $id ) {
        self::delete_form( $id );
      }

      $rdpage = 'isms-contact';
      if($_REQUEST['page'] == 'isms_contact-sent'){
          $rdpage = 'isms-contact-sent';
      }

      echo'<script> window.location="admin.php?page='.$rdpage.'"; </script> ';
      exit;
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