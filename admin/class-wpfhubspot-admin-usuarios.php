<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
/**
* The admin-specific functionality of the plugin.
*
* @link       https://github.com/Efraimcat/wpfclientify/
* @since      1.0.0
*
* @package    WpfClientify
* @subpackage WpfClientify/admin
*/
class Wpfhubspot_Admin_Usuarios extends WP_List_Table {

  /*
  * https://wpmudev.com/blog/wordpress-admin-tables/
  */
  public function get_columns() {
    $table_columns = array(
      'time'       => __( 'Time', 'wpfunos' ),
      'ultima'	   => __( 'Ultima', 'wpfunos' ),
      'ip'	       => __( 'IP', 'wpfunos' ),
      'email'      => __( 'email', 'wpfunos' ),
      'hubspotutk' => __( 'hubspotutk', 'wpfunos' ),
    );
    return $table_columns;
  }

  /*
  *
  */
  public function no_items() {
    _e( 'No hay datos disponibles.', 'wpfhubspot' );
  }

  /*
  *
  */
  public function get_sortable_columns() {
    return $sortable = array(
      'time'       => 'time',
      'ultima'	   => 'ultima',
      'ip'	       => 'ip',
      'email'      => 'email',
      'hubspotutk' => 'hubspotutk',
    );
  }

  /*
  *
  */
  public function prepare_items() {
    // code to handle bulk actions

    //used by WordPress to build and fetch the _column_headers property
    $this->_column_headers = $this->get_column_info();
    $table_data = $this->fetch_table_data();

    // code to handle data operations like sorting and filtering

    // start by assigning your data to the items variable
    $this->items = $table_data;

    // code to handle pagination
    $hubspot_users_per_page = $this->get_items_per_page( 'hubspot_users_per_page' );
    $table_page = $this->get_pagenum();

    // provide the ordered data to the List Table
    // we need to manually slice the data based on the current pagination
    $this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $hubspot_users_per_page ), $hubspot_users_per_page );

    // set the pagination arguments
    $total_hubspot_users = count( $table_data );
    $this->set_pagination_args( array (
      'total_items' => $total_hubspot_users,
      'per_page'    => $hubspot_users_per_page,
      'total_pages' => ceil( $total_hubspot_users/$hubspot_users_per_page )
    ) );

    // check if a search was performed.
    $hubspot_users_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

    // check and process any actions such as bulk actions.
    $this->handle_table_actions();

    // filter the data in case of a search
    if( $hubspot_users_search_key ) {
      $table_data = $this->filter_table_data( $table_data, $hubspot_users_search_key );
      $this->items = $table_data;
    }
  }

  /*
  * filter the table data based on the search key
  */
  public function filter_table_data( $table_data, $search_key ) {
    $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
      foreach( $row as $row_val ) {
        if( stripos( $row_val, $search_key ) !== false ) {
          return true;
        }
      }
    } ) );
    return $filtered_table_data;
  }

  /*
  *
  */
  public function fetch_table_data() {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'wpf_hubspotusers';

    $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'id';
    $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'DESC';

    //$user_query = "SELECT * FROM $wpdb_table ORDER BY id DESC";
    $user_query = "SELECT * FROM $wpdb_table WHERE 1=1";

    //$user_query = $this->procesar_args( $user_query );

    //$user_query .= ' ORDER BY id DESC';
    $user_query .= ' ORDER BY ' .$orderby. ' ' .$order;

    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results( $user_query, ARRAY_A  );

    // return result array to prepare_items.
    return $query_results;

  }

  /*
  *
  */
  public function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      case 'referer': return substr( $item[$column_name],0,50 );
      default: return $item[$column_name];
    }
  }

  /*
  *
  */
  public function procesar_args( $user_query ){

    return $user_query;
    /*
    *
    * nombre            Nombre
    * defuncion         Fecha defunción                 m-d
    * velatorio         Velatorio (tanatorio)           v
    * velatorio_inicio  Velatorio fecha y hora Inicio   a
    * velatorio_final   Velatorio fecha y hora final    b
    * ceremonia         Ceremonia (tanatorio)           c
    * ceremonia_fecha   Ceremonia fecha y hora inicio   e
    *
    */
  }
}
