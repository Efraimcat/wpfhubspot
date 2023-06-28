<?php
if (!defined('ABSPATH')) {
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
class Wpfhubspot_Admin_Usuarios extends WP_List_Table
{

  /*
  * https://wpmudev.com/blog/wordpress-admin-tables/
  */
  public function get_columns()
  {
    $table_columns = array(
      'time'       => __('Time', 'wpfunos'),
      'ultima'     => __('Ultima', 'wpfunos'),
      'ip'         => __('IP', 'wpfunos'),
      'email'      => __('email', 'wpfunos'),
      'hubspotutk' => __('hubspotutk', 'wpfunos'),
      'referencias' => __('referencias', 'wpfunos'),
    );
    return $table_columns;
  }

  /*
  *
  */
  public function no_items()
  {
    _e('No hay datos disponibles.', 'wpfhubspot');
  }

  /*
  *
  */
  public function get_sortable_columns()
  {
    return $sortable = array(
      'time'       => 'time',
      'ultima'     => 'ultima',
      'ip'         => array('INET_ATON(ip)', 'asc'),
      'email'      => 'email',
      'hubspotutk' => 'hubspotutk',
    );
  }

  /*
  *
  */
  public function prepare_items()
  {
    // code to handle bulk actions

    //used by WordPress to build and fetch the _column_headers property
    $this->_column_headers = $this->get_column_info();
    $table_data = $this->fetch_table_data();

    // code to handle data operations like sorting and filtering

    // start by assigning your data to the items variable
    $this->items = $table_data;

    // code to handle pagination
    $hubspot_users_per_page = $this->get_items_per_page('hubspot_users_per_page');
    $table_page = $this->get_pagenum();

    // provide the ordered data to the List Table
    // we need to manually slice the data based on the current pagination
    $this->items = array_slice($table_data, (($table_page - 1) * $hubspot_users_per_page), $hubspot_users_per_page);

    // set the pagination arguments
    $total_hubspot_users = count($table_data);
    $this->set_pagination_args(array(
      'total_items' => $total_hubspot_users,
      'per_page'    => $hubspot_users_per_page,
      'total_pages' => ceil($total_hubspot_users / $hubspot_users_per_page)
    ));

    // check if a search was performed.
    $hubspot_users_search_key = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';

    // check and process any actions such as bulk actions.
    $this->handle_table_actions();

    // filter the data in case of a search
    if ($hubspot_users_search_key) {
      $table_data = $this->filter_table_data($table_data, $hubspot_users_search_key);
      $this->items = $table_data;
    }
  }

  /*
  * filter the table data based on the search key
  */
  public function filter_table_data($table_data, $search_key)
  {
    $filtered_table_data = array_values(array_filter($table_data, function ($row) use ($search_key) {
      foreach ($row as $row_val) {
        if (stripos($row_val, $search_key) !== false) {
          return true;
        }
      }
    }));
    return $filtered_table_data;
  }

  /*
  *
  */
  public function fetch_table_data()
  {
    global $wpdb;
    $wpdb_table = $wpdb->prefix . 'wpf_hubspotusers';

    $orderby = (isset($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'id';
    $order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'DESC';

    //$user_query = "SELECT * FROM $wpdb_table ORDER BY id DESC";
    $user_query = "SELECT * FROM $wpdb_table WHERE 1=1";

    $user_query = $this->procesar_args($user_query);

    //$user_query .= ' ORDER BY id DESC';
    $user_query .= ' ORDER BY ' . $orderby . ' ' . $order;

    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results($user_query, ARRAY_A);

    // return result array to prepare_items.
    return $query_results;
  }

  /*
  *
  */
  public function column_default($item, $column_name)
  {
    switch ($column_name) {
      case 'referer':
        return substr($item[$column_name], 0, 50);
        break;
      case 'referencias':
        $ref = unserialize($item[$column_name]);
        $referencia = '';
        foreach ($ref as $key => $value) {
          $referencia .= $value . ', ';
        }
        return  substr($referencia, 0, 200);
        break;
      default:
        return $item[$column_name];
    }
  }

  /*
  *
  */
  public function procesar_args($user_query)
  {
    if (!empty($_REQUEST['d'])) {
      $search = $_REQUEST['d'];
      $year = substr($search, 0, 4);
      $month = substr($search, 4, 2);
      $day = substr($search, 6, 2);

      if (!empty($year)) {
        $user_query .= ' And YEAR(time)="' . $year . '"';
      }
      if (!empty($month)) {
        $user_query .= ' And MONTH(time)="' . $month . '"';
      }
      if (!empty($day)) {
        $user_query .= ' And DAY(time)="' . $day . '"';
      }
    }
    if (!empty($_REQUEST['m']) && empty($_REQUEST['d'])) {
      $search = $_REQUEST['m'];
      $year = substr($search, 0, 4);
      $month = substr($search, 4, 2);

      if (!empty($year)) {
        $user_query .= ' And YEAR(time)="' . $year . '"';
      }
      if (!empty($month)) {
        $user_query .= ' And MONTH(time)="' . $month . '"';
      }
    }
    if (!empty($_REQUEST['a'])) {
      $search = $_REQUEST['a'];
      $year = substr($search, 0, 4);
      $month = substr($search, 4, 2);
      $day = substr($search, 6, 2);

      if (!empty($year)) {
        $user_query .= ' And YEAR(ultima)="' . $year . '"';
      }
      if (!empty($month)) {
        $user_query .= ' And MONTH(ultima)="' . $month . '"';
      }
      if (!empty($day)) {
        $user_query .= ' And DAY(ultima)="' . $day . '"';
      }
    }
    return $user_query;
  }

  /*
  *
  * https://wordpress.stackexchange.com/questions/223552/how-to-create-custom-filter-options-in-wp-list-table
  *
  */
  public function extra_tablenav($which)
  {
    switch ($which) {
      case 'top':
        // Your html code to output
        global $wpdb, $wp_locale;
        $months = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT YEAR( time ) AS year, MONTH( time ) AS month FROM " . $wpdb->prefix . "wpf_hubspotusers WHERE 1 = 1 ORDER BY time DESC"));
        $days = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT YEAR( time ) AS year, MONTH( time ) AS month, DAY( time ) AS day FROM " . $wpdb->prefix . "wpf_hubspotusers WHERE 1 = 1 ORDER BY time DESC"));
        $ultima = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT YEAR( ultima ) AS year, MONTH( ultima ) AS month, DAY( ultima ) AS day FROM " . $wpdb->prefix . "wpf_hubspotusers WHERE 1 = 1 ORDER BY ultima DESC"));

        $m = isset($_GET['m']) ? (int) $_GET['m'] : 0;
        $d = isset($_GET['d']) ? (int) $_GET['d'] : 0;
        $a = isset($_GET['a']) ? (int) $_GET['a'] : 0;

?>
        <div class="alignleft actions">
          <select name="m" id="filter-by-date">
            <option<?php selected($m, 0); ?> value="0" data-rc="/wp-admin/admin.php?page=wpfunos-UsuariosHubspot">Todos los meses</option>
              <?php
              foreach ($months as $arc_row) {
                $month = zeroise($arc_row->month, 2);
                $year  = $arc_row->year;
                printf(
                  "<option %s value='%s' data-rc='%s'>%s</option>\n",
                  selected($m, $year . $month, false),
                  esc_attr($year . $month),
                  esc_attr('/wp-admin/admin.php?page=wpfunos-UsuariosHubspo&m=' . $year . $month),
                  sprintf(__('%1$s %2$d'), $wp_locale->get_month($month), $year)
                );
              }
              ?>
          </select>
          <select name="d" id="filter-by-day">
            <option<?php selected($d, 0); ?> value="0" data-rc="">Todos los dias</option>
              <?php
              foreach ($days as $arc_row) {
                $day = zeroise($arc_row->day, 2);
                $month = zeroise($arc_row->month, 2);
                $year  = $arc_row->year;
                printf(
                  "<option %s value='%s' data-rc='%s'>%s</option>\n",
                  selected($d, $year . $month . $day, false),
                  esc_attr($year . $month . $day),
                  esc_attr('&d=' . $year . $month . $day),
                  sprintf(__('%1$s %2$s %3$d'), $day, $wp_locale->get_month($month), $year)
                );
              }
              ?>
          </select>
          <select name="a" id="filter-by-ultima">
            <option<?php selected($a, 0); ?> value="0" data-rc="">Todos los dias última</option>
              <?php
              foreach ($ultima as $arc_row) {
                $day = zeroise($arc_row->day, 2);
                $month = zeroise($arc_row->month, 2);
                $year  = $arc_row->year;
                printf(
                  "<option %s value='%s' data-rc='%s'>%s</option>\n",
                  selected($a, $year . $month . $day, false),
                  esc_attr($year . $month . $day),
                  esc_attr('&a=' . $year . $month . $day),
                  sprintf(__('%1$s %2$s %3$d'), $day, $wp_locale->get_month($month), $year)
                );
              }
              ?>
          </select>

          <a href="javascript:void(0)" class="button" onclick="window.location.href =
        jQuery('#filter-by-date option:selected').data('rc') +
        jQuery('#filter-by-day option:selected').data('rc') +
        jQuery('#filter-by-ultima option:selected').data('rc') ;
        ">Filtrar</a>
        </div>
<?php
        break;
      case 'bottom':
        // Your html code to output
        break;
    }
  }
}
