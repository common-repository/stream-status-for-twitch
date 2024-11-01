<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Status_Bar
 * @subpackage Streamweasels_Status_Bar/public/partials
 */
?>

<?php
    $options = get_option('swsb_options');
    $uuid    = rand(1000,9999);
    if (empty($args['twitch']) && empty($args['youtube']) && empty($args['kick'])) {
        $twitchUsername = sanitize_text_field($args['twitch'] ?? $options['swsb_twitch_username'] ?? false);
        $youtubeUsername = sanitize_text_field($args['youtube'] ?? $options['swsb_youtube_username'] ?? false);
        $kickUsername = sanitize_text_field($args['kick'] ??  $options['swsb_kick_username'] ?? false);
    } else {
        $twitchUsername = sanitize_text_field($args['twitch'] ?? false);
        $youtubeUsername = sanitize_text_field($args['youtube'] ?? false);
        $kickUsername = sanitize_text_field($args['kick'] ?? false);
    }
    $displayName = sanitize_text_field($args['display-name'] ?? $options['swsb_bar_title'] ?? 'Display Name');
    $twitchButtonText = sanitize_text_field($args['twitch-text'] ?? $options['swsb_bar_twitch_button_text'] ?? '');
    $youtubeButtonText = sanitize_text_field($args['youtube-text'] ?? $options['swsb_bar_youtube_button_text'] ?? '');
    $kickButtonText = sanitize_text_field($args['kick-text'] ?? $options['swsb_bar_kick_button_text'] ?? '');
    $hideButtons = sanitize_text_field($options['swsb_bar_hide_buttons'] ?? false);
    // Initialize styles string
    $styles = '';

    // Determine and set background color if available
    $bgColour = sanitize_text_field($args['bg-colour'] ?? $options['swsb_bar_background_colour'] ?? false);
    if ($bgColour) {
        $styles .= '--swsb-background-colour: ' . $bgColour . '; ';
    }

    // Set text color if available
    $textColour = sanitize_text_field($args['text-colour'] ?? $options['swsb_bar_text_colour'] ?? false);
    if ($textColour) {
        $styles .= '--swsb-text-colour: ' . $textColour . '; ';
    }

    // Set border top if available
    $borderTop = sanitize_text_field($args['border-top'] ?? $options['swsb_bar_border_top'] ?? false);
    if ($borderTop) {
        $styles .= '--swsb-border-top: ' . $borderTop . 'px; ';
    }

    // Set border top color if available
    $borderTopColour = sanitize_text_field($args['border-top-colour'] ?? $options['swsb_bar_border_top_colour'] ?? false);
    if ($borderTopColour) {
        $styles .= '--swsb-border-top-colour: ' . $borderTopColour . '; ';
    }

    // Set border bottom if available
    $borderBottom = sanitize_text_field($args['border-bottom'] ?? $options['swsb_bar_border_bottom'] ?? false);
    if ($borderBottom) {
        $styles .= '--swsb-border-bottom: ' . $borderBottom . 'px; ';
    }

    // Set border bottom color if available
    $borderBottomColour = sanitize_text_field($args['border-bottom-colour'] ?? $options['swsb_bar_border_bottom_colour'] ?? false);
    if ($borderBottomColour) {
        $styles .= '--swsb-border-bottom-colour: ' . $borderBottomColour . '; ';
    }

    // Set font if available, with a default fallback
    $font = sanitize_text_field($args['font'] ?? $options['swsb_bar_font'] ?? 'luckiest-guy');
    $styles .= '--swsb-headline-font: ' . $font . '; ';

    $usernameCount = 0;
    $twitchUsername &&  $usernameCount++;
    $youtubeUsername &&  $usernameCount++;
    $kickUsername &&  $usernameCount++;

    if ($usernameCount === 0) {
        return false;
    }
?>

<div class="cp-sw-status-bar cp-sw-status-bar--<?php echo $uuid; ?>"
    style="<?php echo trim($styles); ?>"
    data-site-url="<?php echo esc_url( get_site_url() ); ?>"
    data-twitch-username="<?php echo sanitize_text_field($twitchUsername); ?>"
    data-youtube-username="<?php echo sanitize_text_field($youtubeUsername); ?>"
    data-kick-username="<?php echo sanitize_text_field($kickUsername); ?>"
    data-nonce="<?php echo wp_create_nonce( 'wp_rest' ); ?>"
    data-hide-game="<?php echo esc_attr($options['swsb_bar_hide_game'] ?? ''); ?>"
    data-hide-viewers="<?php echo esc_attr($options['swsb_bar_hide_viewers'] ?? ''); ?>"
    data-hide-button="<?php echo esc_attr($options['swsb_bar_hide_button'] ?? ''); ?>"
    data-bar-mode="<?php echo esc_attr($args['mode'] ?? $options['swsb_bar_mode'] ?? 'fixed'); ?>"
    data-bar-position="<?php echo esc_attr($options['swsb_bar_position'] ?? 'top'); ?>"
    data-bar-height="<?php echo esc_attr($options['swsb_bar_height'] ?? ''); ?>"
    data-bar-font="<?php echo esc_attr($options['swsb_bar_font'] ?? ''); ?>"
    data-bar-background-type="<?php echo esc_attr($options['swsb_bar_background_type'] ?? 'solid'); ?>"
    data-bar-loading="<?php echo $usernameCount; ?>"
>
    <div class="cp-sw-status-bar__inner">
        <!-- Left -->
        <div class="cp-sw-status-bar__title">
            <span class="cp-sw-status-bar__indicator l-indicator"></span>
            <span class="cp-sw-status-bar__username">
                <?php echo $displayName; ?>
            </span>
        </div>

        <!-- Middle -->
        <div class="cp-sw-status-bar__middle">

            <div class="cp-sw-status-bar__middle-section cp-sw-status-bar__offline">
                <span class="cp-sw-status-bar__offline--line-1">Offline</span>
            </div>

            <div class="cp-sw-status-bar__middle-section cp-sw-status-bar__online">
                <div class="cp-sw-status-bar__game">
                    <span class="cp-sw-status-bar__game--line-1">Online</span>
                    <span class="cp-sw-status-bar__game--line-2"></span>                
                </div>
                <div class="cp-sw-status-bar__viewers">
                    <span class="cp-sw-status-bar__viewers--line-1">Viewers</span>
                    <span class="cp-sw-status-bar__viewers--line-2">0</span>                          
                </div>
            </div>
        </div>    
        
        <!-- Right -->
        <div class="cp-sw-status-bar__right">
            <div class="swsb-loader"></div>
            <?php if ( ! $hideButtons ) : ?>
                <div class="cp-sw-status-bar__cta-wrapper">
                    <?php if ($kickUsername) { ?>
                        <a class="l-kick-cta" href="https://www.kick.com/<?php echo $kickUsername; ?>" data-status="" target="_blank"><i aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="200" height="200" viewBox="0 0 200 200" xml:space="preserve">
                                <g transform="matrix(0.15 0 0 0.15 100 100)" id="Layer_1"  >
                                    <polygon style="stroke: inherit; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: inherit; fill-rule: evenodd; opacity: 1;" vector-effect="non-scaling-stroke"  points="-491.25,-552.65 -122.8,-552.65 -122.8,-307.03 0,-307.03 0,-429.84 122.82,-429.84 122.82,-552.65 491.25,-552.65 491.25,-184.22 368.44,-184.22 368.44,-61.41 245.63,-61.41 245.63,61.4 368.44,61.4 368.44,184.21 491.25,184.21 491.25,552.65 122.82,552.65 122.82,429.84 0,429.84 0,307.03 -122.8,307.03 -122.8,552.65 -491.25,552.65 -491.25,-552.65 " />
                                </g>
                            </svg>
                            </i><span><?php echo $kickButtonText; ?></span>
                        </a>
                    <?php } ?>
                    <?php if ($twitchUsername) { ?>
                        <a class="l-twitch-cta" href="https://www.twitch.tv/<?php echo $twitchUsername; ?>" data-status="" target="_blank"><i aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="200" height="200" viewBox="0 0 200 200" xml:space="preserve">
                            <g transform="matrix(7.36 0 0 7.36 102.16 102.26)" id="586f34f4-49f9-4464-b359-a2b7b394c763"  >
                            <path style="stroke: inherit; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: inherit; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(-12, -12)" d="M 2.149 0 L 0.5369999999999999 4.119 L 0.5369999999999999 20.955 L 6.268 20.955 L 6.268 24 L 9.492 24 L 12.537 20.955 L 17.194000000000003 20.955 L 23.463 14.685999999999998 L 23.463 -1.7763568394002505e-15 L 2.149000000000001 -1.7763568394002505e-15 z M 21.313000000000002 13.612 L 17.731 17.194 L 12.000000000000002 17.194 L 8.955000000000002 20.238999999999997 L 8.955000000000002 17.193999999999996 L 4.1190000000000015 17.193999999999996 L 4.1190000000000015 2.1489999999999956 L 21.313000000000002 2.1489999999999956 L 21.313000000000002 13.611999999999995 z M 17.731 6.269 L 17.731 12.530999999999999 L 15.582 12.530999999999999 L 15.582 6.268999999999999 L 17.731 6.268999999999999 z M 12.000000000000002 6.269 L 12.000000000000002 12.530999999999999 L 9.851000000000003 12.530999999999999 L 9.851000000000003 6.268999999999999 L 12.000000000000004 6.268999999999999 z" stroke-linecap="round" />
                            </g>
                            </svg>
                            </i><span><?php echo $twitchButtonText; ?></span>
                        </a>
                    <?php } ?>
                    <?php if ($youtubeUsername) { ?>
                        <a class="l-youtube-cta" href="https://www.youtube.com/channel/<?php echo $youtubeUsername; ?>" data-status="" target="_blank"><i aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="200" height="200" viewBox="0 0 200 200" xml:space="preserve">
                            <g transform="matrix(7.5 0 0 7.5 100 100)" id="d234e015-b25c-4c89-87cf-1b33c7debeca"  >
                            <path style="stroke: inherit; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: inherit; fill-rule: nonzero; opacity: 1;" vector-effect="non-scaling-stroke"  transform=" translate(-12, -12)" d="M 19.615 3.184 C 16.011 2.938 7.983999999999998 2.939 4.384999999999998 3.184 C 0.4879999999999982 3.45 0.02899999999999814 5.804 -1.7763568394002505e-15 12 C 0.028999999999998225 18.185 0.4839999999999982 20.549 4.384999999999998 20.816000000000003 C 7.984999999999998 21.061000000000003 16.010999999999996 21.062 19.615 20.816000000000003 C 23.511999999999997 20.550000000000004 23.970999999999997 18.196 24 12.000000000000002 C 23.971 5.815000000000002 23.516 3.4510000000000023 19.615000000000002 3.184000000000001 z M 8.999999999999998 16 L 8.999999999999998 8 L 17 11.993 L 9 16 z" stroke-linecap="round" />
                            </g>
                            </svg>
                            </i><span><?php echo $youtubeButtonText; ?></span>
                        </a>     
                    <?php } ?>    
                </div>   
            <?php endif; ?>
        </div>
    </div>
</div>