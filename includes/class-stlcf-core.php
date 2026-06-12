<?php

// Abort if called directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The core plugin class.
 * This class orchestrates and loads all dependencies and hooks.
 */
class STLCF_Core {

    /**
     * Core objects instances.
     */
    protected $plugin_admin;
    protected $plugin_frontend;

    /**
     * Initialize and load requirements.
     */
    public function __construct() {
        $this->load_dependencies();
    }

    /**
     * Load required files for the plugin.
     */
    private function load_dependencies() {
        require_once STLCF_PLUGIN_DIR . 'includes/class-stlcf-admin.php';
        
        // Load the frontend rendering class
        require_once STLCF_PLUGIN_DIR . 'includes/class-stlcf-frontend.php';
    }

    /**
     * Run the engine and initialize classes.
     */
    public function run() {
        $this->plugin_admin = new STLCF_Admin();
        
        // Instantiate the frontend class to trigger shortcode and redirection controllers
        $this->plugin_frontend = new STLCF_Frontend();
    }
}