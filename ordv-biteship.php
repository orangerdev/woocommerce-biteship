<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ridwan-arifandi.com
 * @since             1.0.0
 * @package           Ordv_Biteship
 *
 * @wordpress-plugin
 * Plugin Name:       OrangerDev - Biteship
 * Plugin URI:        https://ridwan-arifandi.com
 * Description:       Biteship integration with WooCommerce.
 * Version:           1.0.0
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ordv-biteship
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ORDV_BITESHIP_VERSION', '1.0.0' );

define( 'ORDV_BITESHIP_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORDV_BITESHIP_URI', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ordv-biteship-activator.php
 */
function activate_ordv_biteship() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ordv-biteship-activator.php';
	Ordv_Biteship_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ordv-biteship-deactivator.php
 */
function deactivate_ordv_biteship() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ordv-biteship-deactivator.php';
	Ordv_Biteship_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ordv_biteship' );
register_deactivation_hook( __FILE__, 'deactivate_ordv_biteship' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ordv-biteship.php';

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ordv_biteship() {

	$plugin = new Ordv_Biteship();
	$plugin->run();

}

if(!function_exists('__debug')) :
function __debug()
{
	$bt     = debug_backtrace();
	$caller = array_shift($bt);
	$args   = [
		"file"  => $caller["file"],
		"line"  => $caller["line"],
		"args"  => func_get_args()
	];

	do_action('qm/info', $args);
}
endif;

if(!function_exists('__print_debug')) :
function __print_debug()
{
	$bt     = debug_backtrace();
	$caller = array_shift($bt);
	$args   = [
		"file"  => $caller["file"],
		"line"  => $caller["line"],
		"args"  => func_get_args()
	];

	?><pre><?php print_r($args); ?></pre><?php
}
endif;

run_ordv_biteship();

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/orangerdev/woocommerce-biteship',
	__FILE__,
	'ordv-biteship'
);

$myUpdateChecker->setBranch('main');
$myUpdateChecker->setAuthentication('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');