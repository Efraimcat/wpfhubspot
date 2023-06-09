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
class Wpfhubspot_Admin_Forms extends Wpfhubspot_Admin {
  public function __construct( ) {
    $this->FormsUrl =   'https://api.hsforms.com/submissions/v3/integration/secure/submit/25640857/';
    $this->CreateUrl =   'https://api.hubapi.com/crm/v3/objects/contacts/';
    $this->hubspotkey = get_option( 'wpfhubspot_APIHubspotKeyHubspot' );

    add_action( 'wpfhubspot-send-form', array( $this,'wpfhubspotSendForm' ), 10, 1 );
  }

  //POST https://api.hsforms.com/submissions/v3/integration/secure/submit/:portalId/:formGuid
  //https://legacydocs.hubspot.com/docs/methods/forms/submit_form
  //https://legacydocs.hubspot.com/docs/methods/forms/submit_form_v3_authentication#:~:text=As%20this%20API%20is%20authenticated%2C

  /**
  *	 add_action( 'wpfhubspot-send-form', array( $this,'wpfhubspotSendForm' ), 10, 1 );
  *  do_action('wpfhubspot-send-form', $params );
  */
  public function wpfhubspotSendForm( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    $this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
    $this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
    $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotSendForm: ' .$params["email"]. ' utk: ' .$params['hubspotutk'] ) );
    //

    //if( $userIP == get_option( 'wpfunos_IpHubspot' ) && $params["email"] != get_option( 'wpfunos_EmailHubspot' ) && $params['hubspotutk'] == get_option( 'wpfunos_UtkHubspot' ) ){
    if( $userIP == get_option( 'wpfunos_IpHubspot' ) && $params["email"] != get_option( 'wpfunos_EmailHubspot' ) &&  stripos(get_option( 'wpfunos_UtkHubspot' ), $params['hubspotutk']) !== false ){
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
    //if( $params['accion'] == 'Datos usuario funerarias' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias llamamos' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias llamar' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias Presupuesto' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias financiación' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias filtros destino' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias filtros ataúd' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias filtros velatorio' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias filtros ceremonia' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario funerarias filtros distancia' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    //if( $params['accion'] == 'Datos usuario aseguradoras' )$formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';
    //if( $params['accion'] == 'Datos usuario aseguradoras llamamos' )$formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';
    //if( $params['accion'] == 'Datos usuario aseguradoras presupuesto' )$formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';
    //if( $params['accion'] == 'Datos usuario aseguradoras Cold Lead' )$formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';

    $formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( stripos( $params['accion'], 'Datos usuario funerarias' ) !== false ) $formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( stripos( $params['accion'], 'Datos usuario aseguradoras' ) !== false ) $formGuid = 'cbd958b7-dcf6-415f-a4e8-024bf6c342f3';
    if( $params['accion'] == 'Te llamamos gratis' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( $params['accion'] == 'Te llamamos gratis Landings' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( $params['accion'] == 'Asesoramiento gratuito' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( stripos( $params['accion'], 'ECommerce' ) !== false ) $formGuid = 'f6d6aced-19a1-4d4c-b8c2-78d63a73b518';

    $date = new DateTimeImmutable();
    $URLhubspot = $this->FormsUrl . $formGuid ;
    $headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
    $body = '{ "submittedAt": "'.(int)$date->format('Uv').'","fields": [';
      if( strlen( $params["email"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "email","value": "' .sanitize_text_field( $params["email"] ). '"},';
      if( strlen( $params["nombre"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "firstname","value": "' .sanitize_text_field( $params["nombre"] ). '"},';
      if( strlen( $params["telefono"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "phone","value": "' .sanitize_text_field( $params["telefono"] ). '"},';
      if( strlen( $params["donde"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "donde","value": "' .sanitize_text_field( $params["donde"] ). '"},';
      if( strlen( $params["distancia"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "distancia","value": "' .sanitize_text_field( $params["distancia"] ). '"},';
      if( strlen( $params["cuando"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "cuando","value": "' .sanitize_text_field( $params["cuando"] ). '"},';
      if( strlen( $params["destino"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "destino","value": "' .sanitize_text_field( $params["destino"] ). '"},';
      if( strlen( $params["ataud"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ataud","value": "' .sanitize_text_field( $params["ataud"] ). '"},';
      if( strlen( $params["velatorio"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "velatorio","value": "' .sanitize_text_field( $params["velatorio"] ). '"},';
      if( strlen( $params["ceremonia"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ceremonia","value": "' .sanitize_text_field( $params["ceremonia"] ). '"},';
      if( strlen( $params["filtro"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "filtro","value": "' .sanitize_text_field( $params["filtro"] ). '"},';
      if( strlen( $params["referencia"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "referencia","value": "' .sanitize_text_field( $params["referencia"] ). '"},';
      if( strlen( $params["servicio"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "servicio","value": "' .sanitize_text_field( $params["servicio"] ). '"},';
      if( strlen( $params["mensaje"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "mensaje","value": "' .sanitize_text_field( $params["mensaje"] ). '"},';
      if( strlen( $params["precio"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "precio","value": "' .sanitize_text_field( $params["precio"] ). '"},';
      if( strlen( $params["entrada"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "entrada","value": "' .sanitize_text_field( $params["entrada"] ). '"},';
      if( strlen( $params["financiar"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "financiar","value": "' .sanitize_text_field( $params["financiar"] ). '"},';

      if( strlen( $params["address"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "address","value": "' .sanitize_text_field( $params["address"] ). '"},';
      if( strlen( $params["city"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "city","value": "' .sanitize_text_field( $params["city"] ). '"},';
      if( strlen( $params["zip"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "zip","value": "' .sanitize_text_field( $params["zip"] ). '"},';
      if( strlen( $params["state"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "state","value": "' .sanitize_text_field( $params["state"] ). '"},';

      if( strlen( $params["ecommerce_nombre_difunto"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_nombre_difunto","value": "' .sanitize_text_field( $params["ecommerce_nombre_difunto"] ). '"},';
      if( strlen( $params["ecommerce_nif"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_nif","value": "' .sanitize_text_field( $params["ecommerce_nif"] ). '"},';
      if( strlen( $params["ecommerce_nif_difunto"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_nif_difunto","value": "' .sanitize_text_field( $params["ecommerce_nif_difunto"] ). '"},';
      if( strlen( $params["ecommerce_notas_pedido"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_notas_pedido","value": "' .sanitize_text_field( $params["ecommerce_notas_pedido"] ). '"},';
      if( strlen( $params["ecommerce_pedido"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_pedido","value": "' .sanitize_text_field( $params["ecommerce_pedido"] ). '"},';
      if( strlen( $params["ecommerce_producto"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_producto","value": "' .sanitize_text_field( $params["ecommerce_producto"] ). '"},';
      if( strlen( $params["ecommerce_subtotal"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_subtotal","value": "' .sanitize_text_field( $params["ecommerce_subtotal"] ). '"},';
      if( strlen( $params["ecommerce_total"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_total","value": "' .sanitize_text_field( $params["ecommerce_total"] ). '"},';
      if( strlen( $params["ecommerce_impuestos"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_impuestos","value": "' .sanitize_text_field( $params["ecommerce_impuestos"] ). '"},';
      if( strlen( $params["ecommerce_metodo_pago"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ecommerce_metodo_pago","value": "' .sanitize_text_field( $params["ecommerce_metodo_pago"] ). '"},';

      if( strlen( $params["ok"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ok","value": "' .sanitize_text_field( $params["ok"] ). '"},';
      $body .= '{"objectTypeId": "0-1", "name": "accion","value": "' .sanitize_text_field( $params["accion"] ). '"},';
      $body .= '{"objectTypeId": "0-1", "name": "ip","value": "' .sanitize_text_field( $userIP ). '"}],';
      $body .= '"context": { "hutk": "' .$params['hubspotutk']. '", "pageUri": "' .$params['pageUri']. '", "pageName": "' .$params['pageId']. '", "ipAddress": "' .sanitize_text_field( $userIP ). '" },';
      $body .= '"legalConsentOptions": { "consent": { "consentToProcess": true, "text": "Acepto el Aviso legal y la Política de privacidad.", "communications": [{ "value": true, "subscriptionTypeId": 999, "text": "Acepto el Aviso legal y la Política de privacidad." }]}}}';

    $request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'POST'  ) );
    //$userAPIMessage = apply_filters('wpfunos_dumplog', $request );
    //$this->custom_logs(  $userAPIMessage  );
    //$this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
    $bodyrequest = json_decode( $request['body'] );
    $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );

  }

  /**
  * Usuarios que llegan sin hubspotutk.
  */
  public function wpfhubspotCreateContact( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    $this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
    $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotCreateContact: ' .$params["email"] ) );

    $URLhubspot = $this->CreateUrl ;
    $headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
    $body = '{"properties": {';
      if( strlen( $params["email"]  ) > 1 ) $body .= ' "email": "' .sanitize_text_field( $params["email"] ). '",'  ;
      if( strlen( $params["nombre"]  ) > 1 ) $body .= ' "firstname": "' .sanitize_text_field( $params["nombre"] ). '",'  ;
      if( strlen( $params["telefono"]  ) > 1 ) $body .= ' "phone": "' .sanitize_text_field( $params["telefono"] ). '",'  ;
      if( strlen( $params["donde"]  ) > 1 ) $body .= ' "donde": "' .sanitize_text_field( $params["donde"] ). '",'  ;
      if( strlen( $params["distancia"]  ) > 1 ) $body .= ' "distancia": "' .sanitize_text_field( $params["distancia"] ). '",'  ;
      if( strlen( $params["cuando"]  ) > 1 ) $body .= ' "cuando": "' .sanitize_text_field( $params["cuando"] ). '",'  ;
      if( strlen( $params["destino"]  ) > 1 ) $body .= ' "destino": "' .sanitize_text_field( $params["destino"] ). '",'  ;
      if( strlen( $params["ataud"]  ) > 1 ) $body .= ' "ataud": "' .sanitize_text_field( $params["ataud"] ). '",'  ;
      if( strlen( $params["velatorio"]  ) > 1 ) $body .= ' "velatorio": "' .sanitize_text_field( $params["velatorio"] ). '",'  ;
      if( strlen( $params["ceremonia"]  ) > 1 ) $body .= ' "ceremonia": "' .sanitize_text_field( $params["ceremonia"] ). '",'  ;
      if( strlen( $params["filtro"]  ) > 1 ) $body .= ' "filtro": "' .sanitize_text_field( $params["filtro"] ). '",'  ;
      if( strlen( $params["referencia"]  ) > 1 ) $body .= ' "referencia": "' .sanitize_text_field( $params["referencia"] ). '",'  ;
      if( strlen( $params["servicio"]  ) > 1 ) $body .= ' "servicio": "' .sanitize_text_field( $params["servicio"] ). '",'  ;
      if( strlen( $params["mensaje"]  ) > 1 ) $body .= ' "mensaje": "' .sanitize_text_field( $params["mensaje"] ). '",'  ;
      if( strlen( $params["precio"]  ) > 1 ) $body .= ' "precio": "' .sanitize_text_field( $params["precio"] ). '",'  ;
      if( strlen( $params["entrada"]  ) > 1 ) $body .= ' "entrada": "' .sanitize_text_field( $params["entrada"] ). '",'  ;
      if( strlen( $params["financiar"]  ) > 1 ) $body .= ' "financiar": "' .sanitize_text_field( $params["financiar"] ). '",'  ;

      if( strlen( $params["address"]  ) > 1 ) $body .= ' "address": "' .sanitize_text_field( $params["address"] ). '",'  ;
      if( strlen( $params["city"]  ) > 1 ) $body .= ' "city": "' .sanitize_text_field( $params["city"] ). '",'  ;
      if( strlen( $params["zip"]  ) > 1 ) $body .= ' "zip": "' .sanitize_text_field( $params["zip"] ). '",'  ;
      if( strlen( $params["state"]  ) > 1 ) $body .= ' "state": "' .sanitize_text_field( $params["state"] ). '",'  ;

      if( strlen( $params["ecommerce_nombre_difunto"]  ) > 1 ) $body .= ' "ecommerce_nombre_difunto": "' .sanitize_text_field( $params["ecommerce_nombre_difunto"] ). '",'  ;
      if( strlen( $params["ecommerce_nif"]  ) > 1 ) $body .= ' "ecommerce_nif": "' .sanitize_text_field( $params["ecommerce_nif"] ). '",'  ;
      if( strlen( $params["ecommerce_nif_difunto"]  ) > 1 ) $body .= ' "ecommerce_nif_difunto": "' .sanitize_text_field( $params["ecommerce_nif_difunto"] ). '",'  ;
      if( strlen( $params["ecommerce_notas_pedido"]  ) > 1 ) $body .= ' "ecommerce_notas_pedido": "' .sanitize_text_field( $params["ecommerce_notas_pedido"] ). '",'  ;
      if( strlen( $params["ecommerce_pedido"]  ) > 1 ) $body .= ' "ecommerce_pedido": "' .sanitize_text_field( $params["ecommerce_pedido"] ). '",'  ;
      if( strlen( $params["ecommerce_producto"]  ) > 1 ) $body .= ' "ecommerce_producto": "' .sanitize_text_field( $params["ecommerce_producto"] ). '",'  ;
      if( strlen( $params["ecommerce_subtotal"]  ) > 1 ) $body .= ' "ecommerce_subtotal": "' .sanitize_text_field( $params["ecommerce_subtotal"] ). '",'  ;
      if( strlen( $params["ecommerce_total"]  ) > 1 ) $body .= ' "ecommerce_total": "' .sanitize_text_field( $params["ecommerce_total"] ). '",'  ;
      if( strlen( $params["ecommerce_impuestos"]  ) > 1 ) $body .= ' "ecommerce_impuestos": "' .sanitize_text_field( $params["ecommerce_impuestos"] ). '",'  ;
      if( strlen( $params["ecommerce_metodo_pago"]  ) > 1 ) $body .= ' "ecommerce_metodo_pago": "' .sanitize_text_field( $params["ecommerce_metodo_pago"] ). '",'  ;

      if( strlen( $params["ok"]  ) > 1 ) $body .= ' "ok": "' .sanitize_text_field( $params["ok"] ). '",'  ;
      if( strlen( $params["accion"]  ) > 1 ) $body .= ' "accion": "' .sanitize_text_field( $params["accion"] ). '",'  ;
      $body .= ' "ip": "' .sanitize_text_field( $userIP ). '",'  ;
      $body .= ' "hs_legal_basis": "Freely given consent from contact"}}'  ;

    $request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'POST'  ) );
    //$userAPIMessage = apply_filters('wpfunos_dumplog', $request );
    //$this->custom_logs(  $userAPIMessage  );
    $this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
    $bodyrequest = json_decode( $request['body'] );
    $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );

    //"message": "Contact already exists. Existing ID: 20825901",
    //            01234567890123456789012345678901234567890
    //                      1         2         3      ^
    //substr("abcdef", 2, -1);  // returns "cde"
    if( $bodyrequest['category'] === 'CONFLICT'){
      $contactID = substr ( $bodyrequest['message'], 37 );
      $request = wp_remote_post( $URLhubspot.$contactID, array( 'headers' => $headers, 'body' => $body, 'method' => 'PATCH' ) );
      $bodyrequest = json_decode( $request['body'] );
      $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest Update: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );
    }
  }
  /**
  *$params = array(
  *  'nombre' => $wpfnombre,
  *  'email' => $wpfemail,
  *  'telefono' => $Telefono,
  *  'donde' => $wpfubic,
  *  'cuando' => $wpfcuando,
  *  'destino' => $wpfdestino,
  *  'ataud' => $wpfataud,
  *  'velatorio' => $wpfvelatorio,
  *  'ceremonia' => $wpfceremonia,
  *  'referencia' => $wpfnewref,
  *  'accion' => 'Datos usuario funerarias',
  *  'ip' => $userIP,
  *  'hubspotutk' => $hubspotutk,
  *);
  *do_action('wpfhubspot-send-form', $params );
  */


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
