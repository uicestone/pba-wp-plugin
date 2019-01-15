<?php

class PBA_REST_Speech_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'speeches';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<type>movie|talk)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_speeches' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'post_speech' ),
			), array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_speech' ),
			)
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>.+)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_speech' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_speech' ),
			)
		) );
	}

	/**
	 * Get Speeches to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_speeches( $request ) {
		$type = $request->get_param('type');

		$parameter_mappings = array (
			'page' => 'paged',
			'limit' => 'posts_per_page',
		);

		$parameters = array();

		foreach ($parameter_mappings as $param => $mapped_param) {
			if ($request->get_param($param) != null) {
				$parameters[$mapped_param] = $request->get_param($param);
			}
		}

		$parameters['post_type'] = 'speech';
		$parameters['meta_key'] = 'type';
		$parameters['meta_value'] = $type;

		$posts = get_posts($parameters);

		$speeches = array_map(function (WP_Post $post) use ($type) {
			$speech = array(
				'id' => $post->ID,
				'type' => $type,
				'bgid' => get_post_meta($post->ID, 'bgid', true),
				'audioUrl' => get_post_meta($post->ID, 'audio_url', true),
				'authorName' => get_post_meta($post->ID, 'author_name', true),
				'authorTown' => get_post_meta($post->ID, 'author_town', true),
			);
			return (object) $speech;
		}, $posts);

		return rest_ensure_response($speeches);
	}

	/**
	 * Post a Speech to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function post_speech( $request ) {

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$type = $request->get_param('type');
		$files =  $request->get_file_params();
		$body = $request->get_body_params();
		$bgid = $body['bgid'];

		if (!$bgid) {
			return rest_ensure_response(new WP_Error(400, 'Invalid bgid.', $bgid));
		}

		$file = wp_handle_upload($files['audio'], array('test_form' => false));

		if ( $file && empty( $file['error'] ) ) {
			$file['url'];
		} else {
			return rest_ensure_response(new WP_Error(400, $file['error']));
		}

		$speech_id = wp_insert_post(array(
			'post_type' => 'speech',
			'post_title' => $type === 'talk' ? '我要对党说' : '红色电影配音',
			'post_name' => $type  . '-' . time(),
			'post_status' => 'publish'
		));

		add_post_meta($speech_id, 'audio_url', $file['url']);
		add_post_meta($speech_id, 'bgid', $bgid);
		add_post_meta($speech_id, 'type', $type);

		return rest_ensure_response(array(
			'id' => $speech_id,
			'type' => $type,
			'bgid' => $bgid,
			'audioUrl' => $file['url'],
			'qrcodeUrl' => ''
		));
	}

	/**
	 * Update author info to a Speech to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function update_speech( $request ) {

		$id = $request->get_param('id');

		$post = get_post($id);

		if (!$post || $post->post_type !== 'speech') {
			return rest_ensure_response(new WP_Error(404, 'Not found.', $id));
		}

		$body = $request->get_body();
		$data = json_decode($body);

		if (isset($data->authorName)) {
			update_post_meta($id, 'author_name', $data->authorName);
		}

		if (isset($data->authorTown)) {
			update_post_meta($id, 'author_town', $data->authorTown);
		}

		if (isset($data->authorMobile)) {
			update_post_meta($id, 'author_mobile', $data->authorMobile);
		}

		$speech = array(
			'id' => $post->ID,
			'type' => get_post_meta($post->ID, 'type', true),
			'bgid' => get_post_meta($post->ID, 'bgid', true),
			'audioUrl' => get_post_meta($post->ID, 'audio_url', true),
			'authorName' => get_post_meta($post->ID, 'author_name', true),
			'authorTown' => get_post_meta($post->ID, 'author_town', true),
		);

		return rest_ensure_response((object) $speech);
	}

	/**
	 * Get single Speech to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_speech( $request ) {

		$id = $request->get_param('id');

		$post = get_post($id);

		if (!$post || $post->post_type !== 'speech') {
			return rest_ensure_response(new WP_Error(404, 'Not found.', $id));
		}

		$speech = array(
			'id' => $post->ID,
			'type' => get_post_meta($post->ID, 'type', true),
			'bgid' => get_post_meta($post->ID, 'bgid', true),
			'audioUrl' => get_post_meta($post->ID, 'audio_url', true),
			'authorName' => get_post_meta($post->ID, 'author_name', true),
			'authorTown' => get_post_meta($post->ID, 'audio_town', true),
		);

		return rest_ensure_response((object) $speech);
	}

	/**
	 * Delete a Speech to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function delete_speech( $request ) {

	}

}
