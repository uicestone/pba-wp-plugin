<?php

class PBA_Admin {

	public static function init() {
		self::register_post_types();
		self::manage_admin_columns();
		self::add_admin_buttons();
	}

	protected static function register_post_types () {

		register_taxonomy_for_object_type('category', 'attachment');
		register_taxonomy_for_object_type('post_tag', 'attachment');

		add_post_type_support('attachment', '');

		// register_post_type('speech', array(
		// 	'label' => '党建声音',
		// 	'labels' => array(
		// 		'all_items' => '所有党建声音',
		// 		'add_new' => '添加党建声音',
		// 		'add_new_item' => '新党建声音',
		// 		'not_found' => '未找到党建声音'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('title'),
		// 	'menu_icon' => 'dashicons-megaphone',
		// 	'has_archive' => true
		// ));
		//
		// register_post_type('motto', array(
		// 	'label' => '座右铭',
		// 	'labels' => array(
		// 		'all_items' => '所有座右铭',
		// 		'add_new' => '添加座右铭',
		// 		'add_new_item' => '新座右铭',
		// 		'not_found' => '未找到座右铭'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('title', 'editor'),
		// 	'menu_icon' => 'dashicons-clipboard',
		// 	'has_archive' => true
		// ));
		//
		// register_post_type('spot', array(
		// 	'label' => '地图点',
		// 	'labels' => array(
		// 		'all_items' => '所有地图点',
		// 		'add_new' => '添加地图点',
		// 		'add_new_item' => '新地图点',
		// 		'not_found' => '未找到地图点'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('title', 'editor'),
		// 	'menu_icon' => 'dashicons-location-alt',
		// 	'has_archive' => true
		// ));
		//
		// register_post_type('organization', array(
		// 	'label' => '群团组织',
		// 	'labels' => array(
		// 		'all_items' => '所有群团组织',
		// 		'add_new' => '添加群团组织',
		// 		'add_new_item' => '新群团组织',
		// 		'not_found' => '未找到群团组织'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('title', 'thumbnail'),
		// 	'menu_icon' => 'dashicons-groups',
		// 	'has_archive' => true
		// ));
		//
		// register_post_type('room', array(
		// 	'label' => '场馆',
		// 	'labels' => array(
		// 		'all_items' => '所有场馆',
		// 		'add_new' => '添加场馆',
		// 		'add_new_item' => '新场馆',
		// 		'not_found' => '未找到场馆'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('title', 'editor', 'thumbnail'),
		// 	'menu_icon' => 'dashicons-admin-home',
		// 	'has_archive' => true
		// ));
		//
		// register_post_type('event', array(
		// 	'label' => '活动',
		// 	'labels' => array(
		// 		'all_items' => '所有活动',
		// 		'add_new' => '添加活动',
		// 		'add_new_item' => '新活动',
		// 		'not_found' => '未找到活动'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('title', 'editor', 'thumbnail'),
		// 	'menu_icon' => 'dashicons-calendar-alt',
		// 	'has_archive' => true
		// ));
		//
		// register_post_type('appointment', array(
		// 	'label' => '预约报名',
		// 	'labels' => array(
		// 		'all_items' => '所有预约报名',
		// 		'add_new' => '添加预约报名',
		// 		'add_new_item' => '新预约报名',
		// 		'not_found' => '未找到预约报名'
		// 	),
		// 	'public' => true,
		// 	'supports' => array('custom-fields'),
		// 	'capability_type' => 'post',
		// 	'capabilities' => array(
		// 		'create_posts' => false
		// 	),
		// 	'map_meta_cap' => true,
		// 	'menu_icon' => 'dashicons-clock'
		// ));
	}

	protected static function manage_admin_columns ()
	{

		add_filter('manage_speech_posts_columns', function ($columns) {
			$columns['author_town'] = '所属街镇';
			$columns['author_name'] = '姓名';
			$columns['bgid'] = '背景音乐';
			$columns['audio'] = '录音';
			return $columns;
		});

		add_action('manage_speech_posts_custom_column', function ($column_name) {
			global $post;
			switch ($column_name) {
				case 'author_town' :
					$author_town = get_post_meta($post->ID, 'author_town', true);
					echo $author_town;
					break;
				case 'author_name' :
					$author_name = get_post_meta($post->ID, 'author_name', true);
					echo $author_name;
					break;
				case 'bgid' :
					$bgid = get_post_meta($post->ID, 'bgid', true);
					echo $bgid;
					break;
				case 'audio' :
					$url = get_post_meta($post->ID, 'audio_url', true);
					echo '<audio src="' . $url . '" controls></audio>';
					break;
				default;
			}
		});

		add_filter('manage_motto_posts_columns', function ($columns) {
			$columns['text'] = '文字';
			$columns['author_name'] = '作者';
			$columns['image'] = '图片';
			return $columns;
		});

		add_action('manage_motto_posts_custom_column', function ($column_name) {
			global $post;
			switch ($column_name) {
				case 'text' :
					echo $post->post_content;
					break;
				case 'author_name' :
					$author_name = get_post_meta($post->ID, 'author_name', true);
					echo $author_name;
					break;
				case 'image' :
					$image_url = get_post_meta($post->ID, 'image_url', true);
					echo '<a href="' . $image_url . '" target="_blank">' . basename($image_url) . '</a>';
					break;
				default;
			}
		});

		add_filter('manage_spot_posts_columns', function ($columns) {
			$columns['type'] = '类型';
			$columns['town'] = '所属街镇';
			$columns['contact'] = '联系人';
			$columns['phone'] = '电话';
			$columns['address'] = '地址';
			unset($columns['date']);
			return $columns;
		});

		add_action('manage_spot_posts_custom_column', function ($column_name) {
			global $post;
			switch ($column_name) {
				case 'type' :
					$type = get_post_meta($post->ID, 'type', true);
					echo $type;
					break;
				case 'town' :
					$town = get_post_meta($post->ID, 'town', true);
					echo $town;
					break;
				case 'contact' :
					$contact = get_post_meta($post->ID, 'contact', true);
					echo $contact;
					break;
				case 'phone' :
					$phone = get_post_meta($post->ID, 'phone', true);
					echo $phone;
					break;
				case 'address' :
					$address = get_post_meta($post->ID, 'address', true);
					echo $address;
					break;
				default;
			}
		});

		add_filter('manage_room_posts_columns', function ($columns) {
			$columns['floor'] = '楼层';
			$columns['number'] = '房间号';
			$columns['color'] = '颜色';
			$columns['open'] = '开放预约';
			unset($columns['date']);
			return $columns;
		});

		add_action('manage_room_posts_custom_column', function ($column_name) {
			global $post;
			switch ($column_name) {
				case 'floor' :
					$floor = get_post_meta($post->ID, 'floor', true);
					echo $floor;
					break;
				case 'number' :
					$number = get_post_meta($post->ID, 'number', true);
					echo $number;
					break;
				case 'color' :
					$color = get_post_meta($post->ID, 'color', true);
					echo '<div style="width:2em;height:1em;background:' . $color . '"></div>';
					break;
				case 'open' :
					$open = get_post_meta($post->ID, 'open', true);
					echo $open ? '是' : '否';
					break;
				default;
			}
		});

		add_filter('manage_appointment_posts_columns', function ($columns) {
			$columns['type'] = '类型';
			$columns['target'] = '场馆/活动';
			$columns['datetime'] = '日期时间';
			$columns['contact'] = '联系人';
			$columns['phone'] = '联系电话';
			$columns['info'] = '其他信息';
			$columns['review'] = '审核';
			$columns['post_time'] = '提交时间';
			unset($columns['title']);
			unset($columns['date']);
			return $columns;
		});

		add_action('manage_appointment_posts_custom_column', function ($column_name) {
			global $post;
			$type = get_post_meta($post->ID, 'type', true);
			switch ($column_name) {
				case 'type' :
					echo $type;
					break;
				case 'target' :
					if ($type === '活动报名') {
						$event_id = get_post_meta($post->ID, 'event_id', true);
						echo get_the_title($event_id);
					} elseif ($type === '参观预约') {
						echo '参观党建服务中心';
					} elseif ($type === '场馆预约') {
						echo get_post_meta($post->ID, '会议室/培训室', true);
					}
					break;
				case 'datetime' :
					$date = get_post_meta($post->ID, '预约日期', true);
					$time = get_post_meta($post->ID, '预约时间', true);
					echo $date . ' ' . $time;
					break;
				case 'contact' :
					$value = get_post_meta($post->ID, '联系人', true);
					echo $value;
					break;
				case 'phone' :
					$value = get_post_meta($post->ID, '联系电话', true);
					echo $value;
					break;
				case 'info' :
					if ($type === '参观预约') {
						$fields = ['单位名称', '参加人数'];
					} elseif ($type === '场馆预约') {
						$room_number = get_post_meta($post->ID, 'room_number', true);
						if ($room_number === '101') {
							$fields = ['单位名称', '参加人数'];
						} else {
							$fields = ['单位名称', '活动名称', '参加人数', '备注'];
							$fapiao_fields = ['公司名称', '税号', '账号', '开户银行', '单位地址'];
						}
					}

					if (isset($fields)) {
						echo '<ul>' . implode("\n", array_map(function ($field) use ($post) {
								return '<li>' . $field . '：' . get_post_meta($post->ID, $field, true) . '</li>';
							}, $fields)) . '</ul>';
					}

					if (isset($fapiao_fields)) {
						echo '<div class="fapiao"><h4><a href="#">开票信息</a></h4><ul>' . implode(array_map(function ($field) use ($post) {
								return '<li>' . $field . '：' . get_post_meta($post->ID, '开票信息_' . $field, true) . '</li>';
							}, $fapiao_fields)) . '</ul></div>';
					}
					break;
				case 'review' :
					echo '<input type="hidden" name="confirmed" value="' . get_post_meta($post->ID, 'confirmed', true) . '">';
					break;
				case 'post_time' :
					echo get_the_date('Y-m-d H:i:s', $post->ID);
					break;
				default;
			}
		});

		add_filter('post_row_actions', function ($actions, $post) {
			if ($post->post_type == "appointment") {
				unset($actions['inline hide-if-no-js']);
				unset($actions['view']);
				unset($actions['edit']);
			}
			return $actions;
		}, 10, 2);
	}

	protected static function add_admin_buttons () {
		add_action('admin_footer', function () {
			$screen = get_current_screen();
			if ( $screen->post_type != 'appointment' )   // Only add to users.php page
				return;
			?>
			<script type="text/javascript">
				jQuery(document).ready( function($) {
					$('.review.column-review').each(function () {
						var postId = $(this).parent().attr('id').replace('post-', '');
						if ($(this).find('[name="confirmed"]').val() === '1') {
                            $(this).append('<span>已确认</span>');
                        } else if ($(this).find('[name="confirmed"]').val() === '0') {
                            $(this).append('<span>已拒绝</span>');
						} else {
							$(this).append('<form method="POST"><input type="hidden" name="pba_confirm_appointment" value="' + postId + '"><input class="button" name="pba_confirm_appointment_yes" type="submit" value="确认"> <input class="button" type="submit" name="pba_confirm_appointment_no" value="拒绝"></form>');
						}
					});
					$('.column-info .fapiao h4 a').click(function (e) {
					    $(this).parent().next('ul').toggle();
					    e.preventDefault();
					});
				});
			</script>
			<style>
				.column-info { width: 30%; }
				.column-info h4 { margin-bottom: 5px; }
				.column-info ul { margin: 0; }
				.column-info .fapiao ul { display: none; }
			</style>
			<?php
		});

		if (isset($_POST['pba_confirm_appointment'])) {
			$appointment_id = $_POST['pba_confirm_appointment'];
			if (isset($_POST['pba_confirm_appointment_yes'])) {
				update_post_meta($appointment_id, 'confirmed', 1);
			} elseif (isset($_POST['pba_confirm_appointment_no'])) {
				update_post_meta($appointment_id, 'confirmed', 0);
				$room_number = get_post_meta($appointment_id, 'room_number', true);
				$room = get_posts(array('post_type' => 'room', 'meta_key' => 'number', 'meta_value' => $room_number))[0];
				if (!$room) {
					exit('Room not found, number: ' . $room_number);
				}
				$room_id = $room->ID;
				// remove date and time from appointments
				$appointments = json_decode(get_post_meta($room_id, 'appointments', true), JSON_OBJECT_AS_ARRAY);
				$appointments_origin = $appointments;
				$date = get_post_meta($appointment_id, '预约日期', true);
				$time = get_post_meta($appointment_id, '预约时间', true);

				$appointments[$date] = array_filter($appointments[$date], function($t) use($time) { return $t !== $time; });
				if (count($appointments[$date]) !== count($appointments_origin[$date])) {
					// if any time is released, occupied date will be released too
					$full_dates = json_decode(get_post_meta($room_id, 'full_dates', true));
					$full_dates = array_filter($full_dates, function($d) use($date) { return $d !== $date; });
					update_post_meta($room_id, 'full_dates', json_encode($full_dates));
				}

				if (!$appointments[$date]) {
					unset($appointments[$date]);
				}

				update_post_meta($room_id, 'appointments', json_encode($appointments, JSON_UNESCAPED_UNICODE));
			}
		}
	}

}
