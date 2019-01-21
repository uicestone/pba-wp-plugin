<?php

class PBA_REST_Appointment_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'appointments';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_appointments' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'post_appointment' ),
			), array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_appointment' ),
			)
		) );

	}

	/**
	 * Get a list of appointments
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_appointments( $request ) {

		$parameters = array('post_type' => 'appointment', 'limit' => -1);

		$posts = get_posts($parameters);

		$appointments = array_map(function (WP_Post $post) {
			$appointment = array(
				'id' => $post->ID,
				'type' => get_post_meta($post->ID, 'type', true)
			);
			return (object) $appointment;
		}, $posts);

		return rest_ensure_response($appointments);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function post_appointment( $request ) {

		$body = $request->get_body_params();

		if (isset($body['预约日期'])) {
			$body['预约日期'] = date('Y-m-d', strtotime($body['预约日期']) + get_option('gmt_offset') * HOUR_IN_SECONDS);
		}

		if (!$body['type'] || !in_array($body['type'], array('参观预约', '场馆预约', '活动报名'))) {
			return rest_ensure_response(new WP_Error(400, 'Appointment type error.'));
		}

		if ($body['type'] === '参观预约') {
			$room_number = 0;
		} elseif ($body['type'] === '场馆预约') {
			if (!$body['room_number']) {
				return rest_ensure_response(new WP_Error(400, 'Missing appointment room number.'));
			}
			$room_number = $body['room_number'];
		}

		if (isset($room_number)) {

			$room = get_posts(array('post_type' => 'room', 'meta_key' => 'number', 'meta_value' => $room_number))[0];
			if (!$room) {
				return rest_ensure_response(new WP_Error(400, 'Appointment room not found.'));
			}

			$date = $body['预约日期'];
			$time = $body['预约时间'];

			if (!$date || !$time) {
				return rest_ensure_response(new WP_Error(400, 'Missing appointment date or time.'));
			}

			$room_appointments = json_decode(get_post_meta($room->ID, 'appointments', true), JSON_OBJECT_AS_ARRAY);
			$full_dates = json_decode(get_post_meta($room->ID, 'full_dates', true));

			if (!$full_dates) {
				$full_dates = array();
			} elseif (in_array($date, $full_dates)) {
				return rest_ensure_response(new WP_Error(400, 'Room occupied full day.'));
			}

			$time_type = (int)$room_number === 101 || (int)$room_number === 0 ? '5-slot' : '2-slot';

			if ($time_type === '2-slot' && $time === '全天'
				|| $time_type === '2-slot' && count($room_appointments[$date]) === 1 // 非红厅场馆时间段为上午/下午
				|| $time_type === '5-slot' && count($room_appointments[$date]) === 4) { // 其他为5段

				$full_dates[] = $date;
				update_post_meta($room->ID, 'full_dates', json_encode($full_dates));
			}

			$room_appointments[$date][] = $time;

			update_post_meta($room->ID, 'appointments', json_encode($room_appointments, JSON_UNESCAPED_UNICODE));
		}

		$appointment_id = wp_insert_post(array(
			'post_type' => 'appointment',
			'post_status' => 'publish'
		));

		foreach ($body as $key => $value) {
			add_post_meta($appointment_id, $key, $value);
		}

		return rest_ensure_response(array(
			'id' => $appointment_id,
			'type' => get_post_meta($appointment_id, 'type', true)
		));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function delete_appointment( $request ) {

	}

}
