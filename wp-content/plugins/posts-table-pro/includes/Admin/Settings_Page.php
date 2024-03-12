<?php
namespace Barn2\Plugin\Posts_Table_Pro\Admin;

use Barn2\Plugin\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Posts_Table_Pro\Util\Options,
	Barn2\Plugin\Posts_Table_Pro\Util\Util,
	Barn2\PTP_Lib\Registerable,
	Barn2\PTP_Lib\Service,
	Barn2\PTP_Lib\Conditional,
	Barn2\PTP_Lib\Plugin\Licensed_Plugin,
	Barn2\PTP_Lib\Admin\Settings_API_Helper,
	Barn2\PTP_Lib\Admin\Plugin_Promo,
	Barn2\PTP_Lib\Util as Lib_Util;

/**
 * This class handles our plugin settings page in the admin.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Page implements Service, Registerable, Conditional {

	const MENU_SLUG    = 'posts_table';
	const OPTION_GROUP = 'posts_table_pro';

	private $plugin;
	private $default_settings;
	private $services;

	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin           = $plugin;
		$this->default_settings = Table_Args::get_table_defaults();
		$this->services         = [
			new Settings_API_Helper( $this->plugin ),
			new Plugin_Promo( $this->plugin )
		];
	}

	public function is_required() {
		return Lib_Util::is_admin();
	}

	public function register() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );

		Lib_Util::register_services( $this->services );
	}

	public function add_settings_page() {
		add_options_page(
			__( 'Posts Table Pro Settings', 'posts-table-pro' ),
			__( 'Posts Table Pro', 'posts-table-pro' ),
			'manage_options',
			self::MENU_SLUG,
			[ $this, 'display_settings_page' ]
		);
	}

	public function register_settings() {
		register_setting( self::OPTION_GROUP, Options::SHORTCODE_OPTION_KEY, [
			'type'              => 'string', // array type not supported, so just use string
			'description'       => 'Posts Table Pro shortcode defaults',
			'sanitize_callback' => [ $this, 'sanitize_shortcode_settings' ]
		] );

		register_setting( self::OPTION_GROUP, Options::MISC_OPTION_KEY, [
			'type'              => 'string', // array type not supported, so just use string
			'description'       => 'Posts Table Pro miscellaneous settings',
			'sanitize_callback' => [ $this, 'sanitize_misc_settings' ]
		] );

		// Licence Key section.
		Settings_API_Helper::add_settings_section(
			'ptp_license_key', self::MENU_SLUG, '', [ $this, 'display_support_links' ],
			[
				$this->plugin->get_license_setting()->get_license_key_setting(),
				$this->plugin->get_license_setting()->get_license_override_setting()
			]
		);

		// Intro.
		Settings_API_Helper::add_settings_section(
			'ptp_intro', self::MENU_SLUG, __( 'Getting started', 'posts-table-pro' ), [ $this, 'display_intro_description' ], []
		);

		// Table content.
		Settings_API_Helper::add_settings_section(
			'ptp_shortcode_defaults', self::MENU_SLUG, __( 'Table content', 'posts-table-pro' ), false, $this->get_content_settings()
		);

		// Loading.
		Settings_API_Helper::add_settings_section(
			'ptp_post_loading', self::MENU_SLUG, __( 'Loading', 'posts-table-pro' ), false, $this->get_load_settings()
		);

		// Sorting.
		Settings_API_Helper::add_settings_section(
			'ptp_sorting', self::MENU_SLUG, __( 'Sorting', 'posts-table-pro' ), false, $this->get_sort_settings()
		);

		// Table controls.
		Settings_API_Helper::add_settings_section(
			'ptp_table_controls', self::MENU_SLUG, __( 'Controls', 'posts-table-pro' ), false, $this->get_controls_settings()
		);

		// Table design.
		Settings_API_Helper::add_settings_section(
			'ptp_design', self::MENU_SLUG, __( 'Design', 'posts-table-pro' ), [ $this, 'display_table_design_description' ], $this->get_design_settings()
		);

		// Advanced.
		Settings_API_Helper::add_settings_section(
			'ptp_advanced', self::MENU_SLUG, __( 'Advanced', 'posts-table-pro' ), false, $this->get_advanced_settings()
		);
	}

	public function display_settings_page() {
		?>
		<div class="wrap barn2-plugins-settings posts-table-settings">
			<?php do_action( 'barn2_before_plugin_settings', $this->plugin->get_id() ); ?>
			<div class="barn2-settings-inner">
				<h1><?php _e( 'Posts Table Pro Settings', 'posts-table-pro' ); ?></h1>
				<form action="options.php" method="post">
					<?php
					// Output the hidden form fields (_wpnonce, etc)
					settings_fields( self::OPTION_GROUP );

					// Output the sections and their settings
					do_settings_sections( self::MENU_SLUG );
					?>
					<p class="submit">
						<input name="Submit" type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'posts-table-pro' ); ?>" />
					</p>
				</form>
			</div>
			<?php do_action( 'barn2_after_plugin_settings', $this->plugin->get_id() ); ?>
		</div>
		<?php
	}

	public function display_support_links() {
		printf( '<p>%s | %s</p>',
			Lib_Util::format_link( $this->plugin->get_documentation_url(), __( 'Documentation', 'posts-table-pro' ), true ),
			Lib_Util::format_link( $this->plugin->get_support_url(), __( 'Support', 'posts-table-pro' ), true )
		);
	}

	public function display_intro_description() {
		?>
		<p style="margin-bottom:2.5em;"><?php
			printf(
				/* translators: %1$s: HTML for bold text, %2$s: end of bold text, %3$s: HTML for help link, %4$s: end of help link */
				__( 'To create a table, simply add the shortcode %1$s[posts_table]%2$s to any post or page. Then configure your tables using the settings below. '
					. 'You can override any of the settings for individual tables by adding options to the [posts_table] %3$sshortcode%4$s.', 'posts-table-pro' ),
				'<strong>',
				'</strong>',
				Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/posts-table-options/' ), true ),
				'</a>'
			);
			?></p>
		<?php
	}

	public function display_table_design_description() {
		?>
		<p><?php _e( 'Customize the design of the table.', 'posts-table-pro' ); ?></p>
		<?php
	}

	public function sanitize_shortcode_settings( $settings ) {
		$this->plugin->get_license_setting()->save_posted_license_key();

		// Check for missing settings.
		foreach ( [ 'columns', 'image_size', 'links', 'button_text' ] as $option ) {
			if ( empty( $settings[$option] ) ) {
				$settings[$option] = $this->default_settings[$option];
			}
		}

		// Check image size.
		if ( isset( $settings['image_size'] ) ) {
			$settings['image_size'] = preg_replace( '/[^\wx\-]/', '', $settings['image_size'] );
		}

		// Check ints.
		foreach ( [ 'rows_per_page', 'content_length', 'excerpt_length', 'post_limit' ] as $option ) {
			if ( ! isset( $settings[$option] ) ) {
				continue;
			}

			if ( false === ( $int_val = filter_var( $settings[$option], FILTER_VALIDATE_INT ) ) ) {
				$settings[$option] = $this->default_settings[$option];
			}

			// These settings can be a positive integer or -1 only.
			if ( 0 === $int_val || $int_val < -1 ) {
				$settings[$option] = -1;
			}
		}

		// Check bools.
		foreach ( [ 'lightbox', 'shortcodes', 'lazy_load', 'cache', 'reset_button' ] as $option ) {
			if ( ! isset( $settings[$option] ) ) {
				$settings[$option] = false;
			}

			$settings[$option] = filter_var( $settings[$option], FILTER_VALIDATE_BOOLEAN );
		}

		return $settings;
	}

	public function sanitize_misc_settings( $settings ) {
		if ( isset( $settings['cache_expiry'] ) ) {
			$settings['cache_expiry'] = filter_var( $settings['cache_expiry'], FILTER_VALIDATE_INT, [ 'options' => [ 'default' => 6 ] ] );
		}

		// Color options.
		foreach ( [ 'header_bg', 'body_bg', 'body_bg_alt' ] as $option ) {
			if ( ! empty( $settings[$option] ) && ! $this->check_hex_color( $settings[$option] ) ) {
				$settings[$option] = '';
			}
		}

		// Color-size options.
		foreach ( [ 'external_border', 'header_border', 'body_border', 'header_text', 'body_text' ] as $option ) {
			if ( ! empty( $settings[$option]['color'] && ! $this->check_hex_color( $settings[$option]['color'] ) ) ) {
				$settings[$option] = '';
			}

			if ( isset( $settings[$option]['size'] ) ) {
				$settings[$option]['size'] = filter_var( $settings[$option]['size'], FILTER_VALIDATE_INT );
			}
		}

		return $settings;
	}

	private function get_content_settings() {
		return [
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[post_type]',
				'title'   => __( 'Post type', 'posts-table-pro' ),
				'type'    => 'select',
				'desc'    => sprintf(
					__( 'Displays all published posts for this post type. To restrict posts by category, tag, custom field, etc. add the relevant %1$sshortcode option%2$s.', 'posts-table-pro' ),
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/ptp-include-exclude/' ), true ),
					'</a>'
				),
				'options' => $this->get_registered_post_types(),
				'default' => $this->default_settings['post_type'],
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[columns]',
				'title'   => __( 'Columns', 'posts-table-pro' ),
				'type'    => 'text',
				'desc'    => __( 'Enter the columns for your posts tables.', 'posts-table-pro' ) . $this->read_more( 'kb/posts-table-columns/' ),
				'default' => $this->default_settings['columns'],
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[image_size]',
				'title'   => __( 'Image size', 'posts-table-pro' ),
				'type'    => 'text',
				'desc'    => __( 'W x H in pixels, e.g. 80x80.', 'posts-table-pro' ) . $this->read_more( 'kb/ptp-column-widths/#image-size' ),
				'default' => $this->default_settings['image_size'],
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[lightbox]',
				'title'   => __( 'Image lightbox', 'posts-table-pro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Show the featured image in a lightbox', 'posts-table-pro' ),
				'default' => $this->default_settings['lightbox'],
			],
			[
				'id'                => Options::SHORTCODE_OPTION_KEY . '[content_length]',
				'title'             => __( 'Content length', 'posts-table-pro' ),
				'type'              => 'number',
				'class'             => 'small-text',
				'suffix'            => __( 'words', 'posts-table-pro' ),
				'desc'              => __( 'Enter -1 to show the full post content.', 'posts-table-pro' ),
				'default'           => $this->default_settings['content_length'],
				'custom_attributes' => [
					'min' => -1
				]
			],
			[
				'id'                => Options::SHORTCODE_OPTION_KEY . '[excerpt_length]',
				'title'             => __( 'Excerpt length', 'posts-table-pro' ),
				'type'              => 'number',
				'class'             => 'small-text',
				'suffix'            => __( 'words', 'posts-table-pro' ),
				'desc'              => __( 'Enter -1 to show the full excerpt.', 'posts-table-pro' ),
				'default'           => $this->default_settings['excerpt_length'],
				'custom_attributes' => [
					'min' => -1
				]
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[links]',
				'title'   => __( 'Post links', 'posts-table-pro' ),
				'type'    => 'text',
				'desc'    => __( 'Link table data to the relevant post, author, category, tag or term.', 'posts-table-pro' ) . $this->read_more( 'kb/links-posts-table/' ),
				'default' => $this->default_settings['links'],
			],
			[
				'id'      => Options::SHORTCODE_OPTION_KEY . '[button_text]',
				'title'   => __( 'Button text', 'posts-table-pro' ),
				'type'    => 'text',
				'desc'    => __( 'The button text when showing the \'button\' column.', 'posts-table-pro' ) . $this->read_more( 'kb/posts-table-button-column' ),
				'default' => $this->default_settings['button_text'],
			]
		];
	}

	private function get_controls_settings() {
		return [
			[
				'title'             => __( 'Search filters', 'posts-table-pro' ),
				'type'              => 'select',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[filters]',
				'options'           => [
					'false'  => __( 'Disabled', 'posts-table-pro' ),
					'true'   => __( 'Show based on columns in table', 'posts-table-pro' ),
					'custom' => __( 'Custom', 'posts-table-pro' )
				],
				'desc'              => __( 'Dropdown lists to filter the table by category, tag, or custom taxonomy.', 'posts-table-pro' ) . $this->read_more( 'kb/posts-table-filters/' ),
				'default'           => $this->default_settings['filters'],
				'class'             => 'toggle',
				'custom_attributes' => [
					'data-toggle-value' => 'custom',
					'data-toggle-class' => 'custom-filter'
				]
			],
			[
				'title'       => __( 'Custom filters', 'posts-table-pro' ),
				'type'        => 'text',
				'id'          => Options::SHORTCODE_OPTION_KEY . '[filters_custom]',
				'desc'        => __( 'Enter the filters as a comma-separated list, e.g. categories,tags,tax:location', 'posts-table-pro' ),
				'desc_tip'    => __( 'E.g. categories,tags,tax:location', 'posts-table-pro' ),
				'class'       => 'regular-text',
				'field_class' => 'custom-filter'
			],
			[
				'title'   => __( 'Search box', 'posts-table-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[search_box]',
				'options' => [
					'top'    => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'   => __( 'Above and below table', 'posts-table-pro' ),
					'false'  => __( 'Hidden', 'posts-table-pro' )
				],
				'default' => $this->default_settings['search_box']
			],
			[
				'title'   => __( 'Table reset', 'posts-table-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[reset_button]',
				'label'   => __( 'Show the reset link above the table', 'posts-table-pro' ),
				'default' => $this->default_settings['reset_button']
			],
			[
				'title'   => __( 'Page length', 'posts-table-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[page_length]',
				'options' => [
					'top'    => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'   => __( 'Above and below table', 'posts-table-pro' ),
					'false'  => __( 'Hidden', 'posts-table-pro' )
				],
				'desc'    => __( "The position of the 'Show [x] per page' dropdown list.", 'posts-table-pro' ),
				'default' => $this->default_settings['page_length']
			],
			[
				'title'   => __( 'Post totals', 'posts-table-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[totals]',
				'options' => [
					'top'    => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'   => __( 'Above and below table', 'posts-table-pro' ),
					'false'  => __( 'Hidden', 'posts-table-pro' )
				],
				'default' => $this->default_settings['totals']
			],
			[
				'title'   => __( 'Pagination buttons', 'posts-table-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[pagination]',
				'options' => [
					'top'    => __( 'Above table', 'posts-table-pro' ),
					'bottom' => __( 'Below table', 'posts-table-pro' ),
					'both'   => __( 'Above and below table', 'posts-table-pro' ),
					'false'  => __( 'Hidden', 'posts-table-pro' )
				],
				'desc'    => __( 'The position of the paging buttons which scroll between results.', 'posts-table-pro' ),
				'default' => $this->default_settings['pagination']
			]
		];
	}

	private function get_load_settings() {
		return [
			[
				'title'             => __( 'Lazy load', 'posts-table-pro' ),
				'type'              => 'checkbox',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[lazy_load]',
				'label'             => __( 'Load posts one page at a time', 'posts-table-pro' ),
				'desc'              => __( 'Enable this if you have many posts or experience slow page load times.', 'posts-table-pro' ) . '<br/>' .
				sprintf(
					/* translators: %1$s: help link opening tag placeholder, %2$s: help link closing tag placeholder */
					__( 'Warning: Lazy load has %1$ssome limitations%2$s &mdash; it limits the search, sorting and dropdown filters.', 'posts-table-pro' ),
					Lib_Util::format_link_open( Lib_Util::barn2_url( 'kb/posts-table-lazy-load/' ), true ),
					'</a>'
				),
				'default'           => $this->default_settings['lazy_load'],
				'class'             => 'toggle',
				'custom_attributes' => [
					'data-toggle-value' => 0,
					'data-toggle-class' => 'post-limit'
				]
			],
			[
				'title'             => __( 'Post limit', 'posts-table-pro' ),
				'type'              => 'number',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[post_limit]',
				'desc'              => __( 'The maximum number of posts in one table. Enter -1 to show all posts.', 'posts-table-pro' ),
				'default'           => $this->default_settings['post_limit'],
				'class'             => 'small-text',
				'field_class'       => 'post-limit',
				'custom_attributes' => [
					'min' => -1
				]
			],
			[
				'title'             => __( 'Posts per page', 'posts-table-pro' ),
				'type'              => 'number',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[rows_per_page]',
				'desc'              => __( 'The number of posts per page of results. Enter -1 to show all posts on one page.', 'posts-table-pro' ),
				'default'           => $this->default_settings['rows_per_page'],
				'custom_attributes' => [
					'min' => -1
				]
			]
		];
	}

	private function get_sort_settings() {
		return [
			[
				'title'             => __( 'Sort by', 'posts-table-pro' ),
				'type'              => 'select',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[sort_by]',
				'options'           => [
					'title'         => __( 'Title', 'posts-table-pro' ),
					'id'            => __( 'ID', 'posts-table-pro' ),
					'date'          => __( 'Date published', 'posts-table-pro' ),
					'modified'      => __( 'Date modified', 'posts-table-pro' ),
					'menu_order'    => __( 'Page order (menu order)', 'posts-table-pro' ),
					'name'          => __( 'Post slug', 'posts-table-pro' ),
					'author'        => __( 'Author', 'posts-table-pro' ),
					'comment_count' => __( 'Number of comments', 'posts-table-pro' ),
					'rand'          => __( 'Random', 'posts-table-pro' ),
					'custom'        => __( 'Other', 'posts-table-pro' )
				],
				'desc'              => __( 'The initial sort order applied to the table.', 'posts-table-pro' ) . $this->read_more( 'kb/posts-table-sort-options/' ),
				'default'           => $this->default_settings['sort_by'],
				'class'             => 'toggle',
				'custom_attributes' => [
					'data-toggle-value' => 'custom',
					'data-toggle-class' => 'custom-sort'
				]
			],
			[
				'title'       => __( 'Sort column', 'posts-table-pro' ),
				'type'        => 'text',
				'id'          => Options::SHORTCODE_OPTION_KEY . '[sort_by_custom]',
				'class'       => 'regular-text',
				'field_class' => 'custom-sort',
				'desc'        => __( 'Enter any column in your table. Only supported when lazy load is disabled.', 'posts-table-pro' )
			],
			[
				'title'   => __( 'Sort direction', 'posts-table-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[sort_order]',
				'options' => [
					''     => __( 'Automatic', 'posts-table-pro' ),
					'asc'  => __( 'Ascending (A to Z, old to new)', 'posts-table-pro' ),
					'desc' => __( 'Descending (Z to A, new to old)', 'posts-table-pro' )
				],
				'default' => $this->default_settings['sort_order']
			]
		];
	}

	private function get_design_settings() {
		return [
			[
				'id'                => Options::MISC_OPTION_KEY . '[design]',
				'title'             => __( 'Design', 'posts-table-pro' ),
				'type'              => 'radio',
				'options'           => [
					'default' => __( 'Default', 'posts-table-pro' ),
					'custom'  => __( 'Custom', 'posts-table-pro' ),
				],
				'default'           => 'default',
				'class'             => 'toggle',
				'custom_attributes' => [
					'data-toggle-value' => 'custom',
					'data-toggle-class' => 'custom-design'
				]
			],
			[
				'title'             => __( 'Borders', 'posts-table-pro' ),
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[external_border]',
				'desc'              => $this->get_icon( 'external-border.svg', __( 'External border', 'posts-table-pro' ) ) . __( 'External', 'posts-table-pro' ),
				'placeholder'       => __( 'Width', 'posts-table-pro' ),
				'custom_attributes' => [ 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[header_border]',
				/* translators: 'Header' in this context refers to the headings in the table. */
				'desc'              => $this->get_icon( 'header-border.svg', __( 'Header border', 'posts-table-pro' ) ) . __( 'Header', 'posts-table-pro' ),
				'placeholder'       => __( 'Width', 'posts-table-pro' ),
				'custom_attributes' => [ 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[body_border]',
				/* translators: 'Body' in this context refers to the main table content. */
				'desc'              => $this->get_icon( 'cell-border.svg', __( 'Body border', 'posts-table-pro' ) ) . __( 'Body', 'posts-table-pro' ),
				'placeholder'       => __( 'Width', 'posts-table-pro' ),
				'custom_attributes' => [ 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'title'       => __( 'Header background color', 'posts-table-pro' ),
				'type'        => 'color',
				'id'          => Options::MISC_OPTION_KEY . '[header_bg]',
				'field_class' => 'custom-design'
			],
			[
				'title'             => __( 'Header text', 'posts-table-pro' ),
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[header_text]',
				'custom_attributes' => [ 'min' => 8, 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'title'       => __( 'Main background color', 'posts-table-pro' ),
				'type'        => 'color',
				'id'          => Options::MISC_OPTION_KEY . '[body_bg]',
				'field_class' => 'custom-design'
			],
			[
				'title'       => __( 'Alternating background color (optional)', 'posts-table-pro' ),
				'type'        => 'color',
				'id'          => Options::MISC_OPTION_KEY . '[body_bg_alt]',
				'field_class' => 'custom-design'
			],
			[
				'title'             => __( 'Body text', 'posts-table-pro' ),
				'type'              => 'color_size',
				'id'                => Options::MISC_OPTION_KEY . '[body_text]',
				'custom_attributes' => [ 'min' => 8, 'max' => 50 ],
				'field_class'       => 'custom-design'
			],
			[
				'title'       => __( 'Spacing', 'posts-table-pro' ),
				'type'        => 'select',
				'id'          => Options::MISC_OPTION_KEY . '[table_spacing]',
				'options'     => [
					'default'  => __( 'Theme default', 'posts-table-pro' ),
					'compact'  => __( 'Compact', 'posts-table-pro' ),
					'normal'   => __( 'Normal', 'posts-table-pro' ),
					'spacious' => __( 'Spacious', 'posts-table-pro' ),
				],
				'default'     => 'default',
				'field_class' => 'custom-design'
			]
		];
	}

	private function get_advanced_settings() {
		return [
			[
				'title'   => __( 'Pagination type', 'posts-table-pro' ),
				'type'    => 'select',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[paging_type]',
				'options' => [
					'numbers'        => __( 'Page numbers', 'posts-table-pro' ),
					'simple'         => __( 'Prev - Next', 'posts-table-pro' ),
					'simple_numbers' => __( 'Prev - Page numbers - Next', 'posts-table-pro' ),
					'full'           => __( 'First - Prev - Next - Last', 'posts-table-pro' ),
					'full_numbers'   => __( 'First - Prev - Page numbers - Next - Last', 'posts-table-pro' )
				],
				'default' => $this->default_settings['paging_type']
			],
			[
				'title'   => __( 'Shortcodes', 'posts-table-pro' ),
				'type'    => 'checkbox',
				'id'      => Options::SHORTCODE_OPTION_KEY . '[shortcodes]',
				'label'   => __( 'Show shortcodes, HTML and other formatting in the table', 'posts-table-pro' ),
				'default' => $this->default_settings['shortcodes']
			],
			[
				'title'             => __( 'Caching', 'posts-table-pro' ),
				'type'              => 'checkbox',
				'id'                => Options::SHORTCODE_OPTION_KEY . '[cache]',
				'label'             => __( 'Cache table contents to improve load times', 'posts-table-pro' ),
				'default'           => $this->default_settings['cache'],
				'class'             => 'toggle',
				'custom_attributes' => [
					'data-toggle-class' => 'expires-after'
				]
			],
			[
				'title'             => __( 'Cache expiration', 'posts-table-pro' ),
				'type'              => 'number',
				'id'                => Options::MISC_OPTION_KEY . '[cache_expiry]',
				'suffix'            => __( 'hours', 'posts-table-pro' ),
				'desc'              => __( 'Your data will be refreshed after this length of time.', 'posts-table-pro' ),
				'default'           => 6,
				'field_class'       => 'expires-after',
				'custom_attributes' => [
					'min' => 1,
					'max' => 9999
				]
			]
		];
	}

	/**
	 * Check if given value is a valid HEX color.
	 *
	 * @param string $value The color value to check.
	 * @return boolean true if valid.
	 */
	private function check_hex_color( $value ) {
		return (bool) preg_match( '/^#[a-f0-9]{6}$/i', $value );
	}

	private function get_icon( $icon, $alt_text = '' ) {
		return sprintf(
			'<img src="%1$s" alt="%2$s" width="20" height="20" class="icon" />',
			Util::get_asset_url( 'images/' . ltrim( $icon, '/' ) ),
			$alt_text
		);
	}

	private function get_registered_post_types() {
		// Get all post types as objects.
		$post_types = get_post_types( [], 'objects' );

		// Internal WP post types.
		$internal_pts = [
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset',
			'oembed_cache',
			'wp_block'
		];

		// CPTs added by plugins which are not relevant or unsupported.
		$unsupported_pts = [
			'acf-field',
			'acf-field-group',
			'nf_sub',
			'edd_log',
			'edd_payment',
			'edd_discount',
			'product_variation',
			'shop_order_refund',
			'tribe-ea-record',
			'deleted_event'
		];

		$post_types = array_diff_key( $post_types, array_flip( array_merge( $internal_pts, $unsupported_pts ) ) );
		$names      = [];

		foreach ( $post_types as $post_type => $post_type_obj ) {
			$names[$post_type] = $post_type_obj->labels->name;
		}

		return $names;
	}

	private function read_more( $path ) {
		return ' ' . Lib_Util::barn2_link( $path, '', true );
	}

}
