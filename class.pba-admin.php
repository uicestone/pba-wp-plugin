<?php

class PBA_Admin {

	public static function init() {
		self::register_post_types();
		self::manage_admin_columns();
	}

	protected static function register_post_types () {

		register_taxonomy_for_object_type('category', 'attachment');
		register_taxonomy_for_object_type('post_tag', 'attachment');

		add_post_type_support('attachment', '');
		add_post_type_support('page', 'excerpt');

		add_filter( 'big_image_size_threshold', '__return_false' );

	}

	protected static function manage_admin_columns ()
	{

		add_filter('manage_motto_posts_columns', function ($columns) {
			$columns['text'] = '文字';
			$columns['author_name'] = '作者';
			$columns['image'] = '图片';
			return $columns;
		});

	}

}
