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
* @subpackage Wpfunos/admin/partials/registerAndBuild
* @author     Efraim Bayarri <efraim@efraim.cat>
*/
//wpfunos-admin-registerAndBuildAPIHubspot.php
add_settings_section(
  'wpfunos_APIHubspot_section',    							// ID used to identify this section and with which to register options
  'Datos conexiones API Hubspot',        						// Title to be displayed on the administration page
  array( $this, 'wpfunos_display_APIHubspot_account' ), 		// Callback used to render the description of the section
  'wpfunos_APIHubspot_settings'                 				// Page on which to add this section of options
);
add_settings_field(
  'wpfunos_APIHubspotActivaHubspot',
  'Hubspot activo <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfunos_APIHubspotActivaHubspot)</h6>',
  array( $this, 'wpfunos_render_settings_field' ),
  'wpfunos_APIHubspot_settings',
  'wpfunos_APIHubspot_section',
  array('type' => 'input','subtype' => 'checkbox','id' => 'wpfunos_APIHubspotActivaHubspot','name' => 'wpfunos_APIHubspotActivaHubspot','required' => 'true','get_options_list' => '','value_type' => 'normal','wp_data' => 'option')
);
add_settings_field(
  'wpfunos_APIHubspotKeyHubspot',
  'API Key Hubspot <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfunos_APIHubspotKeyHubspot)</h6>',
  array( $this, 'wpfunos_render_settings_field' ),
  'wpfunos_APIHubspot_settings',
  'wpfunos_APIHubspot_section',
  array('type' => 'input','subtype' => 'password','id' => 'wpfunos_APIHubspotKeyHubspot','name' => 'wpfunos_APIHubspotKeyHubspot','required' => 'true','get_options_list' => '','value_type' => 'normal','wp_data' => 'option')
);
// APIHubspotActionsUser (email)
// APIHubspotExlcudedUsers (email)
add_settings_field(
  'wpfunos_APIHubspotActionsUser',
  'Usuario asignado a las tareas <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfunos_APIHubspotActionsUser)</h6><h6 style="font-weight: 400;font-size: 12px;">Dirección email</h6>',
  array( $this, 'wpfunos_render_settings_field' ),
  'wpfunos_APIHubspot_settings',
  'wpfunos_APIHubspot_section',
  array('type' => 'input','subtype' => 'text','id' => 'wpfunos_APIHubspotActionsUser','name' => 'wpfunos_APIHubspotActionsUser','required' => 'true','get_options_list' => '','value_type' => 'normal','wp_data' => 'option')
);
add_settings_field(
  'wpfunos_APIHubspotExlcudedUsers',
  'Direcciones de correo excluidas de Hubspot <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfunos_APIHubspotExlcudedUsers)</h6><h6 style="font-weight: 400;font-size: 12px;">Lista de direcciones email separadas mediante comas</h6>',
  array( $this, 'wpfunos_render_settings_field' ),
  'wpfunos_APIHubspot_settings',
  'wpfunos_APIHubspot_section',
  array('type' => 'input','subtype' => 'text','id' => 'wpfunos_APIHubspotExlcudedUsersr','name' => 'wpfunos_APIHubspotExlcudedUsers','required' => 'true','get_options_list' => '','value_type' => 'normal','wp_data' => 'option')
);


register_setting('wpfunos_APIHubspot_settings', 'wpfunos_APIHubspotKeyHubspot');
register_setting('wpfunos_APIHubspot_settings', 'wpfunos_APIHubspotActivaHubspot');
register_setting('wpfunos_APIHubspot_settings', 'wpfunos_APIHubspotActionsUser');
register_setting('wpfunos_APIHubspot_settings', 'wpfunos_APIHubspotExlcudedUsers');
