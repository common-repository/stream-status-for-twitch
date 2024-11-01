<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Streamweasels_Status_Bar
 * @subpackage Streamweasels_Status_Bar/admin
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Status_Bar_Admin {
    private $plugin_name;

    private $version;

    private $options;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = $this->swsb_get_options();
    }

    public function add_rest_endpoints() {
        $fetchData = new SWSB_Twitch_API();
        $youTubeData = new SWSB_YouTube_API();
        $kickData = new SWSB_Kick_API();
        // rest route for fetching streams
        register_rest_route( 'swsb/v1', '/fetch-streams', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swsb_fetch_streams'),
            'permission_callback' => '__return_true',
        ) );
        register_rest_route( 'swsb/v1', '/fetch-youtube', array(
            'methods'             => 'GET',
            'callback'            => array($youTubeData, 'swsb_fetch_youtube'),
            'permission_callback' => '__return_true',
        ) );
        register_rest_route( 'swsb/v1', '/fetch-youtube-viewers', array(
            'methods'             => 'GET',
            'callback'            => array($youTubeData, 'swsb_fetch_youtube_viewers'),
            'permission_callback' => '__return_true',
        ) );
        register_rest_route( 'swsb/v1', '/fetch-youtube-category', array(
            'methods'             => 'GET',
            'callback'            => array($youTubeData, 'swsb_fetch_youtube_category'),
            'permission_callback' => '__return_true',
        ) );
        register_rest_route( 'swsb/v1', '/fetch-kick', array(
            'methods'             => 'GET',
            'callback'            => array($kickData, 'swsb_fetch_kick'),
            'permission_callback' => '__return_true',
        ) );
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-status-bar-admin.min.css',
            array(),
            '',
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style( 'wp-color-picker' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/streamweasels-status-bar-admin.js',
            array('jquery', 'wp-color-picker'),
            '',
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    // public function enqueue_blocks() {
    //     // Register your custom block using register_block_type
    // 	$blocks_json_path = plugin_dir_path(dirname(__FILE__)) . 'build/';
    // 	register_block_type( $blocks_json_path . 'block.json', array(
    // 		'render_callback' => array($this, 'enqueue_status_bar_cb'),
    // 	) );
    // }
    // public function enqueue_status_bar_cb( $attr ) {
    // 	$output = '<div ' . get_block_wrapper_attributes() . '>';
    // 	$output .= do_shortcode('[sw-status-bar]');
    // 	$output .= '</div>';
    // 	return $output;
    // }
    /**
     * Register the admin page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        add_menu_page(
            'StreamWeasels',
            'Online Status Bar',
            'manage_options',
            'streamweasels-status-bar',
            array($this, 'swsb_showAdmin'),
            'dashicons-warning'
        );
        $tooltipArray = array(
            'Game' => 'Game <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="game=\'\'"></span>',
        );
        // register settings
        register_setting( 'swsb_options', 'swsb_options', array($this, 'swsb_options_validate') );
        // License Settings section
        add_settings_section(
            'swsb_twitch_settings',
            'Twitch Connection',
            false,
            'swsb_twitch_fields'
        );
        add_settings_section(
            'swsb_youtube_settings',
            'YouTube Connection',
            false,
            'swsb_youtube_fields'
        );
        add_settings_section(
            'swsb_kick_settings',
            'Kick Connection',
            false,
            'swsb_kick_fields'
        );
        add_settings_section(
            'swsb_main_settings',
            'Main Settings',
            false,
            'swsb_main_fields'
        );
        add_settings_section(
            'swsb_content_settings',
            'Content Settings',
            false,
            'swsb_content_fields'
        );
        add_settings_section(
            'swsb_appearance_settings',
            'Appearance Settings',
            false,
            'swsb_appearance_fields'
        );
        add_settings_section(
            'swsb_appearance_advanced_settings',
            'Appearance Settings (Advanced)',
            false,
            'swsb_appearance_advanced_fields'
        );
        add_settings_section(
            'swsb_debug_settings',
            'Debug Settings',
            false,
            'swsb_debug_fields'
        );
        // Stream API Fields
        add_settings_field(
            'swsb_twitch_api_connection_status',
            'Connection Status',
            array($this, 'swsb_twitch_api_connection_status_cb'),
            'swsb_twitch_fields',
            'swsb_twitch_settings'
        );
        add_settings_field(
            'swsb_client_token',
            'Auth Token',
            array($this, 'swsb_client_token_cb'),
            'swsb_twitch_fields',
            'swsb_twitch_settings'
        );
        add_settings_field(
            'swsb_client_id',
            'Client ID',
            array($this, 'swsb_client_id_cb'),
            'swsb_twitch_fields',
            'swsb_twitch_settings'
        );
        add_settings_field(
            'swsb_client_secret',
            'Client Secret',
            array($this, 'swsb_client_secret_cb'),
            'swsb_twitch_fields',
            'swsb_twitch_settings'
        );
        add_settings_field(
            'swsb_youtube_api_connection_status',
            'Connection Status',
            array($this, 'swsb_youtube_api_connection_status_cb'),
            'swsb_youtube_fields',
            'swsb_youtube_settings'
        );
        add_settings_field(
            'swsb_youtube_api_key',
            'YouTube API Key',
            array($this, 'swsb_youtube_api_key_cb'),
            'swsb_youtube_fields',
            'swsb_youtube_settings'
        );
        add_settings_field(
            'swsb_kick_api_connection_status',
            'Connection Status',
            array($this, 'swsb_kick_api_connection_status_cb'),
            'swsb_kick_fields',
            'swsb_kick_settings'
        );
        add_settings_field(
            'swsb_twitch_name',
            'Twitch Username',
            array($this, 'swsb_twitch_name_cb'),
            'swsb_main_fields',
            'swsb_main_settings'
        );
        add_settings_field(
            'swsb_youtube_name',
            'YouTube User ID',
            array($this, 'swsb_youtube_name_cb'),
            'swsb_main_fields',
            'swsb_main_settings'
        );
        add_settings_field(
            'swsb_kick_name',
            'Kick Username',
            array($this, 'swsb_kick_name_cb'),
            'swsb_main_fields',
            'swsb_main_settings'
        );
        // Content Fields
        add_settings_field(
            'swsb_bar_title',
            'Status Bar Display Name',
            array($this, 'swsb_bar_title_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        add_settings_field(
            'swsb_bar_hide_game',
            'Hide Game',
            array($this, 'swsb_bar_hide_game_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        add_settings_field(
            'swsb_bar_hide_viewers',
            'Hide Viewers',
            array($this, 'swsb_bar_hide_viewers_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        add_settings_field(
            'swsb_bar_hide_button',
            'Hide Button',
            array($this, 'swsb_bar_hide_button_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        add_settings_field(
            'swsb_bar_twitch_button_text',
            'Twitch Button Text',
            array($this, 'swsb_bar_twitch_button_text_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        add_settings_field(
            'swsb_bar_youtube_button_text',
            'YouTube Button Text',
            array($this, 'swsb_bar_youtube_button_text_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        add_settings_field(
            'swsb_bar_kick_button_text',
            'Kick Button Text',
            array($this, 'swsb_bar_kick_button_text_cb'),
            'swsb_content_fields',
            'swsb_content_settings'
        );
        // Appearance Fields
        add_settings_field(
            'swsb_bar_mode',
            'Status Bar Mode',
            array($this, 'swsb_bar_mode_cb'),
            'swsb_appearance_fields',
            'swsb_appearance_settings'
        );
        add_settings_field(
            'swsb_bar_background_colour',
            'Status Bar Background Colour',
            array($this, 'swsb_bar_background_colour_cb'),
            'swsb_appearance_fields',
            'swsb_appearance_settings'
        );
        add_settings_field(
            'swsb_bar_text_colour',
            'Status Bar Text Colour',
            array($this, 'swsb_bar_text_colour_cb'),
            'swsb_appearance_fields',
            'swsb_appearance_settings'
        );
        add_settings_field(
            'swsb_bar_height',
            'Status Bar Size',
            array($this, 'swsb_bar_height_cb'),
            'swsb_appearance_fields',
            'swsb_appearance_settings'
        );
        add_settings_field(
            'swsb_bar_font',
            'Status Bar Fonts',
            array($this, 'swsb_bar_font_cb'),
            'swsb_appearance_fields',
            'swsb_appearance_settings'
        );
        // Appearance Fields (Advanced)
        add_settings_field(
            'swsb_bar_position',
            'Status Bar Position',
            array($this, 'swsb_bar_position_cb'),
            'swsb_appearance_advanced_fields',
            'swsb_appearance_advanced_settings'
        );
        add_settings_field(
            'swsb_bar_background_type',
            'Status Bar Background Type',
            array($this, 'swsb_bar_background_type_cb'),
            'swsb_appearance_advanced_fields',
            'swsb_appearance_advanced_settings'
        );
        add_settings_field(
            'swsb_bar_border_top',
            'Status Bar Border Top',
            array($this, 'swsb_bar_border_top_cb'),
            'swsb_appearance_advanced_fields',
            'swsb_appearance_advanced_settings'
        );
        add_settings_field(
            'swsb_bar_border_bottom',
            'Status Bar Border Bottom',
            array($this, 'swsb_bar_border_bottom_cb'),
            'swsb_appearance_advanced_fields',
            'swsb_appearance_advanced_settings'
        );
        add_settings_field(
            'swsb_bar_border_top_colour',
            'Status Bar Border Top Colour',
            array($this, 'swsb_bar_border_top_colour_cb'),
            'swsb_appearance_advanced_fields',
            'swsb_appearance_advanced_settings'
        );
        add_settings_field(
            'swsb_bar_border_bottom_colour',
            'Status Bar Border Bottom Colour',
            array($this, 'swsb_bar_border_bottom_colour_cb'),
            'swsb_appearance_advanced_fields',
            'swsb_appearance_advanced_settings'
        );
        // Debug Fields
        add_settings_field(
            'swsb_debug',
            'Error Log',
            array($this, 'swsb_debug_cb'),
            'swsb_debug_fields',
            'swsb_debug_settings'
        );
    }

    public function swsb_twitch_api_connection_status_cb() {
        $connection_status = ( isset( $this->options['swsb_api_connection_status'] ) ? $this->options['swsb_api_connection_status'] : '' );
        $connection_token = ( isset( $this->options['swsb_api_access_token'] ) ? $this->options['swsb_api_access_token'] : '' );
        $connection_expires = ( isset( $this->options['swsb_api_access_token_expires'] ) ? $this->options['swsb_api_access_token_expires'] : '' );
        $connection_error_code = ( isset( $this->options['swsb_api_access_token_error_code'] ) ? $this->options['swsb_api_access_token_error_code'] : '' );
        $connection_error_message = ( isset( $this->options['swsb_api_access_token_error_message'] ) ? $this->options['swsb_api_access_token_error_message'] : '' );
        $connection_expires_meta = '';
        $dateTimestamp1 = '';
        $dateTimestamp2 = '';
        if ( $connection_token !== '' ) {
            $license_status_colour = 'green';
            $license_status_label = 'Twitch API Connected!';
        } else {
            $license_status_colour = 'gray';
            $license_status_label = 'Not Connected';
        }
        if ( $connection_expires !== '' ) {
            $connection_expires_meta = '(expires on ' . $connection_expires . ')';
            $dateTimestamp1 = strtotime( $connection_expires );
            $dateTimestamp2 = strtotime( date( 'Y-m-d' ) );
        }
        if ( $connection_expires !== '' && $dateTimestamp2 > $dateTimestamp1 ) {
            $license_status_colour = 'red';
            $license_status_label = 'Twitch API Connection Expired!';
            $connection_expires_meta = '(expired on ' . $connection_expires . ')';
        }
        if ( $connection_error_code !== '' ) {
            $license_status_colour = 'red';
            $license_status_label = 'Twitch API Connection Error!';
            $connection_expires_meta = '(' . $connection_error_message . ')';
        }
        ?>
		<span style="color: <?php 
        echo esc_html( $license_status_colour );
        ?>; font-weight: bold;"><?php 
        echo esc_html( $license_status_label ) . ' ' . esc_html( $connection_expires_meta );
        ?></span>
		<div class="sw-debug-fields">
			<br>		
			<input type="hidden"  id="swsb-access-token" name="swsb_options[swsb_api_access_token]" value="<?php 
        echo esc_html( $connection_token );
        ?>" />
			<input type="hidden"  id="swsb-access-token-expires" name="swsb_options[swsb_api_access_token_expires]" value="<?php 
        echo esc_html( $connection_expires );
        ?>" />
			<input type="hidden"  id="swsb-access-token-error-code" name="swsb_options[swsb_api_access_token_error_code]" value="<?php 
        echo esc_html( $connection_error_code );
        ?>" />
			<input type="hidden"  id="swsb-access-token-error-message" name="swsb_options[swsb_api_access_token_error_message]" value="<?php 
        echo esc_html( $connection_error_message );
        ?>" />
		</div>
		<?php 
    }

    public function swsb_client_id_cb() {
        $connection_token = ( isset( $this->options['swsb_twitch_api_key'] ) ? $this->options['swsb_twitch_api_key'] : '' );
        $client_id = ( isset( $this->options['swsb_client_id'] ) ? $this->options['swsb_client_id'] : '' );
        ?>

		<?php 
        if ( !empty( $connection_token ) && empty( $client_id ) ) {
            ?>
			<div class="swsb-notice notice-error"><p><strong>Error. Client ID cannot be empty!</strong></p></div>
		<?php 
        }
        ?>		

		<input type="" id="swsb-client-id" name="swsb_options[swsb_client_id]" size='40' value="<?php 
        echo esc_html( $client_id );
        ?>" />

		<?php 
    }

    public function swsb_client_secret_cb() {
        $client_secret = ( isset( $this->options['swsb_client_secret'] ) ? $this->options['swsb_client_secret'] : '' );
        ?>

		<input type="" id="swsb-client-secret" name="swsb_options[swsb_client_secret]" size='40' value="<?php 
        echo esc_html( $client_secret );
        ?>" />

		<?php 
    }

    public function swsb_client_token_cb() {
        $token = ( isset( $this->options['swsb_api_access_token'] ) ? $this->options['swsb_api_access_token'] : '' );
        ?>
		
		<input type="text" disabled id="swsb-client-token" name="" size='40' value="<?php 
        echo esc_html( $token );
        ?>" />

		<input type="hidden" id="swsb-refresh-token" name="swsb_options[swsb_refresh_token]" value="0" />
		<?php 
        submit_button(
            'Refresh Token',
            'delete button-secondary',
            'swsb-refresh-token-submit',
            false,
            array(
                'style' => '',
            )
        );
        ?>

		<?php 
    }

    public function swsb_youtube_api_connection_status_cb() {
        $connection_token = ( isset( $this->options['swsb_youtube_api_key'] ) ? $this->options['swsb_youtube_api_key'] : '' );
        $connection_token_code = ( isset( $this->options['swsb_youtube_api_key_code'] ) ? $this->options['swsb_youtube_api_key_code'] : '' );
        if ( $connection_token_code == '200' ) {
            $license_status_colour = 'green';
            $license_status_label = 'YouTube API Connected!';
        } else {
            if ( $connection_token_code == '400' ) {
                $license_status_colour = 'red';
                $license_status_label = 'API Key Invalid';
            } else {
                $license_status_colour = 'gray';
                $license_status_label = 'Not Connected';
            }
        }
        ?>
		<span style="color: <?php 
        echo $license_status_colour;
        ?>; font-weight: bold;"><?php 
        echo esc_html( $license_status_label );
        ?></span>
		<input type="hidden"  id="swsb-api-key-code" name="swsb_options[swsb_youtube_api_key]" value="<?php 
        echo esc_html( $connection_token_code );
        ?>" />
		<?php 
    }

    public function swsb_youtube_api_key_cb() {
        $api_key = ( isset( $this->options['swsb_youtube_api_key'] ) ? $this->options['swsb_youtube_api_key'] : '' );
        ?>

		<input type="text" id="swsb-api-key" name="swsb_options[swsb_youtube_api_key]" size='40' value="<?php 
        echo esc_html( $api_key );
        ?>" />

		<?php 
    }

    public function swsb_kick_api_connection_status_cb() {
        $connection_token = ( isset( $this->options['swsb_api_key'] ) ? $this->options['swsb_api_key'] : '' );
        $connection_token_code = ( isset( $this->options['swsb_api_key_code'] ) ? $this->options['swsb_api_key_code'] : '' );
        $license_status_colour = 'green';
        $license_status_label = 'Not Required';
        ?>
		<span style="color: <?php 
        echo $license_status_colour;
        ?>; font-weight: bold;"><?php 
        echo esc_html( $license_status_label );
        ?></span>
		<input type="hidden"  id="swsb-api-key-code" name="swsb_options[swsb_api_key_code]" value="<?php 
        echo esc_html( $connection_token_code );
        ?>" />
		<?php 
    }

    public function swsb_twitch_name_cb() {
        $username = ( isset( $this->options['swsb_twitch_username'] ) ? $this->options['swsb_twitch_username'] : '' );
        ?>
		
		<div>
			<input type="text" id="swsb-twitch-username" name="swsb_options[swsb_twitch_username]" size='40' placeholder="example: lirik" value="<?php 
        echo esc_html( $username );
        ?>" />	
		</div>
		<p>Enter a Twitch Username to display Twitch Status.</p>

		<?php 
    }

    public function swsb_youtube_name_cb() {
        $username = ( isset( $this->options['swsb_youtube_username'] ) ? $this->options['swsb_youtube_username'] : '' );
        ?>

		<div>
			<input type="text" id="swsb-youtube-username" name="swsb_options[swsb_youtube_username]" size='40' placeholder="example: UCXuqSBlHAE6Xw-yeJA0Tunw" value="<?php 
        echo esc_html( $username );
        ?>" />	
		</div>
		<p>Enter a YouTube channel ID to display YouTube status. You can convert any YouTube username to ID <a href="https://www.streamweasels.com/tools/youtube-channel-id-and-user-id-convertor/?utm_source=wordpress&utm_medium=youtube-integration&utm_campaign=settings">here</a>.</p>

		<?php 
    }

    public function swsb_kick_name_cb() {
        $username = ( isset( $this->options['swsb_kick_username'] ) ? $this->options['swsb_kick_username'] : '' );
        ?>

		<div>
			<input type="text" id="swsb-kick-username" name="swsb_options[swsb_kick_username]" size='40' placeholder="example: xQc" value="<?php 
        echo esc_html( $username );
        ?>" />	
		</div>
		<p>Enter a Kick Username to display Kick Status.</p>

		<?php 
    }

    public function swsb_bar_mode_cb() {
        $statusBarMode = ( isset( $this->options['swsb_bar_mode'] ) ? $this->options['swsb_bar_mode'] : '' );
        ?>

		<div>
			<select id="swsb-status-bar-mode" name="swsb_options[swsb_bar_mode]">
				<option value="fixed" <?php 
        echo selected( $statusBarMode, 'fixed', false );
        ?>>Fixed</option>
				<option value="absolute" <?php 
        echo selected( $statusBarMode, 'absolute', false );
        ?>>Absolute</option>
				<option value="static" <?php 
        echo selected( $statusBarMode, 'static', false );
        ?>>Static</option>
			</select>
		</div>
		<p>Change how the Status Bar is displayed on the page.</p>
		<p><strong>Fixed</strong> - Status Bar is fixed to the page and scrolls with the user.</p>
		<p><strong>Absolute</strong> - Status Bar is fixed to the page but does not scroll with the user. Useful for themes that already have a fixed header / navigation.</p>
		<p><strong>Static</strong> - Status Bar will stay in place wherever it is placed on the page. </p>
		<?php 
    }

    public function swsb_bar_position_cb() {
        $statusBarPosition = ( isset( $this->options['swsb_bar_position'] ) ? $this->options['swsb_bar_position'] : '' );
        ?>

		<div>
			<select id="swsb-status-bar-position" name="swsb_options[swsb_bar_position]">
				<option value="top" <?php 
        echo selected( $statusBarPosition, 'top', false );
        ?>>Top</option>
				<option value="bottom" <?php 
        echo selected( $statusBarPosition, 'bottom', false );
        ?>>Bottom</option>
			</select>
		</div>
		<p>Change the position of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_background_type_cb() {
        $backgroundType = ( isset( $this->options['swsb_bar_background_type'] ) ? $this->options['swsb_bar_background_type'] : '' );
        ?>

		<div>
			<select id="swsb-status-bar-position" name="swsb_options[swsb_bar_background_type]">
				<option value="solid" <?php 
        echo selected( $backgroundType, 'solid', false );
        ?>>Solid</option>
				<option value="gradient" <?php 
        echo selected( $backgroundType, 'gradient', false );
        ?>>Gradient</option>
			</select>
		</div>
		<p>Change the background type of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_background_colour_cb() {
        $backgroundColour = ( isset( $this->options['swsb_bar_background_colour'] ) ? $this->options['swsb_bar_background_colour'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_background_colour" name="swsb_options[swsb_bar_background_colour]" size='40' value="<?php 
        echo esc_html( $backgroundColour );
        ?>" />	
		</div>
		<p>Change the background colour of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_text_colour_cb() {
        $textColour = ( isset( $this->options['swsb_bar_text_colour'] ) ? $this->options['swsb_bar_text_colour'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_text_colour" name="swsb_options[swsb_bar_text_colour]" size='40' value="<?php 
        echo esc_html( $textColour );
        ?>" />	
		</div>
		<p>Change the text colour of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_height_cb() {
        $height = ( isset( $this->options['swsb_bar_height'] ) ? $this->options['swsb_bar_height'] : '' );
        ?>

		<div>
			<select id="swsb_bar_height" name="swsb_options[swsb_bar_height]">
				<option value="medium" <?php 
        echo selected( $height, 'medium', false );
        ?>>Medium</option>
				<option value="small" <?php 
        echo selected( $height, 'small', false );
        ?>>Small</option>
				<option value="large" <?php 
        echo selected( $height, 'large', false );
        ?>>Large</option>
			</select>
		</div>
		<p>Change the size of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_font_cb() {
        $font = ( isset( $this->options['swsb_bar_font'] ) ? $this->options['swsb_bar_font'] : '' );
        ?>

		<div>
			<select id="swsb_bar_font" name="swsb_options[swsb_bar_font]">
				<option value="luckiest-guy" <?php 
        echo selected( $font, 'luckiest-guy', false );
        ?>>Custom plugin font</option>
				<option value="inherit" <?php 
        echo selected( $font, 'inherit', false );
        ?>>Inherit theme fonts</option>
			</select>
		</div>
		<p>Change the fonts of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_title_cb() {
        $statusBarTitle = ( isset( $this->options['swsb_bar_title'] ) ? $this->options['swsb_bar_title'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_title" name="swsb_options[swsb_bar_title]" size='40' placeholder="Display Name" value="<?php 
        echo esc_html( $statusBarTitle );
        ?>" />	
		</div>
		<p>Enter a custom display name for the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_hide_game_cb() {
        $statusBarHideGame = ( isset( $this->options['swsb_bar_hide_game'] ) ? $this->options['swsb_bar_hide_game'] : '' );
        ?>

		<div>
			<input type="hidden" name="swsb_options[swsb_bar_hide_game]" value="0"/>
			<input type="checkbox" id="swsb_bar_hide_game" name="swsb_options[swsb_bar_hide_game]" value="1" <?php 
        checked( $statusBarHideGame, 1 );
        ?> />	
		</div>
		<p>Hide the 'game playing' content when live.</p>

		<?php 
    }

    public function swsb_bar_hide_viewers_cb() {
        $statusBarHideViewers = ( isset( $this->options['swsb_bar_hide_viewers'] ) ? $this->options['swsb_bar_hide_viewers'] : '' );
        ?>

		<div>
			<input type="hidden" name="swsb_options[swsb_bar_hide_viewers]" value="0"/>
			<input type="checkbox" id="swsb_bar_hide_viewers" name="swsb_options[swsb_bar_hide_viewers]" value="1" <?php 
        checked( $statusBarHideViewers, 1 );
        ?> />	
		</div>
		<p>Hide the 'viewers' content when live.</p>

		<?php 
    }

    public function swsb_bar_hide_button_cb() {
        $statusBarHideButton = ( isset( $this->options['swsb_bar_hide_button'] ) ? $this->options['swsb_bar_hide_button'] : '' );
        ?>

		<div>
			<input type="hidden" name="swsb_options[swsb_bar_hide_button]" value="0"/>
			<input type="checkbox" id="swsb_bar_hide_button" name="swsb_options[swsb_bar_hide_button]" value="1" <?php 
        checked( $statusBarHideButton, 1 );
        ?> />	
		</div>
		<p>Hide the Twitch / YouTube / Kick button when live.</p>

		<?php 
    }

    public function swsb_bar_twitch_button_text_cb() {
        $statusBarTwitchButtonText = ( isset( $this->options['swsb_bar_twitch_button_text'] ) ? $this->options['swsb_bar_twitch_button_text'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_twitch_button_text" name="swsb_options[swsb_bar_twitch_button_text]" size='40' placeholder="Watch on Twitch" value="<?php 
        echo esc_html( $statusBarTwitchButtonText );
        ?>" />	
		</div>
		<p>Custom button text for the Twitch button.</p>

		<?php 
    }

    public function swsb_bar_youtube_button_text_cb() {
        $statusBarYouTubeButtonText = ( isset( $this->options['swsb_bar_youtube_button_text'] ) ? $this->options['swsb_bar_youtube_button_text'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_youtube_button_text" name="swsb_options[swsb_bar_youtube_button_text]" size='40' placeholder="Watch on YouTube" value="<?php 
        echo esc_html( $statusBarYouTubeButtonText );
        ?>" />	
		</div>
		<p>Custom button text for the YouTube button.</p>

		<?php 
    }

    public function swsb_bar_kick_button_text_cb() {
        $statusBarKickButtonText = ( isset( $this->options['swsb_bar_kick_button_text'] ) ? $this->options['swsb_bar_kick_button_text'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_kick_button_text" name="swsb_options[swsb_bar_kick_button_text]" size='40' placeholder="Watch on Kick" value="<?php 
        echo esc_html( $statusBarKickButtonText );
        ?>" />	
		</div>
		<p>Custom button text for the Kick button.</p>

		<?php 
    }

    public function swsb_bar_border_top_cb() {
        $statusBarBorderTop = ( isset( $this->options['swsb_bar_border_top'] ) ? $this->options['swsb_bar_border_top'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_border_top" name="swsb_options[swsb_bar_border_top]" size='40' placeholder="0" value="<?php 
        echo esc_html( $statusBarBorderTop );
        ?>" />	
			<span class="range-bar-value"></span>
		</div>
		<p>Add a Border Top to the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_border_bottom_cb() {
        $statusBarBorderBottom = ( isset( $this->options['swsb_bar_border_bottom'] ) ? $this->options['swsb_bar_border_bottom'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_border_bottom" name="swsb_options[swsb_bar_border_bottom]" size='40' placeholder="0" value="<?php 
        echo esc_html( $statusBarBorderBottom );
        ?>" />	
			<span class="range-bar-value"></span>
		</div>
		<p>Add a Border Bottom to the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_border_top_colour_cb() {
        $borderTopColour = ( isset( $this->options['swsb_bar_border_top_colour'] ) ? $this->options['swsb_bar_border_top_colour'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_border_top_colour" name="swsb_options[swsb_bar_border_top_colour]" size='40' value="<?php 
        echo esc_html( $borderTopColour );
        ?>" />	
		</div>
		<p>Change the border top colour of the Status Bar.</p>

		<?php 
    }

    public function swsb_bar_border_bottom_colour_cb() {
        $borderBottomColour = ( isset( $this->options['swsb_bar_border_bottom_colour'] ) ? $this->options['swsb_bar_border_bottom_colour'] : '' );
        ?>

		<div>
			<input type="text" id="swsb_bar_border_bottom_colour" name="swsb_options[swsb_bar_border_bottom_colour]" size='40' value="<?php 
        echo esc_html( $borderBottomColour );
        ?>" />	
		</div>
		<p>Change the border bottom colour of the Status Bar.</p>

		<?php 
    }

    public function swsb_debug_cb() {
        $dismissForGood = ( isset( $this->options['swsb_dismiss_for_good'] ) ? $this->options['swsb_dismiss_for_good'] : 0 );
        ?>
		
		<p>
			<textarea rows="6" style="width: 100%;"><?php 
        echo get_option( 'swsb_debug_log', '' );
        ?></textarea>
		</p>
		<p>
			<input type="hidden" id="swsb-delete-log" name="swsb_options[swsb_delete_log]" value="0" />
			<input type="hidden" id="swsb-dismiss-for-good" name="swsb_options[swsb_dismiss_for_good]" value="<?php 
        echo esc_html( $dismissForGood );
        ?>" />
			<?php 
        submit_button(
            'Clear logs',
            'delete button-secondary',
            'swsb-delete-log-submit',
            false
        );
        ?>
		</p>

		<?php 
    }

    public function swsb_showAdmin() {
        include 'partials/streamweasels-status-bar-admin-display.php';
    }

    public function swsb_options_validate( $input ) {
        $new_input = [];
        $options = get_option( 'swsb_options' );
        if ( isset( $input['swsb_client_id'] ) ) {
            $new_input['swsb_client_id'] = sanitize_text_field( $input['swsb_client_id'] );
        }
        if ( isset( $input['swsb_client_secret'] ) ) {
            $new_input['swsb_client_secret'] = sanitize_text_field( $input['swsb_client_secret'] );
        }
        if ( isset( $input['swsb_api_access_token'] ) ) {
            $new_input['swsb_api_access_token'] = sanitize_text_field( $input['swsb_api_access_token'] );
        }
        if ( isset( $input['swsb_api_access_token_expires'] ) ) {
            $new_input['swsb_api_access_token_expires'] = sanitize_text_field( $input['swsb_api_access_token_expires'] );
        }
        if ( isset( $input['swsb_api_access_token_meta'] ) ) {
            $new_input['swsb_api_access_token_meta'] = sanitize_text_field( $input['swsb_api_access_token_meta'] );
        }
        if ( isset( $input['swsb_youtube_api_key'] ) ) {
            $new_input['swsb_youtube_api_key'] = sanitize_text_field( $input['swsb_youtube_api_key'] );
        }
        // oAUTH with Twitch
        if ( ($input['swsb_api_access_token'] == '' || $input['swsb_refresh_token'] == 1) && isset( $input['swsb_client_id'] ) && isset( $input['swsb_client_secret'] ) ) {
            $swsb_Twitch_API = new swsb_Twitch_API();
            if ( $input['swsb_refresh_token'] == 1 ) {
                $swsb_Twitch_API->refresh_token();
            }
            $result = $swsb_Twitch_API->get_token( $input['swsb_client_id'], $input['swsb_client_secret'] );
            if ( $result[0] !== 'error' ) {
                $new_input['swsb_api_access_token'] = $result[0];
                $new_input['swsb_api_access_token_expires'] = $result[1];
                $new_input['swsb_api_access_token_error_code'] = '';
                $new_input['swsb_api_access_token_error_message'] = '';
            } else {
                $new_input['swsb_api_access_token'] = '';
                $new_input['swsb_api_access_token_expires'] = '';
                $new_input['swsb_api_access_token_error_code'] = '403';
                $new_input['swsb_api_access_token_error_message'] = $result[1];
            }
        }
        if ( !empty( $input['swsb_youtube_api_key'] ) ) {
            $new_input['swsb_youtube_api_key'] = sanitize_text_field( $input['swsb_youtube_api_key'] );
            $SWSB_YouTube_API = new SWSB_YouTube_API();
            $result = $SWSB_YouTube_API->check_token( $input['swsb_youtube_api_key'] );
            $new_input['swsb_youtube_api_key_code'] = $result;
        }
        $filled_fields = 0;
        if ( isset( $input['swsb_twitch_username'] ) && !empty( $input['swsb_twitch_username'] ) ) {
            $filled_fields++;
        }
        if ( isset( $input['swsb_youtube_username'] ) && !empty( $input['swsb_youtube_username'] ) ) {
            $filled_fields++;
        }
        if ( isset( $input['swsb_kick_username'] ) && !empty( $input['swsb_kick_username'] ) ) {
            $filled_fields++;
        }
        if ( $filled_fields == 1 ) {
            if ( isset( $input['swsb_twitch_username'] ) ) {
                $new_input['swsb_twitch_username'] = sanitize_text_field( $input['swsb_twitch_username'] );
            }
            if ( isset( $input['swsb_youtube_username'] ) ) {
                $new_input['swsb_youtube_username'] = sanitize_text_field( $input['swsb_youtube_username'] );
            }
            if ( isset( $input['swsb_kick_username'] ) ) {
                $new_input['swsb_kick_username'] = sanitize_text_field( $input['swsb_kick_username'] );
            }
        } else {
            $new_input['swsb_twitch_username'] = '';
            $new_input['swsb_youtube_username'] = '';
            $new_input['swsb_kick_username'] = '';
        }
        if ( isset( $input['swsb_bar_title'] ) ) {
            $new_input['swsb_bar_title'] = sanitize_text_field( $input['swsb_bar_title'] );
        }
        if ( isset( $input['swsb_bar_hide_game'] ) ) {
            $new_input['swsb_bar_hide_game'] = (int) $input['swsb_bar_hide_game'];
        }
        if ( isset( $input['swsb_bar_hide_viewers'] ) ) {
            $new_input['swsb_bar_hide_viewers'] = (int) $input['swsb_bar_hide_viewers'];
        }
        if ( isset( $input['swsb_bar_hide_button'] ) ) {
            $new_input['swsb_bar_hide_button'] = (int) $input['swsb_bar_hide_button'];
        }
        if ( isset( $input['swsb_bar_twitch_button_text'] ) ) {
            $new_input['swsb_bar_twitch_button_text'] = sanitize_text_field( $input['swsb_bar_twitch_button_text'] );
        }
        if ( isset( $input['swsb_bar_youtube_button_text'] ) ) {
            $new_input['swsb_bar_youtube_button_text'] = sanitize_text_field( $input['swsb_bar_youtube_button_text'] );
        }
        if ( isset( $input['swsb_bar_kick_button_text'] ) ) {
            $new_input['swsb_bar_kick_button_text'] = sanitize_text_field( $input['swsb_bar_kick_button_text'] );
        }
        if ( isset( $input['swsb_bar_mode'] ) ) {
            $new_input['swsb_bar_mode'] = sanitize_text_field( $input['swsb_bar_mode'] );
        }
        if ( isset( $input['swsb_bar_background_colour'] ) ) {
            $new_input['swsb_bar_background_colour'] = sanitize_text_field( $input['swsb_bar_background_colour'] );
        }
        if ( isset( $input['swsb_bar_text_colour'] ) ) {
            $new_input['swsb_bar_text_colour'] = sanitize_text_field( $input['swsb_bar_text_colour'] );
        }
        if ( isset( $input['swsb_bar_height'] ) ) {
            $new_input['swsb_bar_height'] = sanitize_text_field( $input['swsb_bar_height'] );
        }
        if ( isset( $input['swsb_bar_font'] ) ) {
            $new_input['swsb_bar_font'] = sanitize_text_field( $input['swsb_bar_font'] );
        }
        if ( isset( $input['swsb_delete_log'] ) && $input['swsb_delete_log'] == 1 ) {
            $new_input['swsb_dismiss_for_good'] = 0;
            delete_option( 'swsb_debug_log' );
        }
        return $new_input;
    }

    public function swsb_debug_log( $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            if ( is_array( $message ) || is_object( $message ) ) {
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }

    public function swsb_debug_field( $message ) {
        if ( is_array( $message ) ) {
            $message = print_r( $message, true );
        }
        $log = get_option( 'swsb_debug_log', '' );
        $string = date( 'd.m.Y H:i:s' ) . " : " . $message . "\n";
        $log .= $string;
        // Limit the log to the last 100 lines to prevent it from growing too large.
        $log_lines = explode( "\n", $log );
        if ( count( $log_lines ) > 100 ) {
            $log_lines = array_slice( $log_lines, -100, 100 );
        }
        $log = implode( "\n", $log_lines );
        update_option( 'swsb_debug_log', $log );
    }

    public function swsb_get_options() {
        return get_option( 'swsb_options', array() );
    }

    public function swsb_do_settings_sections(
        $page,
        $icon,
        $desc,
        $status
    ) {
        global $wp_settings_sections, $wp_settings_fields;
        if ( !isset( $wp_settings_sections[$page] ) ) {
            return;
        }
        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            $title = '';
            $description = '';
            if ( $section['title'] ) {
                $title = "<h3 class='hndle'><span class='dashicons {$icon}'></span>{$section['title']}</h3>";
            }
            if ( $desc ) {
                $description = "<p>" . $desc . "</p>";
            }
            echo '<div class="postbox postbox-' . sanitize_title( $title ) . ' postbox-' . $status . '">';
            echo wp_kses( $title, 'post' );
            echo '<div class="inside">';
            echo wp_kses( $description, 'post' );
            if ( $section['callback'] ) {
                call_user_func( $section['callback'], $section );
            }
            echo '<table class="form-table">';
            do_settings_fields( $page, $section['id'] );
            echo '</table>';
            if ( $section['title'] == 'Shortcode' ) {
                echo '';
            } else {
                submit_button();
            }
            echo '</div>';
            if ( !ssb_fs()->is__premium_only() || ssb_fs()->is_free_plan() ) {
                if ( $status == 'pro' ) {
                    echo '<div class="postbox-trial-wrapper"><a href="admin.php?page=streamweasels-status-bar-pricing&trial=true" target="_blank" type="button" class="button button-primary">Free Trial</a></div>';
                }
            }
            echo '</div>';
        }
    }

    public function swsb_handle_api_errors( $response, $url, $context = 'Fetch Streams' ) {
        // Check for errors in the response
        if ( is_wp_error( $response ) ) {
            $this->swsb_debug_field( 'WP Error received on the following URL: ' . $url );
            return new WP_REST_Response($response->get_error_message(), 500);
        }
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code != 200 ) {
            $this->swsb_debug_field( $context . ' returned status code: ' . $response_code );
            $this->swsb_debug_field( $context . ' request URL: ' . $url );
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            $errorMessage = $data['message'] ?? 'No message received...';
            $this->swsb_debug_field( $context . ' returned error message: ' . $errorMessage );
            return new WP_REST_Response("Error in " . $context . ': ' . $response_code . " - " . $errorMessage, $response_code);
        }
    }

}
