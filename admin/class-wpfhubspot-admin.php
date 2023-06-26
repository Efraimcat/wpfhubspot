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

		add_action('admin_init', array( $this, 'registerAndBuildAPIHubspot' ));
		add_shortcode( 'wpfhubspot-userIP', array( $this, 'wpfhubspotUserIP' ));
		add_shortcode( 'wpfhubspot-pageUri', array( $this, 'wpfhubspotPageUri' ));
		add_shortcode( 'wpfhubspot-pageName', array( $this, 'wpfhubspotPageName' ));

		add_action( 'wpfhubspot-usuarios', array( $this, 'wpfhubspotusuarios' ), 10, 1 );

		$this->wpfhubspot_Admin_Forms = new Wpfhubspot_Admin_Forms();
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
		$page_hook = add_submenu_page( 'wpfunosconfig', esc_html__('Usuarios API Hubspot WpFunos', 'wpfhubspot'), esc_html__('Usuarios Hubspot', 'wpfhubspot'), 'administrator', 'wpfunos-UsuariosHubspot', array( $this, 'displayPluginAdminUsuariosHubspot' ));
		add_action( 'load-'.$page_hook, array( $this, 'displayPluginAdminUsuariosHubspot_screen_options' ) );
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
		$this->wpfhubspot_Admin_Usuarios->prepare_items();
		global $wpdb;
		$todos = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."wpf_hubspotusers" ));
		require_once 'partials/wpfhubspot-admin-UsuariosHubspot-display.php';
	}

	public function displayPluginAdminUsuariosHubspot_screen_options(){
		$arguments = array(
			'label' => __( 'Entradas por página', 'wpfhubspot' ),
			'default'	=> 25,
			'option' => 'hubspot_users_per_page'
		);
		add_screen_option( 'per_page', $arguments );
		$this->wpfhubspot_Admin_Usuarios = new Wpfhubspot_Admin_Usuarios();
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
	*
	* add_action( 'wpfhubspot-usuarios', array( $this, 'wpfhubspotusuarios' ), 10, 1 );
	* do_action('wpfhubspot-usuarios',array( 'email' => $email, 'hubspotutk' => $hubspotutk ) );
	*
	*/
	public function wpfhubspotusuarios($record){
		if( stripos( get_option( 'wpfunos_HubspotEmailNo' ), $record["email"] ) !== false ) return;
		$userIP = apply_filters('wpfunos_userIP','dummy');

		if(	stripos( get_option( 'wpfunos_IpHubspot' ), $userIP ) !== false ){
			//$this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
			//$this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotusuarios: Colaborador - Entrada sin cambiar' ) );
			return;
		}
		if ( $record['hubspotutk'] == '') {
			$record['hubspotutk'] = 'fe23'.apply_filters('wpfunos_generate_random_string', 28 );
			//$this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
			//$this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotusuarios: NO hubspotutk:  Nuevo: ' .$record['hubspotutk'] ) );
		}
		if( !isset ( $record['email'] ) || $record['email'] == '' ){
			//$this->custom_logs( $this->dumpPOST($userIP .' - ==========' ) );
			//$this->custom_logs( $this->dumpPOST($userIP .' - wpfhubspotusuarios: NO email' ) );
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpf_hubspotusers';

		$referencias = [];
		$args = array(
			'post_type' => 'usuarios_wpfunos',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array( 'key' => 'wpfunos_userMail', 'value' => $record['email'], 'compare' => '=', ),
			),
		);
		$post_list = get_posts( $args );
		if( $post_list ){
			foreach ($post_list as $post ) {
				$referencias[] = get_post_meta( $post->ID  , 'wpfunos_userReferencia', true );
			}
		}
		$ref = serialize( $referencias );

		$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $record['email'] ), ARRAY_A);

		if ( $results ) {
			//$wpdb->update( $table, $data, $where );
			foreach ( $results as $entrada ){
				//$this->custom_logs( $this->dumpPOST($userIP .' - Update wpfhubspotusuarios ' .$record['email']. ' id: ' .$entrada['id'] ) );
				$wpdb->update(
					$table_name,
					array(
						'ultima' => current_time( 'mysql' ),
						'ip' => $userIP,
						'email' => $record['email'],
						'hubspotutk' => $record['hubspotutk'],
						'referencias' => $ref,
					),
					array(
						'id' => $entrada['id'],
					)
				);
			}
		}else{
			//$wpdb->insert($table,$data);
			//$this->custom_logs( $this->dumpPOST($userIP .' - Insert wpfhubspotusuarios ' .$record['email']) );
			$wpdb->insert(
				$table_name,
				array(
					'time' => current_time( 'mysql' ),
					'ultima' => current_time( 'mysql' ),
					'ip' => $userIP,
					'email' => $record['email'],
					'hubspotutk' => $record['hubspotutk'],
					'referencias' => $ref,
				)
			);
		}

	}
	/*
	*time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	*ultima datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	*email tinytext DEFAULT '' NOT NULL,
	*ip tinytext DEFAULT '' NOT NULL,
	*hubspotutk tinytext DEFAULT '' NOT NULL,
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
