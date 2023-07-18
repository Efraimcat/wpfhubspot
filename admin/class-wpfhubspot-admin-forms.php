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
 * @package    Wpfhubspot
 * @subpackage Wpfhubspot/admin
 */
//POST https://api.hsforms.com/submissions/v3/integration/secure/submit/:portalId/:formGuid
//https://legacydocs.hubspot.com/docs/methods/forms/submit_form
//https://legacydocs.hubspot.com/docs/methods/forms/submit_form_v3_authentication#:~:text=As%20this%20API%20is%20authenticated%2C

// ACCIONES
//'Datos usuario funerarias'
//'Datos usuario funerarias llamamos'
//'Datos usuario funerarias llamar'
//'Datos usuario funerarias Presupuesto'
//'Datos usuario funerarias financiación'
//'Datos usuario funerarias filtros destino'
//'Datos usuario funerarias filtros ataúd'
//'Datos usuario funerarias filtros velatorio'
//'Datos usuario funerarias filtros ceremonia'
//'Datos usuario funerarias filtros distancia'
//'Datos usuario aseguradoras'
//'Datos usuario aseguradoras llamamos'
//'Datos usuario aseguradoras presupuesto'
//'Datos usuario aseguradoras Cold Lead'

class Wpfhubspot_Admin_Forms extends Wpfhubspot_Admin
{
  public function __construct()
  {
    $this->FormsUrl =   'https://api.hsforms.com/submissions/v3/integration/secure/submit/25640857/';
    $this->CreateUrl =   'https://api.hubapi.com/crm/v3/objects/contacts/';
    $this->hubspotkey = get_option('wpfhubspot_APIHubspotKeyHubspot');
    $this->names = array(
      'ataud', 'ceremonia', 'cuando', 'destino', 'distancia', 'donde', 'email', 'entrada', 'filtro', 'firstname', 'financiar',
      'importe', 'mensaje', 'nacimiento', 'phone', 'precio', 'provincia', 'referencia', 'velatorio',

      'servicio', 'serviciodireccion', 'servicioempresa', 'serviciopoblacion', 'servicioprovincia', 'serviciotelefono', 'serviciotitulo',

      'address', 'city', 'state', 'zip',
      'ecommerce_impuestos', 'ecommerce_metodo_pago', 'ecommerce_nif_difunto', 'ecommerce_nif', 'ecommerce_nombre_difunto',
      'ecommerce_notas_pedido', 'ecommerce_pedido', 'ecommerce_producto', 'ecommerce_subtotal', 'ecommerce_total',
    );

    add_action('wpfhubspot-send-form', array($this, 'wpfhubspotSendForm'), 10, 1);
  }

  /**
   *	 add_action( 'wpfhubspot-send-form', array( $this,'wpfhubspotSendForm' ), 10, 1 );
   *  do_action('wpfhubspot-send-form', $params );
   */
  public function wpfhubspotSendForm($params)
  {
    if (stripos(get_option('wpfunos_HubspotEmailNo'), $params["email"]) !== false) return;
    $userIP = apply_filters('wpfunos_userIP', 'dummy');
    //
    $this->custom_logs($this->dumpPOST($userIP . ' - =========='));
    $this->custom_logs($this->dumpPOST($userIP . ' - =========='));
    $this->custom_logs($this->dumpPOST($userIP . ' - wpfhubspotSendForm: ' . $params["email"] . ' utk: ' . $params['hubspotutk'] . ' -> ' . $params["accion"]));
    //
    if ($params['hubspotutk'] == '') {
      $params['hubspotutk'] = 'fe23' . apply_filters('wpfunos_generate_random_string', 28);
      $this->custom_logs($this->dumpPOST($userIP . ' - NO hubspotutk:  Nuevo: ' . $params['hubspotutk']));
    }
    //
    $userid = 0;
    $colaborador = 'No';
    if (is_user_logged_in() && current_user_can('funos_colaborador')) {
      global $current_user;
      wp_get_current_user();
      $colaborador = $current_user->display_name;
      $userid = get_current_user_id();
      $params['hubspotutk'] = 'fe23' . apply_filters('wpfunos_generate_random_string', 28);
      $this->custom_logs($this->dumpPOST($userIP . ' - Colaborador: ' . $userid . ' (' . $colaborador . ') UTK: ' . $params['hubspotutk']));
    }
    //
    $formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if (stripos($params['accion'], 'Datos usuario funerarias') !== false) $formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if (stripos($params['accion'], 'Datos usuario aseguradoras') !== false) $formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';
    if (stripos($params['accion'], 'ECommerce') !== false) $formGuid = 'f6d6aced-19a1-4d4c-b8c2-78d63a73b518';
    if ($params['accion'] == 'Te llamamos gratis') $formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if ($params['accion'] == 'Te llamamos gratis Landings') $formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if ($params['accion'] == 'Asesoramiento gratuito') $formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if ($params['accion'] == 'Formulario pagina financiacion') $formGuid = '91f13f69-946b-4e00-80eb-f706a991561e';
    //
    $date = new DateTimeImmutable();
    $URLhubspot = $this->FormsUrl . $formGuid;
    $headers = array('Authorization' => 'Bearer ' . $this->hubspotkey, 'Content-Type' => 'application/json');
    $body = '{ "submittedAt": "' . (int)$date->format('Uv') . '","fields": [';
    foreach ($this->names as $nombre) {
      $valor = sanitize_text_field(str_replace(array("\'"), ' ', $params[$nombre]));
      if (strlen($params[$nombre]) > 1) $body .= '{"objectTypeId": "0-1", "name": "' . $nombre . '","value": "' . $valor . '"},';
    }
    $body .= '{"objectTypeId": "0-1", "name": "colaborador","value": "' . $colaborador . '"},';
    $body .= '{"objectTypeId": "0-1", "name": "userid","value": "' . $userid . '"},';
    $body .= '{"objectTypeId": "0-1", "name": "accion","value": "' . sanitize_text_field($params["accion"]) . '"},';
    $body .= '{"objectTypeId": "0-1", "name": "ip","value": "'     . sanitize_text_field($userIP) .           '"}],';
    $body .= '"context": { "hutk": "' . $params['hubspotutk'] . '", "pageUri": "' . $params['pageUri'] . '", "pageName": "' . $params['pageId'] . '", "ipAddress": "' . sanitize_text_field($userIP) . '" },';
    $body .= '"legalConsentOptions": { "consent": { "consentToProcess": true, "text": "Acepto el Aviso legal y la Política de privacidad.", "communications": [{ "value": true, "subscriptionTypeId": 999, "text": "Acepto el Aviso legal y la Política de privacidad." }]}}}';

    //$this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
    $request = wp_remote_post($URLhubspot, array('headers' => $headers, 'body' => $body, 'method' => 'POST'));
    //$userAPIMessage = apply_filters('wpfunos_dumplog', $request );
    //$this->custom_logs(  $userAPIMessage  );
    $bodyrequest = json_decode($request['body']);
    $this->custom_logs($this->dumpPOST($userIP . ' - $bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest)));
  }


  /*********************************/
  /*****  UTILS               ******/
  /*********************************/
  /**
   * Utility: create entry in the log file.
   * $this->custom_logs( $this->dumpPOST($message) );
   */
  private function custom_logs($message)
  {
    $upload_dir = wp_upload_dir();
    if (is_array($message)) {
      $message = json_encode($message);
    }
    if (!file_exists($upload_dir['basedir'] . '/wpfunos-logs')) {
      mkdir($upload_dir['basedir'] . '/wpfunos-logs');
    }
    $time = current_time("d-M-Y H:i:s:v");
    $ban = "#$time: $message\r\n";
    $file = $upload_dir['basedir'] . '/wpfunos-logs/wpfunos-hubspotlog-' . current_time("Y-m-d") . '.log';
    $open = fopen($file, "a");
    fputs($open, $ban);
    fclose($open);
  }

  private function dumpPOST($data, $indent = 0)
  {
    $retval = '';
    $prefix = \str_repeat(' |  ', $indent);
    if (\is_numeric($data)) $retval .= "Number: $data";
    elseif (\is_string($data)) $retval .= "String: '$data'";
    elseif (\is_null($data)) $retval .= "NULL";
    elseif ($data === true) $retval .= "TRUE";
    elseif ($data === false) $retval .= "FALSE";
    elseif (is_array($data)) {
      $indent++;
      foreach ($data as $key => $value) {
        $retval .= "\r\n$prefix [$key] = ";
        $retval .= $this->dumpPOST($value, $indent);
      }
    } elseif (is_object($data)) {
      $retval .= "Object (" . get_class($data) . ")";
      $indent++;
      foreach ($data as $key => $value) {
        $retval .= "\r\n$prefix $key -> ";
        $retval .= $this->dumpPOST($value, $indent);
      }
    }
    return $retval;
  }
}
