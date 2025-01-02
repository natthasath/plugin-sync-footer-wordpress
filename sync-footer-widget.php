<?php
/*
Plugin Name: Sync Footer Widget
Description: Sync footer content from a GitHub Gist and display in a widget.
Version: 1.0
Author: Natthasath Saksupanara
*/

// Register widget
class SyncFooterWidget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'sync_footer_widget',
            __('Sync Footer Widget', 'text_domain'),
            array('description' => __('Displays footer synced from GitHub Gist', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $gist_url = !empty($instance['gist_url']) ? $instance['gist_url'] : '';
        echo $args['before_widget'];
        if ($gist_url) {
            $response = wp_remote_get($gist_url);
            if (is_array($response) && !is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                echo $body;
            } else {
                echo '<p>Error fetching footer content.</p>';
            }
        } else {
            echo '<p>No Gist URL provided.</p>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        $gist_url = !empty($instance['gist_url']) ? $instance['gist_url'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('gist_url'); ?>">Gist URL:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('gist_url'); ?>" name="<?php echo $this->get_field_name('gist_url'); ?>" type="text" value="<?php echo esc_attr($gist_url); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['gist_url'] = (!empty($new_instance['gist_url'])) ? sanitize_text_field($new_instance['gist_url']) : '';
        return $instance;
    }
}

function register_sync_footer_widget() {
    register_widget('SyncFooterWidget');
}
add_action('widgets_init', 'register_sync_footer_widget');
