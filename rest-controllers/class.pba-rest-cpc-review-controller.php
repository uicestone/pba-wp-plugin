<?php

class PBA_REST_CPC_Review_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'cpc-review';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<date_type>.*)/(?P<mm>\d\d)/(?P<dd>\d\d)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_cpc_review' ),
			)
		) );

		register_rest_route( $this->namespace, '/user-count/(?P<date_type>.*)/(?P<mm>\d\d)/(?P<dd>\d\d)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_user_count' ),
			)
		) );

		register_rest_route( $this->namespace, '/user-count', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_user_count_total' ),
			)
		) );
	}

	/**
	 * Get CPC review of a day
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_cpc_review( $request ) {

		$date_type = $request->get_param('date_type');
		$month = $request->get_param('mm');
		$day = $request->get_param('dd');

		$mm = str_pad($month, 2, '0', STR_PAD_LEFT);
		$dd = str_pad($day, 2, '0', STR_PAD_LEFT);

		$user_count_total = get_option('user_count_' . $date_type, 1);
		$user_count_this_day = get_option('user_count_' . $date_type . '_' . $mm . $dd, 0);

		update_option('user_count_' . $date_type, ++$user_count_total);
		update_option('user_count_' . $date_type . '_' . $mm . $dd, ++$user_count_this_day);

		$post = get_page_by_path('cpc-review-' . $mm . '-' . $dd, OBJECT, 'post');

		$item = (object) array(
			'id' => $post->ID,
			'title' => get_the_title($post->ID),
			'content' => wpautop($post->post_content),
			'slug' => $post->post_name,
		);

		return rest_ensure_response($item);
	}

	/**
	 * Get user count of a date type
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_user_count( $request ) {
		$date_type = $request->get_param('date_type');
		$month = $request->get_param('mm');
		$day = $request->get_param('dd');
		$mm = str_pad($month, 2, '0', STR_PAD_LEFT);
		$dd = str_pad($day, 2, '0', STR_PAD_LEFT);
		$user_count_total = get_option('user_count_' . $date_type, 1);
		$user_count_this_day = get_option('user_count_' . $date_type . '_' . $mm . $dd, 0);
		$response = array(
			'count' => (int)$user_count_this_day,
			'percentage' => round($user_count_this_day / $user_count_total * 100, 1)
		);
		return rest_ensure_response($response);
	}

	/**
	 * Get total user count of CPC Review
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_user_count_total( $request ) {

		$user_count_total = 0;

		foreach (array('birth', 'memo', 'enroll') as $date_type) {
			$user_count_total += get_option('user_count_' . $date_type, 0);
		}

		$response = array(
			'count' => $user_count_total
		);

		return rest_ensure_response($response);
	}

}
