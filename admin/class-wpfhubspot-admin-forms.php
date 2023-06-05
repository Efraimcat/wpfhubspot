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
class Wpfhubspot_Admin_Forms extends Wpfhubspot_Admin {
  public function __construct( ) {
    $this->FormsUrl =   'https://api.hsforms.com/submissions/v3/integration/secure/submit/25640857/';
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
    $this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotSendForm: ' .$params["email"]) );
    //
    if( $userIP == '79.157.131.56' && $params["email"] != 'clientes@funos.es'){
      $this->custom_logs( $this->dumpPOST($userIP .' - Entrada 79.157.131.56') );
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
        return;
      }
    }
    //
    if ( $params['hubspotutk'] == '') {
      $this->custom_logs( $this->dumpPOST($userIP .' - NO hubspotutk' ) );
      return;
    }

    if( $params['accion'] == 'Datos usuario funerarias' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( $params['accion'] == 'Datos usuario funerarias llamamos' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( $params['accion'] == 'Datos usuario funerarias llamar' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( $params['accion'] == 'Datos usuario funerarias Presupuesto' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( $params['accion'] == 'Datos usuario funerarias financiación' )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';
    if( strpos( $params['accion'], 'Datos usuario funerarias filtros' ) )$formGuid = '482e3e8e-3001-477a-aa47-aab334f837b8';

    if( $params['accion'] == 'Te llamamos gratis' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( $params['accion'] == 'Te llamamos gratis Landings' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';
    if( $params['accion'] == 'Asesoramiento gratuito' )$formGuid = '832d7a8a-415c-4c7b-a3cf-c01edcdc4967';

    $date = new DateTimeImmutable();
    $URLhubspot = $this->FormsUrl . $formGuid ;
    $headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
    $body = '{ "submittedAt": "'.(int)$date->format('Uv').'","fields": [';
      if( strlen( $params["email"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "email","value": "' .sanitize_text_field( $params["email"] ). '"},';
      if( strlen( $params["nombre"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "firstname","value": "' .sanitize_text_field( $params["nombre"] ). '"},';
      if( strlen( $params["telefono"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "phone","value": "' .sanitize_text_field( $params["telefono"] ). '"},';
      if( strlen( $params["donde"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "donde","value": "' .sanitize_text_field( $params["donde"] ). '"},';
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
      if( strlen( $params["ok"]  ) > 1 ) $body .= '{"objectTypeId": "0-1", "name": "ok","value": "' .sanitize_text_field( $params["ok"] ). '"},';
      $body .= '{"objectTypeId": "0-1", "name": "accion","value": "' .sanitize_text_field( $params["accion"] ). '"},';
      $body .= '{"objectTypeId": "0-1", "name": "ip","value": "' .sanitize_text_field( $userIP ). '"}],';
      $body .= '"context": { "hutk": "' .$params['hubspotutk']. '", "pageUri": "' .$params['pageUri']. '", "pageName": "' .$params['pageId']. '", "ipAddress": "' .sanitize_text_field( $userIP ). '" },';
      $body .= '"legalConsentOptions": { "consent": { "consentToProcess": true, "text": "Acepto el Aviso legal y la Política de privacidad.", "communications": [{ "value": true, "subscriptionTypeId": 999, "text": "Acepto el Aviso legal y la Política de privacidad." }]}}}';

      $request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'POST'  ) );

      //$this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
      $bodyrequest = json_decode( $request['body'] );
      $this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );

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
