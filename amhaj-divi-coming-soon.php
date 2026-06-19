<?php
/*
Plugin Name: Amhaj Divi Coming Soon
Description: Display a selected page as a Coming Soon page while allowing admins full access.
Version: 1.0.1
Author: Al Mamun Hossen
Author URI: https://www.almamunhossen.com
Text Domain: amhaj-divi-coming-soon
Email: almamunhossen@gmail.com
*/

if (!defined('ABSPATH')) exit;

class Amhaj_Divi_Coming_Soon {
    const OPTION_ENABLED = 'adcs_enabled';
    const OPTION_PAGE_ID = 'adcs_page_id';

    public function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'handle_manual_update_check']);
        add_action('admin_notices', [$this, 'admin_notices']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('template_redirect', [$this, 'coming_soon_mode']);

        // Initialize self-contained GitHub auto-updater in admin screens
        if (is_admin()) {
            new Amhaj_Divi_Coming_Soon_Updater(__FILE__, 'almamunhossen', 'amhaj-divi-coming-soon');
        }
    }

    public function menu() {
        add_options_page(
            'Amhaj Divi Coming Soon',
            'Amhaj Coming Soon',
            'manage_options',
            'amhaj-coming-soon',
            [$this, 'settings_page']
        );
    }

    public function register_settings() {
        register_setting('adcs_group', self::OPTION_ENABLED, [
            'type' => 'boolean',
            'sanitize_callback' => [__CLASS__, 'sanitize_enabled'],
            'default' => 0,
        ]);
        register_setting('adcs_group', self::OPTION_PAGE_ID, [
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ]);
    }

    public static function sanitize_enabled($val) {
        return $val ? 1 : 0;
    }

    /**
     * Enqueue CSS and JS assets for the settings page.
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_amhaj-coming-soon') {
            return;
        }

        wp_enqueue_style(
            'adcs-admin-style',
            plugins_url('includes/admin-style.css', __FILE__),
            [],
            '1.0.1'
        );

        wp_enqueue_script(
            'adcs-admin-script',
            plugins_url('includes/admin-script.js', __FILE__),
            [],
            '1.0.1',
            true
        );

        wp_localize_script('adcs-admin-script', 'adcsAdminParams', [
            'confirm_text' => esc_html__('Are you sure you want to force check for updates? This will clear the cached data and poll GitHub directly.', 'amhaj-divi-coming-soon')
        ]);
    }

    /**
     * Handle manual check updates action safely using nonces and capability checks.
     */
    public function handle_manual_update_check() {
        if (isset($_GET['page']) && $_GET['page'] === 'amhaj-coming-soon' && isset($_GET['action']) && $_GET['action'] === 'check_updates') {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to check for updates.', 'amhaj-divi-coming-soon'));
            }
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'adcs_check_updates')) {
                wp_die(esc_html__('Security check failed. Please try again.', 'amhaj-divi-coming-soon'));
            }
            
            // Clear GitHub release cache transient
            delete_site_transient('adcs_github_release_info');
            
            // Force WordPress to re-evaluate plugin updates
            delete_site_transient('update_plugins');
            
            wp_safe_redirect(admin_url('options-general.php?page=amhaj-coming-soon&updates_checked=1'));
            exit;
        }
    }

    /**
     * Display success notice after manual update check.
     */
    public function admin_notices() {
        if (isset($_GET['page']) && $_GET['page'] === 'amhaj-coming-soon' && isset($_GET['updates_checked']) && $_GET['updates_checked'] === '1') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('GitHub updates cache cleared! WordPress will look for update releases now.', 'amhaj-divi-coming-soon'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Custom styled administrative settings interface.
     */
    public function settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'amhaj-divi-coming-soon'));
        }

        $enabled = (int) get_option(self::OPTION_ENABLED);
        $selected_page_id = (int) get_option(self::OPTION_PAGE_ID);
        $selected_page_title = $selected_page_id ? get_the_title($selected_page_id) : '';
        $permalink = $selected_page_id ? get_permalink($selected_page_id) : '#';
        ?>
        <div class="wrap">
            <div class="adcs-wrap">
                <div class="adcs-header">
                    <div>
                        <h1 class="adcs-title"><?php esc_html_e('Amhaj Coming Soon Panel', 'amhaj-divi-coming-soon'); ?></h1>
                        <div class="adcs-subtitle"><?php esc_html_e('Manage your site visibility during construction.', 'amhaj-divi-coming-soon'); ?></div>
                    </div>
                    <span class="adcs-badge"><?php echo esc_html('v1.0.1'); ?></span>
                </div>

                <div class="adcs-grid">
                    <div class="adcs-card">
                        <h2 class="adcs-card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <?php esc_html_e('Settings Configuration', 'amhaj-divi-coming-soon'); ?>
                        </h2>

                        <form method="post" action="options.php">
                            <?php settings_fields('adcs_group'); ?>
                            
                            <div class="adcs-form-group">
                                <label class="adcs-form-label"><?php esc_html_e('Enable Coming Soon Mode', 'amhaj-divi-coming-soon'); ?></label>
                                <label class="adcs-switch">
                                    <input type="hidden" name="<?php echo esc_attr(self::OPTION_ENABLED); ?>" value="0" />
                                    <input type="checkbox" name="<?php echo esc_attr(self::OPTION_ENABLED); ?>" value="1" <?php checked($enabled, 1); ?>>
                                    <span class="adcs-slider"></span>
                                </label>
                                <div class="adcs-form-help">
                                    <?php esc_html_e('When active, non-admin visitors will be redirected to the landing page below.', 'amhaj-divi-coming-soon'); ?>
                                </div>
                            </div>

                            <div class="adcs-form-group">
                                <label class="adcs-form-label" for="<?php echo esc_attr(self::OPTION_PAGE_ID); ?>"><?php esc_html_e('Redirect Landing Page', 'amhaj-divi-coming-soon'); ?></label>
                                <?php
                                wp_dropdown_pages([
                                    'name' => self::OPTION_PAGE_ID,
                                    'id' => self::OPTION_PAGE_ID,
                                    'selected' => $selected_page_id,
                                    'show_option_none' => '-- ' . esc_html__('Select Page', 'amhaj-divi-coming-soon') . ' --',
                                    'class' => 'adcs-select'
                                ]);
                                ?>
                                <div class="adcs-form-help">
                                    <?php esc_html_e('Select the page you have built for Coming Soon/Maintenance mode.', 'amhaj-divi-coming-soon'); ?>
                                </div>
                            </div>

                            <div style="margin-top: 30px;">
                                <button type="submit" class="adcs-btn-submit">
                                    <?php esc_html_e('Save Changes', 'amhaj-divi-coming-soon'); ?>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <div class="adcs-card" style="margin-bottom: 20px;">
                            <h3 class="adcs-card-title" style="font-size: 16px; margin-bottom: 15px;">
                                <?php esc_html_e('Current Status', 'amhaj-divi-coming-soon'); ?>
                            </h3>
                            <div class="adcs-status-indicator">
                                <span class="adcs-pulse <?php echo $enabled ? 'active' : 'inactive'; ?>"></span>
                                <strong>
                                    <?php echo $enabled ? esc_html__('Coming Soon is ACTIVE', 'amhaj-divi-coming-soon') : esc_html__('Coming Soon is INACTIVE', 'amhaj-divi-coming-soon'); ?>
                                </strong>
                            </div>
                            <?php if ($enabled && $selected_page_id): ?>
                                <div style="font-size: 13px; color: #94a3b8;">
                                    <?php esc_html_e('Redirecting visitors to:', 'amhaj-divi-coming-soon'); ?><br>
                                    <a href="<?php echo esc_url($permalink); ?>" target="_blank" style="color: #38bdf8; text-decoration: none; word-break: break-all;">
                                        <?php echo esc_html($selected_page_title ? $selected_page_title : $permalink); ?> &rarr;
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="adcs-card">
                            <h3 class="adcs-card-title" style="font-size: 16px; margin-bottom: 15px;">
                                <?php esc_html_e('GitHub Auto-Updates', 'amhaj-divi-coming-soon'); ?>
                            </h3>
                            <ul class="adcs-meta-list">
                                <li class="adcs-meta-item">
                                    <span class="adcs-meta-label"><?php esc_html_e('GitHub Repository', 'amhaj-divi-coming-soon'); ?></span>
                                    <span class="adcs-meta-value">almamunhossen/amhaj-divi-coming-soon</span>
                                </li>
                                <li class="adcs-meta-item">
                                    <span class="adcs-meta-label"><?php esc_html_e('Local Version', 'amhaj-divi-coming-soon'); ?></span>
                                    <span class="adcs-meta-value">1.0.1</span>
                                </li>
                                <li class="adcs-meta-item">
                                    <span class="adcs-meta-label"><?php esc_html_e('Update System', 'amhaj-divi-coming-soon'); ?></span>
                                    <span class="adcs-meta-value" style="color: #10b981;"><?php esc_html_e('Connected', 'amhaj-divi-coming-soon'); ?></span>
                                </li>
                            </ul>
                            
                            <?php
                            $check_updates_url = wp_nonce_url(
                                add_query_arg([
                                    'action' => 'check_updates'
                                ], admin_url('options-general.php?page=amhaj-coming-soon')),
                                'adcs_check_updates'
                            );
                            ?>
                            <a href="<?php echo esc_url($check_updates_url); ?>" class="adcs-btn-secondary" style="width: 100%; box-sizing: border-box;">
                                <?php esc_html_e('Force Check Updates', 'amhaj-divi-coming-soon'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function coming_soon_mode() {
        if (is_admin()) {
            return;
        }

        if (!get_option(self::OPTION_ENABLED)) {
            return;
        }

        if (current_user_can('manage_options')) {
            return;
        }

        if (wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }

        if (is_feed() || is_preview()) {
            return;
        }

        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
            return;
        }

        $page_id = absint(get_option(self::OPTION_PAGE_ID));
        if (!$page_id) {
            return;
        }

        if (is_page($page_id)) {
            return;
        }

        $permalink = get_permalink($page_id);
        if (!$permalink) {
            return;
        }

        wp_safe_redirect($permalink);
        exit;
    }

    /**
     * Flush rewrite rules on activation.
     */
    public static function activate() {
        flush_rewrite_rules();
    }

    /**
     * Flush rewrite rules on deactivation.
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * Self-contained class to handle automatic updates directly from GitHub releases.
 */
class Amhaj_Divi_Coming_Soon_Updater {
    private $file;
    private $slug;
    private $username;
    private $repo;
    private $github_data;
    private $local_data;

    public function __construct($file, $username, $repo) {
        $this->file = $file;
        $this->slug = plugin_basename($file);
        $this->username = $username;
        $this->repo = $repo;

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_popup_details'], 20, 3);
        add_filter('upgrader_source_selection', [$this, 'source_selection'], 10, 4);
    }

    /**
     * Retrieve local plugin headers securely and cache them in memory.
     */
    private function get_local_plugin_data() {
        if (!empty($this->local_data)) {
            return $this->local_data;
        }
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $this->local_data = get_plugin_data($this->file);
        return $this->local_data;
    }

    /**
     * Fetch latest release details from GitHub API (or cached transient).
     */
    private function get_github_release_info() {
        if (!empty($this->github_data)) {
            return $this->github_data;
        }

        $transient_key = 'adcs_github_release_info';
        $cached = get_site_transient($transient_key);
        if ($cached !== false) {
            return $cached;
        }

        $url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases/latest";
        
        $args = [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            ],
            'timeout' => 10,
        ];

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            set_site_transient($transient_key, [], HOUR_IN_SECONDS);
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data) || isset($data['message'])) {
            set_site_transient($transient_key, [], HOUR_IN_SECONDS);
            return false;
        }

        set_site_transient($transient_key, $data, 12 * HOUR_IN_SECONDS);
        $this->github_data = $data;
        return $data;
    }

    /**
     * Check if a newer version is available on GitHub.
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release = $this->get_github_release_info();
        if (!$release || empty($release['tag_name'])) {
            return $transient;
        }

        $github_version = ltrim($release['tag_name'], 'v');
        $plugin_data = $this->get_local_plugin_data();
        $current_version = $plugin_data['Version'];

        if (version_compare($github_version, $current_version, '>')) {
            $obj = new stdClass();
            $obj->slug = dirname($this->slug);
            $obj->plugin = $this->slug;
            $obj->new_version = $github_version;
            $obj->url = $plugin_data['PluginURI'];
            $obj->package = $release['zipball_url'];

            $transient->response[$this->slug] = $obj;
        }

        return $transient;
    }

    /**
     * Provide details modal info when plugin details are viewed.
     */
    public function plugin_popup_details($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (empty($args->slug) || $args->slug !== dirname($this->slug)) {
            return $result;
        }

        $release = $this->get_github_release_info();
        if (!$release || empty($release['tag_name'])) {
            return $result;
        }

        $plugin_data = $this->get_local_plugin_data();
        $github_version = ltrim($release['tag_name'], 'v');

        $res = new stdClass();
        $res->name = $plugin_data['Name'];
        $res->slug = dirname($this->slug);
        $res->version = $github_version;
        $res->author = $plugin_data['AuthorName'];
        $res->homepage = $plugin_data['PluginURI'];
        $res->download_link = $release['zipball_url'];
        
        $res->sections = [
            'description' => wp_kses_post($plugin_data['Description']),
            'changelog'   => wpautop(wp_kses_post($release['body'] ?? '')),
        ];

        return $res;
    }

    /**
     * Ensure correct folder extraction naming during installs.
     */
    public function source_selection($source, $remote_source, $upgrader, $hook_extra) {
        global $wp_filesystem;
        
        if (isset($hook_extra['plugin']) && $hook_extra['plugin'] === $this->slug) {
            $correct_dir = dirname($this->slug);
            $source_dir = basename($source);
            
            if ($source_dir !== $correct_dir) {
                $new_source = dirname($source) . '/' . $correct_dir;
                if ($wp_filesystem->move($source, $new_source)) {
                    return $new_source;
                }
            }
        }
        return $source;
    }
}

new Amhaj_Divi_Coming_Soon();

register_activation_hook(__FILE__, ['Amhaj_Divi_Coming_Soon', 'activate']);
register_deactivation_hook(__FILE__, ['Amhaj_Divi_Coming_Soon', 'deactivate']);

