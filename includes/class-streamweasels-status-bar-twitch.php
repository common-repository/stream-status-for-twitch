<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SWSB_Twitch_API' ) ) {

	class SWSB_Twitch_API extends Streamweasels_Status_Bar_Admin {

		private $token_url = 'https://id.twitch.tv/oauth2/token';
		private $client_id;
		private $client_secret;
		private $auth_token;		

		public function __construct() {
			$options = get_option('swsb_options');
			$this->client_id = (!empty($options['swsb_client_id'])) ? $options['swsb_client_id'] : '';
			$this->auth_token = (!empty($options['swsb_api_access_token'])) ? $options['swsb_api_access_token'] : '';
		}

		public function refresh_token() {
            delete_transient( 'swsb_twitch_token' );
            delete_transient( 'swsb_twitch_token_expires' );
		}

		public function get_token($clientId="",$clientSecret="") {
			$token = get_transient( 'swsb_twitch_token' );
            $expires = get_transient( 'swsb_twitch_token_expires' );
			$clientIdVar = ($clientId !== '' ? $clientId : $this->client_id);
			$clientSecretVar = ($clientSecret !== '' ? $clientSecret : $this->client_secret);

			if ( $token !== false ) {
				return array($token, $expires);
			}

			$args = [
				'client_id' => $clientIdVar,
				'client_secret' => $clientSecretVar,
				'grant_type' => 'client_credentials'
			];

			$headers = [
				'Content-Type' => 'application/json'
			];

			$response = wp_remote_post( $this->token_url, [
				'headers' => $headers,
				'body'    => wp_json_encode( $args ),
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				$this->swsb_debug_field($response);
				return array('error');
			}

			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );

			if ( $result === false || !isset( $result['access_token'] ) ) {
				delete_transient( 'swsb_twitch_auth_token' );
				delete_transient( 'swsb_twitch_auth_token_expires' );
				$this->swsb_debug_field($result);
				return array('error', $result['message']);
			}
			
			$token = $result['access_token'];
            $expires = $result['expires_in'];
			$today = time();
			$todayPlusExpires = $today + $expires;
			$expiresDate = date('F j, Y', $todayPlusExpires);

			set_transient( 'swsb_twitch_token', sanitize_text_field($token), $result['expires_in'] - 30 );
            set_transient( 'swsb_twitch_token_expires', sanitize_text_field($expiresDate), $result['expires_in'] - 30 );

			return array(esc_attr($token), esc_attr($expiresDate));
		}

		public function swsb_fetch_streams(WP_REST_Request $request) {

			$nonce = $request->get_header('X-WP-Nonce');
			if (!wp_verify_nonce($nonce, 'wp_rest')) {
				return new WP_REST_Response('Nonce verification failed', 403);
			}

			$authToken = $this->auth_token;
			$clientId = $this->client_id;
			$baseUrl = "https://api.twitch.tv/helix/streams";
			
			$channel = $request->get_param('user_login');

			$queryParams = [];
			if (!empty($channel)) {
				$queryParams['user_login'] = strtolower($channel);
			}
			
			$queryString = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
			$url = $baseUrl . '?' . $queryString;
			
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Client-ID' => $clientId,
					'Authorization' => 'Bearer ' . $authToken,
				),
			));			

			$errorResponse = $this->swsb_handle_api_errors($response, $url, 'Fetch Twitch Status');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}
	
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
	
		}		
	}
}