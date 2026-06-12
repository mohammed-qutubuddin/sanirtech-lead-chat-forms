<?php
/**
 * Plugin Name:       SanirTech Lead Chat Forms
 * Description:       Secure, high-converting lead capture form builder with direct chat routing.
 * Version:           1.0.1
 * Author:            Abdul Nasir
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sanirtech-lead-chat-forms
 * Requires PHP:      7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'STLCF_VERSION', '1.0.1' );
define( 'STLCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STLCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STLCF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

function stlcf_activate_plugin() {
    require_once STLCF_PLUGIN_DIR . 'includes/class-stlcf-activator.php';
    stlcf_Activator::activate();
}
register_activation_hook( __FILE__, 'stlcf_activate_plugin' );

function stlcf_run_plugin() {
    require_once STLCF_PLUGIN_DIR . 'includes/class-stlcf-core.php';
    $stlcf_plugin = new stlcf_Core();
    $stlcf_plugin->run();
}

stlcf_run_plugin();