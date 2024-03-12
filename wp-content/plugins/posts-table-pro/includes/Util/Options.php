<?php
namespace Barn2\Plugin\Posts_Table_Pro\Util;

/**
 * Functions for handling the Posts Table Pro plugin options.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Options {

	const SHORTCODE_OPTION_KEY = 'ptp_shortcode_defaults';
	const MISC_OPTION_KEY      = 'ptp_misc_settings';

	public static function get_shortcode_options( array $defaults = [] ) {
		return self::sanitize_shortcode_options( self::get_option( self::SHORTCODE_OPTION_KEY, $defaults ), $defaults );
	}

	public static function get_additional_options() {
		$defaults = [
			'cache_expiry' => 6,
			'design'       => 'default'
		];

		return self::get_option( self::MISC_OPTION_KEY, $defaults );
	}

	public static function get_cache_expiration_length() {
		$options = self::get_additional_options();

		return filter_var( $options['cache_expiry'], FILTER_VALIDATE_INT, [
			'options' => [ 'default' => 6, 'min_range' => 1 ]
			] );
	}

	private static function get_option( $option, $default ) {
		$value = get_option( $option, $default );

		if ( empty( $value ) || ( is_array( $default ) && ! is_array( $value ) ) ) {
			$value = $default;
		}

		if ( is_array( $value ) && is_array( $default ) ) {
			$value = array_merge( $default, $value );
		}

		return $value;
	}

	private static function sanitize_shortcode_options( array $options, array $defaults = [] ) {
		if ( empty( $options ) ) {
			return $defaults;
		}

		$options = array_merge( $defaults, $options );

		// Check free text options are not empty.
		foreach ( [ 'columns', 'image_size', 'links' ] as $arg ) {
			if ( empty( $options[$arg] ) && ! empty( $defaults[$arg] ) ) {
				$options[$arg] = $defaults[$arg];
			}
		}

		// Sanitize custom filters option.
		if ( 'custom' === $options['filters'] ) {
			$options['filters'] = ! empty( $options['filters_custom'] ) ? $options['filters_custom'] : $defaults['filters'];
		}

		unset( $options['filters_custom'] );

		// Sanitize sort by option.
		if ( 'custom' === $options['sort_by'] ) {
			$options['sort_by'] = ! empty( $options['sort_by_custom'] ) ? $options['sort_by_custom'] : $defaults['sort_by'];
		}

		unset( $options['sort_by_custom'] );

		// Convert 'true' or 'false' strings to booleans.
		$options = array_map( [ Util::class, 'maybe_parse_bool' ], $options );

		return $options;
	}

}
