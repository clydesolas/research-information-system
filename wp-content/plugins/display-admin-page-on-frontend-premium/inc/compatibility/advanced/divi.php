<?php

if (isset($_GET['asdfplaksd'])) {
	return;
}
if (!class_exists('WPFA_DIVI')) {

	class WPFA_DIVI {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {

			if (!$this->_is_divi_enabled()) {
				return;
			}
			add_filter('vg_frontend_admin/compatible_default_editors', array($this, 'add_compatible_default_editor'));
			add_action('get_edit_post_link', array($this, 'modify_edit_link'), 100, 2);
			add_filter('admin_url', array($this, 'modify_add_new_link'));
			$this->create_new_divi_page();
			if (!empty($_GET['vgfa_referrer'])) {
				add_filter('page_link', array($this, 'add_referrer_to_divi_builder_url'));
				add_filter('post_link', array($this, 'add_referrer_to_divi_builder_url'));
			}
		}

		function add_referrer_to_divi_builder_url($url) {
			if (!empty($_GET['et_fb_activation_nonce'])) {
				$url = add_query_arg('vgfa_referrer', $_GET['vgfa_referrer'], $url);
			}
			return $url;
		}

		function _is_divi_enabled() {
			if (defined('ET_BUILDER_VERSION') || defined('ET_BUILDER_PLUGIN_VERSION')) {
				return true;
			}
			$global_dashboard_id = (int) VG_Admin_To_Frontend_Obj()->get_settings('global_dashboard_id');
			if ($global_dashboard_id && stripos(get_blog_option($global_dashboard_id, 'template'), 'divi') !== false) {
				return true;
			}
			return false;
		}

		/**
		 * Default post information to use when populating the "Write Post" form.
		 *
		 * @since 2.0.0
		 *
		 * @param string $post_type    Optional. A post type string. Default 'post'.
		 * @param bool   $create_in_db Optional. Whether to insert the post into database. Default false.
		 * @return WP_Post Post object containing all the default post data as attributes
		 */
		function get_default_post_to_edit($post_type = 'post', $create_in_db = false) {
			$post_title = '';
			if (!empty($_REQUEST['post_title'])) {
				$post_title = esc_html(wp_unslash($_REQUEST['post_title']));
			}

			$post_content = '';
			if (!empty($_REQUEST['content'])) {
				$post_content = esc_html(wp_unslash($_REQUEST['content']));
			}

			$post_excerpt = '';
			if (!empty($_REQUEST['excerpt'])) {
				$post_excerpt = esc_html(wp_unslash($_REQUEST['excerpt']));
			}

			if ($create_in_db) {
				$post_id = wp_insert_post(
						array(
							'post_title' => __('Auto Draft'),
							'post_type' => $post_type,
							'post_status' => 'draft',
						),
						false,
						false
				);
				$post = get_post($post_id);
				if (current_theme_supports('post-formats') && post_type_supports($post->post_type, 'post-formats') && get_option('default_post_format')) {
					set_post_format($post, get_option('default_post_format'));
				}
				wp_after_insert_post($post, false, null);

				// Schedule auto-draft cleanup.
				if (!wp_next_scheduled('wp_scheduled_auto_draft_delete')) {
					wp_schedule_event(time(), 'daily', 'wp_scheduled_auto_draft_delete');
				}
			} else {
				$post = new stdClass;
				$post->ID = 0;
				$post->post_author = '';
				$post->post_date = '';
				$post->post_date_gmt = '';
				$post->post_password = '';
				$post->post_name = '';
				$post->post_type = $post_type;
				$post->post_status = 'draft';
				$post->to_ping = '';
				$post->pinged = '';
				$post->comment_status = get_default_comment_status($post_type);
				$post->ping_status = get_default_comment_status($post_type, 'pingback');
				$post->post_pingback = get_option('default_pingback_flag');
				$post->post_category = get_option('default_category');
				$post->page_template = 'default';
				$post->post_parent = 0;
				$post->menu_order = 0;
				$post = new WP_Post($post);
			}

			/**
			 * Filters the default post content initially used in the "Write Post" form.
			 *
			 * @since 1.5.0
			 *
			 * @param string  $post_content Default post content.
			 * @param WP_Post $post         Post object.
			 */
			$post->post_content = (string) apply_filters('default_content', $post_content, $post);

			/**
			 * Filters the default post title initially used in the "Write Post" form.
			 *
			 * @since 1.5.0
			 *
			 * @param string  $post_title Default post title.
			 * @param WP_Post $post       Post object.
			 */
			$post->post_title = (string) apply_filters('default_title', $post_title, $post);

			/**
			 * Filters the default post excerpt initially used in the "Write Post" form.
			 *
			 * @since 1.5.0
			 *
			 * @param string  $post_excerpt Default post excerpt.
			 * @param WP_Post $post         Post object.
			 */
			$post->post_excerpt = (string) apply_filters('default_excerpt', $post_excerpt, $post);

			return $post;
		}

		function create_new_divi_page() {
			if (empty($_GET['wpfa_divi_new']) || !$this->_can_use_divi()) {
				return;
			}
			$post_type = !empty($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'post';
			$post = $this->get_default_post_to_edit($post_type, true);
			$divi_url = $this->modify_edit_link(add_query_arg(array('post' => $post->ID, 'action' => 'edit', 'classic-editor' => '1'), admin_url('post.php')), $post->ID);
			wp_safe_redirect($divi_url);
			exit();
		}

		function modify_add_new_link($url) {
			if (preg_match('/post-new.php/', $url) && $this->_can_use_divi()) {
				$parts = parse_url($url);
				if (!isset($parts['query'])) {
					$parts['query'] = '';
				}
				parse_str($parts['query'], $query_parameters);
				$query_parameters['wpfa_divi_new'] = 1;
				$url = add_query_arg($query_parameters, home_url('/'));
			}
			return $url;
		}

		function _can_use_divi() {
			$default_editor = VG_Admin_To_Frontend_Obj()->get_settings('default_editor', '');
			return function_exists('et_pb_is_allowed') && et_pb_is_allowed('use_visual_builder') && $default_editor === 'divi' && et_pb_is_allowed('divi_builder_control');
		}

		function modify_edit_link($link, $post_id) {
			if (!$this->_can_use_divi() || !et_builder_fb_enabled_for_post($post_id)) {
				return $link;
			}

			if (et_fb_is_enabled() && !empty($_GET['vgfa_referrer'])) {
				$referrer = preg_replace('/\#.+$/', '', esc_url(base64_decode($_GET['vgfa_referrer'])));
				$link = $referrer . '#wpfa:' . base64_encode('post.php?action=edit&post=' . $post_id);
			} elseif (!et_fb_is_enabled()) {
				$page_url = get_permalink($post_id);
				$use_visual_builder_url = et_pb_is_pagebuilder_used($post_id) ?
						et_fb_get_builder_url($page_url) :
						add_query_arg(array(
							'et_fb_activation_nonce' => wp_create_nonce('et_fb_activation_nonce_' . $post_id),
								), $page_url);

				if (!empty($_GET['vgfa_referrer'])) {
					$use_visual_builder_url = add_query_arg('vgfa_referrer', $_GET['vgfa_referrer'], $use_visual_builder_url);
				}
				$link = esc_url_raw($use_visual_builder_url);
			}
			return $link;
		}

		function add_compatible_default_editor($editors) {
			$editors['divi'] = 'Divi';
			return $editors;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPFA_DIVI::$instance) {
				WPFA_DIVI::$instance = new WPFA_DIVI();
				WPFA_DIVI::$instance->init();
			}
			return WPFA_DIVI::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPFA_DIVI_Obj')) {

	function WPFA_DIVI_Obj() {
		return WPFA_DIVI::get_instance();
	}

}

add_action('init', 'WPFA_DIVI_Obj');
