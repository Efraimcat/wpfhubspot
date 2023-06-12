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

class Wpfhubspot_Admin_Forms extends Wpfhubspot_Admin {
  public function __construct( ) {
    $this->FormsUrl =   'https://api.hsforms.com/submissions/v3/integration/secure/submit/25640857/';
    $this->CreateUrl =   'https://api.hubapi.com/crm/v3/objects/contacts/';
    $this->hubspotkey = get_option( 'wpfhubspot_APIHubspotKeyHubspot' );
    $this->names = array(
      'ataud', 'ceremonia', 'cuando', 'destino', 'distancia', 'donde', 'email', 'entrada', 'filtro', 'firstname', 'financiar',
      'mensaje', 'nacimiento', 'ok', 'phone', 'precio', 'referencia', 'servicio', 'velatorio',

      'address', 'city', 'state', 'zip',
      'ecommerce_impuestos', 'ecommerce_metodo_pago', 'ecommerce_nif_difunto', 'ecommerce_nif', 'ecommerce_nombre_difunto',
      'ecommerce_notas_pedido', 'ecommerce_pedido', 'ecommerce_producto', 'ecommerce_subtotal', 'ecommerce_total',
    );

    add_action( 'wpfhubspot-send-form', array( $this,'wpfhubspotSendForm' ), 10, 1 );
  }

  /**
  *	 add_action( 'wpfhubspot-send-form', array( $this,'wpfhubspotSendForm' ), 10, 1 );
  *  do_action('wpfhubspot-send-form', $params );
  */
  public function wpfhubspotSendForm( $params ){
    if( stripos( get_option( 'wpfunos_HubspotEmailNo' ), $params["email"] ) !== false ) return;
    $userIP = apply_filters('wpfunos_userIP','dummy');
    //
    $this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
    $this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
    $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotSendForm: ' .$params["email"]. ' utk: ' .$params['hubspotutk']. ' -> ' .$params["accion"] ) );
    //
    if(
      $params["email"] != get_option( 'wpfunos_EmailHubspot' ) &&
      stripos( get_option( 'wpfunos_IpHubspot' ), $userIP ) !== false  &&
      stripos( get_option( 'wpfunos_UtkHubspot' ), $params['hubspotutk'] ) !== false
    ){
      if ( is_user_logged_in() ) {
        $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotSendForm: Alejandro conectado.' ) );
      }else{
        $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotSendForm: Alejandro sin conectar.' ) );
      }
      $this->custom_logs( $this->dumpPOST($userIP .' - Entrada ' .get_option( 'wpfunos_IpHubspot' ). '. utk: ' .$params['hubspotutk'] ) );
      global $wpdb;
      $table_name = $wpdb->prefix . 'wpf_hubspotusers';
      $results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $params["email"] ), ARRAY_A);
      if ( $results ) {
        foreach ( $results as $entrada ){
          $params['hubspotutk'] = $entrada['hubspotutk'];
          $this->custom_logs( $this->dumpPOST($userIP .' - Cambio a hubspotutk guardado. ' .$entrada['hubspotutk'] ) );
        }
      }else{
        $this->custom_logs( $this->dumpPOST($userIP .' - ERROR: No tenemos hubspotutk guardado.' ) );
        $this->wpfhubspotCreateContact( $params );
        return;
      }
    }
    //
    if ( $params['hubspotutk'] == '') {
      $this->custom_logs( $this->dumpPOST($userIP .' - NO hubspotutk' ) );
      $this->wpfhubspotCreateContact( $params );
      return;
    }
    //
    $formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( stripos( $params['accion'], 'Datos usuario funerarias' ) !== false ) $formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( stripos( $params['accion'], 'Datos usuario aseguradoras' ) !== false ) $formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';
    if( stripos( $params['accion'], 'ECommerce' ) !== false ) $formGuid = 'f6d6aced-19a1-4d4c-b8c2-78d63a73b518';
    if( $params['accion'] == 'Te llamamos gratis' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( $params['accion'] == 'Te llamamos gratis Landings' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( $params['accion'] == 'Asesoramiento gratuito' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    //
    $date = new DateTimeImmutable();
    $URLhubspot = $this->FormsUrl . $formGuid ;
    $headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
    $body = '{ "submittedAt": "'.(int)$date->format('Uv').'","fields": ['; //'}'
    foreach ($this->names as $value) {
      if( strlen( $params[$value]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "' .$value. '","value": "' .sanitize_text_field( $params[$value] ). '"},';
    }
    $body .= '{"objectTypeId": "0-1", "name": "accion","value": "' .sanitize_text_field( $params["accion"] ). '"},';
    $body .= '{"objectTypeId": "0-1", "name": "ip","value": "'     .sanitize_text_field( $userIP ).           '"}],';
    $body .= '"context": { "hutk": "' .$params['hubspotutk']. '", "pageUri": "' .$params['pageUri']. '", "pageName": "' .$params['pageId']. '", "ipAddress": "' .sanitize_text_field( $userIP ). '" },';
    $body .= '"legalConsentOptions": { "consent": { "consentToProcess": true, "text": "Acepto el Aviso legal y la Política de privacidad.", "communications": [{ "value": true, "subscriptionTypeId": 999, "text": "Acepto el Aviso legal y la Política de privacidad." }]}}}';

    //$this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
    $request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'POST'  ) );
    //$userAPIMessage = apply_filters('wpfunos_dumplog', $request );
    //$this->custom_logs(  $userAPIMessage  );
    $bodyrequest = json_decode( $request['body'] );
    $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );
  }

  /**
  * Usuarios que llegan sin hubspotutk.
  */
  public function wpfhubspotCreateContact( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    $this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
    $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotCreateContact: ' .$params["email"] ). ' -> ' .$params["accion"] );

    $URLhubspot = $this->CreateUrl ;
    $headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
    $body = '{"properties": {'; //'}'
    foreach ($this->names as $value) {
      if( strlen( $params[$value] ) > 1 ) $body .= ' "' .$value. '": "' .sanitize_text_field( $params[$value] ). '",' ;
    }
    $body .= ' "accion": "' .sanitize_text_field( $params["accion"] ). '",'  ;
    $body .= ' "ip": "' .sanitize_text_field( $userIP ). '",'  ;
    $body .= ' "hs_legal_basis": "Freely given consent from contact"}}'  ;

    $this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
    $request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'POST'  ) );
    $bodyrequest = json_decode( $request['body'] );
    $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest nuevo contacto: Hubspot id: ' .$bodyrequest['id'] ) );

    //"message": "Contact already exists. Existing ID: 20825901",
    //            01234567890123456789012345678901234567890
    //                      1         2         3      ^
    //substr("abcdef", 2, -1);  // returns "cde"
    if( $bodyrequest['category'] === 'CONFLICT'){
      $contactID = substr ( $bodyrequest['message'], 37 );
      $request = wp_remote_post( $URLhubspot.$contactID, array( 'headers' => $headers, 'body' => $body, 'method' => 'PATCH' ) );
      $bodyrequest = json_decode( $request['body'] );
      $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest crear contacto: Hubspot id: ' .$bodyrequest['id'] ) );
    }
  }

  /*********************************/
  /*****  UTILS               ******/
  /*********************************/
  /**
  * Utility: create entry in the log file.
  * $this->custom_logs( $this->dumpPOST($message) );
  */
  private function custom_logs($message){
    $upload_dir = wp_upload_dir();
    if (is_array($message)) {
      $message = json_encode($message);
    }
    if (!file_exists( $upload_dir['basedir'] . '/wpfunos-logs') ) {
      mkdir( $upload_dir['basedir'] . '/wpfunos-logs' );
    }
    $time = current_time("d-M-Y H:i:s:v");
    $ban = "#$time: $message\r\n";
    $file = $upload_dir['basedir'] . '/wpfunos-logs/wpfunos-hubspotlog-' . current_time("Y-m-d") . '.log';
    $open = fopen($file, "a");
    fputs($open, $ban);
    fclose( $open );
  }

  private function dumpPOST($data, $indent=0) {
    $retval = '';
    $prefix=\str_repeat(' |  ', $indent);
    if (\is_numeric($data)) $retval.= "Number: $data";
    elseif (\is_string($data)) $retval.= "String: '$data'";
    elseif (\is_null($data)) $retval.= "NULL";
    elseif ($data===true) $retval.= "TRUE";
    elseif ($data===false) $retval.= "FALSE";
    elseif (is_array($data)) {
      $indent++;
      foreach($data AS $key => $value) {
        $retval.= "\r\n$prefix [$key] = ";
        $retval.= $this->dump($value, $indent);
      }
    }
    elseif (is_object($data)) {
      $retval.= "Object (".get_class($data).")";
      $indent++;
      foreach($data AS $key => $value) {
        $retval.= "\r\n$prefix $key -> ";
        $retval.= $this->dump($value, $indent);
      }
    }
    return $retval;
  }
}
