<?php
/*
Plugin Name: Sync Footer Widget
Description: Sync footer content from a GitHub Page and display it in a widget. Works even with Airplane Mode enabled.
Version: 1.2
Author: Natthasath Saksupanara, Wanwisa Lohachat
Requires at least: 5.6
Requires PHP: 7.4
Tested up to: 6.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: sync-footer-widget
Domain Path: /languages
*/

// Disable Airplane Mode for Sync Footer Widget
add_filter('airplane_mode_is_enabled', function($enabled) {
    if (did_action('widgets_init')) {
        return false; // Disable Airplane Mode
    }
    return $enabled;
});

// Allow external requests specifically for the Sync Footer Widget
add_filter('pre_http_request', function($pre, $args, $url) {
    if (strpos($url, 'github.io') !== false) {
        return false; // Allow the request to proceed
    }
    return $pre;
}, 10, 3);

// Register widget
class SFW_SyncFooterWidget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'sfw_sync_footer_widget',
            __('Sync Footer Widget', 'sync-footer-widget'),
            array('description' => __('Displays footer synced from GitHub Pages', 'sync-footer-widget'))
        );
    }

    public function widget($args, $instance) {
        $github_url = !empty($instance['github_url']) ? $instance['github_url'] : '';
        echo $args['before_widget'];
        if ($github_url) {
            $response = wp_remote_get($github_url, array('timeout' => 10, 'reject_unsafe_urls' => false));
            if (is_array($response) && !is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                echo $body; // Output the HTML content from GitHub Pages
            } else {
                echo '<p>' . esc_html__('Error fetching footer content. Please check the GitHub URL.', 'sync-footer-widget') . '</p>';
            }
        } else {
            echo '<p>' . esc_html__('No GitHub URL provided.', 'sync-footer-widget') . '</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $github_url = !empty($instance['github_url']) ? $instance['github_url'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('github_url'); ?>">
                <?php esc_html_e('GitHub Pages URL:', 'sync-footer-widget'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('github_url'); ?>" name="<?php echo $this->get_field_name('github_url'); ?>" type="text" value="<?php echo esc_attr($github_url); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['github_url'] = (!empty($new_instance['github_url'])) ? esc_url_raw($new_instance['github_url']) : '';
        return $instance;
    }
}

// Register widget
function sfw_register_sync_footer_widget() {
    register_widget('SFW_SyncFooterWidget');
}
add_action('widgets_init', 'sfw_register_sync_footer_widget');
