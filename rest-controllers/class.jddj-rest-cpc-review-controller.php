<?php

class JDDJ_REST_CPC_Review_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'cpc-review';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<mm>\d\d)/(?P<dd>\d\d)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_cpc_review' ),
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

		$month = $request->get_param('mm');
		$day = $request->get_param('dd');

		$mm = str_pad($month, 2, '0', STR_PAD_LEFT);
		$dd = str_pad($day, 2, '0', STR_PAD_LEFT);

		$post = get_page_by_path('cpc-review-' . $mm . '-' . $dd, OBJECT, 'post');

		$item = (object) array(
			'id' => $post->ID,
			'title' => get_the_title($post->ID),
			'content' => wpautop($post->post_content),
			'slug' => $post->post_name,
		);

		return rest_ensure_response($item);
	}

}
