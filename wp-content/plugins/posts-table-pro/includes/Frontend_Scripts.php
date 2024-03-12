<?php

namespace Barn2\Plugin\Posts_Table_Pro;

use Barn2\Plugin\Posts_Table_Pro\Util\Options;
use Barn2\Plugin\Posts_Table_Pro\Util\Util;
use Barn2\PTP_Lib\Conditional;
use Barn2\PTP_Lib\CSS_Util;
use Barn2\PTP_Lib\Plugin\Plugin;
use Barn2\PTP_Lib\Registerable;
use Barn2\PTP_Lib\Service;
use Barn2\PTP_Lib\Util as Lib_Util;

/**
 * Responsible for registering the front-end styles and scripts in Posts Table Pro.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Frontend_Scripts implements Service, Registerable, Conditional {

	const SCRIPT_HANDLE      = 'posts-table-pro';
	const DATATABLES_VERSION = '1.11.3';

	private $plugin;
	private $script_version;

	public function __construct( Plugin $plugin ) {
		$this->plugin         = $plugin;
		$this->script_version = $this->plugin->get_version();
	}

	public function is_required() {
		return Lib_Util::is_front_end();
	}

	public function register() {
		// Register front-end styles and scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_head_scripts' ], 20 );

		add_filter( 'posts_table_language_defaults', [ $this, 'format_totals_strings' ], 20 );
	}

	public function register_styles() {
		wp_register_style( 'jquery-datatables-ptp', Util::get_asset_url( 'js/datatables/datatables.min.css' ), [], self::DATATABLES_VERSION );
		wp_register_style( 'photoswipe', Util::get_asset_url( 'js/photoswipe/photoswipe.min.css' ), [], '4.1.3' );
		wp_register_style( 'photoswipe-default-skin', Util::get_asset_url( 'js/photoswipe/default-skin/default-skin.min.css' ), [ 'photoswipe' ], '4.1.3' );
		wp_register_style( 'select2-ptp', Util::get_asset_url( 'js/select2/select2.min.css' ), [], '4.0.13' );

		wp_register_style( self::SCRIPT_HANDLE, Util::get_asset_url( 'css/styles.min.css' ), [ 'jquery-datatables-ptp', 'select2-ptp' ], $this->script_version );

		// Add RTL data - we need suffix to correctly format RTL stylesheet when minified.
		wp_style_add_data( self::SCRIPT_HANDLE, 'rtl', 'replace' );
		wp_style_add_data( self::SCRIPT_HANDLE, 'suffix', '.min' );

		$misc_options = Options::get_additional_options();

		// If using custom style, build CSS and add inline style data.
		if ( ! empty( $misc_options['design'] ) && 'custom' === $misc_options['design'] ) {
			wp_add_inline_style( self::SCRIPT_HANDLE, self::build_custom_styles( $misc_options, Util::TABLE_CLASS ) );
		}

		// Header styles - we just a dummy handle as we only need inline styles in <head>.
		wp_register_style( 'posts-table-pro-head', false );

		// Ensure tables don't 'flicker' on page load - visibility is set by JS when table initialised.
		wp_add_inline_style( 'posts-table-pro-head', '.posts-data-table { visibility: hidden; }' );
	}

	public function register_scripts() {
		$suffix = Lib_Util::get_script_suffix();

		wp_register_script( 'jquery-datatables-ptp', Util::get_asset_url( "js/datatables/datatables{$suffix}.js" ), [ 'jquery' ], self::DATATABLES_VERSION, true );
		wp_register_script( 'jquery-blockui', Util::get_asset_url( 'js/jquery-blockui/jquery.blockUI.min.js' ), [ 'jquery' ], '2.70.0', true );
		wp_register_script( 'photoswipe', Util::get_asset_url( 'js/photoswipe/photoswipe.min.js' ), [], '4.1.3', true );
		wp_register_script( 'photoswipe-ui-default', Util::get_asset_url( 'js/photoswipe/photoswipe-ui-default.min.js' ), [ 'photoswipe' ], '4.1.3', true );
		wp_register_script( 'select2-ptp', Util::get_asset_url( 'js/select2/select2.full.min.js' ), [ 'jquery' ], '4.0.13', true );

		if ( apply_filters( 'posts_table_use_fitvids', true ) ) {
			wp_register_script( 'fitvids', Util::get_asset_url( 'js/jquery-fitvids/jquery.fitvids.min.js' ), [ 'jquery' ], '1.1', true );
		}

		wp_register_script(
			self::SCRIPT_HANDLE,
			Util::get_asset_url( "js/posts-table-pro{$suffix}.js" ),
			[ 'jquery', 'jquery-datatables-ptp', 'jquery-blockui', 'select2-ptp' ],
			$this->script_version,
			true
		);

		$script_params = [
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'     => wp_create_nonce( self::SCRIPT_HANDLE ),
			'ajax_action'    => 'ptp_load_posts',
			'table_class'    => esc_attr( Util::get_table_class() ),
			'enable_select2' => apply_filters( 'posts_table_enable_select2', true ),
			'language'       => apply_filters(
				'posts_table_language_defaults',
				[
					/* translators: _TOTAL_: placeholder for total number of posts; _POSTS_: plural form of the post type (e.g. "posts") */
					'info'         => __( '_TOTAL_ _POSTS_', 'posts-table-pro' ),
					/* translators: _POSTS_: plural form of the post type (e.g. "posts") */
					'infoEmpty'    => __( '0 _POSTS_', 'posts-table-pro' ),
					'infoFiltered' => __( '(_MAX_ in total)', 'posts-table-pro' ),
					'lengthMenu'   => __( 'Show _MENU_ per page', 'posts-table-pro' ),
					/* translators: _POSTS_: plural form of the post type (e.g. "posts") */
					'emptyTable'   => __( 'No matching _POSTS_.', 'posts-table-pro' ),
					/* translators: _POSTS_: plural form of the post type (e.g. "posts") */
					'zeroRecords'  => __( 'No matching _POSTS_.', 'posts-table-pro' ),
					'search'       => apply_filters( 'posts_table_search_label', __( 'Search:', 'posts-table-pro' ) ),
					'paginate'     => [
						'first'    => __( 'First', 'posts-table-pro' ),
						'last'     => __( 'Last', 'posts-table-pro' ),
						'next'     => __( 'Next', 'posts-table-pro' ),
						'previous' => __( 'Previous', 'posts-table-pro' ),
					],
					'thousands'    => _x( ',', 'thousands separator', 'posts-table-pro' ),
					'decimal'      => _x( '.', 'decimal mark', 'posts-table-pro' ),
					'aria'         => [
						/* translators: ARIA text for sorting column in ascending order */
						'sortAscending'  => __( ': activate to sort column ascending', 'posts-table-pro' ),
						/* translators: ARIA text for sorting column in descending order */
						'sortDescending' => __( ': activate to sort column descending', 'posts-table-pro' ),
					],
					/* @deprecated 2.3 posts_table_filter_label replaced by posts_table_search_filter_label. */
					'filterBy'     => apply_filters( 'posts_table_search_filter_label', apply_filters( 'posts_table_filter_label', '' ) ),
					'resetButton'  => apply_filters( 'posts_table_reset_button', __( 'Reset', 'posts-table-pro' ) ),
					'totalsSingle' => '_POST_',
					'totalsPlural' => '_POSTS_'
				]
			),
		];

		wp_add_inline_script(
			self::SCRIPT_HANDLE,
			sprintf( 'var posts_table_params = %s;', wp_json_encode( apply_filters( 'posts_table_pro_script_params', $script_params ) ) ),
			'before'
		);

	}

	public function load_head_scripts() {
		wp_enqueue_style( 'posts-table-pro-head' );
	}

	public function format_totals_strings( array $strings ) {
		// If _POST_ and _POSTS_ placeholders are found, we replace with the post type name, defaulting to 'post' and 'posts'.
		// If a custom single/plural has been set by 3rd party code, we use that instead.
		$single = '_POST_' !== $strings['totalsSingle'] ? $strings['totalsSingle'] : _x( 'post', 'posts total text - singular', 'posts-table-pro' );
		$plural = '_POSTS_' !== $strings['totalsPlural'] ? $strings['totalsPlural'] : _x( 'posts', 'posts total text - plural', 'posts-table-pro' );

		if ( apply_filters( 'posts_table_use_post_type_labels_in_total', true ) ) {
			$options       = Options::get_shortcode_options();
			$post_type     = ! empty( $options['post_type'] ) ? $options['post_type'] : 'post';
			$post_type_obj = get_post_type_object( $post_type );

			if ( $post_type_obj && ! empty( $post_type_obj->labels ) ) {
				$single = strtolower( $post_type_obj->labels->singular_name );
				$plural = strtolower( $post_type_obj->labels->name );
			}
		}

		// Remove any arrays as we can't search/replace on them using str_replace.
		$strings_to_replace = array_filter( $strings, 'is_scalar' );

		// Merge back any arrays we removed.
		$strings = array_merge(
			$strings,
			str_replace(
				[ '_POST_', '_POSTS_' ],
				[ $single, $plural ],
				$strings_to_replace
			)
		);

		return $strings;
	}

	public static function load_table_scripts( Table_Args $args = null ) {
		if ( ! apply_filters( 'posts_table_load_frontend_scripts', true ) ) {
			return;
		}

		wp_enqueue_style( self::SCRIPT_HANDLE );
		wp_enqueue_script( self::SCRIPT_HANDLE );

		if ( $args ) {
			if ( $args->shortcodes ) {
				// Add fitVids.js for responsive video if we're displaying shortcodes.
				if ( apply_filters( 'posts_table_use_fitvids', true ) ) {
					wp_enqueue_script( 'fitvids' );
				}

				// Queue media element and playlist scripts/styles.
				wp_enqueue_style( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-playlist' );

				add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
			}

			// Enqueue Photoswipe for image lightbox.
			if ( $args->lightbox ) {
				wp_enqueue_style( 'photoswipe-default-skin' );
				wp_enqueue_script( 'photoswipe-ui-default' );

				add_action( 'wp_footer', [ self::class, 'load_photoswipe_template' ] );
			}
		}
	}

	public static function load_photoswipe_template() {
		Util::include_template( 'photoswipe.php' );
	}

	private function build_custom_styles( $options, $table_class ) {
		$styles         = [];
		$class_selector = '.' . $table_class;

		// Ensure all keys for table design options are set.
		$options = array_merge(
			array_fill_keys( [ 'external_border', 'header_border', 'body_border', 'header_bg', 'body_bg', 'body_bg_alt', 'header_text', 'body_text', 'table_spacing' ], '' ),
			$options
		);

		// External border.
		if ( $this->valid_color_size_setting( $options['external_border'] ) ) {
			$styles[] = [
				'selector' => $class_selector,
				'css'      => CSS_Util::build_border_style( $options['external_border'], 'all', true )
			];
		}

		// Header border.
		if ( $this->valid_color_size_setting( $options['header_border'] ) ) {
			$styles[] = [
				'selector' => $class_selector . ' thead th',
				'css'      => CSS_Util::build_border_style( $options['header_border'], 'bottom', true )
			];

			$styles[] = [
				'selector' => $class_selector . ' tfoot th',
				'css'      => CSS_Util::build_border_style( $options['header_border'], 'top', true )
			];
		}

		// Body border.
		if ( $this->valid_color_size_setting( $options['body_border'] ) ) {
			$styles[] = [
				'selector' => $class_selector . ' tbody td',
				'css'      => CSS_Util::build_border_style( $options['body_border'], [ 'left', 'top' ], true )
			];

			// Remove border-top for the first row.
			$styles[] = [
				'selector' => sprintf( '%s tbody tr:first-child td', $class_selector ),
				'css'      => 'border-top: none !important;'
			];

			// Remove border-left for the first column and the first column after the control column (if using).
			$styles[] = [
				'selector' => sprintf( '%1$s td:first-child, %1$s td.control + td', $class_selector ),
				'css'      => 'border-left: none !important;'
			];

			// Ensure child row borders match main table cells.
			$styles[] = [
				'selector' => sprintf( 'table.%s > tbody > tr.child ul.dtr-details > li', $table_class ),
				'css'      => CSS_Util::build_border_style( $options['body_border'], 'bottom' )
			];
		}

		// Header background color.
		if ( ! empty( $options['header_bg'] ) ) {
			$styles[] = [
				'selector' => sprintf( '%1$s thead th, %1$s tfoot th', $class_selector ),
				'css'      => CSS_Util::build_background_style( $options['header_bg'], true )
			];
		}

		// Body background color.
		if ( ! empty( $options['body_bg'] ) ) {
			$styles[] = [
				'selector' => $class_selector . ' tbody td',
				'css'      => CSS_Util::build_background_style( $options['body_bg'], true )
			];
		}

		// Alternating background color.
		if ( ! empty( $options['body_bg_alt'] ) ) {
			$styles[] = [
				'selector' => $class_selector . ' tbody tr:nth-child(2n) td',
				'css'      => CSS_Util::build_background_style( $options['body_bg_alt'], true )
			];
		}

		// Header text.
		if ( $this->valid_color_size_setting( $options['header_text'] ) ) {
			$styles[] = [
				'selector' => sprintf( '%1$s thead th, %1$s tfoot th', $class_selector ),
				'css'      => CSS_Util::build_font_style( $options['header_text'], true )
			];
		}

		// Body text
		if ( $this->valid_color_size_setting( $options['body_text'] ) ) {
			$styles[] = [
				'selector' => $class_selector . ' tbody td',
				'css'      => CSS_Util::build_font_style( $options['body_text'], true )
			];
		}

		// Spacing
		if ( 'default' !== $options['table_spacing'] ) {
			$padding = null;

			switch ( $options['table_spacing'] ) {
				case 'compact':
					$padding = 5;
					break;
				case 'normal':
					$padding = 8;
					break;
				case 'spacious':
					$padding = 12;
					break;
			}

			if ( $padding ) {
				$left_right_padding = $padding + 2;

				$styles[] = [
					'selector' => sprintf( 'table.%s tbody td', $table_class ),
					'css'      => sprintf( 'padding: %upx %upx;', $padding, $left_right_padding )
				];

				$header_padding = $padding + 2;

				$styles[] = [
					'selector' => sprintf( 'table.%1$s thead th, table.%1$s tfoot th', $table_class ),
					'css'      => sprintf( 'padding: %1$upx 18px %1$upx %2$upx;', $header_padding, $left_right_padding )
				];

				$styles[] = [
					'selector' => sprintf( '.rtl table.%1$s thead th, .rtl table.%1$s tfoot th', $table_class ),
					'css'      => sprintf( 'padding-left: 18px; padding-right: %upx;', $left_right_padding )
				];
			}
		}

		$result = array_reduce(
			$styles,
			function ( $carry, $style ) {
				if ( ! empty( $style['css'] ) ) {
					$carry .= sprintf( '%1$s { %2$s } ', $style['selector'], $style['css'] );
				}

				return $carry;
			},
			''
		);

		return $result;
	}

	private function valid_color_size_setting( $color_size ) {
		if ( ! is_array( $color_size ) ) {
			return false;
		}

		return ( isset( $color_size['size'] ) && is_numeric( $color_size['size'] ) ) || ! empty( $color_size['color'] );
	}

}
