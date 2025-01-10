<?php
function bypass_airplane_mode_for_sync_footer() {
    // Check if the Airplane Mode plugin is active and if we are in the admin area
    if (is_admin() && defined('AIRPLANEMODE') && AIRPLANEMODE) {
        // Enqueue the CSS for the Sync Footer plugin regardless of Airplane Mode
        wp_enqueue_style('sync-footer-style', 'https://natthasath.github.io/plugin-sync-footer-wordpress/assets/footer.css');
    }
}
add_action('wp_enqueue_scripts', 'bypass_airplane_mode_for_sync_footer', 20);