<?php
/**
 * YouTube API Class
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SWSB_YouTube_API' ) ) {

	class SWSB_YouTube_API extends Streamweasels_Status_Bar_Admin {

		private $token_url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=streamweasels';
		private $channel_url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails';
		private $playlist_url = 'https://www.googleapis.com/youtube/v3/playlists?part=snippet';
		private $live_url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&type=video';
		private $category_url = 'https://www.googleapis.com/youtube/v3/videoCategories?part=snippet'; 
		private $viewers_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,liveStreamingDetails';
		private $apiKey;
		private $debug = false;

		public function __construct() {
			$options = get_option('swsb_options');
			$this->apiKey = (!empty($options['swsb_youtube_api_key'])) ? $options['swsb_youtube_api_key'] : '';
		}	

        public function swsb_fetch_youtube(WP_REST_Request $request) {

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 403);
			}	
			
			$channel = $request->get_param('user_login');

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];
			
			$response = wp_remote_get($this->live_url . '&key=' . $this->apiKey . '&channelId=' . $channel, [
				'headers' => $headers,
				'timeout' => 15
			]);

			$errorResponse = $this->swsb_handle_api_errors($response, $this->live_url . '&key=' . $this->apiKey . '&channelId=' . $channel, 'Fetch YouTube Status');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
        }   
		
        public function swsb_fetch_youtube_viewers(WP_REST_Request $request) {

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 403);
			}	
			
			$id = $request->get_param('id');

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];
			
			$response = wp_remote_get($this->viewers_url . '&key=' . $this->apiKey . '&id=' . $id, [
				'headers' => $headers,
				'timeout' => 15
			]);

			$errorResponse = $this->swsb_handle_api_errors($response, $this->viewers_url . '&key=' . $this->apiKey . '&id=' . $id, 'Fetch YouTube Viewers');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
        }
		
        public function swsb_fetch_youtube_category(WP_REST_Request $request) {

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 403);
			}	
			
			$id = $request->get_param('id');

			$headers = [
				'referer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '',
			];
			
			$response = wp_remote_get($this->category_url . '&key=' . $this->apiKey . '&id=' . $id, [
				'headers' => $headers,
				'timeout' => 15
			]);

			$errorResponse = $this->swsb_handle_api_errors($response, $this->viewers_url . '&key=' . $this->apiKey . '&id=' . $id, 'Fetch YouTube Category');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
        } 		

		public function check_token($apiKey="") {

			$headers = [
				'referer' => $_SERVER['HTTP_REFERER']
			];

			$response = wp_remote_get( $this->token_url.'&key='.$apiKey, [
				'headers' => $headers,
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				$this->swsb_debug_field('SWYI - Token Query Failed - '.$this->token_url.'&key='.$apiKey);
				return array('error');
			}
			$result = wp_remote_retrieve_body( $response );
            $resultCode = wp_remote_retrieve_response_code( $response );

			if ($resultCode == 400) {
				// Invalid API Key
				$this->swsb_debug_field('SWYI - Token Query Failed - '.$this->token_url.'&key='.$apiKey);
			}

            return $resultCode;
		}
	}
}