<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Wpfhubspot
* @subpackage Wpfhubspot/admin
* @author     Efraim Bayarri <efraim@efraim.cat>
*/

require_once 'class-wpfhubspot-admin-forms.php';
require_once 'class-wpfhubspot-admin-usuarios.php';

class Wpfhubspot_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->hubspotkey = get_option( 'wpfhubspot_APIHubspotKeyHubspot' );
		$this->ContactsUrl =   'https://api.hubapi.com/crm/v3/objects/contacts';
		$this->DealsUrl =      'https://api.hubapi.com/crm/v3/objects/deals';
		$this->TicketsUrl =    'https://api.hubapi.com/crm/v3/objects/tickets';
		$this->PipelinesUrl =  'https://api.hubapi.com/crm/v3/pipelines';
		$this->PropertiesUrl = 'https://api.hubapi.com/crm/v3/properties';

		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 12);
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 12);

		add_action('admin_init', array( $this, 'registerAndBuildAPIHubspot' ));
		add_shortcode( 'wpfhubspot-userIP', array( $this, 'wpfhubspotUserIP' ));
		add_shortcode( 'wpfhubspot-pageUri', array( $this, 'wpfhubspotPageUri' ));
		add_shortcode( 'wpfhubspot-pageName', array( $this, 'wpfhubspotPageName' ));

		add_action( 'wpfhubspot-contact-OK', array( $this,'wpfhubspotContactOK' ), 10, 1 );


		$this->wpfhubspot_Admin_Forms = new Wpfhubspot_Admin_Forms();
		$this->wpfhubspot_Admin_Usuarios = new Wpfhubspot_Admin_Usuarios();
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpfhubspot-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpfhubspot-admin.js', array( 'jquery' ), $this->version, false );
	}

	/*********************************/
	/*****  ADMIN MENU          ******/
	/*********************************/
	/**
	* Admin menu.
	*/
	public function addPluginAdminMenu() {
		//add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position = null )
		add_submenu_page( 'wpfunosconfig', esc_html__('Configuración API Hubspot WpFunos', 'wpfhubspot'), esc_html__('API Hubspot', 'wpfhubspot'), 'administrator', 'wpfunos-APIHubspot', array( $this, 'displayPluginAdminAPIHubspot' ));
		add_submenu_page( 'wpfunosconfig', esc_html__('Usuarios API Hubspot WpFunos', 'wpfhubspot'), esc_html__('Usuarios Hubspot', 'wpfhubspot'), 'administrator', 'wpfunos-UsuariosHubspot', array( $this, 'displayPluginAdminUsuariosHubspot' ));
	}

	/**
	* Api Hubspot menu display.
	*/
	public function displayPluginAdminAPIHubspot() {
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', array($this,'wpfhubspotSettingsMessages'));
			do_action('admin_notices', sanitize_text_field($_GET['error_message']));
		}
		require_once 'partials/wpfhubspot-admin-APIHubspot-display.php';
	}
	public function displayPluginAdminUsuariosHubspot() {
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', array($this,'wpfhubspotSettingsMessages'));
			do_action('admin_notices', sanitize_text_field($_GET['error_message']));
		}
		//$this->masterdatos_list_table->prepare_items();
		//global $wpdb;
		//$todos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."wpf_masterdatos" ));

		//require_once 'partials/wpfhubspot-admin-UsuariosHubspot-display.php';

	}

	public function registerAndBuildAPIHubspot() {
		require_once 'partials/wpfhubspot-admin-registerAndBuildAPIHubspot.php';
	}

	/**
	* Display Admin settings error messages.
	*/
	public function wpfhubspotSettingsMessages($error_message) {
		switch ($error_message) {
			case '1':
			$message = esc_html__('Hubo un error al agregar esta configuración. Inténtalo de nuevo. Si esto persiste, envíenos un correo electrónico.', 'wpfhubspot');
			$err_code = esc_attr('wpfhubspot_setting');
			$setting_field = 'wpfhubspot_setting';
			break;
		}
		$type = 'error';
		add_settings_error($setting_field, $err_code, $message, $type);
	}

	/**
	* Custom Post Type Metabox Render fields.
	*/
	public function wpfhubspot_render_settings_field($args){
		do_action('wpfunos_render',$args);
	}

	/*********************************/
	/*****  SHORTCODES          ******/
	/*********************************/
	/**
	* add_shortcode( 'wpfhubspot-userIP', array( $this, 'wpfhubspotUserIP' ));
	*/
	public function wpfhubspotUserIP( $atts, $content = "" ) {
		$IP = apply_filters('wpfunos_userIP','dummy');
		return $IP;
	}
	/**
	* add_shortcode( 'wpfhubspot-pageUri', array( $this, 'wpfhubspotPageUri' ));
	*/
	public function wpfhubspotPageUri( $atts, $content = "" ) {
		global $wp;
		return  home_url($wp->request);
	}
	/**
	* add_shortcode( 'wpfhubspot-pageName', array( $this, 'wpfhubspotPageName' ));
	*/
	public function wpfhubspotPageName( $atts, $content = "" ) {
		return wp_title();
	}



	/*********************************/
	/*****  HOOKS               ******/
	/*********************************/
	/**
	*      $params = array(
	*				'Nombre' => $Nombre,
	*				'email' => $email,
	*				'telefono' => $telefono,
	*				'politica' => $politica,
	*				'ubicacion' => $ubicacion,
	*				'cuando' => $cuando,
	*				'destino' => $destino,
	*				'ataud' => $ataud,
	*				'velatorio' => $velatorio,
	*				'ceremonia' => $ceremonia,
	*				'Referencia' => $Referencia,
	*				'Servicio' => $Servicio,
	*				'Precio' => $Precio,
	*				'accion' => $accion,
	*				'ok' => $ok,
	*				'ip' => $ip,
	*				'nacimiento' => $nacimiento,
	*				'financiar' => $financiar,
	*				'importe' => $importe,
	*				'plazos_inferior' => $plazos_inferior,
	*				'plazos_superior' => $plazos_superior
	*      );
	*      do_action( 'wpfclientify-process-entry', $params );
	*			 do_action('wpfhubspot-process-service-entry', array( 'userID' => $userID, 'email' => $wpfemail ) );
	*      // END Clientify
	*/
	/**
	*	 add_action( 'wpfhubspot-contact-OK', array( $this,'wpfhubspotContactOK' ), 10, 1 );
	*  do_action('wpfhubspot-contact-OK', array( 'userID' => $userID, 'email' => $wpfemail, 'ok' => 'ok' ) );
	*/
	public function wpfhubspotContactOK( $params ){
		$userIP = apply_filters('wpfunos_userIP','dummy');

		if( $userIP == '79.157.131.56' ) return;

		$userID = $params['userID'];
		$email = $params['email'];
		$ok = $params['ok'];
		if( $userID != '0' ) $hubspotID = get_post_meta( $userID, 'wpfunos_userHubspotIDusuario', true );
		$this->custom_logs( $this->dumpPOST($userIP .' - ==========') );
		$this->custom_logs( $this->dumpPOST($userIP .' - userID: '. $userID .' Email: '. $email .' hubspotID: '. $hubspotID ) );
		if( $hubspotID == '' ){
			$hubspotID = $this->wpfhubspotGetUser( $params );
			if( $userID != '0' ) update_post_meta( $userID, 'wpfunos_userHubspotIDusuario', $hubspotID );
		}
		$this->custom_logs( $this->dumpPOST($userIP .' - $hubspotID: '. $hubspotID ) );
		$params['hubspotID'] = $hubspotID;
		$confirmacion = $this->wpfhubspotSendOK( $params );
	}

	/**
	* $this->wpfhubspotSearchUser( $params );
	*
	*/
	private function wpfhubspotGetUser( $params ){
		$userIP = apply_filters('wpfunos_userIP','dummy');
		$URLhubspot = $this->ContactsUrl;
		$headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
		$body = '{
			"properties": {
				"email":  "'.sanitize_text_field( $params["email"] ).'"
			}
		}';
		$request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'POST'  ) );
		$bodyrequest = json_decode( $request['body'] );
		//$this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );

		if( $bodyrequest->status != 'error'){
			$hubspotID = $bodyrequest->id;
			$this->custom_logs( $this->dumpPOST($userIP .' - El usuario NO existe. Creando nuevo usuario: ' .$hubspotID ) );
		}else{
			//"message": "Contact already exists. Existing ID: 20825901",
			//            012345678901234567890123456789012345678901234567890
			//                      1         2         3         4
			$hubspotID= substr($bodyrequest->message,37,8);
			$this->custom_logs( $this->dumpPOST($userIP .' - El usuario ya existe: ' .$hubspotID ) );
		}
		return $hubspotID;
	}

	/**
	* $confirmacion = $this->wpfhubspotSendOK( $params );
	*
	*/
	private function wpfhubspotSendOK( $params ){
		$userIP = apply_filters('wpfunos_userIP','dummy');
		$URLhubspot = $this->ContactsUrl. '/' .$params['hubspotID'] ;
		$headers = array( 'Authorization' => 'Bearer '.$this->hubspotkey , 'Content-Type' => 'application/json');
		////if( strlen( $params['form_name']  ) > 1 ) $body .= '{"field": "oportunidades_origen",    "value": "'.sanitize_text_field( $params['form_name'] ). '"},';
		$body = '{
			"properties": {';
				if( strlen( $params["email"]  ) > 1 ) $body .= '"email": "'.sanitize_text_field( $params["email"] ). '",';
				if( strlen( $params["telefono"]  ) > 1 ) $body .= '"telefono": "'.sanitize_text_field( $params["telefono"] ). '",';
				if( strlen( $params["nombre"]  ) > 1 ) $body .= '"nombre_y_apellidos": "'.sanitize_text_field( $params["nombre"] ). '",';
				if( strlen( $params["accion"]  ) > 1 ) $body .= '"accion": "'.sanitize_text_field( $params["accion"] ). '",';
				if( strlen( $params["servicio"]  ) > 1 ) $body .= '"servicio": "'.sanitize_text_field( $params["servicio"] ). '",';
				if( strlen( $params["precio"]  ) > 1 ) $body .= '"precio": "'.sanitize_text_field( $params["precio"] ). '",';
				if( strlen( $params["donde"]  ) > 1 ) $body .= '"donde": "'.sanitize_text_field( $params["donde"] ). '",';
				if( strlen( $params["cuando"]  ) > 1 ) $body .= '"cuando": "'.sanitize_text_field( $params["cuando"] ). '",';
				if( strlen( $params["destino"]  ) > 1 ) $body .= '"destino": "'.sanitize_text_field( $params["destino"] ). '",';
				if( strlen( $params["ataud"]  ) > 1 ) $body .= '"ataud": "'.sanitize_text_field( $params["ataud"] ). '",';
				if( strlen( $params["velatorio"]  ) > 1 ) $body .= '"velatorio": "'.sanitize_text_field( $params["velatorio"] ). '",';
				if( strlen( $params["ceremonia"]  ) > 1 ) $body .= '"ceremonia": "'.sanitize_text_field( $params["ceremonia"] ). '",';
				if( strlen( $params["referencia"]  ) > 1 ) $body .= '"referencia": "'.sanitize_text_field( $params["referencia"] ). '",';
				if( strlen( $params["accion"]  ) > 1 ) $body .= '"accion": "'.sanitize_text_field( $params["accion"] ). '",';
				$body .= '"politica_de_privacidad": "Checked", "ok":  "'.sanitize_text_field( $params["ok"] ).'", "ip":  "'.sanitize_text_field( $userIP ).'"
			}
		}';
		//$this->custom_logs( $this->dumpPOST($userIP .' - $URLhubspot: '. $URLhubspot ) );
		//$this->custom_logs( $this->dumpPOST($userIP .' - $body: '. $body ) );
		$request = wp_remote_post( $URLhubspot, array( 'headers' => $headers, 'body' => $body, 'method' => 'PATCH'  ) );
		$bodyrequest = json_decode( $request['body'] );
		if( $bodyrequest->id != ''){
			$this->custom_logs( $this->dumpPOST($userIP .' - Envio >'.$params["ok"]. '< correcto para usuario: '. $bodyrequest->id .' acción: '. $params["accion"] ) );
		}else{
			$this->custom_logs( $this->dumpPOST($userIP .' - $body: '. apply_filters('wpfunos_dumplog', $body  ) ) );
			$this->custom_logs( $this->dumpPOST($userIP .' - $bodyrequest: '. apply_filters('wpfunos_dumplog', $bodyrequest  ) ) );
		}
	}
	// [body] = String: '{"status":"error","message":"Contact already exists. Existing ID: 20825901","correlationId":"e77b1289-623c-4cc6-b481-cb0cc803a3ba","category":"CONFLICT"}'
	/**
	*{
	*  "status": "error",
	*  "message": "Contact already exists. Existing ID: 20825901",
	*  "correlationId": "dbc97dab-fc56-4bb3-af8b-a8177b35d0d1",
	*  "category": "CONFLICT"
	*}
	*
	*{
	*	"id": "22055101",
	*	"properties": {
	*		"createdate": "2023-05-31T08:46:57.986Z",
	*		"email": "info@efraim.cat",
	*		"hs_all_contact_vids": "22055101",
	*		"hs_email_domain": "efraim.cat",
	*		"hs_is_contact": "true",
	*		"hs_is_unworked": "true",
	*		"hs_lifecyclestage_lead_date": "2023-05-31T08:46:57.986Z",
	*		"hs_object_id": "22055101",
	*		"hs_pipeline": "contacts-lifecycle-pipeline",
	*		"lastmodifieddate": "2023-05-31T08:46:57.986Z",
	*		"lifecyclestage": "lead"
	*	},
	*	"createdAt": "2023-05-31T08:46:57.986Z",
	*	"updatedAt": "2023-05-31T08:46:57.986Z",
	*	"archived": false
	*}
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
