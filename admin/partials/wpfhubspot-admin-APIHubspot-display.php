<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
/**
* The admin-specific functionality of the plugin.
*
* @link       https://github.com/Efraimcat/wpfhubspot/
* @since      1.0.0
*
* @package
* @subpackage wpfhubspot/admin/partials
* @author     Efraim Bayarri <efraim@efraim.cat>
*/
?>
<div class="wrap">
  <div id="icon-themes" class="icon32"></div>
  <h2><?php esc_html_e( get_admin_page_title() .' '.$this->version. ' ('  .get_option( "wpf_db_version" ). ')' ); ?></h2>
  <h2><?php esc_html_e( 'API Hubspot WpFunos', 'wpfhubspot' ); ?></h2>
  <div style="margin-top: 10px;margin-bottom: 10px;"><?php echo date_i18n( 'd F Y H:i:s', current_time( 'timestamp', 0 ) );?></div>
  <hr/>
  <?php settings_errors(); ?>
  <form method="POST" action="options.php">
    <?php
    settings_fields('wpfhubspot_APIHubspot_settings');
    do_settings_sections('wpfhubspot_APIHubspot_settings');
    ?>
    <hr />
    <?php submit_button(); ?>
  </form>
</div>
