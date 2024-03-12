<?php

namespace Barn2\Plugin\Posts_Table_Pro\Data;

use Barn2\Plugin\Posts_Table_Pro\Util\Util;

/**
 * Gets post data for the tags column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Tags extends Abstract_Table_Data {

	public function get_data() {
		$tags = '';

		if ( array_intersect( [ 'all', 'tags' ], $this->links ) ) {
			$tags = Util::empty_if_false( get_the_tag_list( '', $this->get_separator( 'tags' ), '', $this->post->ID ) );
		} else {
			$the_tags = get_the_tags( $this->post->ID );

			if ( $the_tags && is_array( $the_tags ) ) {
				$tags = implode( parent::get_separator( 'tags' ), wp_list_pluck( $the_tags, 'name' ) );
			}
		}

		return apply_filters( 'posts_table_data_tags', $tags, $this->post );
	}

}
