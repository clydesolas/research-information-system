<?php
namespace Barn2\Plugin\Posts_Table_Pro\Util;

/**
 * Column utility functions.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Columns_Util {

	/**
	 * @var array Global column defaults.
	 */
	private static $column_defaults = null;

	public static function column_defaults() {

		if ( empty( self::$column_defaults ) ) {
			// Priority values are used to determine visiblity at small screen sizes (1 = highest priority).
			self::$column_defaults = apply_filters( 'posts_table_column_defaults', [
				'id'            => [ 'heading' => __( 'ID', 'posts-table-pro' ), 'priority' => 3 ],
				'title'         => [ 'heading' => __( 'Title', 'posts-table-pro' ), 'priority' => 1 ],
				'content'       => [ 'heading' => __( 'Content', 'posts-table-pro' ), 'priority' => 8 ],
				'excerpt'       => [ 'heading' => __( 'Summary', 'posts-table-pro' ), 'priority' => 4 ],
				'date'          => [ 'heading' => __( 'Date', 'posts-table-pro' ), 'priority' => 5 ],
				'date_modified' => [ 'heading' => __( 'Last Modified', 'posts-table-pro' ), 'priority' => 5 ],
				'author'        => [ 'heading' => __( 'Author', 'posts-table-pro' ), 'priority' => 9 ],
				'categories'    => [ 'heading' => __( 'Categories', 'posts-table-pro' ), 'priority' => 6 ],
				'tags'          => [ 'heading' => __( 'Tags', 'posts-table-pro' ), 'priority' => 10 ],
				'status'        => [ 'heading' => __( 'Status', 'posts-table-pro' ), 'priority' => 11 ],
				'image'         => [ 'heading' => __( 'Image', 'posts-table-pro' ), 'priority' => 2 ],
				'button'        => [ 'heading' => '', 'priority' => 7 ]
				] );
		}

		return self::$column_defaults;
	}

	public static function is_custom_field( $column ) {
		return $column && 'cf:' === substr( $column, 0, 3 );
	}

	public static function get_custom_field( $column ) {
		if ( self::is_custom_field( $column ) ) {
			return substr( $column, 3 );
		}
		return false;
	}

	public static function is_custom_taxonomy( $column ) {
		return $column && 'tax:' === substr( $column, 0, 4 );
	}

	public static function get_custom_taxonomy( $column ) {
		if ( self::is_custom_taxonomy( $column ) ) {
			return substr( $column, 4 );
		}
		return false;
	}

	public static function is_filter_column( $column ) {
		return $column && '_hfilter' === substr( $column, -8 );
	}

	public static function get_filter_column( $column ) {
		if ( self::is_filter_column( $column ) ) {
			return substr( $column, 0, -8 );
		}
		return false;
	}

	public static function get_column_class( $column ) {
		$column_class_suffix = self::unprefix_column( $column );

		// Certain classes are reserved for use by DataTables Responsive, so we need to strip these to prevent conflicts.
		$column_class_suffix = trim( str_replace( [ 'mobile', 'tablet', 'desktop' ], '', $column_class_suffix ), '_- ' );

		return $column_class_suffix ? Util::sanitize_class_name( 'col-' . $column_class_suffix ) : '';
	}

	public static function get_column_data_source( $column ) {
		// '.' not allowed in data source
		return str_replace( '.', '', $column );
	}

	public static function get_column_name( $column ) {
		return self::unprefix_column( $column );
	}

	public static function get_column_taxonomy( $column ) {
		if ( $hidden = self::is_filter_column( $column ) ) {
			$column = self::get_filter_column( $column );
		}

		$tax = $column;

		if ( 'categories' === $column ) {
			$tax = 'category';
		} elseif ( 'tags' === $column ) {
			$tax = 'post_tag';
		} elseif ( self::is_custom_taxonomy( $column ) ) {
			$tax = self::get_custom_taxonomy( $column );
		}
		if ( taxonomy_exists( $tax ) ) {
			return $tax;
		}
		return false;
	}

	public static function unprefix_column( $column ) {
		if ( false !== ( $str = strstr( $column, ':' ) ) ) {
			$column = substr( $str, 1 );
		}
		return $column;
	}

	public static function check_blank_heading( $heading ) {
		return 'blank' === $heading ? '' : $heading;
	}

}
