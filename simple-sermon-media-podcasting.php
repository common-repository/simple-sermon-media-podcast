<?php

/**
 *
 * The plugin bootstrap file
 *
 * @link              https://www.elitaft.com
 * @since             1.0.0
 * @package           Sermon_Media
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Sermon Media & Podcast
 * Plugin URI:        https://www.elitaft.com/technology/simple-sermon-media-podcast/
 * Description:       Provides media and podcast support for video and audio sermons.
 * Version:           1.0.13
 * Author:            Eli Taft
 * Author URI:        https://www.elitaft.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sermon-media
 * Domain Path:       /languages
 */

namespace simple_sermon_media_podcasting;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.  We use SemVer (https://semver.org)
 */
define( 'SERMON_MEDIA_VERSION', '1.0.13' );

/**
 * The code that runs during plugin activation.
 */
function activate_sermon_media() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sermon-media-activator.php';
	Sermon_Media_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_sermon_media() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sermon-media-deactivator.php';
	Sermon_Media_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'simple_sermon_media_podcasting\activate_sermon_media' );
register_deactivation_hook( __FILE__, 'simple_sermon_media_podcasting\deactivate_sermon_media' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sermon-media.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function sermon_media() {
	$plugin = new Sermon_Media();
	$plugin->run();

}
sermon_media();

