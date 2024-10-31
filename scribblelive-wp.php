<?php

/**
 * Scribble Live WP
 *
 * A simple plugin to provide better search friendly embedcodes.
 *
 * @link              http://www.scribblelive.com/
 * @since             1.0.0
 * @package           Scribble_Live_Wp
 *
 * @wordpress-plugin
 * Plugin Name:       Scribble Live
 * Plugin URI:        http://www.scribblelive.com/
 * Description:       A simple shortcode plugin to provide better search friendly embedcodes.
 * Version:           1.1.0.0
 * Author:            Scribble Live
 * Author URI:        http://www.scribblelive.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       scribble-live-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-scribble-live-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_scribble_live_wp() {

	$plugin = new Scribble_Live_Wp();
	$plugin->run();

}
run_scribble_live_wp();
