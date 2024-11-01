<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.elitaft.com
 * @since      1.0.0
 *
 * @package    Sermon_Media
 * @subpackage Sermon_Media/includes
 */

namespace simple_sermon_media_podcasting;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Sermon_Media
 * @subpackage Sermon_Media/includes
 * @author     Eli Taft <eli@elitaft.com>
 */
class Sermon_Media_Deactivator {
	public static function deactivate() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sermon-media-admin.php';
		Sermon_Media_Admin::remove_feed();
	}
}

