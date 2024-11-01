<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.elitaft.com
 * @since      1.0.0
 *
 * @package    Sermon_Media
 * @subpackage Sermon_Media/public
 */

namespace simple_sermon_media_podcasting;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sermon_Media
 * @subpackage Sermon_Media/public
 * @author     Eli Taft <eli@elitaft.com>
 */
class Sermon_Media_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Sermon_Media    The ID of this plugin.
	 */
	private $Sermon_Media;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $sermon_media    the name of the plugin.
	 * @param    string $version         The version of this plugin.
	 */
	public function __construct( $Sermon_Media, $version ) {
		$this->Sermon_Media = $Sermon_Media;
		$this->version      = $version;
	}

	/**
	 * Registers the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->Sermon_Media,
			plugin_dir_url( __FILE__ ) . 'css/sermon-media-public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Registers the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->Sermon_Media,
			plugin_dir_url( __FILE__ ) . 'js/sermon-media-public.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Displays media options at the top of the post if media metadata is found
	 *
	 * @since    1.0.0
	 * @param    string $content    Content of the current post
	 */
	public function sermon_media_the_content( $content ) {
		global $post;

		if (
			( $post->post_type == 'sermon_media_post' ) &&
			( is_singular( 'sermon_media_post' ) || is_archive() ) && ! is_admin() ) {
			$video = esc_url_raw( get_post_meta( $post->ID, 'sermon_media_video_url', true ) );
			$mp3   = esc_url_raw( get_post_meta( $post->ID, 'sermon_media_mp3_url', true ) );
			$bible = esc_attr( get_post_meta( $post->ID, 'sermon_media_bible_passage', true ) );

			if ( is_archive() ) {
				if ( $bible != '' ) {
					$bible_div = $this->format_bible_html( $bible );
				}
				return $bible_div . '<a class="smet0_exerpt_link_to_sermon" href="'
					   . get_permalink( $post ) . '">Hear this Sermon</a>';
			}

			$media_content = $this->create_media_content( $video, $mp3, $bible );
			$content       = $media_content . $content;
		}
		return $content;
	}

	/**
	 * Displays Custom Post Type in Archive Listings was saved.
	 *
	 * @since    1.0.0
	 * @param    WP_Query $query    The WP_Query instance (passed by reference).
	 */
	public function add_post_type_to_archives( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
			$query->set(
				'post_type',
				array( 'post', 'sermon_media_post' )
			);
		}
	}

	/**
	 * Overwrites the default excerpt for this custom post type.
	 *
	 * @since    1.0.0
	 * @param    string $more    The post excerpt.
	 */
	public function excerpt_string( $more ) {
		global $post;

		if ( get_post_type() == 'sermon_media_post' ) {
			return $this->sermon_media_the_content( $post->post_content );
		}
		return $more;
	}

	/**
	 * Converts a YouTube link to an iframe embed
	 *
	 * @since    1.0.0
	 * @param    string $link    The YouTube link
	 */
	private function convert_youtube( $link ) {
		$new_url = preg_replace(
			'/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i',
			'//www.youtube.com/embed/$2',
			$link
		);
		$new_url = add_query_arg( 'enablejsapi', 'true', $new_url );
		return '<iframe class="smet0_iframe" src="' . $new_url . '" allowfullscreen></iframe>';
	}

	/**
	 * Displays the Bible passage along with the prefix setting in a div tag.
	 *
	 * @since 1.0.0
	 * @param    string $bible    The Bible reference
	 */
	private function format_bible_html( $bible ) {
		$prefix = esc_attr( get_option( 'smet0_podcast_bible_passage_prefix' ) );
		return '<div id="smet0_bible_passage">' . $prefix . ' ' . $bible . '</div>';
	}

	/**
	 * Create the HTML for the sermon media
	 *
	 * @since    1.0.0
	 * @param    string $video    The YouTube link
	 * @param    string $mp3      The MP3 link
	 * @param    string $bible    The Bible reference
	 */
	private function create_media_content( $video = '', $mp3 = '', $bible = '' ) {

		$media_content = '';
		$video_tab     = '';
		$video_div     = '';
		$mp3_tabs      = '';
		$mp3_divs      = '';
		$bible_div     = '';

		// If none of the settings exist, return nothing
		if ( $video == '' && $mp3 == '' && $bible == '' ) {
			return $media_content;
		}

		if ( $video != '' ) {
			if (
				( strpos( $video, 'https://www.youtube.com/watch' ) === 0 ) ||
				( strpos( $video, 'https://youtu.be/' ) === 0 ) ) {
				$video = $this->convert_youtube( $video );
			} else {
				$video = '[video src="' . $video . '"]';
			}

			$video_tab = '<button class="smet0_tablinks" id="smet0_video_tab">Video</button>';
			$video_div = '  <div id="smet0_video" class="smet0_tabcontent">' . $video . '</div>';
		}

		if ( $mp3 != '' ) {
			// do_shortcode, because although a single post will display the shortcode correctly,
			// excerpts would just show the shortcodes.  Doing it here ensures that the player
			// will show for both posts and archive exerpts.
			$mp3_player = do_shortcode( '[audio src="' . $mp3 . '"]' );
			$mp3_tabs   = '<button class="smet0_tablinks" id="smet0_audio_tab">Audio</button>';
			$mp3_tabs  .= '<button class="smet0_tablinks" id="smet0_download_tab">Download</button>';

			$mp3_divs  = '<div id="smet0_audio" class="smet0_tabcontent">' . $mp3_player . '</div>';
			$mp3_divs .= '<div id="smet0_download" class="smet0_tabcontent"><a href="' . $mp3 . '" download>Download</a></div>';
		}

		if ( $bible != '' ) {
			$bible_div = $this->format_bible_html( $bible );
		}

		// If either video or mp3 exists, set up the tabbed content
		if ( $video != '' || $mp3 != '' ) {
			$media_content  = '<div class="smet0_tabbed_content" id="smet0_tabbed_content">';
			$media_content .= '<div class="smet0_tabs">' . $video_tab . $mp3_tabs . '</div>';
			$media_content .= $video_div . $mp3_divs . '</div>' . $bible_div;
		}

		return $media_content;
	}

	/**
	 * Get the current URL taking into account HTTPS and Port
	 *
	 * @since    1.0.3
	 */
	public static function get_current_url() {
		$url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
		$url .= '://' . $_SERVER['SERVER_NAME'];
		$url .= in_array( $_SERVER['SERVER_PORT'], array( '80', '443' ) ) ? '' : ':' . $_SERVER['SERVER_PORT'];
		$url .= $_SERVER['REQUEST_URI'];
		return $url;
	}
}

