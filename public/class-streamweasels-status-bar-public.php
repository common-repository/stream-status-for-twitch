<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Streamweasels_Status_Bar
 * @subpackage Streamweasels_Status_Bar/public
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Status_Bar_Public {
    private $plugin_name;

    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-status-bar-public.min.css',
            array(),
            '',
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-status-bar-public.min.js',
            array(),
            '',
            false
        );
    }

    public function streamweasels_body_class( $classes ) {
        $options = get_option( 'swsb_options' );
        $twitchUsername = $options['swsb_twitch_username'] ?? false;
        $youtubeUsername = $options['swsb_youtube_username'] ?? false;
        $kickUsername = $options['swsb_kick_username'] ?? false;
        $usernameCount = 0;
        $twitchUsername && $usernameCount++;
        $youtubeUsername && $usernameCount++;
        $kickUsername && $usernameCount++;
        if ( $usernameCount > 0 ) {
            $classes[] = 'swsb-size--' . (( !empty( $options['swsb_bar_height'] ) ? $options['swsb_bar_height'] : 'medium' ));
            $classes[] = 'swsb-position--' . (( !empty( $options['swsb_bar_position'] ) ? $options['swsb_bar_position'] : 'top' ));
            $classes[] = 'swsb-mode--' . (( !empty( $options['swsb_bar_mode'] ) ? $options['swsb_bar_mode'] : 'fixed' ));
        }
        return $classes;
    }

    public function streamweasels_body_open() {
        if ( doing_action( 'wp_body_open' ) ) {
            remove_action( 'wp_footer', array($this, 'streamweasels_body_open') );
        }
        echo do_shortcode( '[sw-status-bar]' );
    }

    public function streamweasels_shortcode() {
        add_shortcode( 'sw-status-bar', array($this, 'get_streamweasels_shortcode') );
    }

    public function get_streamweasels_shortcode( $args ) {
        ob_start();
        include 'partials/streamweasels-status-bar-public-display.php';
        return ob_get_clean();
    }

}
