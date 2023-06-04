<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
/**
* The admin-specific functionality of the plugin.
*
* @link       https://github.com/Efraimcat/wpfunos/
* @since      1.0.0
*
* @package    Wpfunos
* @subpackage Wpfunos/admin
* @author     Efraim Bayarri <efraim@efraim.cat>
*/
?>
<div class="wrap">
  <h2><?php esc_html_e( get_admin_page_title() .' '.$this->version. ' ('  .get_option( "wpf_db_version" ). ')' ); ?></h2>
  <?php settings_errors(); ?>
  <h3><?php esc_html_e( 'WpHubspot', 'wpfhubspot' )?></h3>
  <div style="margin-top: 10px;margin-bottom: 10px;"><?php echo date_i18n( 'd F Y H:i:s', current_time( 'timestamp', 0 ) );?></div>
  <div>
    <strong>Total: </strong><?php //echo number_format_i18n ( count( $todos ) ); ?>
  </div>
  <div id="wpfhubspot_list_table">
    <div id="wpfhubspot_list_table-body">
      <form id="wpfhubspot_list_table-form" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php
        //$this->masterdatos_list_table->search_box( __( 'Buscar', 'wpfhubspot' ), 'wpfhubspot-find');
        //$this->masterdatos_list_table->display();
        ?>
      </form>
    </div>
  </div>
</div>
