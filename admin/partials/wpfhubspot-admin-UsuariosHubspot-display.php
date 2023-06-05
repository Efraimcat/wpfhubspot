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
    <strong>Total: </strong><?php echo number_format_i18n ( count( $todos ) ); ?>
  </div>
  <div id="wpfhubspot-list-table">
    <div id="wpfhubspot-list-table-body">
      <form id="wpfhubspot-list-table-form" method="get">
        <?php
        $this->wpfhubspot_Admin_Usuarios->search_box( __( 'Buscar', 'wpfhubspot' ), 'wpfhubspot-find');
        $this->wpfhubspot_Admin_Usuarios->display();;
        ?>
      </form>
    </div>
  </div>
</div>
