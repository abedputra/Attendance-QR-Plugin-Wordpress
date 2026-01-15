=== Attendance with QR Code ===
Contributors: muliatech, abedputra
Tags: attendance, qr code, employee tracking, student attendance, time tracking, location tracking
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.0
Stable tag: 3.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A comprehensive WordPress plugin for tracking employee or student attendance using QR codes with mobile app integration.

== Description ==

Attendance with QR Code is a powerful WordPress plugin designed for organizations, schools, and businesses that need an efficient attendance management system. Generate unique QR codes for each employee or student, and track their attendance through a mobile app that scans QR codes for quick check-in and check-out.

The plugin automatically calculates work hours, overtime, late time, and early departures. It includes comprehensive reporting features with search and filtering capabilities, and allows you to export attendance data to CSV or XLSX format for record keeping.

= Features =

* **QR Code Generation**: Generate unique QR codes for each employee or student
* **Mobile App Integration**: Scan QR codes using mobile app for attendance tracking
* **Automatic Calculations**: Work hours, overtime, late time, and early out time calculated automatically
* **Location Tracking**: GPS location recorded for attendance verification
* **Attendance Reports**: Comprehensive reporting with search and date filtering
* **CSV/XLSX Export**: Export attendance data for external use
* **WordPress User Import**: Import names from WordPress users database
* **Timezone Support**: Configure timezone for accurate time tracking
* **Settings Management**: Flexible configuration for work hours and system settings
* **Security Features**: Built-in security with nonce verification and input sanitization
* **Responsive Design**: Mobile-friendly admin interface

= Usage =

1. **Configure Settings**: Go to **Report** → **Settings** and configure work hours, timezone, and security key
2. **Generate QR Codes**: Go to **Report** → **Generate QR** and create QR codes for each employee/student
3. **Import Users** (Optional): Import names from WordPress users database
4. **Track Attendance**: Use mobile app to scan QR codes for check-in and check-out
5. **View Reports**: Access attendance reports with search and filtering options
6. **Export Data**: Export attendance data to CSV or XLSX format

= Mobile App Setup =

1. Generate the Mobile App QR Code from **Report** → **Settings**
2. Scan the QR code with the mobile app (one-time setup)
3. The app will be configured with your website URL and security key
4. Employees/students can now use the app to scan their personal QR codes

== Installation ==

1. Upload the `attendance_with_qr_code` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Report** → **Settings** to configure your attendance system
4. Generate QR codes from **Report** → **Generate QR**
5. Set up the mobile app using the QR code from Settings page

== Frequently Asked Questions ==

= How do I generate QR codes? =

Go to **Report** → **Generate QR**, enter the employee or student name, and click "Generate QR Code". Download and distribute the QR code to the respective person.

= Can I import names from WordPress users? =

Yes! Go to **Report** → **History QR** and click "Import Names From WordPress Users". The system will automatically import first name + last name from user meta.

= How do I configure work hours? =

Go to **Report** → **Settings** and set the Start Time and End Time (24-hour format, e.g., 08:00:00 for 8 AM). The system will use these times to calculate late time, overtime, and early out time.

= Can I export attendance data? =

Yes! Go to **Report** → **Report**, apply any filters if needed, and click "Export to CSV" or "Export to XLSX". The file will download with all filtered data.

= How do I search attendance records? =

In the **Report** page, you can search by name (partial match) or filter by date range. Combine both filters for precise results.

= What timezone does the system use? =

The system uses the timezone configured in **Report** → **Settings**. Make sure to select the correct timezone for your location.

= How do I set up the mobile app? =

1. Generate the Mobile App QR Code from **Report** → **Settings**
2. Scan this QR code with the mobile app (one-time setup)
3. The app will be configured with your website URL and security key
4. Employees/students can now scan their personal QR codes using the app

= Can I track location with attendance? =

Yes, if the mobile app supports GPS tracking, the system will record the location (GPS coordinates) when employees/students check in and check out.

= How does the system calculate work hours? =

Work hours are automatically calculated from the difference between check-out time and check-in time. Overtime is calculated if check-out time exceeds the configured end time.

= What if someone is late? =

The system automatically calculates late time based on the difference between actual check-in time and the configured start time. This is displayed in the attendance reports.

== Screenshots ==

1. Attendance Report with search and filtering
2. Generate QR Code interface
3. Settings page with work hours configuration
4. History QR with imported names
5. Export functionality

== Changelog ==

= 3.0.0 =
* Complete redesign of admin interface following WordPress standards
* Improved security with proper sanitization and escaping
* Enhanced code structure using plugin constants
* Improved form layouts and styling
* Better error handling and validation
* Enhanced export functionality
* Improved QR code generation with better preview
* Updated mobile app integration support
* Improved timezone configuration
* Enhanced location tracking support
* Code cleanup and optimization

= 2.x =
* Initial features implementation
* Basic QR code generation
* Attendance tracking functionality
* Basic report functionality

== Upgrade Notice ==

= 3.0.0 =
Major update with redesigned interface, improved security, and enhanced features. Update recommended for all users.
