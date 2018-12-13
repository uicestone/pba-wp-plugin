<?php

class PBJD_Admin {

	public static function init() {
		self::register_post_types();
		self::manage_admin_columns();

	}

	protected static function register_post_types () {

		register_taxonomy_for_object_type('category', 'attachment');
		register_taxonomy_for_object_type('post_tag', 'attachment');

		add_post_type_support('attachment', '');

		register_post_type('speech', array(
			'label' => '党建声音',
			'labels' => array(
				'all_items' => '所有党建声音',
				'add_new' => '添加党建声音',
				'add_new_item' => '新党建声音',
				'not_found' => '未找到党建声音'
			),
			'public' => true,
			'supports' => array('title'),
			'menu_icon' => 'dashicons-megaphone',
			'has_archive' => true
		));

		register_post_type('motto', array(
			'label' => '座右铭',
			'labels' => array(
				'all_items' => '所有座右铭',
				'add_new' => '添加座右铭',
				'add_new_item' => '新座右铭',
				'not_found' => '未找到座右铭'
			),
			'public' => true,
			'supports' => array('title', 'editor'),
			'menu_icon' => 'dashicons-clipboard',
			'has_archive' => true
		));

		register_post_type('spot', array(
			'label' => '地图点',
			'labels' => array(
				'all_items' => '所有地图点',
				'add_new' => '添加地图点',
				'add_new_item' => '新地图点',
				'not_found' => '未找到地图点'
			),
			'public' => true,
			'supports' => array('title', 'editor'),
			'menu_icon' => 'dashicons-location-alt',
			'has_archive' => true
		));

		register_post_type('room', array(
			'label' => '房间',
			'labels' => array(
				'all_items' => '所有房间',
				'add_new' => '添加房间',
				'add_new_item' => '新房间',
				'not_found' => '未找到房间'
			),
			'public' => true,
			'supports' => array('title', 'editor', 'thumbnail'),
			'menu_icon' => 'dashicons-admin-home',
			'has_archive' => true
		));

		register_post_type('appointment', array(
			'label' => '预约报名',
			'labels' => array(
				'all_items' => '所有预约报名',
				'add_new' => '添加预约报名',
				'add_new_item' => '新预约报名',
				'not_found' => '未找到预约报名'
			),
			'public' => true,
			'supports' => array('custom-fields'),
			'menu_icon' => 'dashicons-clock'
		));
	}

	protected static function manage_admin_columns () {

		add_filter('manage_speech_posts_columns', function($columns) {
			$columns['author_town'] = '所属街镇';
			$columns['author_name'] = '姓名';
			$columns['bgid'] = '背景音乐';
			$columns['audio'] = '录音';
			return $columns;
		});

		add_action('manage_speech_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
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

		add_filter('manage_motto_posts_columns', function($columns) {
			$columns['text'] = '文字';
			$columns['author_name'] = '作者';
			$columns['image'] = '图片';
			return $columns;
		});

		add_action('manage_motto_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
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

		add_filter('manage_spot_posts_columns', function($columns) {
			$columns['type'] = '类型';
			$columns['town'] = '所属街镇';
			$columns['contact'] = '联系人';
			$columns['phone'] = '电话';
			$columns['address'] = '地址';
			unset($columns['date']);
			return $columns;
		});

		add_action('manage_spot_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
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

		add_filter('manage_room_posts_columns', function($columns) {
			$columns['floor'] = '楼层';
			$columns['number'] = '房间号';
			$columns['color'] = '颜色';
			$columns['open'] = '开放预约';
			unset($columns['date']);
			return $columns;
		});

		add_action('manage_room_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
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

		add_filter('manage_appointment_posts_columns', function($columns) {
			$columns['type'] = '类型';
			unset($columns['date']);
			return $columns;
		});

		add_action('manage_appointment_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
				case 'type' :
					$type = get_post_meta($post->ID, 'type', true);
					echo $type;
					break;
				default;
			}
		});
	}

}
