<?php
namespace Barn2\Plugin\Posts_Table_Pro\Util;

use WP_Term;
use const Barn2\Plugin\Posts_Table_Pro\PLUGIN_FILE;

/**
 * Utility functions for Posts Table Pro.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Util {

	const TABLE_CLASS         = 'posts-data-table';
	const TABLE_WRAPPER_CLASS = 'posts-table-wrapper';

	/**
	 * Combination of array_pad and array_slice.
	 *
	 * @param array $array Input array
	 * @param int $size The size of the array to return
	 * @param mixed $pad What to pad with
	 * @return array The result
	 */
	public static function array_pad_and_slice( $array, $size, $pad ) {
		if ( ! is_array( $array ) ) {
			$array = [];
		}
		return array_slice( array_pad( $array, $size, $pad ), 0, $size );
	}

	/**
	 * Similar to <code>array_diff_assoc</code>, but does a loose type comparison on array values (== not ===).
	 * Supports multi-dimensional arrays, but doesn't support passing more than two arrays.
	 *
	 * @param array $array1 The main array to compare against
	 * @param array $array2 The array to compare with
	 * @return array All entries in $array1 which are not present in $array2 (including key check)
	 */
	public static function array_diff_assoc( $array1, $array2 ) {
		if ( empty( $array1 ) || ! is_array( $array1 ) ) {
			return [];
		}

		if ( empty( $array2 ) || ! is_array( $array2 ) ) {
			return $array1;
		}

		foreach ( $array1 as $k1 => $v1 ) {
			if ( array_key_exists( $k1, $array2 ) ) {
				$v2 = $array2[$k1];

				if ( $v2 == $v1 ) {
					unset( $array1[$k1] );
				}
			}
		}
		return $array1;
	}

	/**
	 * Similar to <code>wp_list_pluck</code> or <code>array_column</code> but plucks several keys from the source array.
	 *
	 * @param array $list The array of arrays to extract the keys from
	 * @param array|string $keys The list of keys to pluck
	 * @return array An array returned in the same order as $list, but where each item in the array contains just the specified $keys
	 */
	public static function list_pluck_array( $list, $keys = [] ) {
		$result    = [];
		$keys_comp = array_flip( (array) $keys );

		// Return empty array if there are no keys to extract
		if ( ! $keys_comp ) {
			return [];
		}

		foreach ( $list as $key => $item ) {
			if ( ! is_array( $item ) ) {
				// Make sure we have an array to pluck from
				continue;
			}
			$item = array_intersect_key( $item, $keys_comp );

			foreach ( $item as $child_key => $child ) {
				if ( is_array( $child ) ) {
					$item[$child_key] = self::list_pluck_array( $child, $keys );
				}
			}

			$result[$key] = $item;
		}

		return $result;
	}

	public static function string_list_to_array( $arg ) {
		if ( is_array( $arg ) ) {
			return $arg;
		}
		return array_filter( array_map( 'trim', explode( ',', $arg ) ) );
	}

	// SANITIZE FUNCTIONS

	public static function empty_if_false( $var ) {
		if ( false === $var ) {
			return '';
		}
		return $var;
	}

	public static function maybe_parse_bool( $maybe_bool ) {
		if ( is_bool( $maybe_bool ) ) {
			return $maybe_bool;
		} elseif ( '1' == $maybe_bool || 'true' === $maybe_bool ) {
			return true;
		} elseif ( '' === $maybe_bool || '0' == $maybe_bool || 'false' === $maybe_bool ) {
			return false;
		} else {
			return $maybe_bool;
		}
	}

	public static function set_object_vars( $object, array $vars ) {
		$has = get_object_vars( $object );

		foreach ( $has as $name => $old ) {
			$object->$name = isset( $vars[$name] ) && ( null !== $vars[$name] ) ? $vars[$name] : $old;
		}
	}

	public static function sanitize_list_arg( $arg, $allow_space = false ) {
		if ( is_string( $arg ) ) {
			// Allows any "word" (letter, digit or underscore), comma, full-stop, colon, hyphen, plus sign and (optionally) a space.
			$pattern = $allow_space ? '/[^\w,\.\:\-\+ ]/' : '/[^\w,\.\:\-\+]/';
			return preg_replace( $pattern, '', $arg );
		}

		return $arg;
	}

	public static function sanitize_list_arg_allow_space( $arg ) {
		return self::sanitize_list_arg( $arg, true );
	}

	public static function sanitize_numeric_list_arg( $arg ) {
		if ( is_string( $arg ) ) {
			// Allows decimal digit or comma
			return preg_replace( '/[^\d,]/', '', $arg );
		}

		return $arg;
	}

	public static function sanitize_string_or_bool_arg( $arg ) {
		$maybe_bool = self::maybe_parse_bool( $arg );
		return is_bool( $maybe_bool ) ? $maybe_bool : filter_var( $arg, FILTER_SANITIZE_STRING );
	}

	public static function sanitize_class_name( $class ) {
		return preg_replace( '/[^a-zA-Z0-9-_]/', '', $class );
	}

	/**
	 * Parse a term arg (as given in various table options) into a sorted array, keyed by taxonomy.
	 *
	 * Terms args are specified as a list of items (comma-separated) in the format taxonomy:slug or taxonomy:term_id. The slugs/ids can be specified
	 * without the taxonomy if they belong to the previous taxonomy, e.g. some_category:term1,term2,term3.
	 *
	 * @param string $term_arg The term arg to parse.
	 * @param boolean $include_relations Whether to include the relationships between terms and taxonomies (OR, AND, etc) in the result. If set
	 *    to true, an additional '_relations' key is returned in the result.
	 * @return array The sorted terms, keyed by taxonomy.
	 */
	public static function parse_term_arg( $term_arg, $include_relations = false ) {
		if ( empty( $term_arg ) ) {
			return [];
		}

		$term_array = preg_split( '/([,\+])/', $term_arg, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		$result   = [];
		$taxonomy = false;
		$relation = null;

		foreach ( $term_array as $term ) {
			// preg_split also returns the delimiters, so we store the current relation based on this: ',' = OR '+' = AND.
			if ( ',' === $term ) {
				$relation = 'OR';
				continue;
			} elseif ( '+' === $term ) {
				$relation = 'AND';
				continue;
			}

			// Split term around the colon and check valid.
			$term_split = explode( ':', $term, 2 );

			if ( 1 === count( $term_split ) ) {
				// Need to have an existing taxonomy to process this term.
				if ( ! $taxonomy ) {
					continue;
				}

				$result[$taxonomy][] = $term_split[0];

				// Store the relation between the terms.
				if ( $include_relations && $relation ) {
					$result['_relations'][$taxonomy] = $relation;
				}
			} elseif ( 2 === count( $term_split ) ) {
				$taxonomy = $term_split[0];

				if ( ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}

				$result[$taxonomy][] = $term_split[1];

				if ( $include_relations ) {
					// If there's no relation set, default to OR - if there are additional terms for this taxonomy this may be updated later.
					if ( ! isset( $result['_relations'][$taxonomy] ) ) {
						$result['_relations'][$taxonomy] = 'OR';
					}

					// We set the outer relation based on the delimiter between the first and second taxonomies (if there is more than one taxonomy).
					// If there are more than two taxonomies, we ignore any further relations as you can only set one 'outer' relation between taxonomy queries.
					if ( $relation && ! isset( $result['_relations']['_outer'] ) ) {
						$result['_relations']['_outer'] = $relation;
					}
				}
			}
		}

		return $result;
	}

	// TERMS

	public static function get_the_term_names( $post, $taxonomy, $sep = ', ' ) {
		$terms = get_the_terms( $post, $taxonomy );
		if ( ! $terms || ! is_array( $terms ) ) {
			return '';
		}
		return implode( $sep, wp_list_pluck( $terms, 'name' ) );
	}

	public static function get_all_term_children( $term_ids, $taxonomy, $include_parents = false ) {
		if ( empty( $term_ids ) ) {
			return [];
		}

		$result = $include_parents ? $term_ids : [];

		foreach ( (array) $term_ids as $term_id ) {
			$result = array_merge( $result, get_term_children( $term_id, $taxonomy ) );
		}

		// Remove duplicates
		return array_unique( $result );
	}

	public static function convert_to_term_ids( $terms, $taxonomy ) {
		$result = [];

		if ( empty( $terms ) ) {
			return $result;
		}

		if ( ! is_array( $terms ) ) {
			$terms = explode( ',', str_replace( '+', ',', $terms ) );
		}

		foreach ( $terms as $slug ) {
			if ( is_numeric( $slug ) ) {
				$result[] = (int) $slug;
			} else {
				$_term = get_term_by( 'slug', $slug, $taxonomy );

				if ( $_term instanceof WP_Term ) {
					$result[] = $_term->term_id;
				}
			}
		}
		return $result;
	}

	// ADVANCED CUSTOM FIELDS

	public static function get_acf_field_object( $field, $post_id = false ) {
		$field_obj = false;

		if ( ! $post_id && function_exists( 'acf_get_field' ) ) {
			// If we're not getting field for a specific post, just check field exists (ACF Pro only)
			$field_obj = acf_get_field( $field );
		} elseif ( function_exists( 'get_field_object' ) ) {
			$field_obj = get_field_object( $field, $post_id, [ 'format_value' => false ] );
		}

		if ( $field_obj ) {
			$field_obj = array_merge( [ 'type' => '' ], $field_obj );

			if ( in_array( $field_obj['type'], [ 'date_picker', 'date_time_picker' ] ) && isset( $field_obj['date_format'] ) ) {
				// In ACF v4 and below, date picker fields used jQuery date formats and 'return_format' was called 'date_format'
				$field_obj['return_format'] = self::jquery_to_php_date_format( $field_obj['date_format'] );

				// In ACF v4 and below, display_format used jQuery date format
				if ( isset( $field_obj['display_format'] ) ) {
					$field_obj['display_format'] = self::jquery_to_php_date_format( $field_obj['display_format'] );
				}
			}

			return $field_obj;
		}

		return false;
	}

	public static function is_acf_active() {
		return class_exists( '\ACF' );
	}

	// DATES

	/**
	 * Convert a jQuery date format to a PHP one. E.g. 'dd-mm-yy' becomes 'd-m-Y'.
	 * @see http://api.jqueryui.com/datepicker/ for jQuery formats.
	 *
	 * @param string $jquery_date_format The jQuery date format to convert.
	 * @return string The equivalent PHP date format.
	 */
	public static function jquery_to_php_date_format( $jquery_date_format ) {
		$result = $jquery_date_format;

		if ( false === strpos( $result, 'dd' ) ) {
			$result = str_replace( 'd', 'j', $result );
		}
		if ( false === strpos( $result, 'mm' ) ) {
			$result = str_replace( 'm', 'n', $result );
		}
		if ( false === strpos( $result, 'oo' ) ) {
			$result = str_replace( 'o', 'z', $result );
		}

		return str_replace( [ 'dd', 'oo', 'DD', 'mm', 'MM', 'yy' ], [ 'd', 'z', 'l', 'm', 'F', 'Y' ], $result );
	}

	public static function is_european_date_format( $format ) {
		// It's EU format if the day comes first
		return $format && in_array( substr( $format, 0, 1 ), [ 'd', 'j' ] );
	}

	/**
	 * Is the value passed a valid UNIX epoch time (i.e. seconds elapsed since 1st January 1970)?
	 *
	 * Not a perfect implementation as it will return false for valid timestamps representing dates
	 * between 31st October 1966 and 3rd March 1973, but this is needed to prevent valid dates held
	 * in numeric formats (e.g. 20171201) being wrongly interpreted as timestamps.
	 *
	 * @param mixed $value The value to check
	 * @return boolean True if $value is a valid epoch timestamp
	 */
	public static function is_unix_epoch_time( $value ) {
		return is_numeric( $value ) && (int) $value == $value && strlen( (string) absint( $value ) ) > 8;
	}

	/**
	 * Convert a date string to a timestamp. A wrapper around strtotime which accounts for dates already formatted
	 * as a timestamp.
	 *
	 * @param string $date The date to convert to a timestamp.
	 * @return int|boolean The timestamp (number of seconds since the Epoch) for this date, or false on failure.
	 */
	public static function strtotime( $date ) {
		if ( self::is_unix_epoch_time( $date ) ) {
			// Already a UNIX timestamp so no need to convert, just return as an int.
			return (int) $date;
		}

		return strtotime( $date );
	}

	// SEARCH

	public static function is_valid_search_term( $search_term ) {
		$min_length = max( 1, absint( apply_filters( 'posts_table_minimum_search_term_length', 3 ) ) );
		return ! empty( $search_term ) && strlen( $search_term ) >= $min_length;
	}

	// IMAGES

	public static function get_image_size_width( $size ) {
		$width = false;

		if ( is_array( $size ) ) {
			$width = $size[0];
		} elseif ( is_string( $size ) ) {
			$sizes = wp_get_additional_image_sizes();

			if ( isset( $sizes[$size]['width'] ) ) {
				$width = $sizes[$size]['width'];
			} elseif ( $w = get_option( "{$size}_size_w" ) ) {
				$width = $w;
			}
		}
		return $width;
	}

	// OTHER

	public static function doing_lazy_load() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX && is_string( filter_input( INPUT_POST, 'table_id', FILTER_SANITIZE_STRING ) );
	}

	public static function format_post_link( $post, $link_text = '', $link_class = '' ) {
		$target = $class  = '';

		if ( ! $link_text ) {
			$link_text = get_the_title( $post );
		}

		if ( apply_filters( 'posts_table_open_posts_in_new_tab', false ) ) {
			$target = ' target="_blank"';
		}

		if ( $link_class ) {
			$class = sprintf( ' class="%s"', esc_attr( $link_class ) );
		}

		return sprintf( '<a href="%1$s"%3$s%4$s>%2$s</a>', get_permalink( $post ), $link_text, $target, $class );
	}

	public static function get_asset_url( $path = '' ) {
		return plugins_url( 'assets/' . ltrim( $path, '/' ), PLUGIN_FILE );
	}

	public static function get_wrapper_class() {
		$template = sanitize_html_class( strtolower( get_template() ) );
		return apply_filters( 'posts_table_wrapper_class', self::TABLE_WRAPPER_CLASS ) . ' ' . $template;
	}

	public static function get_table_class() {
		return apply_filters( 'posts_table_class', self::TABLE_CLASS );
	}

	public static function include_template( $template_name ) {
		$template_name = ltrim( $template_name, '/' );

		if ( $located = locate_template( 'ptp_templates/' . $template_name, false ) ) {
			include_once $located;
		} else {
			include_once plugin_dir_path( PLUGIN_FILE ) . 'templates/' . $template_name;
		}
	}

}
