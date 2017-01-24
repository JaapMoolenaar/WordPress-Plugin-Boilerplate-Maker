<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Satsume_Product_Tabs
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Plugin Boilerplate
 * Plugin URI:        http://example.com/satsume-product-tabs-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       satsume-product-tabs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-satsume-product-tabs-activator.php
 */
function activate_satsume_product_tabs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-satsume-product-tabs-activator.php';
	Satsume_Product_Tabs_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-satsume-product-tabs-deactivator.php
 */
function deactivate_satsume_product_tabs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-satsume-product-tabs-deactivator.php';
	Satsume_Product_Tabs_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_satsume_product_tabs' );
register_deactivation_hook( __FILE__, 'deactivate_satsume_product_tabs' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-satsume-product-tabs.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_satsume_product_tabs() {

	$plugin = new Satsume_Product_Tabs();
	$plugin->run();

}
run_satsume_product_tabs();
