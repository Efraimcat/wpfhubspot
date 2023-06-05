<?php

/**
* @link              https://efraim.cat
* @since             1.0.0
* @package           Wpfhubspot
*
* @wordpress-plugin
* Plugin Name:       WpfHubspot
* Plugin URI:        https://github.com/Efraimcat/wpfhubspot/
* Description:       Funcionalidades para funos.es. Conector Hubspot
* Version:           1.2.2
* Author:            Efraim Bayarri
* Author URI:        https://efraim.cat
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       wpfhubspot
* Domain Path:       /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPFHUBSPOT_VERSION', '1.2.2' );

function activate_wpfhubspot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpfhubspot-activator.php';
	Wpfhubspot_Activator::activate();
}

function deactivate_wpfhubspot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpfhubspot-deactivator.php';
	Wpfhubspot_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpfhubspot' );
register_deactivation_hook( __FILE__, 'deactivate_wpfhubspot' );

require plugin_dir_path( __FILE__ ) . 'includes/class-wpfhubspot.php';

function run_wpfhubspot() {

	$plugin = new Wpfhubspot();
	$plugin->run();

}
run_wpfhubspot();
