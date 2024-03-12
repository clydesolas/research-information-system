<?php

namespace Barn2\Plugin\Posts_Table_Pro\Data;

use Barn2\PTP_Lib\Table\Table_Data_Interface;

/**
 * Abstract post data class used to fetch data for a post in the table.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
abstract class Abstract_Table_Data implements Table_Data_Interface {

	protected $post;
	protected $links;

	public function __construct( $post, $links = '' ) {
		$this->post  = $post;
		$this->links = $links ? (array) $links : [];
	}

	protected static function get_separator( $item_type ) {
		$sep = ', ';

		if ( 'custom_field_row' === $item_type ) {
			$sep = '<br/>';
		}

		return apply_filters( 'posts_table_separator', apply_filters( "posts_table_separator_$item_type", $sep ) );
	}

	public function get_filter_data() {
		return '';
	}

	public function get_sort_data() {
		return '';
	}

}
