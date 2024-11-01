<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SWSB_Kick_API' ) ) {

	class SWSB_Kick_API extends Streamweasels_Status_Bar_Admin {

        private $live_url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&type=video';
        private $apiKey;

		public function __construct() {
			$options = get_option('swsb_options');
            $this->apiKey = (!empty($options['swsb_youtube_api_key'])) ? $options['swsb_youtube_api_key'] : '';
		}

		public function swsb_fetch_kick(WP_REST_Request $request) {

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 40);
			}

            $headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];
			
			$channel = $request->get_param('user_login');
						
			// $response = wp_remote_get($this->live_url . '&key=' . $this->apiKey . '&channelId=' . $channel, [
			// 	'headers' => $headers,
			// 	'timeout' => 15
			// ]);

			$response = wp_remote_get('https://kick.com/api/v1/channels/' . $channel, [
				'headers' => $headers,
				'timeout' => 15
			]);

            if (is_wp_error($response)) {
                error_log('Error: ' . $response->get_error_message());
            } else {
                error_log('Response: ' . print_r($response, true));
            }            

			$errorResponse = $this->swsb_handle_api_errors($response, 'https://kick.com/api/v1/channels/' . $channel, 'Fetch Kick Status');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}
	
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
	
        }
	}
}