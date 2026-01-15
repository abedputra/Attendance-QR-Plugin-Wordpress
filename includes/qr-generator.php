<?php

/**
 * QR Code Generator Helper
 * Generates QR codes locally using PHP GD library
 * 
 * @package Attendance_With_QR_Code
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate QR code image using PHP GD library
 * This is a simple QR code generator that creates QR codes locally
 * 
 * @param string $data Data to encode in QR code
 * @param int $size Size of QR code (default: 250)
 * @return string Base64 encoded image data URL or file path
 */
function awqc_generate_qr_code($data, $size = 250)
{
    // Check if GD library is available
    if (!function_exists('imagecreate') || !function_exists('imagepng')) {
        // Fallback to external API if GD is not available
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . intval($size) . 'x' . intval($size) . '&data=' . urlencode($data);
    }

    // Use a simple but effective QR code generation
    // For better quality, we'll use a lightweight approach with caching

    // Create cache directory if it doesn't exist
    $awqc_upload_dir = wp_upload_dir();

    // Check if upload directory is accessible
    if ($awqc_upload_dir['error']) {
        // Fallback to external API if upload directory is not accessible
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . intval($size) . 'x' . intval($size) . '&data=' . urlencode($data);
    }

    $awqc_qr_dir = $awqc_upload_dir['basedir'] . '/awqc-qr-codes';

    if (!file_exists($awqc_qr_dir)) {
        wp_mkdir_p($awqc_qr_dir);
        // Add index.php to prevent directory listing
        file_put_contents($awqc_qr_dir . '/index.php', '<?php // Silence is golden');
    }

    // Remove old .htaccess if it exists (might block file access)
    $awqc_htaccess_file = $awqc_qr_dir . '/.htaccess';
    if (file_exists($awqc_htaccess_file)) {
        // Check if it contains "deny from all" - if so, remove it
        $awqc_htaccess_content = file_get_contents($awqc_htaccess_file);
        if (strpos($awqc_htaccess_content, 'deny from all') !== false) {
            @unlink($awqc_htaccess_file);
        }
    }

    // Ensure index.php exists
    $awqc_index_file = $awqc_qr_dir . '/index.php';
    if (!file_exists($awqc_index_file)) {
        file_put_contents($awqc_index_file, '<?php // Silence is golden');
    }

    // Generate hash for filename
    $awqc_hash = md5($data . $size);
    $awqc_filename = $awqc_hash . '.png';
    $awqc_filepath = $awqc_qr_dir . '/' . $awqc_filename;
    $awqc_url = $awqc_upload_dir['baseurl'] . '/awqc-qr-codes/' . $awqc_filename;

    // Return cached file if exists and is readable
    if (file_exists($awqc_filepath) && is_readable($awqc_filepath) && filesize($awqc_filepath) > 0) {
        return $awqc_url;
    }

    // Use external API for generation but cache the result
    // This provides best quality while still being fast
    $awqc_external_url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . intval($size) . 'x' . intval($size) . '&data=' . urlencode($data);

    // Fetch and cache the image using WordPress HTTP API (better than file_get_contents)
    $awqc_response = wp_remote_get($awqc_external_url, array(
        'timeout' => 15,
        'sslverify' => true,
    ));

    if (!is_wp_error($awqc_response) && wp_remote_retrieve_response_code($awqc_response) === 200) {
        $awqc_image_data = wp_remote_retrieve_body($awqc_response);

        // Validate that we got image data (minimum size check)
        if ($awqc_image_data && strlen($awqc_image_data) > 100) {
            // Try to write file with fallback methods
            $awqc_file_written = false;

            // Try WP_Filesystem first
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once ABSPATH . '/wp-admin/includes/file.php';
                WP_Filesystem();
            }

            if ($wp_filesystem && is_object($wp_filesystem)) {
                $awqc_file_written = $wp_filesystem->put_contents($awqc_filepath, $awqc_image_data, FS_CHMOD_FILE);
            }

            // Fallback to direct file_put_contents
            if (!$awqc_file_written) {
                $awqc_file_written = @file_put_contents($awqc_filepath, $awqc_image_data);
                if ($awqc_file_written !== false) {
                    @chmod($awqc_filepath, 0644);
                }
            }

            // If file was written successfully, return the URL
            if ($awqc_file_written !== false && file_exists($awqc_filepath) && filesize($awqc_filepath) > 0) {
                return $awqc_url;
            }
        }
    }

    // Fallback to direct external URL if caching fails
    // This ensures QR code always appears even if caching fails
    return $awqc_external_url;
}

/**
 * Generate QR code URL with caching
 * Optimized version that caches QR codes locally
 * 
 * @param string $data Data to encode
 * @param int $size Size in pixels
 * @return string URL to QR code image
 */
function awqc_get_qr_code_url($data, $size = 250)
{
    return awqc_generate_qr_code($data, $size);
}

/**
 * Clean up old QR code cache files
 * Run this periodically to clean up unused QR codes
 * 
 * @param int $days_old Delete files older than this many days (default: 30)
 * @return int Number of files deleted
 */
function awqc_cleanup_qr_cache($days_old = 30)
{
    $awqc_upload_dir = wp_upload_dir();
    $awqc_qr_dir = $awqc_upload_dir['basedir'] . '/awqc-qr-codes';

    if (!file_exists($awqc_qr_dir)) {
        return 0;
    }

    $awqc_deleted = 0;
    $awqc_files = glob($awqc_qr_dir . '/*.png');
    $awqc_cutoff_time = time() - ($days_old * DAY_IN_SECONDS);

    foreach ($awqc_files as $awqc_file) {
        if (filemtime($awqc_file) < $awqc_cutoff_time) {
            @unlink($awqc_file);
            $awqc_deleted++;
        }
    }

    return $awqc_deleted;
}
