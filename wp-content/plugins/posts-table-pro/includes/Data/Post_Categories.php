<?php

namespace Barn2\Plugin\Posts_Table_Pro\Data;

/**
 * Gets the post data for the categories column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Categories extends Abstract_Table_Data {

	public function get_data() {
		if ( array_intersect( [ 'all', 'categories' ], $this->links ) ) {
			$categories = get_the_category_list( parent::get_separator( 'categories' ), '', $this->post->ID );
		} else {
			$the_categories = get_the_category( $this->post->ID );

			if ( $the_categories ) {
				$categories = implode( parent::get_separator( 'categories' ), wp_list_pluck( $the_categories, 'name' ) );
			} else {
				$categories = apply_filters( 'the_category', __( 'Uncategorized', 'posts-table-pro' ), parent::get_separator( 'categories' ), '' );
			}
		}

		return apply_filters( 'posts_table_data_categories', $categories, $this->post );
	}

}
