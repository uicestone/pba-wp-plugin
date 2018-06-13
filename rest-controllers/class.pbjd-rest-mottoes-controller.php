<?php

class PBJD_REST_Motto_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'mottoes';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_mottoes' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'post_motto' ),
			), array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_motto' ),
			)
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>.+)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_motto' ),
			)
		) );
	}

	/**
	 * Get a list of mottoes
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_mottoes( $request ) {
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

		$parameters['post_type'] = 'motto';

		$posts = get_posts($parameters);

		$mottoes = array_map(function (WP_Post $post) {
			$motto = array(
				'id' => $post->ID,
				'text' => $post->post_content,
				'authorName' => get_post_meta($post->ID, 'author_name', true),
				'imageUrl' => get_post_meta($post->ID, 'image_url', true)
			);
			return (object) $motto;
		}, $posts);

		return rest_ensure_response($mottoes);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function post_motto( $request ) {

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$files =  $request->get_file_params();
		$body = $request->get_body_params();
		$author_name = $body['authorName'];
		$text = $body['text'];

		if (!$author_name) {
			return rest_ensure_response(new WP_Error(400, 'Invalid author name.', $author_name));
		}

		if (!$text) {
			return rest_ensure_response(new WP_Error(400, 'Empty text.'));
		}

		$file = wp_handle_upload($files['image'], array('test_form' => false));

		if ( $file && empty( $file['error'] ) ) {
			$file['url'];
		} else {
			return rest_ensure_response(new WP_Error(400, $file['error']));
		}

		$motto_id = wp_insert_post(array(
			'post_type' => 'motto',
			'post_title' => '我的座右铭',
			'post_name' => 'motto'  . '-' . time(),
			'post_content' => $text,
			'post_status' => 'publish'
		));

		$motto = get_post($motto_id);

		add_post_meta($motto_id, 'image_url', $file['url']);
		add_post_meta($motto_id, 'author_name', $author_name);

		return rest_ensure_response(array(
			'id' => $motto_id,
			'text' => $motto->post_content,
			'authorName' => $author_name,
			'imageUrl' => $file['url'],
			'qrcodeUrl' => ''
		));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_motto( $request ) {

		$id = $request->get_param('id');

		$post = get_post($id);

		if (!$post || $post->post_type !== 'motto') {
			return rest_ensure_response(new WP_Error(404, 'Not found.', $id));
		}

		$motto = array(
			'id' => $id,
			'text' => $post->post_content,
			'imageUrl' => get_post_meta($id, 'image_url', true),
			'authorName' => get_post_meta($id, 'author_name', true),
		);

		return rest_ensure_response((object) $motto);
	}
	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function delete_motto( $request ) {

	}

}
