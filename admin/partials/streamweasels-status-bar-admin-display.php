<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Status_Bar
 * @subpackage Streamweasels_Status_Bar/admin/partials
 */
?>

<?php 
switch ( get_admin_page_title() ) {
    case '[Layout] Wall':
        $activePage = 'wall';
        break;
    case '[Layout] Player':
        $activePage = 'player';
        break;
    case '[Layout] Rail':
        $activePage = 'rail';
        break;
    case '[Layout] Feature':
        $activePage = 'feature';
        break;
    case '[Layout] Status':
        $activePage = 'status';
        break;
    case '[Layout] Nav':
        $activePage = 'nav';
        break;
    case '[Layout] Vods':
        $activePage = 'vods';
        break;
    case '[Layout] Showcase':
        $activePage = 'showcase';
        break;
    default:
        $activePage = 'wall';
}
?>
<div class="cp-streamweasels-status-bar cp-streamweasels-status-bar--<?php 
echo ( ssb_fs()->can_use_premium_code() ? 'paid' : 'free' );
?> wrap">
    <div class="cp-streamweasels-status-bar__header">
        <div class="cp-streamweasels-status-bar__header-logo">
            <img src="<?php 
echo plugin_dir_url( __FILE__ ) . '../img/sw-full-logo.png';
?>">
        </div>
        <div class="cp-streamweasels-status-bar__header-title">
            <h3>StreamWeasels</h3>
            <p>Online Status Bar <?php 
?>for WordPress</p>
        </div>        
    </div>
    <div class="cp-streamweasels-status-bar__wrapper">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">   
                    <form id="sw-form" method="post" action="options.php">
                        <?php 
if ( get_admin_page_title() == 'StreamWeasels' ) {
    ?>
                            <?php 
    settings_fields( 'swsb_options' );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_twitch_fields',
        'dashicons-twitch',
        'To display Twitch status, this plugin requires an active Twitch Auth Token to work. <a href="https://support.streamweasels.com/article/12-how-to-setup-a-client-id-and-client-secret" target="_blank">Click here</a> to learn more about Twitch Auth Tokens.',
        'free'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_youtube_fields',
        'dashicons-youtube',
        'To display YouTube status, this plugin requires an active YouTube API key to work. <a href="https://support.streamweasels.com/article/26-how-to-setup-a-youtube-api-key" target="_blank">Click here</a> to learn more about YouTube API keys.',
        'free'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_kick_fields',
        'dashicons-xing',
        'Kick doesn\'t currently require an API connection, but it\' very possible they will in the future.',
        'free'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_main_fields',
        'dashicons-admin-site',
        '  ',
        'free'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_content_fields',
        'dashicons-button',
        '  ',
        'free'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_appearance_fields',
        'dashicons-art',
        '  ',
        'free'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_appearance_advanced_fields',
        'dashicons-art',
        '  ',
        'pro'
    );
    ?>
                            <?php 
    $this->swsb_do_settings_sections(
        'swsb_debug_fields',
        'dashicons-twitch',
        'This plugin requires an active Twitch / YouTube / Kick Auth Token to work. <a href="https://support.streamweasels.com/article/12-how-to-setup-a-client-id-and-client-secret" target="_blank">Click here</a> to learn more about Twitch Auth Tokens.',
        'free'
    );
    ?>
                        <?php 
}
?>                                                                                                                                         
                    </form>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <?php 
include 'streamweasels-status-bar-admin-sidebar.php';
?>
            </div>
        </div>
    </div>
</div>