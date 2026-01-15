<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wpdb;
$awqc_table_name = $wpdb->prefix . 'settings'; // table name

$awqc_message = '';
$awqc_notice  = '';

$awqc_default = array(
    'id'            => 0,
    'start_time'    => '',
    'out_time'      => '',
    'key_insert'    => '',
    'timezone'      => '',
);

$awqc_timezones = array(
    'Pacific/Midway'       => "(GMT-11:00) Midway Island",
    'US/Samoa'             => "(GMT-11:00) Samoa",
    'US/Hawaii'            => "(GMT-10:00) Hawaii",
    'US/Alaska'            => "(GMT-09:00) Alaska",
    'US/Pacific'           => "(GMT-08:00) Pacific Time (US &amp; Canada)",
    'America/Tijuana'      => "(GMT-08:00) Tijuana",
    'US/Arizona'           => "(GMT-07:00) Arizona",
    'US/Mountain'          => "(GMT-07:00) Mountain Time (US &amp; Canada)",
    'America/Chihuahua'    => "(GMT-07:00) Chihuahua",
    'America/Mazatlan'     => "(GMT-07:00) Mazatlan",
    'America/Mexico_City'  => "(GMT-06:00) Mexico City",
    'America/Monterrey'    => "(GMT-06:00) Monterrey",
    'Canada/Saskatchewan'  => "(GMT-06:00) Saskatchewan",
    'US/Central'           => "(GMT-06:00) Central Time (US &amp; Canada)",
    'US/Eastern'           => "(GMT-05:00) Eastern Time (US &amp; Canada)",
    'US/East-Indiana'      => "(GMT-05:00) Indiana (East)",
    'America/Bogota'       => "(GMT-05:00) Bogota",
    'America/Lima'         => "(GMT-05:00) Lima",
    'America/Caracas'      => "(GMT-04:30) Caracas",
    'Canada/Atlantic'      => "(GMT-04:00) Atlantic Time (Canada)",
    'America/La_Paz'       => "(GMT-04:00) La Paz",
    'America/Santiago'     => "(GMT-04:00) Santiago",
    'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
    'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
    'Greenland'            => "(GMT-03:00) Greenland",
    'Atlantic/Stanley'     => "(GMT-02:00) Stanley",
    'Atlantic/Azores'      => "(GMT-01:00) Azores",
    'Atlantic/Cape_Verde'  => "(GMT-01:00) Cape Verde Is.",
    'Africa/Casablanca'    => "(GMT) Casablanca",
    'Europe/Dublin'        => "(GMT) Dublin",
    'Europe/Lisbon'        => "(GMT) Lisbon",
    'Europe/London'        => "(GMT) London",
    'Africa/Monrovia'      => "(GMT) Monrovia",
    'Europe/Amsterdam'     => "(GMT+01:00) Amsterdam",
    'Europe/Belgrade'      => "(GMT+01:00) Belgrade",
    'Europe/Berlin'        => "(GMT+01:00) Berlin",
    'Europe/Bratislava'    => "(GMT+01:00) Bratislava",
    'Europe/Brussels'      => "(GMT+01:00) Brussels",
    'Europe/Budapest'      => "(GMT+01:00) Budapest",
    'Europe/Copenhagen'    => "(GMT+01:00) Copenhagen",
    'Europe/Ljubljana'     => "(GMT+01:00) Ljubljana",
    'Europe/Madrid'        => "(GMT+01:00) Madrid",
    'Europe/Paris'         => "(GMT+01:00) Paris",
    'Europe/Prague'        => "(GMT+01:00) Prague",
    'Europe/Rome'          => "(GMT+01:00) Rome",
    'Europe/Sarajevo'      => "(GMT+01:00) Sarajevo",
    'Europe/Skopje'        => "(GMT+01:00) Skopje",
    'Europe/Stockholm'     => "(GMT+01:00) Stockholm",
    'Europe/Vienna'        => "(GMT+01:00) Vienna",
    'Europe/Warsaw'        => "(GMT+01:00) Warsaw",
    'Europe/Zagreb'        => "(GMT+01:00) Zagreb",
    'Europe/Athens'        => "(GMT+02:00) Athens",
    'Europe/Bucharest'     => "(GMT+02:00) Bucharest",
    'Africa/Cairo'         => "(GMT+02:00) Cairo",
    'Africa/Harare'        => "(GMT+02:00) Harare",
    'Europe/Helsinki'      => "(GMT+02:00) Helsinki",
    'Europe/Istanbul'      => "(GMT+02:00) Istanbul",
    'Asia/Jerusalem'       => "(GMT+02:00) Jerusalem",
    'Europe/Kiev'          => "(GMT+02:00) Kyiv",
    'Europe/Minsk'         => "(GMT+02:00) Minsk",
    'Europe/Riga'          => "(GMT+02:00) Riga",
    'Europe/Sofia'         => "(GMT+02:00) Sofia",
    'Europe/Tallinn'       => "(GMT+02:00) Tallinn",
    'Europe/Vilnius'       => "(GMT+02:00) Vilnius",
    'Asia/Baghdad'         => "(GMT+03:00) Baghdad",
    'Asia/Kuwait'          => "(GMT+03:00) Kuwait",
    'Africa/Nairobi'       => "(GMT+03:00) Nairobi",
    'Asia/Riyadh'          => "(GMT+03:00) Riyadh",
    'Europe/Moscow'        => "(GMT+03:00) Moscow",
    'Asia/Tehran'          => "(GMT+03:30) Tehran",
    'Asia/Baku'            => "(GMT+04:00) Baku",
    'Europe/Volgograd'     => "(GMT+04:00) Volgograd",
    'Asia/Muscat'          => "(GMT+04:00) Muscat",
    'Asia/Tbilisi'         => "(GMT+04:00) Tbilisi",
    'Asia/Yerevan'         => "(GMT+04:00) Yerevan",
    'Asia/Kabul'           => "(GMT+04:30) Kabul",
    'Asia/Karachi'         => "(GMT+05:00) Karachi",
    'Asia/Tashkent'        => "(GMT+05:00) Tashkent",
    'Asia/Kolkata'         => "(GMT+05:30) Kolkata",
    'Asia/Kathmandu'       => "(GMT+05:45) Kathmandu",
    'Asia/Yekaterinburg'   => "(GMT+06:00) Ekaterinburg",
    'Asia/Almaty'          => "(GMT+06:00) Almaty",
    'Asia/Dhaka'           => "(GMT+06:00) Dhaka",
    'Asia/Novosibirsk'     => "(GMT+07:00) Novosibirsk",
    'Asia/Bangkok'         => "(GMT+07:00) Bangkok",
    'Asia/Jakarta'         => "(GMT+07:00) Jakarta",
    'Asia/Makassar'        => "(GMT+08:00) Makassar",
    'Asia/Krasnoyarsk'     => "(GMT+08:00) Krasnoyarsk",
    'Asia/Chongqing'       => "(GMT+08:00) Chongqing",
    'Asia/Hong_Kong'       => "(GMT+08:00) Hong Kong",
    'Asia/Kuala_Lumpur'    => "(GMT+08:00) Kuala Lumpur",
    'Australia/Perth'      => "(GMT+08:00) Perth",
    'Asia/Singapore'       => "(GMT+08:00) Singapore",
    'Asia/Taipei'          => "(GMT+08:00) Taipei",
    'Asia/Ulaanbaatar'     => "(GMT+08:00) Ulaan Bataar",
    'Asia/Urumqi'          => "(GMT+08:00) Urumqi",
    'Asia/Irkutsk'         => "(GMT+09:00) Irkutsk",
    'Asia/Seoul'           => "(GMT+09:00) Seoul",
    'Asia/Tokyo'           => "(GMT+09:00) Tokyo",
    'Australia/Adelaide'   => "(GMT+09:30) Adelaide",
    'Australia/Darwin'     => "(GMT+09:30) Darwin",
    'Asia/Yakutsk'         => "(GMT+10:00) Yakutsk",
    'Australia/Brisbane'   => "(GMT+10:00) Brisbane",
    'Australia/Canberra'   => "(GMT+10:00) Canberra",
    'Pacific/Guam'         => "(GMT+10:00) Guam",
    'Australia/Hobart'     => "(GMT+10:00) Hobart",
    'Australia/Melbourne'  => "(GMT+10:00) Melbourne",
    'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
    'Australia/Sydney'     => "(GMT+10:00) Sydney",
    'Asia/Vladivostok'     => "(GMT+11:00) Vladivostok",
    'Asia/Magadan'         => "(GMT+12:00) Magadan",
    'Pacific/Auckland'     => "(GMT+12:00) Auckland",
    'Pacific/Fiji'         => "(GMT+12:00) Fiji",
);

/**
 * Generate random string for security key
 *
 * @return string Hashed random string
 */
function awqc_generate_random_string()
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);

    return sha1($characters[wp_rand(0, $charactersLength - 1)]);
}

/**
 * Validate form settings data
 *
 * @param array $item Form data to validate
 * @return bool|string True if valid, error message string if invalid
 */
function awqc_valid_form_settings($item)
{
    $messages = array();

    if (empty($item['start_time'])) {
        $messages[] = esc_html__('Start time is required', 'attendance_with_qr_code');
    }
    if (empty($item['out_time'])) {
        $messages[] = esc_html__('Out time is required', 'attendance_with_qr_code');
    }
    if (empty($item['key_insert'])) {
        $messages[] = esc_html__('Key is required', 'attendance_with_qr_code');
    }
    if (empty($item['timezone'])) {
        $messages[] = esc_html__('Time zone is required', 'attendance_with_qr_code');
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
        'start_time' => isset($_REQUEST['start_time']) ? sanitize_text_field(wp_unslash($_REQUEST['start_time'])) : '',
        'out_time' => isset($_REQUEST['out_time']) ? sanitize_text_field(wp_unslash($_REQUEST['out_time'])) : '',
        'key_insert' => isset($_REQUEST['key_insert']) ? sanitize_text_field(wp_unslash($_REQUEST['key_insert'])) : '',
        'timezone' => isset($_REQUEST['timezone']) ? sanitize_text_field(wp_unslash($_REQUEST['timezone'])) : '',
    );
    
    // validate data, and if all ok save item to database
    $awqc_item_valid = awqc_valid_form_settings($awqc_item);
    if ($awqc_item_valid === true) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $awqc_result = $wpdb->update(
            $awqc_table_name,
            array(
                'start_time' => $awqc_item['start_time'],
                'out_time' => $awqc_item['out_time'],
                'key_insert' => $awqc_item['key_insert'],
                'timezone' => $awqc_item['timezone'],
            ),
            array('id' => $awqc_item['id']),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );
        
        if ($awqc_result !== false) {
            $awqc_message = esc_html__('Successfully updated', 'attendance_with_qr_code');
        } else {
            $awqc_notice = esc_html__('There was an error while updating', 'attendance_with_qr_code');
        }
    } else {
        // if $awqc_item_valid not true it contains error message(s)
        $awqc_notice = $awqc_item_valid;
    }
}

// Show data form settings table
$awqc_table_name_escaped = esc_sql($awqc_table_name);
// Table name is already escaped with esc_sql()
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
$awqc_get_data = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$awqc_table_name_escaped} WHERE id = %d LIMIT 1",
    1
));

// Initialize default values if no data found
if (!$awqc_get_data) {
    $awqc_get_data = (object) array(
        'id' => 0,
        'start_time' => '08:00:00',
        'out_time' => '17:00:00',
        'key_insert' => '',
        'timezone' => 'Asia/Makassar'
    );
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Settings', 'attendance_with_qr_code'); ?></h1>
    <hr class="wp-header-end">

    <?php if (!empty($awqc_notice)) : ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo wp_kses_post($awqc_notice); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($awqc_message)) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($awqc_message); ?></p>
        </div>
    <?php endif; ?>

    <div class="notice notice-info" style="margin: 20px 0;">
        <p><strong><?php echo esc_html__('System Configuration', 'attendance_with_qr_code'); ?></strong> <?php echo esc_html__('Configure the attendance system settings including work hours, timezone, and security key. These settings affect how attendance is tracked and calculated.', 'attendance_with_qr_code'); ?></p>
    </div>

    <div class="row-col">
        <div class="col-60">
            <div class="awqc-form-item">
                <form id="form" method="POST">
                    <input type="hidden" name="id" value="<?php echo esc_attr($awqc_get_data->id); ?>">
                    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce(basename(__FILE__))); ?>" />

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="start_time"><?php echo esc_html__('Start Time', 'attendance_with_qr_code'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="start_time" name="start_time" class="regular-text" value="<?php echo esc_attr($awqc_get_data->start_time); ?>" placeholder="08:00:00" />
                                    <p class="description">
                                        <span class="dashicons dashicons-clock" aria-hidden="true"></span>
                                        <?php echo esc_html__('Default start time for work day. Format: 24-hour time (HH:MM:SS). Example: 08:00:00', 'attendance_with_qr_code'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="out_time"><?php echo esc_html__('End Time', 'attendance_with_qr_code'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="out_time" name="out_time" class="regular-text" value="<?php echo esc_attr($awqc_get_data->out_time); ?>" placeholder="17:00:00" />
                                    <p class="description">
                                        <span class="dashicons dashicons-clock" aria-hidden="true"></span>
                                        <?php echo esc_html__('Default end time for work day. Format: 24-hour time (HH:MM:SS). Example: 17:00:00', 'attendance_with_qr_code'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="key_insert"><?php echo esc_html__('Security Key', 'attendance_with_qr_code'); ?></label>
                                </th>
                                <td>
                                    <div class="display-flex">
                                        <input type="text" id="key" name="key_insert" class="regular-text" value="<?php echo esc_attr($awqc_get_data->key_insert); ?>" readonly />
                                        <button type="button" onclick="event.preventDefault(); awqcMyFunction();" class="button button-secondary">
                                            <span class="dashicons dashicons-update" style="margin-top: 3px;"></span> <?php echo esc_html__('Generate New Key', 'attendance_with_qr_code'); ?>
                                        </button>
                                    </div>
                                    <p class="description">
                                        <span class="dashicons dashicons-lock" aria-hidden="true"></span>
                                        <?php echo esc_html__('Security key for mobile app authentication. Click "Generate New Key" to create a new secure key.', 'attendance_with_qr_code'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="timezone"><?php echo esc_html__('Timezone', 'attendance_with_qr_code'); ?></label>
                                </th>
                                <td>
                                    <select name="timezone" id="timezone" class="regular-text">
                                        <?php
                                        foreach ($awqc_timezones as $awqc_timezone_key => $awqc_timezone_label) {
                                            $awqc_selected = ($awqc_timezone_key === $awqc_get_data->timezone) ? 'selected="selected"' : '';
                                            echo '<option value="' . esc_attr($awqc_timezone_key) . '" ' . esc_attr($awqc_selected) . '>' . esc_html($awqc_timezone_label) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="description">
                                        <span class="dashicons dashicons-admin-site" aria-hidden="true"></span>
                                        <?php echo esc_html__('Select your timezone. All attendance times will be recorded according to this timezone.', 'attendance_with_qr_code'); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <?php submit_button(esc_html__('Save Settings', 'attendance_with_qr_code'), 'primary', 'submit', false); ?>
                    </p>
                </form>
            </div>
        </div>

        <div class="col-40">
            <div class="awqc-qr-preview">
                <h3><?php echo esc_html__('Mobile App QR Code', 'attendance_with_qr_code'); ?></h3>
                <?php
                $awqc_path = site_url();
                $awqc_key  = isset($awqc_get_data->key_insert) ? $awqc_get_data->key_insert : '';
                if (empty($awqc_key)) {
                    echo '<p>' . esc_html__('Please generate a security key first.', 'attendance_with_qr_code') . '</p>';
                } else {
                    $awqc_qr_data = json_encode(array('url' => $awqc_path, 'key' => $awqc_key));
                    // Use local QR generator with caching for faster loading
                    $awqc_qr_image_url = awqc_get_qr_code_url($awqc_qr_data, 250);
                    
                    echo '<img src="' . esc_url($awqc_qr_image_url) . '" alt="' . esc_attr__('Mobile App QR Code', 'attendance_with_qr_code') . '">';
                    ?>
                    <p class="description" style="margin-top: 10px;">
                        <strong><?php echo esc_html__('First Time Setup', 'attendance_with_qr_code'); ?></strong><br>
                        <?php echo esc_html__('This QR code is used for the first time opening the mobile app. Scan this QR code once to configure the app with your website URL and security key.', 'attendance_with_qr_code'); ?>
                    </p>
                    <a href="<?php echo esc_url($awqc_qr_image_url); ?>" download="mobile-app-qr-code.png" class="button button-secondary button-center">
                        <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> <?php echo esc_html__('Download QR Code', 'attendance_with_qr_code'); ?>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

</div><!-- .wrap -->

<script>
    function awqcMyFunction() {
        document.getElementById("key").value = "<?php echo esc_js(awqc_generate_random_string()); ?>";
    }
</script>