<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add new menu to the Admin Control Panel
add_action('admin_menu', 'awqc_attendance_menu');

/**
 * Add a new top level menu link to the ACP
 */
function awqc_attendance_menu()
{
    add_menu_page(
        __('Report', 'attendance_with_qr_code'),
        __('Report', 'attendance_with_qr_code'),
        'manage_options',
        'awqc-attendance-menu',
        'awqc_page_attendance',
        'dashicons-buddicons-buddypress-logo',
        6
    );

    add_submenu_page(
        'awqc-attendance-menu',
        __('Generate QR', 'attendance_with_qr_code'),
        __('Generate QR', 'attendance_with_qr_code'),
        'manage_options',
        'awqc-generate-qr',
        'awqc_page_generate_qr'
    );

    add_submenu_page(
        'awqc-attendance-menu',
        __('History QR', 'attendance_with_qr_code'),
        __('History QR', 'attendance_with_qr_code'),
        'manage_options',
        'awqc-history-qr',
        'awqc_page_history_qr'
    );

    add_submenu_page(
        'awqc-attendance-menu',
        __('Settings', 'attendance_with_qr_code'),
        __('Settings', 'attendance_with_qr_code'),
        'manage_options',
        'awqc-settings',
        'awqc_page_settings'
    );
}

function awqc_page_attendance()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'attendance_with_qr_code'));
    }
    include AWQC_PLUGIN_DIR . 'page/report.php';
}

function awqc_page_history_qr()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'attendance_with_qr_code'));
    }
    include AWQC_PLUGIN_DIR . 'page/history-qr.php';
}

function awqc_page_settings()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'attendance_with_qr_code'));
    }
    include AWQC_PLUGIN_DIR . 'page/settings.php';
}

function awqc_page_generate_qr()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'attendance_with_qr_code'));
    }
    include AWQC_PLUGIN_DIR . 'page/generate-qr.php';
}
