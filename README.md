# Amhaj Divi Coming Soon

[![WordPress Version](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPL%20v2%20or%20later-orange.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-green.svg)](https://php.net)

**Amhaj Divi Coming Soon** is a lightweight, clean, and highly efficient WordPress plugin designed to put your website into "Coming Soon" or "Maintenance" mode. It is designed to work seamlessly with the Divi Builder (or any other WordPress theme/page builder), allowing administrators full access to build and preview the site while redirecting public visitors to a designated Coming Soon landing page.

---

## 🔒 Security Hardened

Security has been hardened using modern WordPress best practices:
- **Strict Capability Verification**: Restricts settings modification and update check actions specifically to administrators (`manage_options` capability).
- **Anti-CSRF Protection (Nonces)**: Uses WordPress Nonces to verify and authorize actions like forcing updates.
- **Output Escaping & Sanitization**: Employs `esc_html()`, `esc_attr()`, `esc_url()`, and `wp_kses_post()` across all settings views.
- **Direct Access Prevention**: Blocks direct file access to prevent directory traversal/unauthorized execution.

---

## 🔄 GitHub Auto-Updates

The plugin features a built-in, self-contained GitHub Update System that keeps the plugin updated automatically from the latest GitHub releases without requiring any third-party update plugins:
- **Release Tracking**: Monitors releases on the official GitHub repository (`almamunhossen/amhaj-divi-coming-soon`).
- **Performance Optimized (Transient Caching)**: Caches GitHub API responses locally for 12 hours to prevent any overhead or slow page loads.
- **Seamless WordPress Integration**: Integrates directly with native WordPress updates, showing release details and handling the download/folder renaming securely.
- **Manual Force-Check**: Includes a "Force Check Updates" button that clears the cache and polls GitHub immediately for the latest version.

---

## 🚀 Features

- **One-Click Activation**: Toggle your Coming Soon/Maintenance mode on or off instantly with a sleek iOS-style toggle.
- **Custom Page Selection**: Select any existing WordPress page as your Coming Soon page (e.g., custom-built with Divi).
- **Premium Admin Interface**: Features a beautiful modern dashboard UI with a pulsing status indicator (Green for Active, Amber for Inactive).
- **Seamless Admin Bypass**: Logged-in administrators can navigate, design, and preview the site normally without being redirected.
- **Developer-Friendly Logic**:
  - Automatically bypasses AJAX (`wp-doing-ajax()`), WP Cron (`wp-doing-cron()`), and REST API (`REST_REQUEST`) calls.
  - Bypasses post/page previews and RSS feeds.
  - Safe handling of `wp-login.php` to prevent login lockouts.
  - Standard safe redirects via `wp_safe_redirect`.
- **Zero Overhead**: Minimal footprint, written with performance in mind using native WordPress settings APIs.

---

## 🛠️ Installation

### Method 1: Manual Upload
1. Download the plugin files (or compress the `amhaj-divi-coming-soon` directory into a `.zip` archive).
2. Go to your WordPress Dashboard, navigate to **Plugins > Add New > Upload Plugin**.
3. Choose the `.zip` file and click **Install Now**.
4. Click **Activate Plugin**.

### Method 2: FTP Installation
1. Upload the entire `amhaj-divi-coming-soon` folder to the `/wp-content/plugins/` directory on your web server.
2. Go to **Plugins > Installed Plugins** in your WordPress Dashboard.
3. Find **Amhaj Divi Coming Soon** in the list and click **Activate**.

---

## ⚙️ Configuration & Usage

Once activated, follow these steps to configure the plugin:

1. Create a beautiful landing page using the Divi Page Builder (or your editor of choice) and publish it.
2. In your WordPress admin menu, navigate to **Settings > Amhaj Coming Soon**.
3. Select the page you created from the **Redirect Landing Page** dropdown.
4. Toggle the **Enable Coming Soon Mode** switch.
5. Click **Save Changes**.

Public (non-logged-in) users trying to access any page on your website will now be safely redirected to the selected Coming Soon page. Logged-in admins can continue editing and previewing all pages of the website.

---

## 🔍 How it Works

The plugin checks every non-admin request via the `template_redirect` action:
- It returns early (does nothing) if the request is inside the WP Admin, is an AJAX/Cron/REST request, is a feed/preview, or points to the login page.
- If the current user has the `manage_options` capability (e.g., Administrators), they are allowed full access to preview or build.
- If Coming Soon mode is active, any visitor requesting a page other than the designated Coming Soon page is redirected using `wp_safe_redirect`.

---

## 📄 License

This project is licensed under the GPL v2 or later.

---

## 👤 Author

* **Al Mamun Hossen**
* **Website**: [almamunhossen.com](https://www.almamunhossen.com)
* **Email**: [almamunhossen@gmail.com](mailto:almamunhossen@gmail.com)
