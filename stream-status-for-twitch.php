<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.streamweasels.com
 * @since             1.0.0
 * @package           Streamweasels_Status_Bar
 *
 * @wordpress-plugin
 * Plugin Name:       SW Status Bar - Display Online Status for Twitch / Kick / YouTube
 * Plugin URI:        https://www.streamweasels.com
 * Description:       Display Twitch / Kick / YouTube Live Status.
 * Version:           2.1.8
 * Author:            StreamWeasels
 * Author URI:        https://www.streamweasels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       streamweasels-status-bar
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'STREAMWEASELS_STATUS_BAR_VERSION', '2.1.8' );
if ( function_exists( 'ssb_fs' ) ) {
    ssb_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'ssb_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ssb_fs() {
            global $ssb_fs;
            if ( !isset( $ssb_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ssb_fs = fs_dynamic_init( array(
                    'id'             => '14573',
                    'slug'           => 'streamweasels-status-bar',
                    'premium_slug'   => 'streamweasels-status-bar-paid',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_5c26c2ee476895aa72e8321a4a29a',
                    'is_premium'     => false,
                    'premium_suffix' => '(Paid)',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 10,
                        'is_require_payment' => true,
                    ),
                    'menu'           => array(
                        'slug'    => 'streamweasels-status-bar',
                        'support' => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $ssb_fs;
        }

        // Init Freemius.
        ssb_fs();
        // Signal that SDK was initiated.
        do_action( 'ssb_fs_loaded' );
    }
    // Plugin Folder Path
    if ( !defined( 'SWSB_PLUGIN_DIR' ) ) {
        define( 'SWSB_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-streamweasels-status-bar-activator.php
     */
    function activate_streamweasels_status_bar() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-status-bar-activator.php';
        Streamweasels_Status_Bar_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-streamweasels-status-bar-deactivator.php
     */
    function deactivate_streamweasels_status_bar() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-status-bar-deactivator.php';
        Streamweasels_Status_Bar_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'activate_streamweasels_status_bar' );
    register_deactivation_hook( __FILE__, 'deactivate_streamweasels_status_bar' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-status-bar.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_streamweasels_status_bar() {
        $plugin = new Streamweasels_Status_Bar();
        $plugin->run();
    }

    run_streamweasels_status_bar();
}