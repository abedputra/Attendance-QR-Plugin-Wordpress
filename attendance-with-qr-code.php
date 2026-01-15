<?php

/**
 * Plugin Name: Attendance with QR Code
 * Plugin URI: https://github.com/abedputra/Attendance-QR-Plugin-Wordpress
 * Description: Plugin for employees or students attendance with QR.
 * Version: 3.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: MuliaTech
 * Author URI: https://abedputra.my.id
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: attendance-with-qr-code
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AWQC_VERSION', '3.0.0');
define('AWQC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AWQC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AWQC_PLUGIN_FILE', __FILE__);

// Include required files
require_once AWQC_PLUGIN_DIR . 'includes/import-db.php';
require_once AWQC_PLUGIN_DIR . 'includes/qr-generator.php';
require_once AWQC_PLUGIN_DIR . 'includes/create-menu.php';

/**
 * Register styles and scripts on initialization.
 */
function awqc_register_scripts()
{
    wp_register_style('awqc-attendance-style', AWQC_PLUGIN_URL . 'assets/css/attendance-style.css', array(), AWQC_VERSION, 'all');
}
add_action('init', 'awqc_register_scripts');

/**
 * Enqueue the registered styles and scripts.
 */
function awqc_enqueue_styles()
{
    wp_enqueue_style('awqc-attendance-style');
}
add_action('wp_enqueue_scripts', 'awqc_enqueue_styles');
add_action('admin_enqueue_scripts', 'awqc_enqueue_styles');

// Create database tables on plugin load
add_action('plugins_loaded', 'awqc_create_table');
