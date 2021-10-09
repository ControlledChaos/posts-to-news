<?php
/**
 * Posts to News Plugin
 *
 * @package     Posts_to_News
 * @version     1.0.0
 * @author      Controlled Chaos Design <greg@ccdzine.com>
 * @copyright   Copyright © 2019, Controlled Chaos Design
 * @link        https://github.com/ControlledChaos/posts-to-news
 * @license     GPL-3.0+ http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Plugin Name:  Posts to News
 * Plugin URI:   https://github.com/ControlledChaos/posts-to-news
 * Description:  A ClassicPress or WordPress plugin to change the default posts to "news".
 * Version:      1.0.0
 * Author:       Controlled Chaos Design
 * Author URI:   http://ccdzine.com/
 * License:      GPL-3.0+
 * License URI:  https://www.gnu.org/licenses/gpl.txt
 * Text Domain:  posts-to-news
 * Domain Path:  /languages
 * Tested up to: 5.8
 */

namespace Posts_to_News;

/**
 * License & Warranty
 *
 * Posts to News is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Posts to News is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Posts to News. If not, see {URI to Plugin License}.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Post to news
 *
 * Changes the default WordPress post name to News and replace the Dashicon.
 *
 * @since  1.0.0
 * @access public
 */
final class Posts_To_News {

	/**
	 * Menu name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string The menu name of the post type.
	 */
	protected $menu_name = 'News';

	/**
	 * Singular name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string The singular name of the post type.
	 */
	protected $singular = 'news post';

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'admin_menu', [ $this, 'change_menu_labels' ] );
		add_action( 'init', [ $this, 'change_page_labels' ] );
		add_action( 'admin_menu', [ $this, 'change_menu_icon' ] );
		add_filter( 'post_updated_messages', [ $this, 'change_page_messages' ] );
		add_action( 'admin_head', [ $this, 'dashboard_icons' ] );
		add_action( 'admin_footer', [ $this, 'at_glance_text' ] );
	}

	/**
	 * Change post type labels in the admin menu
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $menu Gets the admin menu.
	 * @global object $submenu Gets the admin submenus.
	 * @return string Returns the various labels.
	 */
	public function change_menu_labels() {

		// Access global variables.
		global $menu, $submenu;

		// The "Posts" position in the admin menu.
		$menu[5][0] = $this->menu_name();

		// Submenus of the "Posts" position in the admin menu.
		if ( current_user_can( 'edit_posts' ) ) {
			$submenu['edit.php'][5][0]  = __( 'News', 'posts-to-news' );
			$submenu['edit.php'][10][0] = __( 'Add News', 'posts-to-news' );
		}

		if ( current_user_can( 'manage_categories' ) ) {
			$submenu['edit.php'][15][0] = __( 'News Categories', 'posts-to-news' );
			$submenu['edit.php'][16][0] = __( 'News Tags', 'posts-to-news' );
		}
	}

	/**
	 * Change post type labels on admin pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $wp_post_types Gets the array of admin page labels.
	 * @return string Returns the various labels.
	 */
	public function change_page_labels() {

		// Access global variables.
		global $wp_post_types;

		// The labels of the array.
		$labels = $wp_post_types['post']->labels;
		$labels->name               = __( 'News Posts', 'posts-to-news' );
		$labels->menu_name          = $this->menu_name();
		$labels->singular_name      = ucwords( $this->singular() );
		$labels->add_new            = __( 'Add News', 'posts-to-news' );
		$labels->add_new_item       = __( 'Add News Post', 'posts-to-news' );
		$labels->edit_item          = __( 'Edit News Post', 'posts-to-news' );
		$labels->new_item           = __( 'Add News Post', 'posts-to-news' );
		$labels->view_item          = __( 'View News Post', 'posts-to-news');
		$labels->search_items       = __( 'Search News Posts', 'posts-to-news' );
		$labels->not_found          = __( 'No News Posts found', 'posts-to-news' );
		$labels->not_found_in_trash = __( 'No News Posts found in Trash', 'posts-to-news' );
		$labels->all_items          = __( 'All News', 'posts-to-news'  );
		$labels->name_admin_bar     = ucwords( $this->singular() );
	}

	/**
	 * Menu name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the translated menu name.
	 */
	public function menu_name() {
		$menu_name = $this->menu_name;
		return __( $menu_name, 'posts-to-news' );
	}

	/**
	 * Singular name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the translated singular name.
	 */
	public function singular() {
		$singular = $this->singular;
		return __( $singular, 'posts-to-news' );
	}

	/**
	 * Change the pin icon to a megaphone
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $menu Gets the admin menu.
	 * @return string Returns the various labels.
	 */
	public function change_menu_icon() {

		// Access global variables.
		global $menu;

		foreach ( $menu as $key => $val ) {

			if ( $this->menu_name() == $val[0] ) {
				$menu[$key][6] = 'dashicons-megaphone';
			}
		}
	}

	/**
	 * Change post messages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param array $messages Gets the array of messages.
	 * @global object $post Gets the post object.
	 * @global object $post_ID Gets the post ID.
	 * @return array Returns the array of messages.
	 */
	public function change_page_messages( $messages ) {

		// Access global variables.
		global $post, $post_ID;

		// Conditional message for revisions.
		if ( isset( $_GET['revision'] ) ) {
			$revision = sprintf(
				__( '%1s %2s' ),
				__( 'News post restored to revision from', 'posts-to-news' ),
				wp_post_revision_title( (int) $_GET['revision'], false )
			);
		} else {
			$revision = false;
		}

		// Updated message.
		$updated = sprintf(
			__( '%1s <a href="%2s">%3s</a>' ),
			__( 'News updated.', 'posts-to-news' ),
			esc_url( get_permalink( $post_ID ) ),
			__( 'View News Post', 'posts-to-news' )
		);

		// Published message.
		$published = sprintf(
			__( '%1s <a href="%2s">%3s</a>' ),
			__( 'News published.', 'posts-to-news' ),
			esc_url( get_permalink( $post_ID ) ),
			__( 'View News Post', 'posts-to-news' )
		);

		// Submitted message.
		$submitted = sprintf(
			__( '%1s <a target="_blank" href="%2s">%3s</a>' ),
			__( 'News submitted.', 'posts-to-news' ),
			esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ),
			__( 'Preview News Post', 'posts-to-news' )
		);

		// Scheduled message.
		$scheduled = sprintf(
			__( '%1s <strong>%2s</strong>. <a target="_blank" href="%3s">%4s</a>' ),
			__( 'News scheduled for:', 'posts-to-news' ),
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
			esc_url( get_permalink( $post_ID ) ),
			__( 'Preview News Post', 'posts-to-news' )
		);

		// Draft updated message.
		$draft = sprintf(
			__( '%1s <a target="_blank" href="%2s">%3s</a>' ),
			__( 'News draft updated.', 'posts-to-news' ),
			esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ),
			__( 'Preview News Post', 'posts-to-news' )
		);

		// The array of messages for the Posts post type.
		$messages['post'] = [

			// First is unused. Messages start at index 1.
			0  => null,
			1  => $updated,
			2  => __( 'Custom field updated.', 'posts-to-news' ),
			3  => __( 'Custom field deleted.', 'posts-to-news' ),
			4  => __( 'News updated.', 'posts-to-news' ),
			5  => $revision,
			6  => $published,
			7  => __( 'News saved.', 'posts-to-news' ),
			8  => $submitted,
			9  => $scheduled,
			10 => $draft
		];

		// Return the array of messages.
		return $messages;
	}

	/**
	 * News posts dashboard icon
	 *
	 * Changes the posts icon in the At a Glance dashboard widget.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the style block in the admin head.
	 */
	public function dashboard_icons() {

		// Get the screen ID to target the Dashboard.
        $screen = get_current_screen();

        // Bail if not on the Dashboard screen.
        if ( $screen->id != 'dashboard' ) {
			return;
		}

		// Minified style block.
		$style = '<style>#dashboard_right_now .post-count a[href="edit.php?post_type=post"]::before,#dashboard_right_now .post-count span::before{content:"\f488"!important;}</style>';

		// Print the style block.
		echo $style;
	}

	/**
	 * News posts dashboard text
	 *
	 * Changes the posts text in the At a Glance dashboard widget.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the script block in the admin head.
	 */
	public function at_glance_text() {

		// Get the screen ID to target the Dashboard.
        $screen = get_current_screen();

        // Bail if not on the Dashboard screen.
        if ( $screen->id != 'dashboard' ) {
			return;
		} ?>
		<script>jQuery(document).ready(function(a){a('.post-count a[href="edit.php?post_type=post"]').text(function(){return a(this).text().replace('1 Post','1 News Post')}),a('.post-count a[href="edit.php?post_type=post"]').text(function(){return a(this).text().replace('Posts','News Posts')})});</script>
	<?php }

}

// New instance of the class.
new Posts_To_News();
