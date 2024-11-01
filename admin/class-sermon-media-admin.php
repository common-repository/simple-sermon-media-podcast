<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.elitaft.com
 * @since      1.0.0
 *
 * @package    Sermon_Media
 * @subpackage Sermon_Media/admin
 */

namespace simple_sermon_media_podcasting;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Sermon_Media
 * @subpackage Sermon_Media/admin
 * @author     Eli Taft <eli@elitaft.com>
 */
class Sermon_Media_Admin {


	private $Plugin_ID;
	private $version;

	/**
	 * The name of the podcast feed.
	 *
	 * @since    1.0.3
	 * @access   private
	 * @var      string    $feed_name    The name of the podcast feed.
	 */
	private static $feed_name = 'sermon-media-podcast';


	/**
	 * Initializes the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $Plugin_ID    The ID of this plugin.
	 * @param    string $version      The version of this plugin.
	 */
	public function __construct( $Plugin_ID, $version ) {
		$this->Plugin_ID = $Plugin_ID;
		$this->version   = $version;
	}

	/**
	 * Registers the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->Plugin_ID, plugin_dir_url( __FILE__ ) . 'css/sermon-media-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Registers the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->Plugin_ID, plugin_dir_url( __FILE__ ) . 'js/sermon-media-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Redirects to the options page after the plugin is activated.
	 *
	 * @since    1.0.6
	 */
	public function redirect_to_settings() {
		if ( get_option( 'smet0_activation_redirect', false ) ) {
			delete_option( 'smet0_activation_redirect' );
			exit( wp_redirect( 'edit.php?post_type=sermon_media_post&page=smet0_options' ) );
		}
	}

	/**
	 * Adds an admin "Options" page associated with this custom post type.
	 *
	 * @since    1.0.0
	 */
	public function settings_page() {
		add_submenu_page(
			'edit.php?post_type=sermon_media_post',
			'Simple Sermon Media & Podcast Options',
			'Options',
			'manage_options',
			'smet0_options',
			__CLASS__ . '::my_plugin_options'
		);
	}

	/**
	 * Registers the Custom Post Type
	 *
	 * @since    1.0.0
	 */
	public function sermon_media_custom_post_type() {
		$labels = array(
			'name'           => 'Sermons',
			'singular_name'  => 'Sermon',
			'menu_name'      => 'Sermons',
			'name_admin_bar' => 'Sermon',
		);
		$args   = array(
			'description'              => 'Sermon Media Post Type',
			'labels'                   => $labels,
			'supports'                 => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'taxonomies'               => array( 'category', 'post_tag' ),
			'public'                   => true,
			'has_archive'              => true,
			'rewrite'                  => array(
				'slug'       => 'sermons/%cat%',
				'with_front' => true,
			),
			'cptp_permalink_structure' => '%post_id%',
		);
		register_post_type( 'sermon_media_post', $args );
	}

	/**
	 * Adds rewrite rules for the custom post type to support categories, no categories, and
	 * archive pages.
	 *
	 * @since    1.0.6
	 */
	public static function generate_rewrite_rules() {

		add_rewrite_rule(
			'^sermons/(.*)/(.*)/?$',
			'index.php?post_type=sermon_media_post&name=$matches[2]',
			'top'
		);

		add_rewrite_rule(
			'^sermons/(.*)/?$',
			'index.php?post_type=sermon_media_post&name=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^sermons/?$',
			'index.php?post_type=sermon_media_post',
			'top'
		);
	}

	/**
	 * Updates the permalink of our custom post type to include category if one is selected.
	 *
	 * @since    1.0.6
	 */
	public static function change_link( $post_link, $id = 0 ) {
		$post = get_post( $id );
		if ( $post->post_type == 'sermon_media_post' ) {
			if ( is_object( $post ) ) {
				$terms = wp_get_object_terms( $post->ID, array( 'category' ) );
				if ( $terms ) {
					return str_replace( '%cat%', $terms[0]->slug, $post_link );
				} else {
					return str_replace( '%cat%/', '', $post_link );
				}
			}
		}
		return $post_link;
	}

	/**
	 * Adds the feed to WordPress feeds
	 *
	 * @since    1.0.0
	 */
	public static function add_feed() {
			add_feed( self::$feed_name, array( get_called_class(), 'my_podcast_rss' ) );
			delete_option( 'rewrite_rules' );
			self::generate_rewrite_rules();
	}

	/**
	 * Removes the feed
	 *
	 * @since    1.0.3
	 */
	public static function remove_feed() {
			$hook = 'do_feed_' . self::$feed_name;
			remove_action( $hook, array( get_called_class(), 'my_podcast_rss' ), 10, 1 );
			delete_option( 'rewrite_rules' );
	}

	/**
	 * Loads the template used for the podcast RSS feed.
	 *
	 * @since    1.0.0
	 */
	public static function my_podcast_rss() {
			require_once plugin_dir_path( __FILE__ ) . '../public/templates/archive-sermon_media_post.php';
	}


	/**
	 * Registers the Custom Metaboxes
	 *
	 * @since    1.0.0
	 */
	public function sermon_media_post_metaboxes() {
		global $wp_meta_boxes;
		add_meta_box( 'sermon_media_metaboxes', 'Sermon Media', array( $this, 'sermon_media_metaboxes_html' ), 'sermon_media_post', 'normal', 'high' );
	}

	/**
	 * Registers the Callback for add_meta_box call
	 *
	 * @since    1.0.0
	 */
	public function sermon_media_metaboxes_html() {

		global $post;
		$custom = get_post_custom( $post->ID );
		$bible  = isset( $custom['sermon_media_bible_passage'][0] ) ? $custom['sermon_media_bible_passage'][0] : '';
		$video  = isset( $custom['sermon_media_video_url'][0] ) ? $custom['sermon_media_video_url'][0] : '';
		$mp3    = isset( $custom['sermon_media_mp3_url'][0] ) ? $custom['sermon_media_mp3_url'][0] : '';

		?>
			<p>
			  <label>
				<div class="smet0_tooltip">Bible Passage: <span class="smet0_tooltiptext">ex: John 1:1-3.</span></div>
			  </label>
			  &nbsp;
			  <input name="sermon_media_bible_passage" value="<?php echo esc_attr( $bible ); ?>">
			</p>
			<p>
			  <label>
				<div class="smet0_tooltip">Video URL: <span class="smet0_tooltiptext">Currently only supports YouTube.</span></div>
			  </label>
			  &nbsp;
			  <input name="sermon_media_video_url" value="<?php echo esc_url( $video ); ?>" size="50">
			</p>
			<p>
			  <label>
				<div class="smet0_tooltip">MP3 URL: <span class="smet0_tooltiptext">Must reside on the same server as this WordPress site so that we can calculate the duration</span></div>
			  </label>
			  &nbsp;
			  <input name="sermon_media_mp3_url" value="<?php echo esc_url( $mp3 ); ?>" size="50">
			</p>
		<?php
	}

	/**
	 * Saves the Metabox values on post save
	 *
	 * @since    1.0.0
	 */
	public function sermon_media_save_post() {

		if ( ( empty( $_POST ) ) || ( ! current_user_can( 'edit_posts' ) ) ) {
			return;
		}

		global $post;

		$bible = sanitize_text_field( $_POST['sermon_media_bible_passage'] );
		$video = esc_url_raw( $_POST['sermon_media_video_url'] );
		$audio = esc_url_raw( $_POST['sermon_media_mp3_url'] );
		update_post_meta( $post->ID, 'sermon_media_bible_passage', $bible );
		update_post_meta( $post->ID, 'sermon_media_video_url', $video );
		update_post_meta( $post->ID, 'sermon_media_mp3_url', $audio );
	}

	/**
	 * Returns an error class to be added to the corresponding HTML element if
	 * an error was added to the error array
	 *
	 * @since    1.0.0
	 * @param    array $error_array    An array of errors
	 */
	private static function ife( $error_array, $error_key ) {
		if ( array_key_exists( $error_key, $error_array ) ) {
			return 'class="smet0_field_error"';
		}
		return '';
	}

	/**
	 * Provides the options page.
	 *
	 * @since    1.0.0
	 */
	public static function my_plugin_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$smet0_podcast_title                = esc_attr( get_option( 'smet0_podcast_title' ) );
		$smet0_podcast_desc                 = esc_attr( get_option( 'smet0_podcast_desc' ) );
		$smet0_podcast_email                = esc_attr( get_option( 'smet0_podcast_email' ) );
		$smet0_podcast_posts_per_page       = esc_attr( get_option( 'smet0_podcast_posts_per_page' ) );
		$smet0_podcast_image                = esc_url_raw( get_option( 'smet0_podcast_image' ) );
		$smet0_podcast_category             = esc_attr( get_option( 'smet0_podcast_category' ) );
		$smet0_podcast_subcategory          = esc_attr( get_option( 'smet0_podcast_subcategory' ) );
		$smet0_podcast_explicit             = esc_attr( get_option( 'smet0_podcast_explicit' ) );
		$smet0_podcast_owner                = esc_attr( get_option( 'smet0_podcast_owner' ) );
		$smet0_podcast_email                = esc_attr( get_option( 'smet0_podcast_email' ) );
		$smet0_podcast_bible_passage_prefix = esc_attr( get_option( 'smet0_podcast_bible_passage_prefix' ) );

		if ( $smet0_podcast_title == '' ) {
			$smet0_podcast_title = esc_attr( get_bloginfo( 'name' ) );
		}
		if ( $smet0_podcast_desc == '' ) {
			$smet0_podcast_desc = esc_attr( get_bloginfo( 'description' ) );
		}
		if ( $smet0_podcast_owner == '' ) {
			$smet0_podcast_owner = esc_attr( get_bloginfo( 'name' ) );
		}
		if ( $smet0_podcast_email == '' ) {
			$smet0_podcast_email = esc_attr( get_bloginfo( 'admin_email' ) );
		}
		if ( $smet0_podcast_posts_per_page == '' ) {
			$smet0_podcast_posts_per_page = '100';
		}

		$smet0e = array(); // errors

		if ( isset( $_POST['smet0_options_form_submitted'] ) && $_POST['smet0_options_form_submitted'] == '1' ) {

			check_admin_referer( 'smet0_options_form_submitted' );

			// Mandatory fields
			$smet0_podcast_title = sanitize_text_field( $_POST['smet0_podcast_title'] );
			if ( $smet0_podcast_title == '' ) {
				$smet0e['ti'] = 'You must enter a title.';
			}

			$smet0_podcast_desc = sanitize_text_field( $_POST['smet0_podcast_desc'] );
			if ( $smet0_podcast_desc == '' ) {
				$smet0e['des'] = 'You must enter a description';
			}

			$smet0_podcast_posts_per_page = sanitize_text_field( $_POST['smet0_podcast_posts_per_page'] );
			if ( ( $smet0_podcast_posts_per_page ) == '' or ( ! is_numeric( $smet0_podcast_posts_per_page ) ) ) {
				$smet0e['ppp'] = 'You must specific a posts per page value.';
			}

			$smet0_podcast_image = esc_url_raw( $_POST['smet0_podcast_image'] );
			if ( $smet0_podcast_image == '' ) {
				$smet0e['img'] = 'You must specific a podcast image URL.';
			}

			$smet0_podcast_category = sanitize_text_field( $_POST['smet0_podcast_category'] );
			if ( $smet0_podcast_category == '' ) {
				$smet0e['cat'] = 'You must specific at least a main podcast category';
			}

			$smet0_podcast_explicit = sanitize_text_field( $_POST['smet0_podcast_explicit'] );
			if ( $smet0_podcast_explicit != '0' && $smet0_podcast_explicit != '1' ) {
				$smet0e['exp'] = 'You must indicate whether your podcast is explicit.';
			}

			$smet0_podcast_owner = sanitize_text_field( $_POST['smet0_podcast_owner'] );
			if ( $smet0_podcast_owner == '' ) {
				$smet0_podcast_owner = get_bloginfo( 'name' );
			}

			$smet0_podcast_email = sanitize_email( $_POST['smet0_podcast_email'] );
			if ( $smet0_podcast_email == '' ) {
				$smet0e['ema'] = '1';
			}

			// Optional fields (blank is okay, no error)
			$smet0_podcast_bible_passage_prefix = sanitize_text_field( $_POST['smet0_podcast_bible_passage_prefix'] );
			$smet0_podcast_subcategory          = sanitize_text_field( $_POST['smet0_podcast_subcategory'] );

			if ( count( $smet0e ) > 0 ) {
				echo '<div class="error"><p><strong>Please correct the errors below</strong>:</div>';
			} else {

				update_option( 'smet0_podcast_bible_passage_prefix', $smet0_podcast_bible_passage_prefix );
				update_option( 'smet0_podcast_title', $smet0_podcast_title );
				update_option( 'smet0_podcast_desc', $smet0_podcast_desc );
				update_option( 'smet0_podcast_posts_per_page', $smet0_podcast_posts_per_page );
				update_option( 'smet0_podcast_image', $smet0_podcast_image );
				update_option( 'smet0_podcast_category', $smet0_podcast_category );
				update_option( 'smet0_podcast_subcategory', $smet0_podcast_subcategory );
				update_option( 'smet0_podcast_explicit', $smet0_podcast_explicit );
				update_option( 'smet0_podcast_owner', $smet0_podcast_owner );
				update_option( 'smet0_podcast_email', $smet0_podcast_email );

				echo '<div class="updated"><p><strong>Settings saved</strong></p></div>';
			}
		}

		?>
		<div class="wrap sermon-media-options-form" id="smet0_options_form">

		  <h1>Simple Sermon Media & Podcast Options</h1>
		  <?php
			foreach ( $smet0e as $smet0_key => $smet0_value ) {
				echo '<span id="smet0_error_notice">' . $smet0_value . '</span>';
			}
			?>
		  <hr />

		  <form name="smet0_options" method="post" action="">

			<h2>Display Options</h2>
			<div class="smet0_options_form_section">
			  <h3>Prefix for the Bible Passage listing</h3>
			  <p>Text to display in front of the Bible passage on the sermon post, such as "Today's Bible passage was: ".
				 Note: If you do not provide a Bible passage with your sermon post, this section won't be displayed.
			  </p>
			  <p>Prefix: <input type="text" name="smet0_podcast_bible_passage_prefix" value="<?php echo esc_attr( $smet0_podcast_bible_passage_prefix ); ?>" size="50"></p>
			</div>

			<hr />

			<h2>Podcast Options</h2>

			<p><strong>Podcast RSS Url: <a href="<?php echo esc_url_raw( get_bloginfo( 'url' ) ); ?>/feed/sermon-media-podcast/" target="_blank"><?php echo esc_url( get_bloginfo( 'url' ) ) . '/feed/sermon-media-podcast/'; ?></a></strong></p>

			<div class="smet0_options_form_section">
			  <h3>Podcast Title</h3>
			  <p>Title: <input type="text" name="smet0_podcast_title" value="<?php echo esc_attr( $smet0_podcast_title ); ?>" size="40" <?php echo self::ife( $smet0e, 'ti' ); ?>></p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Podcast Description</h3>
			  <p>Description: <input type="text" name="smet0_podcast_desc" value="<?php echo esc_attr( $smet0_podcast_desc ); ?>" size="100" <?php echo self::ife( $smet0e, 'des' ); ?>></p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Posts per page</h3>
			  <p>The maximum number of podcast episodes that can be returned</p>
			  <p>Posts per page: <input type="text" name="smet0_podcast_posts_per_page" value="<?php echo esc_attr( $smet0_podcast_posts_per_page ); ?>" size="5" <?php echo self::ife( $smet0e, 'ppp' ); ?>></p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Podcast Image URL</h3>
			  <p>A link to the main image that will be associated with your podcast.  Recommended size: 3000 x 3000 pixels.</p>
			  <p>Podcast Image URL: <input type="text" name="smet0_podcast_image" value="<?php echo esc_url( $smet0_podcast_image ); ?>" size="100" <?php echo self::ife( $smet0e, 'img' ); ?>></p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Podcast Category</h3>
			  <p>It is recommended to follow <a href="https://help.apple.com/itc/podcasts_connect/#/itc9267a2f12" target="_blank">iTunes' offical list of supported categories.</a>
				 You can optionally choose a subcategory as well.  As these categories change over time, we have not provided a hard-coded list to select from.  You'll have to
				 enter them manually :)</p>
			  <p>Podcast Category: <input type="text" name="smet0_podcast_category" value="<?php echo esc_attr( $smet0_podcast_category ); ?>" size="20" <?php echo self::ife( $smet0e, 'cat' ); ?>></p>
			  <p>Podcast Subcategory: <input type="text" name="smet0_podcast_subcategory" value="<?php echo esc_attr( $smet0_podcast_subcategory ); ?>" size="20"></p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Explicit?</h3>
			  <p>Does your podcast contain explicit content?</p>
				<div <?php echo self::ife( $smet0e, 'exp' ); ?>>
				  <label>
					  <input type="radio" name="smet0_podcast_explicit" value="1" <?php checked( '1', esc_attr( $smet0_podcast_explicit ) ); ?> > Yes
				  </label>
				  <label>
					  <input type="radio" name="smet0_podcast_explicit" value="0" <?php checked( '0', esc_attr( $smet0_podcast_explicit ) ); ?> > No
				  </label>
				</div>
			  </p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Podcast Owner Name</h3>
			  <p>The name of the person / organization that owns the podcast.  Defaults to the WordPress site name.</p>
			  <p>Podcast Owner: <input type="text" name="smet0_podcast_owner" value="<?php echo esc_attr( $smet0_podcast_owner ); ?>" size="50"></p>
			</div>

			<div class="smet0_options_form_section">
			  <h3>Podcast Owner Email</h3>
			  <p>The email of the person / organization that owns the podcast.  Defaults to the WordPress admin email.</p>
			  <p>Podcast Email: <input type="text" name="smet0_podcast_email" value="<?php echo esc_attr( $smet0_podcast_email ); ?>" size="50" <?php echo self::ife( $smet0e, 'ema' ); ?>></p>
			</div>

			<hr />

			<p class="submit">
			  <input type="hidden" name="smet0_options_form_submitted" value="1">
			  <?php wp_nonce_field( 'smet0_options_form_submitted' ); ?>
			  <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
			</p>
		  </form>
		</div>
		<?php
	}
}

