(function ($) {
	'use strict';

	jQuery(document).ready(function (a) {
		if (jQuery('body').hasClass('toplevel_page_streamweasels-status-bar')) {
			jQuery(document).on(
				'click',
				'#swsb-refresh-token-submit',
				function (a) {
					jQuery('#swsb-refresh-token').val('1');
				}
			);

			jQuery(document).on(
				'click',
				'#swsb-delete-log-submit',
				function (a) {
					jQuery('#swsb-delete-log').val('1');
					jQuery('#swsb-dismiss-for-good').val('1');
				}
			);

			jQuery('#swsb_bar_background_colour').wpColorPicker();
			jQuery('#swsb_bar_text_colour').wpColorPicker();
			jQuery('#swsb_bar_border_top_colour').wpColorPicker();
			jQuery('#swsb_bar_border_bottom_colour').wpColorPicker();

			var borderTop = document.querySelector('#swsb_bar_border_top');
			var borderTopVal = borderTop.value;
			var borderTopInit = new Powerange(borderTop, {
				callback: function () {
					borderTop.nextElementSibling.nextElementSibling.innerHTML =
						borderTop.value + 'px';
				},
				step: 1,
				max: 10,
				start: borderTopVal,
				hideRange: true,
			});

			var borderBottom = document.querySelector(
				'#swsb_bar_border_bottom'
			);
			var borderBottomVal = borderBottom.value;
			var borderBottomInit = new Powerange(borderBottom, {
				callback: function () {
					borderBottom.nextElementSibling.nextElementSibling.innerHTML =
						borderBottom.value + 'px';
				},
				step: 1,
				max: 10,
				start: borderBottomVal,
				hideRange: true,
			});

			var error = `
			<div class="notice notice-error" style="padding: 20px; font-size: 16px; background-color: #f44336; color: white; border: 1px solid #d32f2f;">
				<p>
					<strong>Error:</strong> The free plugin can only check the live status for one service at a time. Please enter a username for only one service.<br>
					<a href="admin.php?page=streamweasels-status-bar-pricing&trial=true" target="_blank" style="color: #ffeb3b; text-decoration: underline;">Upgrade to the paid plugin</a> to check live status for multiple services.
				</p>
			</div>`;

			function checkFilledFields() {
				var twitchUsername = jQuery('#swsb-twitch-username').val();
				var youtubeUsername = jQuery('#swsb-youtube-username').val();
				var kickUsername = jQuery('#swsb-kick-username').val();

				var filledFields = [
					twitchUsername,
					youtubeUsername,
					kickUsername,
				].filter(function (value) {
					return value !== '';
				}).length;

				jQuery('.postbox-main-settings .inside .notice').remove();

				if (filledFields > 1) {
					jQuery('.postbox-main-settings .inside').prepend(error);
					jQuery('html').animate(
						{
							scrollTop: jQuery('.postbox-main-settings').offset()
								.top,
						},
						1000
					);
				}
			}

			if (
				jQuery('.cp-streamweasels-status-bar').hasClass(
					'cp-streamweasels-status-bar--free'
				)
			) {
				// Add input event listeners to all fields
				jQuery(
					'#swsb-twitch-username, #swsb-youtube-username, #swsb-kick-username'
				).on('input', function () {
					checkFilledFields();
				});

				// Add submit event listener to the form
				jQuery('#sw-form').on('submit', function (e) {
					var twitchUsername = jQuery('#swsb-twitch-username').val();
					var youtubeUsername = jQuery(
						'#swsb-youtube-username'
					).val();
					var kickUsername = jQuery('#swsb-kick-username').val();

					var filledFields = [
						twitchUsername,
						youtubeUsername,
						kickUsername,
					].filter(function (value) {
						return value !== '';
					}).length;

					if (filledFields > 1) {
						e.preventDefault();
						jQuery(
							'.postbox-main-settings .inside .notice'
						).remove();
						jQuery('.postbox-main-settings .inside').prepend(error);
						jQuery('html').animate(
							{
								scrollTop: jQuery(
									'.postbox-main-settings'
								).offset().top,
							},
							1000
						);
					}
				});
			}
		}
	});
})(jQuery);
