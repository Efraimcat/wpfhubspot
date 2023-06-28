<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Efraimcat/wpfhubspot/
 * @since      1.0.0
 *
 * @package    wpfhubspot
 * @subpackage wpfhubspot/admin/partials/registerAndBuild
 * @author     Efraim Bayarri <efraim@efraim.cat>
 */
//wpfhubspot-admin-registerAndBuildAPIHubspot.php
add_settings_section(
  'wpfhubspot_APIHubspot_section',                  // ID used to identify this section and with which to register options
  'Datos conexiones API Hubspot',                    // Title to be displayed on the administration page
  array($this, 'wpfhubspot_display_APIHubspot_account'),     // Callback used to render the description of the section
  'wpfhubspot_APIHubspot_settings'                         // Page on which to add this section of options
);
add_settings_field(
  'wpfhubspot_APIHubspotActivaHubspot',
  'Hubspot activo <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfhubspot_APIHubspotActivaHubspot)</h6>',
  array($this, 'wpfhubspot_render_settings_field'),
  'wpfhubspot_APIHubspot_settings',
  'wpfhubspot_APIHubspot_section',
  array('type' => 'input', 'subtype' => 'checkbox', 'id' => 'wpfhubspot_APIHubspotActivaHubspot', 'name' => 'wpfhubspot_APIHubspotActivaHubspot', 'required' => 'true', 'get_options_list' => '', 'value_type' => 'normal', 'wp_data' => 'option')
);
add_settings_field(
  'wpfhubspot_APIHubspotKeyHubspot',
  'API Key Hubspot <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfhubspot_APIHubspotKeyHubspot)</h6>',
  array($this, 'wpfhubspot_render_settings_field'),
  'wpfhubspot_APIHubspot_settings',
  'wpfhubspot_APIHubspot_section',
  array('type' => 'input', 'subtype' => 'password', 'id' => 'wpfhubspot_APIHubspotKeyHubspot', 'name' => 'wpfhubspot_APIHubspotKeyHubspot', 'required' => 'true', 'get_options_list' => '', 'value_type' => 'normal', 'wp_data' => 'option')
);
add_settings_field(
  'wpfhubspot_APIHubspotActionsUser',
  'Usuario asignado a las tareas <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfhubspot_APIHubspotActionsUser)</h6><h6 style="font-weight: 400;font-size: 12px;">Dirección email</h6>',
  array($this, 'wpfhubspot_render_settings_field'),
  'wpfhubspot_APIHubspot_settings',
  'wpfhubspot_APIHubspot_section',
  array('type' => 'input', 'subtype' => 'text', 'id' => 'wpfhubspot_APIHubspotActionsUser', 'name' => 'wpfhubspot_APIHubspotActionsUser', 'required' => 'true', 'get_options_list' => '', 'value_type' => 'normal', 'wp_data' => 'option')
);
add_settings_field(
  'wpfhubspot_APIHubspotExlcudedUsers',
  'Direcciones de correo excluidas de Hubspot <h6 style="font-style: italic;font-weight: 400;font-size: 12px;">(wpfhubspot_APIHubspotExlcudedUsers)</h6><h6 style="font-weight: 400;font-size: 12px;">Lista de direcciones email separadas mediante comas</h6>',
  array($this, 'wpfhubspot_render_settings_field'),
  'wpfhubspot_APIHubspot_settings',
  'wpfhubspot_APIHubspot_section',
  array('type' => 'input', 'subtype' => 'text', 'id' => 'wpfhubspot_APIHubspotExlcudedUsersr', 'name' => 'wpfhubspot_APIHubspotExlcudedUsers', 'required' => 'true', 'get_options_list' => '', 'value_type' => 'normal', 'wp_data' => 'option')
);


register_setting('wpfhubspot_APIHubspot_settings', 'wpfhubspot_APIHubspotKeyHubspot');
register_setting('wpfhubspot_APIHubspot_settings', 'wpfhubspot_APIHubspotActivaHubspot');
register_setting('wpfhubspot_APIHubspot_settings', 'wpfhubspot_APIHubspotActionsUser');
register_setting('wpfhubspot_APIHubspot_settings', 'wpfhubspot_APIHubspotExlcudedUsers');
