<?php

/**
 * API Endpoint for Attendance Check-in/Check-out
 * This file handles QR code scanning requests from mobile app
 */

// Load WordPress
$path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__);
require_once($path . 'wp-load.php');

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Init table names
$awqc_table_name     = $wpdb->prefix . 'attendances';
$awqc_table_settings = $wpdb->prefix . 'settings';

// Only allow POST requests
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    wp_send_json_error(array('message' => "You can't access this page!"), 403);
}

// Sanitize and validate input
// Note: This is a public API endpoint for mobile app, nonce verification is not required
// Security is handled via security key verification
// phpcs:ignore WordPress.Security.NonceVerification.Missing
$awqc_key = isset($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : '';

// Get data from settings
$awqc_table_settings_escaped = esc_sql($awqc_table_settings);
// Table name is already escaped with esc_sql()
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
$awqc_get_data = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$awqc_table_settings_escaped} WHERE id = %d LIMIT 1",
    1
));

// Check if settings exist
if (!$awqc_get_data) {
    wp_send_json_error(array('message' => 'Settings not found. Please configure the system first.'), 404);
}

$awqc_data = array(
    'start'    => $awqc_get_data->start_time,
    'out'      => $awqc_get_data->out_time,
    'key'      => $awqc_get_data->key_insert,
    'timezone' => $awqc_get_data->timezone,
);

// Validate timezone
if (empty($awqc_data['timezone']) || !in_array($awqc_data['timezone'], timezone_identifiers_list())) {
    $awqc_data['timezone'] = 'UTC'; // Fallback to UTC
}

// IMPORTANT: Date and time are determined by the SERVER based on timezone settings,
// NOT from the mobile device. This prevents users from manipulating time by changing device settings.
// The server calculates the current date/time using the configured timezone from settings.
try {
    $awqc_date_time_now = new DateTime("now", new DateTimeZone($awqc_data['timezone']));
} catch (Exception $e) {
    wp_send_json_error(array('message' => 'Invalid timezone configuration.'), 500);
}

// Validate security key
if (empty($awqc_key)) {
    wp_send_json_error(array('message' => 'Security key is required.'), 400);
}

// Verify security key using hash_equals for timing attack prevention
if (!hash_equals($awqc_data['key'], $awqc_key)) {
    wp_send_json_error(array('message' => 'Invalid security key.'), 403);
}

// Sanitize and validate input parameters
// Note: This is a public API endpoint for mobile app, nonce verification is not required
// Security is handled via security key verification above
// IMPORTANT: We only accept: q (command), name, location, and key from the mobile app.
// We do NOT accept date/time from the device - server determines this based on timezone settings.
// phpcs:ignore WordPress.Security.NonceVerification.Missing
$awqc_command = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Missing
$awqc_name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Missing
$awqc_location = isset($_POST['location']) ? sanitize_text_field(wp_unslash($_POST['location'])) : '';

// Validate command
if (!in_array($awqc_command, array('in', 'out'))) {
    wp_send_json_error(array('message' => 'Invalid command. Use "in" or "out".'), 400);
}

// Validate name
if (empty($awqc_name)) {
    wp_send_json_error(array('message' => 'Name is required.'), 400);
}

// Validate location (optional but recommended)
if (empty($awqc_location)) {
    $awqc_location = ''; // Allow empty location but sanitize
}

// Get current date based on server timezone (not from device)
$awqc_date = $awqc_date_time_now->format('Y-m-d');

// Process check-in or check-out
if ($awqc_command === 'in') {
    $awqc_table_name_escaped = esc_sql($awqc_table_name);
    // Table name is already escaped with esc_sql()
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
    $awqc_check_data_in = $wpdb->prepare(
        "SELECT * FROM {$awqc_table_name_escaped} WHERE `name` = %s AND `date` = %s AND `in_time` IS NOT NULL AND `late_time` IS NOT NULL AND `out_time` IS NULL AND `out_location` IS NULL",
        $awqc_name,
        $awqc_date
    );
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $awqc_is_exist = $wpdb->get_results($awqc_check_data_in);

    if (count($awqc_is_exist) > 0) {
        wp_send_json_error(array('message' => 'Already checked in for today.'), 409);
    }

    // Get current time based on server timezone (not from device)
    $awqc_in_time        = $awqc_date_time_now->format('H:i:s');
    $awqc_change_in_time = strtotime($awqc_in_time);
    if ($awqc_change_in_time === false) {
        wp_send_json_error(array('message' => 'Unable to parse check-in time.'), 500);
    }

    // Get late time
    $awqc_start_time_stamp = strtotime($awqc_data['start']);
    if ($awqc_start_time_stamp === false) {
        wp_send_json_error(array('message' => 'Invalid start time configuration.'), 500);
    }
    $awqc_get_late_time = awqc_get_time($awqc_change_in_time - $awqc_start_time_stamp);
    $awqc_late_time     = sprintf('%02d:%02d:%02d', $awqc_get_late_time[0], $awqc_get_late_time[1], $awqc_get_late_time[2]);

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
    $awqc_insert_data = $wpdb->insert(
        $awqc_table_name,
        array(
            'name'        => $awqc_name,
            'date'        => $awqc_date,
            'in_location' => $awqc_location,
            'in_time'     => $awqc_in_time,
            'late_time'   => $awqc_late_time,
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );

    if ($awqc_insert_data === false) {
        wp_send_json_error(array('message' => 'Database error: ' . $wpdb->last_error), 500);
    }

    wp_send_json_success(array(
        'message'  => 'Check-in successful!',
        'date'     => $awqc_date,
        'time'     => $awqc_in_time,
        'location' => $awqc_location,
        'query'    => 'Check-in',
    ));
} elseif ($awqc_command === 'out') {
    $awqc_table_name_escaped = esc_sql($awqc_table_name);
    // Table name is already escaped with esc_sql()
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
    $awqc_check_data_out = $wpdb->prepare(
        "SELECT * FROM {$awqc_table_name_escaped} WHERE `name` = %s AND `date` = %s AND `out_time` IS NULL AND `out_location` IS NULL ORDER BY `date` DESC LIMIT 1",
        $awqc_name,
        $awqc_date
    );
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $awqc_check_in_record = $wpdb->get_row($awqc_check_data_out);
    if (!$awqc_check_in_record || empty($awqc_check_in_record->in_time)) {
        wp_send_json_error(array('message' => 'Please check-in first.'), 400);
    }

    // Validate and parse in_time
    $awqc_get_in_database = strtotime($awqc_check_in_record->in_time);
    if ($awqc_get_in_database === false) {
        wp_send_json_error(array('message' => 'Invalid check-in time found in database.'), 500);
    }

    // Get current time based on server timezone (not from device)
    $awqc_out_time        = $awqc_date_time_now->format('H:i:s');
    $awqc_change_out_time = strtotime($awqc_out_time);
    if ($awqc_change_out_time === false) {
        wp_send_json_error(array('message' => 'Unable to parse check-out time.'), 500);
    }

    // Get work hour
    $awqc_get_work_hour = awqc_get_time($awqc_change_out_time - $awqc_get_in_database);
    $awqc_work_hour     = sprintf('%02d:%02d:%02d', $awqc_get_work_hour[0], $awqc_get_work_hour[1], $awqc_get_work_hour[2]);

    // Validate and parse out time from settings
    $awqc_out_time_stamp = strtotime($awqc_data['out']);
    if ($awqc_out_time_stamp === false) {
        wp_send_json_error(array('message' => 'Invalid out time configuration.'), 500);
    }

    // Get over time
    $awqc_get_over_time = awqc_get_time($awqc_change_out_time - $awqc_out_time_stamp);
    if ($awqc_get_in_database > $awqc_out_time_stamp || $awqc_change_out_time < $awqc_out_time_stamp) {
        $awqc_over_time = '00:00:00';
    } else {
        $awqc_over_time = sprintf('%02d:%02d:%02d', $awqc_get_over_time[0], $awqc_get_over_time[1], $awqc_get_over_time[2]);
    }

    // Early out time
    $awqc_get_early_out_time = awqc_get_time($awqc_out_time_stamp - $awqc_change_out_time);
    if ($awqc_get_in_database > $awqc_out_time_stamp) {
        $awqc_early_out_time = '00:00:00';
    } else {
        $awqc_early_out_time = sprintf('%02d:%02d:%02d', $awqc_get_early_out_time[0], $awqc_get_early_out_time[1], $awqc_get_early_out_time[2]);
    }

    // Update check-out data
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
    $awqc_update_data = $wpdb->update(
        $awqc_table_name,
        array(
            'out_location'   => $awqc_location,
            'out_time'       => $awqc_out_time,
            'work_hour'      => $awqc_work_hour,
            'over_time'      => $awqc_over_time,
            'early_out_time' => $awqc_early_out_time,
        ),
        array(
            'name'     => $awqc_name,
            'date'     => $awqc_date,
            'out_time' => null,
        ),
        array('%s', '%s', '%s', '%s', '%s'),
        array('%s', '%s', '%s')
    );

    if ($awqc_update_data === false) {
        wp_send_json_error(array('message' => 'Database error: ' . $wpdb->last_error), 500);
    }

    if ($awqc_update_data === 0) {
        wp_send_json_error(array('message' => 'No record found to update. Please check-in first.'), 404);
    }

    wp_send_json_success(array(
        'message'  => 'Check-out successful!',
        'date'     => $awqc_date,
        'time'     => $awqc_out_time,
        'location' => $awqc_location,
        'query'    => 'Check-out',
    ));
}

/**
 * Convert seconds to hours, minutes, seconds array
 *
 * @param int $total Total seconds
 * @return array Array with hours, minutes, seconds
 */
function awqc_get_time($total)
{
    // Ensure $total is an integer and non-negative
    $total = max(0, (int) $total);

    $hours          = (int) ($total / 3600);
    $seconds_remain = ($total - ($hours * 3600));
    $minutes        = (int) ($seconds_remain / 60);
    $seconds        = ($seconds_remain - ($minutes * 60));

    return array($hours, $minutes, $seconds);
}
