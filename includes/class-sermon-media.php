<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.elitaft.com
 * @since      1.0.0
 *
 * @package    Sermon_Media
 * @subpackage Sermon_Media/includes
 */

namespace simple_sermon_media_podcasting;

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sermon_Media
 * @subpackage Sermon_Media/includes
 * @author     Eli Taft <eli@elitaft.com>
 */
class Sermon_Media {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sermon_Media_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $Sermon_Media    The string used to uniquely identify this plugin.
	 */
	protected $Sermon_Media;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SERMON_MEDIA_VERSION' ) ) {
			$this->version = SERMON_MEDIA_VERSION;
		} else {
			$this->version = '1.0.2';
		}
		$this->Sermon_Media = 'sermon-media';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sermon_Media_Loader. Orchestrates the hooks of the plugin.
	 * - Sermon_Media_Admin. Defines all hooks for the admin area.
	 * - Sermon_Media_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sermon-media-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sermon-media-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sermon-media-public.php';

		$this->loader = new Sermon_Media_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sermon_Media_Admin( $this->get_Sermon_Media(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'sermon_media_custom_post_type' );
		$this->loader->add_action( 'add_meta_boxes_sermon_media_post', $plugin_admin, 'sermon_media_post_metaboxes' );
		$this->loader->add_action( 'save_post_sermon_media_post', $plugin_admin, 'sermon_media_save_post' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'redirect_to_settings' );
		$this->loader->add_filter( 'post_type_link', $plugin_admin, 'change_link', 1, 3 );
		$this->loader->add_action( 'init', 'simple_sermon_media_podcasting\Sermon_Media_Admin', 'add_feed' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sermon_Media_Public( $this->get_Sermon_Media(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'sermon_media_the_content' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_public, 'add_post_type_to_archives' );
		$this->loader->add_filter( 'get_the_excerpt', $plugin_public, 'excerpt_string' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_Sermon_Media() {
		return $this->Sermon_Media;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sermon_Media_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}

