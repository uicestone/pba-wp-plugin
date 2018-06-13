<?php

class PBJD_REST_Weather_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'weather';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_weather' ),
			)
		) );
	}

	/**
	 * Get weather
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_weather( $request ) {

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

}
