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
class Wpfhubspot_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 12);
		add_action('admin_init', array( $this, 'registerAndBuildAPIHubspot' ));

	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpfhubspot-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpfhubspot-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	* Admin menu.
	*/
	public function addPluginAdminMenu() {
		//add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position = null )
		add_submenu_page( 'wpfunosconfig', esc_html__('Configuración API Hubspot WpFunos', 'wpfunos'), esc_html__('API Hubspot', 'wpfunos'), 'administrator', 'wpfunos-APIHubspot', array( $this, 'displayPluginAdminAPIHubspot' ));
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

	public function registerAndBuildAPIHubspot() {
		require_once 'partials/wpfhubspot-admin-registerAndBuildAPIHubspot.php';
	}

	/**
	* Display Admin settings error messages.
	*/
	public function wpfhubspotSettingsMessages($error_message) {
		switch ($error_message) {
			case '1':
			$message = esc_html__('Hubo un error al agregar esta configuración. Inténtalo de nuevo. Si esto persiste, envíenos un correo electrónico.', 'wpfunos');
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

}