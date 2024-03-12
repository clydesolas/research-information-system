<?php
namespace Barn2\Plugin\Posts_Table_Pro;

use Barn2\Plugin\Posts_Table_Pro\Util\Util,
	Barn2\Plugin\Posts_Table_Pro\Util\Options;

/**
 * Responsible for storing and validating the posts table arguments.
 * Parses an array of args into the corresponding properties.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Args {

	/**
	 * @var array The args array.
	 */
	private $args = [];

	/* Table params */
	public $columns;
	public $headings; // built from headings
	public $widths;
	public $auto_width;
	public $priorities;
	public $column_breakpoints;
	public $responsive_control;
	public $responsive_display;
	public $wrap;
	public $show_footer;
	public $search_on_click;
	public $filters;
	public $filter_headings; // built from filters
	public $scroll_offset;
	public $content_length;
	public $excerpt_length;
	public $links;
	public $lazy_load;
	public $cache;
	public $image_size;
	public $lightbox;
	public $shortcodes;
	public $date_format;
	public $date_columns;
	public $no_posts_message;
	public $no_posts_filtered_message;
	public $paging_type;
	public $page_length;
	public $search_box;
	public $totals;
	public $pagination;
	public $reset_button;
	public $button_text;

	/* Query params */
	public $rows_per_page;
	public $post_limit;
	public $sort_by;
	public $sort_order;
	public $post_type;
	public $status;
	public $category;
	public $exclude_category;
	public $tag;
	public $term;
	public $exclude_term;
	public $numeric_terms;
	public $cf;
	public $year;
	public $month;
	public $day;
	public $author;
	public $exclude;
	public $include;
	public $search_term;

	/* Internal params */
	public $show_hidden;

	/* Lazy load params */
	public $offset;
	public $user_search_term;
	public $search_filters = [];

	/**
	 * @var array The default table parameters
	 */
	private static $default_args = [
		'columns'                   => 'image,title,excerpt,categories,author,date', // allowed: id, title, content, excerpt, date, categories (or category), tags, author, status, image, tax:<taxonomy_name>, cf:<custom_field>
		'widths'                    => '',
		'auto_width'                => true,
		'priorities'                => '',
		'column_breakpoints'        => '',
		'responsive_control'        => 'inline', // inline or column
		'responsive_display'        => 'child_row', // child_row, child_row_visible, or modal
		'wrap'                      => true,
		'show_footer'               => false,
		'search_on_click'           => true,
		'filters'                   => false,
		'scroll_offset'             => 15,
		'content_length'            => 15,
		'excerpt_length'            => -1,
		'links'                     => 'title,categories,tags,terms,author', // set to all or none, or any combination of id, title, terms, tags, categories, author, image
		'lazy_load'                 => false,
		'cache'                     => false,
		'image_size'                => '70x70',
		'lightbox'                  => true,
		'shortcodes'                => false,
		'date_format'               => '',
		'date_columns'              => '',
		'no_posts_message'          => '',
		'no_posts_filtered_message' => '',
		'paging_type'               => 'numbers',
		'page_length'               => 'bottom',
		'search_box'                => 'top',
		'totals'                    => 'bottom',
		'pagination'                => 'bottom',
		'reset_button'              => true,
		'button_text'               => 'View',
		'rows_per_page'             => 25,
		'post_limit'                => 500,
		'sort_by'                   => 'date',
		'sort_order'                => '', // no default set - see parse_args()
		'post_type'                 => 'post',
		'status'                    => 'publish',
		'category'                  => '', // list of slugs or IDs
		'exclude_category'          => '', // list of slugs or IDs
		'tag'                       => '', // list of slugs or IDs
		'term'                      => '', // list of terms of the form <taxonomy>:<term>
		'exclude_term'              => '', // list of terms of the form <taxonomy>:<term>
		'numeric_terms'             => false, // set to true if using categories, tags or terms with numeric slugs
		'cf'                        => '', // list of custom fields of the form <field_key>:<field_value>
		'year'                      => '',
		'month'                     => '',
		'day'                       => '',
		'author'                    => '', // list of author IDs
		'exclude'                   => '', // list of post IDs
		'include'                   => '', // list of post IDs
		'search_term'               => '',
		'show_hidden'               => false
	];

	public function __construct( array $args = [] ) {
		$this->set_args( $args );
	}

	public function get_args() {
		return $this->args;
	}

	public function set_args( array $args ) {
		// Lazy load args need to be merged in
		$hidden = [
			'offset'           => $this->offset,
			'user_search_term' => $this->user_search_term,
			'search_filters'   => $this->search_filters
		];

		// Update args
		$this->args = array_merge( $hidden, $this->args, $args );

		// Parse/validate args & update properties
		$this->parse_args( $this->args );
	}

	/**
	 * @deprecated 2.3.2 Use Barn2\Plugin\Posts_Table_Pro\Table_Args::get_user_defaults() instead
	 */
	public static function get_defaults() {
		_deprecated_function( __METHOD__, '2.3.2', self::class . '::get_user_defaults' );
		return self::get_user_defaults();
	}

	public static function get_table_defaults() {
		return apply_filters( 'posts_table_table_default_args', self::$default_args );
	}

	public static function get_user_defaults() {
		return apply_filters( 'posts_table_user_default_args', Options::get_shortcode_options( self::get_table_defaults() ) );
	}

	private function parse_args( array $args ) {
		$defaults = self::get_user_defaults();

		// Merge in defaults so we know all args have been set.
		$args = wp_parse_args( $args, $defaults );

		// Setup validation callbacks
		$sanitize_list = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_list_arg' ]
		];

		$sanitize_numeric_list = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_numeric_list_arg' ]
		];

		$sanitize_string_array = [
			'filter' => FILTER_SANITIZE_STRING,
			'flags'  => FILTER_REQUIRE_ARRAY
		];

		$sanitize_string_or_bool = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_string_or_bool_arg' ]
		];

		$validation = apply_filters(
			'posts_table_args_validation',
			[
				'columns'                   => is_array( $args['columns'] ) ? $sanitize_string_array : FILTER_SANITIZE_STRING,
				'widths'                    => $sanitize_list,
				'auto_width'                => FILTER_VALIDATE_BOOLEAN,
				'priorities'                => $sanitize_numeric_list,
				'column_breakpoints'        => $sanitize_list,
				'responsive_control'        => FILTER_SANITIZE_STRING,
				'responsive_display'        => FILTER_SANITIZE_STRING,
				'wrap'                      => FILTER_VALIDATE_BOOLEAN,
				'show_footer'               => FILTER_VALIDATE_BOOLEAN,
				'search_on_click'           => FILTER_VALIDATE_BOOLEAN,
				'filters'                   => is_array( $args['filters'] ) ? $sanitize_string_array : $sanitize_string_or_bool,
				'scroll_offset'             => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default' => $defaults['scroll_offset']
					]
				],
				'content_length'            => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['content_length'],
						'min_range' => -1
					]
				],
				'excerpt_length'            => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['excerpt_length'],
						'min_range' => -1
					]
				],
				'links'                     => $sanitize_string_or_bool,
				'lazy_load'                 => FILTER_VALIDATE_BOOLEAN,
				'cache'                     => FILTER_VALIDATE_BOOLEAN,
				'image_size'                => $sanitize_list,
				'lightbox'                  => FILTER_VALIDATE_BOOLEAN,
				'shortcodes'                => FILTER_VALIDATE_BOOLEAN,
				'date_format'               => FILTER_SANITIZE_STRING,
				'date_columns'              => $sanitize_list,
				'no_posts_message'          => FILTER_SANITIZE_STRING,
				'no_posts_filtered_message' => FILTER_SANITIZE_STRING,
				'paging_type'               => FILTER_SANITIZE_STRING,
				'page_length'               => $sanitize_string_or_bool,
				'search_box'                => $sanitize_string_or_bool,
				'totals'                    => $sanitize_string_or_bool,
				'pagination'                => $sanitize_string_or_bool,
				'reset_button'              => FILTER_VALIDATE_BOOLEAN,
				'button_text'               => FILTER_SANITIZE_STRING,
				'rows_per_page'             => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['rows_per_page'],
						'min_range' => -1
					]
				],
				'post_limit'                => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['post_limit'],
						'min_range' => -1,
						'max_range' => 5000,
					]
				],
				'sort_by'                   => FILTER_SANITIZE_STRING,
				'sort_order'                => FILTER_SANITIZE_STRING,
				'post_type'                 => $sanitize_list,
				'status'                    => $sanitize_list,
				'category'                  => $sanitize_list,
				'exclude_category'          => $sanitize_list,
				'tag'                       => $sanitize_list,
				'term'                      => $sanitize_list,
				'exclude_term'              => $sanitize_list,
				'numeric_terms'             => FILTER_VALIDATE_BOOLEAN,
				'cf'                        => FILTER_SANITIZE_STRING,
				'year'                      => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['year'],
						'min_range' => 1
					]
				],
				'month'                     => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['month'],
						'min_range' => 1,
						'max_range' => 12
					]
				],
				'day'                       => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['day'],
						'min_range' => 1,
						'max_range' => 31
					]
				],
				'author'                    => $sanitize_numeric_list,
				'exclude'                   => $sanitize_numeric_list,
				'include'                   => $sanitize_numeric_list,
				'search_term'               => [
					'filter' => FILTER_SANITIZE_STRING,
					'flags'  => FILTER_FLAG_NO_ENCODE_QUOTES
				],
				// Internal params
				'show_hidden'               => FILTER_VALIDATE_BOOLEAN,
				// Lazy load params
				'offset'                    => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => 0,
						'min_range' => 0,
					]
				],
				'user_search_term'          => [
					'filter' => FILTER_SANITIZE_STRING,
					'flags'  => FILTER_FLAG_NO_ENCODE_QUOTES
				],
				'search_filters'            => $sanitize_string_array
			],
			$args
		);

		// Sanitize/validate all args
		$args = filter_var_array( $args, $validation );

		// Set custom object properties from consuming plugins (e.g. DLP)
		$custom_properties = apply_filters( 'posts_table_args_custom_object_properties', [] );
		$this->set_custom_properties( $custom_properties );

		// Set object properties from args
		Util::set_object_vars( $this, $args );

		// Fill in any blank properties
		foreach ( [ 'columns', 'post_type', 'status', 'image_size', 'sort_by', 'links' ] as $arg ) {
			if ( empty( $this->$arg ) ) {
				$this->$arg = $defaults[ $arg ];
			}
		}

		// Make sure boolean args are definitely booleans - sometimes filter_var_array doesn't convert them properly
		foreach ( array_filter( $validation, [ $this, 'array_filter_validate_boolean' ] ) as $arg => $val ) {
			$this->$arg = ( $this->$arg === true || $this->$arg === 'true' ) ? true : false;
		}

		// Convert some list-based args to arrays - columns, filters, links, category, tag, term, exclude_term, cf and post_type are handled separately.
		foreach ( [ 'columns', 'widths', 'priorities', 'column_breakpoints', 'date_columns', 'status', 'exclude', 'include', 'exclude_category' ] as $arg ) {
			$this->$arg = Util::string_list_to_array( $this->$arg );
		}

		// Columns, headings and filters.
		$this->parse_columns( $this->columns );
		$this->parse_filters( $this->filters, $this->columns );

		// Widths and priorities
		if ( $this->widths ) {
			$this->widths = Util::array_pad_and_slice( $this->widths, count( $this->columns ), 'auto' );
		}

		if ( $this->priorities ) {
			$this->priorities = Util::array_pad_and_slice( $this->priorities, count( $this->columns ), 'default' );
		}

		// Responsive stuff
		if ( ! in_array( $this->responsive_control, [ 'inline', 'column' ] ) ) {
			$this->responsive_control = $defaults['responsive_control'];
		}

		if ( ! in_array( $this->responsive_display, [ 'child_row', 'child_row_visible', 'modal' ] ) ) {
			$this->responsive_display = $defaults['responsive_display'];
		}

		if ( $this->column_breakpoints ) {
			$this->column_breakpoints = Util::array_pad_and_slice( $this->column_breakpoints, count( $this->columns ), 'default' );
		}

		// Display options (page length, etc)
		foreach ( [ 'page_length', 'search_box', 'totals', 'pagination' ] as $display_option ) {
			if ( ! in_array( $this->$display_option, [ 'top', 'bottom', 'both', false ], true ) ) {
				$this->$display_option = $defaults[ $display_option ];
			}
		}

		// Links - used to control whether certain data items are links or plain text
		$this->links = is_string( $this->links ) ? strtolower( $this->links ) : $this->links;

		if ( true === $this->links || 'all' === $this->links ) {
			$this->links = [ 'all' ];
		} elseif ( false === $this->links || 'none' === $this->links ) {
			$this->links = [];
		} else {
			$linkable_columns = apply_filters( 'posts_table_linkable_columns', [ 'id', 'author', 'terms', 'tags', 'categories', 'title', 'image' ] );
			$this->links      = array_intersect( explode( ',', $this->links ), $linkable_columns );
		}

		// Paging type
		if ( ! in_array( $this->paging_type, [ 'numbers', 'simple', 'simple_numbers', 'full', 'full_numbers' ] ) ) {
			$this->paging_type = $defaults['paging_type'];
		}

		// Image size
		$size_arr           = explode( 'x', str_replace( ',', 'x', $this->image_size ) );
		$size_numeric_count = count( array_filter( $size_arr, 'is_numeric' ) );

		if ( 1 === $size_numeric_count ) {
			// One number, so use for both width and height
			$this->image_size = [ $size_arr[0], $size_arr[0] ];
		} elseif ( 2 === $size_numeric_count ) {
			// Width and height specified
			$this->image_size = $size_arr;
		} // otherwise assume it's a text-based image size, e.g. 'thumbnail'

		$this->set_image_column_width();

		// Validate date columns
		if ( $this->date_columns && is_array( $this->date_columns ) ) {
			foreach ( $this->date_columns as $key => $column ) {
				// Date column must be present in table, otherwise remove it
				if ( ! in_array( $column, $this->columns ) ) {
					unset( $this->date_columns[ $key ] );
				}
			}

			$this->date_columns = array_values( $this->date_columns ); // re-key
		} else {
			$this->date_columns = [];
		}

		// Sort by - force the use of column name if sorting by modified date.
		if ( 'modified' === $this->sort_by ) {
			$this->sort_by = 'date_modified';
		}

		// Sort order.
		$this->sort_order = strtolower( $this->sort_order );

		if ( ! in_array( $this->sort_order, [ 'asc', 'desc' ] ) ) {
			// Default to descending order for date sorting, ascending for everything else.
			$this->sort_order = in_array( $this->sort_by, array_merge( [ 'date', 'date_modified' ], $this->date_columns ) ) ? 'desc' : 'asc';
		}

		// Search term.
		if ( ! Util::is_valid_search_term( $this->search_term ) ) {
			$this->search_term = '';
		}

		// Content length, exceprt length, rows per page and post limit can be positive integer or -1.
		foreach ( [ 'content_length', 'excerpt_length', 'rows_per_page', 'post_limit' ] as $arg ) {
			// Sanity check in case filter set an invalid value.
			if ( ! is_int( $this->$arg ) || $this->$arg < -1 ) {
				$this->$arg = $defaults[ $arg ];
			}

			if ( 0 === $this->$arg ) {
				$this->$arg = -1;
			}
		}

		// Filter post limit
		$this->post_limit = apply_filters( 'posts_table_max_posts_limit', $this->post_limit, $this );

		// Ignore post limit if lazy loaded and the default post limit is used.
		if ( $this->lazy_load && (int) $defaults['post_limit'] === $this->post_limit ) {
			$this->post_limit = -1;
		}

		if ( 'attachment' === $this->post_type ) {
			// Attachments have a status of 'inherit' so we need to set status otherwise no results will be returned
			$this->status = [ 'inherit' ];
		}

		// Validate post type
		if ( 'any' !== $this->post_type ) {
			$this->post_type = array_filter( Util::string_list_to_array( $this->post_type ), 'post_type_exists' );

			// Nav menu items not allowed.
			$this->post_type = array_diff( $this->post_type, [ 'nav_menu_item' ] );

			if ( empty( $this->post_type ) ) {
				$this->post_type = Util::string_list_to_array( $defaults['post_type'] );
			}
		}

		// Ensure private posts are hidden if the current user doesn't have the required capability.
		if ( in_array( 'private', $this->status ) ) {
			$private_allowed = true;

			if ( 'any' === $this->post_type && ! current_user_can( 'read_private_posts' ) ) {
				// Bit of a hack when using 'any' post type - just check read_private_posts cap.
				$private_allowed = false;
			} else {
				foreach ( (array) $this->post_type as $post_type ) {
					$cap = false;

					if ( $post_type_object = get_post_type_object( $post_type ) ) {
						$cap = $post_type_object->cap->read_private_posts;
					}
					if ( ! $cap ) {
						$cap = 'read_private_' . $post_type . 's';
					}

					if ( ! current_user_can( $cap ) ) {
						$private_allowed = false;
						break;
					}
				}
			}

			if ( ! $private_allowed ) {
				$this->status = array_diff( $this->status, [ 'private' ] );

				if ( empty( $this->status ) ) {
					$this->status = Util::string_list_to_array( $defaults['status'] );
				}
			}
		}

		// Prevent user error where category is used instead of term when specifying a custom taxonomy.
		if ( false !== strpos( $this->category, ':' ) && empty( $this->term ) ) {
			$this->term     = $this->category;
			$this->category = '';
		}

		// Ignore post limit if lazy loading and the default product limit is used.
		if ( $this->lazy_load && $defaults['post_limit'] === $this->post_limit ) {
			$this->post_limit = -1;
		}

		// If enabling shortcodes, display the full content
		if ( $this->shortcodes ) {
			$this->content_length = -1;
		}

		// If auto width disabled, must use inline responsive control otherwise control column is always shown
		if ( ! $this->auto_width ) {
			$this->responsive_control = 'inline';
		}

		do_action( 'posts_table_parse_args', $this );
	}

	/**
	 * Validate and parse the columns and headings to use in posts table.
	 *
	 * @param string[] $columns A columns array
	 */
	private function parse_columns( $columns ) {
		$parsed = self::parse_columns_arg( $columns );

		if ( empty( $parsed['columns'] ) ) {
			$defaults = self::get_user_defaults();
			$parsed   = self::parse_columns_arg( $defaults['columns'] );
		}

		$this->columns  = $parsed['columns'];
		$this->headings = $parsed['headings'];
	}

	private static function parse_columns_arg( $columns ) {
		$parsed   = [];
		$headings = [];

		if ( ! is_array( $columns ) ) {
			$columns = Util::string_list_to_array( $columns );
		}

		foreach ( $columns as $column ) {
			$prefix = sanitize_key( strtok( $column, ':' ) );
			$col    = false;

			if ( in_array( $prefix, [ 'cf', 'tax' ] ) ) {
				// Custom field or taxonomy.
				$suffix = trim( strtok( ':' ) );

				if ( ! $suffix ) {
					continue; // no custom field or taxonomy specified
				} elseif ( 'tax' === $prefix && ! taxonomy_exists( $suffix ) ) {
					continue; // invalid taxonomy
				}

				$col = $prefix . ':' . $suffix;
			} else {
				// Standard or custom column.
				$col = $prefix;
			}

			// Only add column if valid and not added already.
			if ( $col && ! in_array( $col, $parsed ) ) {
				$parsed[]   = $col;
				$headings[] = self::sanitize_heading( strtok( '' ) ); // fetch rest of heading
			}
		}

		return [
			'columns'  => $parsed,
			'headings' => $headings
		];
	}

	private function parse_filters( $filters, $columns ) {
		$parsed = self::parse_filters_arg( $filters, $columns );

		$this->filters         = $parsed['filters'] ? $parsed['filters'] : false;
		$this->filter_headings = $parsed['headings'];
	}

	private static function parse_filters_arg( $filters, $columns ) {
		$parsed         = [];
		$headings       = [];
		$filter_columns = $filters;

		if ( true === $filter_columns ) {
			$filter_columns = $columns;
		} elseif ( empty( $filter_columns ) ) {
			$filter_columns = [];
		}

		if ( ! is_array( $filter_columns ) ) {
			$filter_columns = Util::string_list_to_array( $filter_columns );
		}

		foreach ( $filter_columns as $filter ) {
			$f      = false;
			$prefix = strtok( $filter, ':' );

			$filterable_columns = apply_filters( 'posts_table_filterable_columns', [ 'categories', 'tags' ] );
			if ( in_array( $prefix, $filterable_columns ) ) {
				// Category or tags filter.
				$f = $prefix;
			} elseif ( 'tax' === $prefix ) {
				// Custom taxonomy filter.
				$tax = trim( strtok( ':' ) );

				if ( taxonomy_exists( $tax ) ) {
					$f = 'tax:' . $tax;
				}
			}

			if ( $f && ! in_array( $f, $parsed ) ) {
				$parsed[]   = $f;
				$headings[] = self::sanitize_heading( strtok( '' ) ); // fetch rest of heading
			}
		}

		return [
			'filters'  => $parsed,
			'headings' => $headings
		];
	}

	private function set_image_column_width() {
		if ( false === ( $image_col = array_search( 'image', $this->columns ) ) ) {
			return;
		}

		if ( $this->widths && isset( $this->widths[ $image_col ] ) && 'auto' !== $this->widths[ $image_col ] ) {
			return;
		}

		if ( $image_col_width = Util::get_image_size_width( $this->image_size ) ) {
			if ( ! $this->widths ) {
				$this->widths = array_fill( 0, count( $this->columns ), 'auto' );
			}
			$this->widths[ $image_col ] = $image_col_width . 'px';
		}
	}

	private function array_filter_validate_boolean( $var ) {
		return $var === FILTER_VALIDATE_BOOLEAN;
	}

	private function set_custom_properties( $custom_properties ) {
		if ( ! is_array( $custom_properties ) || empty( $custom_properties ) ) {
			return;
		}

		foreach ( $custom_properties as $property ) {
			$this->{$property} = '';
		}
	}

	private static function sanitize_heading( $heading ) {
		return esc_html( $heading );
	}

}
