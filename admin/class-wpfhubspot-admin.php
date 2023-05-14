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

		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 20);
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
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
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
	public function wpfhubspot_render_settings_field($args)
	{
		if ($args['wp_data'] == 'option') {
			$wp_data_value = get_option($args['name']);
		} elseif ($args['wp_data'] == 'post_meta') {
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
		}

		switch ($args['type']) {
			case 'input':
			$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
			if ($args['subtype'] != 'checkbox') {
				$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
				$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
				$step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
				$min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
				$max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
				$size = (isset($args['size'])) ? 'size="' . $args['size'] . '"' : 'size="40"';
				$placeholder = (isset($args['placeholder'])) ? 'placeholder="' . $args['placeholder'] . '"' : '';

				$class = (isset($args['class'])) ? 'class="' . $args['class'] . '"' : '';
				$imagenid = (isset($args['imagenid'])) ? 'data-imagen-id="' . $args['imagenid'] . '"' : '';

				if (isset($args['disabled'])) {
					// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
					echo $prependStart . '<input type="' . $args['subtype'] . '" '.$class. ' '.$imagenid. ' id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" ' . $size . ' disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' .$placeholder. ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
				} else {
					echo $prependStart . '<input type="' . $args['subtype'] . '" '.$class. ' '.$imagenid. ' id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' .$placeholder. ' ' . $min . ' name="' . $args['name'] . '" ' . $size . ' value="' . esc_attr($value) . '" />' . $prependEnd;
				}
				/* <input required="required" '.$disabled.' type="number" step="any" id="'.'wpfunos_cost2" name="'.'wpfunos_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.'wpfunos_cost" step="any" name="'.'wpfunos_cost" value="' . esc_attr( $cost ) . '" /> */
			} else {
				$checked = ($value) ? 'checked' : '';
				?>
				<input type="<?php esc_html_e( $args['subtype'] ); ?>"
				id="<?php esc_html_e( $args['id'] ); ?>"
				<?php esc_html_e( $args['required'] ); ?>
				name="<?php esc_html_e( $args['name'] ); ?>" size="40" value="1"
				<?php esc_html_e( $checked ); ?> /><?php
			}
			break;
			default:
			break;
		}
	}

}
