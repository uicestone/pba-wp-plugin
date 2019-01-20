<?php

class PBA_REST_Misc_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/weather', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_weather' ),
			)
		) );

		register_rest_route( $this->namespace, '/signed-in-member-count', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_signed_in_member_count' ),
			)
		) );

		register_rest_route( $this->namespace, '/sign-in', array(
			array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'sign_in' ),
			)
		) );

		register_rest_route( $this->namespace, '/verify-mobile', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'verify_mobile' ),
			)
		) );

		register_rest_route( $this->namespace, '/my-sign-in', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_my_sign_in' ),
			)
		) );

		register_rest_route( $this->namespace, '/my-yuyue', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_my_yuyue' ),
			)
		) );

		register_rest_route( $this->namespace, '/my-speech', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_my_speech' ),
			)
		) );
	}

	/**
	 * Get weather
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_weather() {

		$key = constant('SENI_KEY');
		$location = 'shanghai';
		$language = 'zh-Hans';
		$unit = 'c';

		$query = compact('location', 'key', 'language', 'unit');

		$url = 'https://api.seniverse.com/v3/weather/now.json?' . http_build_query($query);

		$result = json_decode(file_get_contents($url));

		$weather_now = $result->results[0]->now;
		$weather_now->icon = 'https://s1.sencdn.com/web/icons/3d_50/' . $weather_now->code .'.png';

		return rest_ensure_response($weather_now);
	}

	/**
	 * Get signed in member count
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_signed_in_member_count() {

		$count = (int) get_option('signed_in_member_count', 0);

		return rest_ensure_response(compact('count'));
	}

	/**
	 * Post sign-in data
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function sign_in( $request ) {
		$body = $request->get_body();
		$data = json_decode($body);

		$users_matched = get_users(array('meta_query' => array(
			'relation' => 'OR',
			array('key' => 'id_card', 'value' => $data->idCard),
			array('key' => 'mobile', 'value' => $data->mobile)
		)));

		if (!$users_matched && ($data->mobile || $data->idCard)) {
			$user_id = wp_insert_user(array(
				'user_login' => $data->mobile,
				'display_name' => $data->name
			));
			$count = (int) get_option('signed_in_member_count', 0);
			update_option('signed_in_member_count', ++$count);
		}

		if (count($users_matched) === 1) {
			$user_id = $users_matched[0]->ID;
		}
		elseif(empty($user_id)) {
			return rest_ensure_response(new WP_Error(400, '匹配用户错误', $data));
		}

		$user_fields = array('name', 'id_card' => 'idCard', 'mobile', 'unit', 'organization', 'sex', 'residence', 'speciality');

		$member_info = array(
			'id' => $user_id
		);

		foreach ($user_fields as $store_key => $input_key) {
			if (is_numeric($store_key)) {
				$store_key = $input_key;
			}
			if (isset($data->$input_key) && $data->$input_key) {
				update_user_meta($user_id, $store_key, $data->$input_key);
			}

			$member_info[$input_key] = get_user_meta($user_id, $store_key, true);
		}

		return rest_ensure_response($member_info);
	}

	/**
	 * Verify mobile
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function verify_mobile( $request ) {
		$mobile = preg_replace('/[\s\-]/', '', $request->get_param('mobile'));
		$code = $request->get_param('code');

		if ($code) {
			$code_option = json_decode(get_option('mobile_code_' . $mobile));
			if (isset($code_option->code) && $code_option->code === $code && $code_option->expires_at >= time()) {
				$token = $mobile . ' ' . sha1($mobile . '-' . NONCE_KEY);
				return rest_ensure_response(array('token' => $token));
			}
			return rest_ensure_response(new WP_Error(403, '短信验证码错误'));
		} else {
			$code_option = json_decode(get_option('mobile_code_' . $mobile));
			if (!isset($code_option->code) || $code_option->expires_at < time()) {
				$code = rand(1000, 9999);
				update_option('mobile_code_' . $mobile, json_encode(array('code'=>(string)$code, 'expires_at'=>time()+600)));
			} else {
				$code = $code_option->code;
			}
			error_log('[DEBUG] SMS code is ' . $code);
			if (function_exists('aliyun_send_mobile_code')) {
				error_log('Fail to send SMS, function aliyun_send_mobile_code not exists.');
				$result = aliyun_send_mobile_code($mobile, $code);
				return rest_ensure_response(array('message' => '短信验证码已发送'));
			}
			return rest_ensure_response(new WP_Error(500, '短信服务不可用'));
		}

	}

	/**
	 * My sign-in data
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_my_sign_in( $request )
	{
		$token = $_SERVER['HTTP_AUTHORIZATION'];
		$token_parts = explode(' ', $token);
		$mobile = $token_parts[0];
		$hash = $token_parts[1];
		if ($hash === sha1($mobile . '-' . NONCE_KEY)) {

			$users_matched = get_users(array('meta_query' => array(
				array('key' => 'mobile', 'value' => $mobile)
			)));

			if (count($users_matched) === 1) {
				$user_id = $users_matched[0]->ID;
			}
			elseif(empty($user_id)) {
				return rest_ensure_response(new WP_Error(400, '匹配用户错误'));
			}

			$user_fields = array('name', 'unit', 'organization');

			$user_data = array();

			foreach ($user_fields as $user_field) {
				$user_data[$user_field] = get_user_meta($user_id, $user_field, true);
			}

			return rest_ensure_response($user_data);
		}
	}

	/**
	 * My yuyue data
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_my_yuyue( $request )
	{
		$token = $_SERVER['HTTP_AUTHORIZATION'];
		$token_parts = explode(' ', $token);
		$mobile = $token_parts[0];
		$hash = $token_parts[1];
		if ($hash === sha1($mobile . '-' . NONCE_KEY)) {

			$posts = get_posts(array('post_type' => 'appointment', 'posts_per_page' => -1, 'meta_query' => array(
				array('key' => '联系电话', 'value' => $mobile)
			)));

			$appointments = array_map(function($post) {
				return array(
					'target' => get_post_meta($post->ID, 'target', true)
				);
			}, $posts);

			return rest_ensure_response($appointments);

		}
	}

	/**
	 * My speech data
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_my_speech( $request )
	{
		$token = $_SERVER['HTTP_AUTHORIZATION'];
		$token_parts = explode(' ', $token);
		$mobile = $token_parts[0];
		$hash = $token_parts[1];
		if ($hash === sha1($mobile . '-' . NONCE_KEY)) {

			$posts = get_posts(array('post_type' => 'speech', 'posts_per_page' => -1, 'meta_query' => array(
				array('key' => 'author_mobile', 'value' => $mobile)
			)));

			$speeches = array_map(function (WP_Post $post) {
				$speech = array(
					'id' => $post->ID,
					'type' => get_post_meta($post->ID, 'type', true),
					'bgid' => get_post_meta($post->ID, 'bgid', true),
					'audioUrl' => get_post_meta($post->ID, 'audio_url', true),
					'authorName' => get_post_meta($post->ID, 'author_name', true),
					'authorTown' => get_post_meta($post->ID, 'author_town', true),
				);
				return (object) $speech;
			}, $posts);

			return rest_ensure_response($speeches);

		}
	}
}
