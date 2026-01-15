<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$awqc_table_name = $wpdb->prefix . 'history_qr'; // table name

$awqc_message = '';
$awqc_notice  = '';

$awqc_default = array(
    'id'   => 0,
    'name' => ''
);

/**
 * Validate generate QR form data
 *
 * @param array $item Form data to validate
 * @return bool|string True if valid, error message string if invalid
 */
function awqc_validate_generate_qr($item)
{
    $messages = array();

    if (empty($item['name'])) {
        $messages[] = esc_html__('Name is required', 'attendance_with_qr_code');
    }

    if (empty($messages)) {
        return true;
    }

    return implode('<br />', $messages);
}

// If submit will update the data
if (isset($_REQUEST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), basename(__FILE__))) {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'attendance_with_qr_code'));
    }

    // Sanitize input data
    $awqc_item = array(
        'id' => isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0,
        'name' => isset($_REQUEST['name']) ? sanitize_text_field(wp_unslash($_REQUEST['name'])) : '',
    );

    // validate data, and if all ok save item to database
    $awqc_item_valid = awqc_validate_generate_qr($awqc_item);
    if ($awqc_item_valid === true) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $awqc_result = $wpdb->insert($awqc_table_name, array('name' => $awqc_item['name']), array('%s'));
        $awqc_item['id'] = $wpdb->insert_id;
        if ($awqc_result) {
            $awqc_message = esc_html__('QR code was successfully generated', 'attendance_with_qr_code');
        } else {
            $awqc_notice = esc_html__('There was an error while generating the QR code', 'attendance_with_qr_code');
        }
    } else {
        // if $awqc_item_valid not true it contains error message(s)
        $awqc_notice = $awqc_item_valid;
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Generate QR Code', 'attendance_with_qr_code'); ?></h1>
    <hr class="wp-header-end">
    
    <?php if (!empty($awqc_notice)) : ?>
        <div class="notice notice-error is-dismissible" style="margin: 20px 0;">
            <p><?php echo wp_kses_post($awqc_notice); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($awqc_message)) : ?>
        <div class="notice notice-success is-dismissible" style="margin: 20px 0;">
            <p><?php echo esc_html($awqc_message); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="notice notice-info" style="margin: 20px 0;">
        <p><strong><?php echo esc_html__('Generate QR Code', 'attendance_with_qr_code'); ?></strong> <?php echo esc_html__('Create QR codes for employees or students. Enter a name and generate a unique QR code that can be used for attendance tracking.', 'attendance_with_qr_code'); ?></p>
    </div>

    <div class="row-col">
        <div class="col-60">
            <div class="awqc-form-item">
                <form id="form" method="POST">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(basename(__FILE__))); ?>" />

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="name"><?php echo esc_html__('Employee/Student Name', 'attendance_with_qr_code'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="name" name="name" class="regular-text" placeholder="<?php esc_attr_e('Enter full name', 'attendance_with_qr_code'); ?>" value="<?php echo isset($_POST['name']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['name']))) : ''; ?>" required>
                                    <p class="description">
                                        <span class="dashicons dashicons-info-outline" aria-hidden="true"></span>
                                        <?php echo esc_html__('Enter the full name of the employee or student. This name will be encoded in the QR code.', 'attendance_with_qr_code'); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <input type="submit" id="submit" class="button button-primary" name="submit" value="<?php esc_attr_e('Generate QR Code', 'attendance_with_qr_code'); ?>">
                    </p>
                </form>
            </div>
        </div>

        <div class="col-40">
            <div class="awqc-qr-preview">
                <h3><?php echo esc_html__('QR Code Preview', 'attendance_with_qr_code'); ?></h3>
                <?php
                // Display QR code if form was successfully submitted
                // Use sanitized value from form processing above if available
                if (!empty($awqc_message) && isset($awqc_item) && !empty($awqc_item['name'])) {
                    $awqc_name = $awqc_item['name'];
                    $awqc_qr = json_encode(array('name' => $awqc_name));
                    // Use local QR generator with caching for faster loading
                    $awqc_qr_image_url = awqc_get_qr_code_url($awqc_qr, 250);
                    $awqc_qr_download_url = awqc_get_qr_code_url($awqc_qr, 400);
                    
                    echo '<img src="' . esc_url($awqc_qr_image_url) . '" alt="' . esc_attr__('QR Code', 'attendance_with_qr_code') . '">';
                    echo '<p style="margin: 10px 0;"><strong>' . esc_html($awqc_name) . '</strong></p>';
                    echo '<a href="' . esc_url($awqc_qr_download_url) . '" download="qr-code-' . esc_attr(sanitize_file_name($awqc_name)) . '.png" class="button button-primary button-center">';
                    echo '<span class="dashicons dashicons-download" style="margin-top: 3px;"></span> ' . esc_html__('Download QR Code', 'attendance_with_qr_code');
                    echo '</a>';
                } else {
                    echo '<img src="' . esc_url(AWQC_PLUGIN_URL . 'assets/img/qr-default.png') . '" alt="' . esc_attr__('QR Code Preview', 'attendance_with_qr_code') . '" style="opacity: 0.5;">';
                    echo '<p class="description" style="margin-top: 10px; font-size: 13px;">' . esc_html__('Enter a name and click "Generate QR Code" to create a QR code.', 'attendance_with_qr_code') . '</p>';
                }
                ?>
            </div>
        </div>
    </div>

</div><!-- .wrap -->