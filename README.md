<img width="1536" height="1024" alt="Attendance Qr Code Plugin WP and Flutter App" src="https://github.com/user-attachments/assets/2b484743-ea9c-4fc2-82af-019d0dfd38c7" />

# Attendance with QR Code - WordPress Plugin

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.0%2B-purple.svg)](https://php.net/)

A comprehensive WordPress plugin for tracking employee or student attendance using QR codes. Perfect for organizations, schools, and businesses that need an efficient attendance management system.

**This plugin works seamlessly with the [Attendance with QR Code Flutter Mobile App](https://github.com/abedputra/Attendance-QR-Flutter-App) to provide a complete attendance management solution.**

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Usage](#-usage)
- [Mobile App Integration](#-mobile-app-integration)
- [Admin Features](#-admin-features)
- [Export & Reports](#-export--reports)
- [Related Projects](#-related-projects)
- [Contributing](#-contributing)
- [Support](#-support)
- [License](#-license)

## ✨ Features

### Core Features
- **QR Code Generation**: Generate unique QR codes for each employee or student
- **QR Code Scanning**: Scan QR codes using mobile app for quick attendance check-in/check-out
- **Attendance Tracking**: Automatic tracking of in-time, out-time, and work hours
- **Location Tracking**: Record GPS location for attendance verification
- **Late Time Calculation**: Automatic calculation of late arrivals and early departures
- **Overtime Tracking**: Track overtime hours automatically
- **Work Hours Calculation**: Automatic calculation of total work hours per day
- **Attendance Reports**: Comprehensive reporting system with search and filtering
- **CSV/XLSX Export**: Export attendance data to CSV or XLSX format
- **WordPress User Integration**: Import names from WordPress users database
- **Timezone Support**: Configure timezone for accurate time tracking
- **Settings Management**: Flexible configuration for work hours and system settings
- **Responsive Design**: Mobile-friendly admin interface
- **Security**: Built-in security features including nonce verification and input sanitization

### User Experience
- Clean, modern admin interface
- Easy QR code generation
- Quick attendance history lookup
- Visual attendance reports with date filtering
- Export functionality for record keeping
- Search by name or date range

## 📦 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.0 or higher
- **MySQL**: 5.6 or higher
- **Mobile App**: [Flutter Mobile App](https://github.com/abedputra/Attendance-QR-Flutter-App) for QR code scanning (required for full functionality)

## 🚀 Installation

### Method 1: Manual Installation

1. **Download the plugin**
   - Download the plugin files or clone the repository

2. **Upload to WordPress**
   - Navigate to your WordPress installation directory
   - Go to `wp-content/plugins/`
   - Upload the `attendance_with_qr_code` folder here

3. **Activate the plugin**
   - Log in to your WordPress admin panel
   - Go to **Plugins** → **Installed Plugins**
   - Find **Attendance with QR Code** and click **Activate**

4. **Configure settings**
   - Go to **Report** → **Settings** in the admin menu
   - Set your work hours, timezone, and security key
   - Save settings

### Method 2: WordPress Admin Upload

1. **Zip the plugin folder**
   - Compress the `attendance_with_qr_code` folder into a `.zip` file

2. **Upload via WordPress**
   - Go to **Plugins** → **Add New** → **Upload Plugin**
   - Choose the zip file and click **Install Now**
   - Click **Activate Plugin**

3. **Configure settings**
   - Go to **Report** → **Settings**
   - Configure your attendance system settings

## 🎯 Quick Start

1. **Configure System Settings**
   - Go to **Report** → **Settings**
   - Set your **Start Time** (e.g., 08:00:00)
   - Set your **End Time** (e.g., 17:00:00)
   - Enter the **Number of Users**
   - Generate a **Security Key** for mobile app authentication
   - Select your **Timezone**
   - Click **Save Settings**

2. **Generate QR Codes**
   - Go to **Report** → **Generate QR**
   - Enter employee/student name
   - Click **Generate QR Code**
   - Download and distribute the QR code

3. **Import Users (Optional)**
   - Go to **Report** → **History QR**
   - Click **Import Names From WordPress Users**
   - System will automatically import user names from WordPress database

4. **Track Attendance**
   - Use the mobile app to scan QR codes
   - Employees/students scan QR code for check-in
   - Scan again for check-out
   - System automatically records attendance data

5. **View Reports**
   - Go to **Report** → **Report**
   - Search by name or filter by date range
   - Export data to CSV or XLSX format

## 📖 Usage

### Generating QR Codes

1. Navigate to **Report** → **Generate QR**
2. Enter the full name of the employee or student
3. Click **Generate QR Code**
4. Download and print the QR code
5. Distribute to the respective person

**Note**: Each QR code is unique and tied to the person's name. Make sure to generate separate QR codes for each individual.

### Importing Names from WordPress Users

If you have users in your WordPress database, you can quickly import their names:

1. Go to **Report** → **History QR**
2. Click **Import Names From WordPress Users**
3. The system will automatically import:
   - First Name + Last Name from user meta
   - Skip users without names
   - Skip duplicate entries

### Viewing Attendance Reports

1. Go to **Report** → **Report**
2. Use the search functionality:
   - **Search by Name**: Enter a name to filter
   - **Date From**: Start date for filtering
   - **Date To**: End date for filtering
3. Click **Search** to filter results
4. View attendance data including:
   - Name
   - Date
   - In Time
   - Out Time
   - Work Hours
   - Overtime
   - Late Time
   - Early Out Time
   - Location (if available)

### Exporting Attendance Data

1. Go to **Report** → **Report**
2. Apply any filters (optional)
3. Click **Export to CSV** or **Export to XLSX**
4. The file will download with all filtered data

### Managing QR Code History

1. Go to **Report** → **History QR**
2. View all generated QR codes
3. Search for specific names
4. Delete QR codes if needed (will remove from history)

## 📱 Mobile App Integration

This plugin is designed to work seamlessly with the **Attendance with QR Code Flutter Mobile App** for scanning QR codes and recording attendance.

### Flutter Mobile App

**Official Mobile App Repository**: [https://github.com/abedputra/Attendance-QR-Flutter-App](https://github.com/abedputra/Attendance-QR-Flutter-App)

The companion Flutter mobile app provides:
- **QR Code Scanning**: Fast and accurate QR code scanning
- **Check-In/Check-Out**: Simple one-tap attendance recording
- **GPS Location Tracking**: Automatic location recording
- **Offline Support**: Local database storage
- **Attendance History**: View all records locally
- **Modern UI**: Clean Material Design interface

### First Time Setup

1. **Install the Mobile App**
   - Download and install the Flutter app from the GitHub repository
   - Or build from source: [https://github.com/abedputra/Attendance-QR-Flutter-App](https://github.com/abedputra/Attendance-QR-Flutter-App)

2. **Generate Configuration QR Code**
   - Go to **Report** → **Settings** in WordPress admin
   - Generate the **Mobile App QR Code** (contains website URL and security key)

3. **Configure the App**
   - Open the Flutter mobile app
   - On first launch, you'll see the setup screen
   - Scan the configuration QR code from WordPress settings
   - The app will automatically configure:
     - Server URL (your WordPress site URL)
     - Security Key (for API authentication)

4. **Grant Permissions**
   - Allow camera access (for QR scanning)
   - Allow location access (for GPS tracking)

### How It Works

1. **Check-In Process**
   - Employee/student opens the Flutter mobile app
   - Navigates to **Check-In** from main menu
   - Waits for GPS location to be accurate
   - Taps **Scan QR** button
   - Scans their personal QR code (generated from WordPress)
   - System records:
     - Name (from QR code)
     - Date (determined by server, not device)
     - In Time (determined by server based on timezone settings)
     - Location (GPS coordinates from device)
     - Location accuracy

2. **Check-Out Process**
   - Employee/student opens the Flutter mobile app
   - Navigates to **Check-Out** from main menu
   - Waits for GPS location to be accurate
   - Taps **Scan QR** button
   - Scans their personal QR code
   - System records:
     - Out Time (determined by server)
     - Location (GPS coordinates)
     - Calculates work hours, overtime, late time, early out time

### API Integration

The mobile app communicates with WordPress via REST API:

**Endpoint**: `{your-site-url}/wp-content/plugins/attendance_with_qr_code/insert-attendance.php`

**Request Method**: POST

**Request Parameters**:
- `key`: Security key (from WordPress settings)
- `name`: Employee/student name (from QR code)
- `q`: Command ('in' for check-in, 'out' for check-out)
- `location`: GPS location string

**Response Format**:
```json
{
  "success": true,
  "data": {
    "message": "Check-in successful!",
    "date": "2025-01-15",
    "time": "14:30:00",
    "location": "Location string",
    "query": "Check-in"
  }
}
```

### Security Features

- **Server-Side Time**: Date and time are determined by the WordPress server based on configured timezone, preventing time manipulation
- **Security Key Authentication**: All API requests require valid security key
- **Input Validation**: All inputs validated on both client and server
- **Location Verification**: GPS location recorded for attendance verification

### Mobile App Features

- ✅ QR code scanning with camera
- ✅ GPS location tracking
- ✅ Location accuracy indicator
- ✅ Offline database storage
- ✅ Attendance history viewing
- ✅ Settings management
- ✅ Error handling and user feedback
- ✅ Modern Material Design UI

## 🎛️ Admin Features

### Attendance Report
- View all attendance records
- Search by employee/student name
- Filter by date range
- Export to CSV or XLSX
- Visual table with sorting capabilities

### Generate QR Code
- Generate unique QR codes for individuals
- Preview QR code before downloading
- Download high-quality QR code images
- Name validation

### History QR
- View all generated QR codes
- Search functionality
- Import names from WordPress users
- Delete QR codes
- Bulk management

### Settings
- Configure work hours (start time and end time)
- Set number of users
- Generate security key for mobile app
- Timezone configuration
- Mobile app QR code for initial setup

## 📊 Export & Reports

### Export Formats

- **CSV**: Comma-separated values format
  - Compatible with Excel, Google Sheets, etc.
  - Easy to import into other systems

- **XLSX**: Excel format
  - Native Excel format
  - Preserves formatting

### Report Data Includes

- Employee/Student Name
- Date
- In Time
- Out Time
- Work Hours (calculated)
- Overtime Hours (calculated)
- Late Time (if late)
- Early Out Time (if left early)
- In Location (GPS coordinates)
- Out Location (GPS coordinates)

### Filtering Options

- Filter by name (partial match)
- Filter by date range
- Combine filters for precise results

## 🔒 Security Features

- **Nonce Verification**: All forms use WordPress nonce verification
- **Input Sanitization**: All user inputs are sanitized
- **Output Escaping**: All outputs are escaped to prevent XSS
- **Capability Checks**: Admin pages require `manage_options` capability
- **SQL Injection Protection**: All database queries use prepared statements
- **Direct Access Prevention**: All files check for ABSPATH

## 🎨 Customization

The plugin includes CSS classes that you can customize:

- `.awqc-form-item`: Form container styling
- `.awqc-qr-preview`: QR code preview box
- `.awqc-search-form`: Search form container
- `.wp-list-table`: Table styling (WordPress default)

Add custom CSS in your theme's `style.css` or use a custom CSS plugin to match your site's design.

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

### How to Contribute

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make your changes**
   - Follow WordPress coding standards
   - Add comments for complex logic
   - Test your changes thoroughly
4. **Commit your changes**
   ```bash
   git commit -m "Add: Description of your feature"
   ```
5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```
6. **Create a Pull Request**

### Contribution Guidelines

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Write clear commit messages
- Test your code before submitting
- Update documentation if needed
- Be respectful and constructive in discussions

### Areas for Contribution

- 🐛 Bug fixes
- ✨ New features
- 📚 Documentation improvements
- 🎨 UI/UX enhancements
- 🌐 Translations
- ⚡ Performance optimizations
- 🔒 Security improvements
- 📱 Mobile app enhancements (contribute to [Flutter App Repository](https://github.com/abedputra/Attendance-QR-Flutter-App))

## 📚 Related Projects

- **Flutter Mobile App**: [Attendance with QR Code Flutter App](https://github.com/abedputra/Attendance-QR-Flutter-App)
  - Official companion mobile app for this WordPress plugin
  - Built with Flutter for Android and iOS
  - Provides QR code scanning and attendance recording
  - Open source and available on GitHub

## 📧 Support

- **Developer**: Abed Putra
- **Website**: [https://abedputra.my.id](https://abedputra.my.id)
- **GitHub**: [https://github.com/abedputra](https://github.com/abedputra)

If you encounter any issues or have questions:
1. Check the documentation above
2. Review the FAQ section
3. Check the [Flutter App Repository](https://github.com/abedputra/Attendance-QR-Flutter-App) for mobile app issues
4. Contact support through the developer's website

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Abed Putra

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 🙏 Credits

- Built with ❤️ by [Abed Putra](https://abedputra.my.id)
- Uses WordPress core functionality
- Icons from WordPress Dashicons
- QR Code generation via [QR Server API](https://goqr.me/api/)
- Mobile app built with [Flutter](https://flutter.dev/)

## 📚 Changelog

### Version 3.0.0
- Complete redesign of admin interface
- Improved security with proper sanitization and escaping
- Enhanced code structure following WordPress standards
- Updated to use plugin constants
- Improved form layouts and styling
- Better error handling
- Enhanced export functionality
- Improved QR code generation
- Mobile app integration support
- Timezone configuration
- Location tracking support

### Version 2.x
- Initial features implementation
- Basic QR code generation
- Attendance tracking
- Report functionality

---

**Made with ❤️ for the WordPress community**
