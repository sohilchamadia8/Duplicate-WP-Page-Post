<?php
/**
 * @link              https://sohilchamadia8.wordpress.com/
 * @since             1.0.0
 * @package           Duplicate_WP_Post_Page
 *
 * @wordpress-plugin
 * Plugin Name:       Duplicate WP Post Page
 * Plugin URI:        https://github.com/sohilchamadia8/duplicate-wp-page-post
 * Description:       Purpose of the plugin is to replicate the different post typ in wordpress like post and page.
 * Version:           1.0.0
 * Author:            Sohil B. Chamadia
 * Author URI:        https://sohilchamadia8.wordpress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       duplicate-wp-post-page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'Duplicate_WP_Post_Page_VERSION', '1.0.0' );

/**
 * Begins execution of the plugin.
 * The core plugin class that is used to define hooks and code stuff for replication of page and post.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-duplicate-wp-post-page.php';

