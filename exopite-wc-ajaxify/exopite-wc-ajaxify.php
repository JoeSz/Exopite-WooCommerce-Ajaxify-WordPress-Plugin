<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://joe.szalai.org
 * @since             1.0.0
 * @package           Exopite_Wc_Ajaxify
 *
 * @wordpress-plugin
 * Plugin Name:       Exopite WooCommerce Ajaxify
 * Plugin URI:        https://joe.szalai.org/exopite/exopite-wc-ajaxify
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           20180329
 * Author:            Joe Szalai
 * Author URI:        https://joe.szalai.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       exopite-wc-ajaxify
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EXOPITE_WC_AJAXIFY_VERSION', '20180329' );
define( 'EXOPITE_WC_AJAXIFY_PLUGIN_NAME', 'exopite-seo-core' );
define( 'EXOPITE_WC_AJAXIFY_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-exopite-wc-ajaxify-activator.php
 */
function activate_exopite_wc_ajaxify() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-exopite-wc-ajaxify-activator.php';
	Exopite_Wc_Ajaxify_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-exopite-wc-ajaxify-deactivator.php
 */
function deactivate_exopite_wc_ajaxify() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-exopite-wc-ajaxify-deactivator.php';
	Exopite_Wc_Ajaxify_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_exopite_wc_ajaxify' );
register_deactivation_hook( __FILE__, 'deactivate_exopite_wc_ajaxify' );

/*
 * Update
 */
if ( is_admin() ) {

    /**
     * A custom update checker for WordPress plugins.
     *
     * Useful if you don't want to host your project
     * in the official WP repository, but would still like it to support automatic updates.
     * Despite the name, it also works with themes.
     *
     * @link http://w-shadow.com/blog/2011/06/02/automatic-updates-for-commercial-themes/
     * @link https://github.com/YahnisElsts/plugin-update-checker
     * @link https://github.com/YahnisElsts/wp-update-server
     */
    if( ! class_exists( 'Puc_v4_Factory' ) ) {

        require_once join( DIRECTORY_SEPARATOR, array( EXOPITE_WC_AJAXIFY_PATH, 'vendor', 'plugin-update-checker', 'plugin-update-checker.php' ) );

    }

    $MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://update.szalai.org/?action=get_metadata&slug=' . EXOPITE_WC_AJAXIFY_PLUGIN_NAME, //Metadata URL.
        __FILE__, //Full path to the main plugin file.
        EXOPITE_WC_AJAXIFY_PLUGIN_NAME //Plugin slug. Usually it's the same as the name of the directory.
    );

}
// End Update

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-exopite-wc-ajaxify.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_exopite_wc_ajaxify() {

	$plugin = new Exopite_Wc_Ajaxify();
	$plugin->run();

}
run_exopite_wc_ajaxify();
