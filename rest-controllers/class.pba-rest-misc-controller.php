<?php

class PBA_REST_Misc_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'pba/v1';
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
	 * Post sign in data
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
}
