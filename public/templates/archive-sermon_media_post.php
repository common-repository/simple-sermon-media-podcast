<?php

/**
 * A template for printing the podcast RSS feed.
 *
 * @link       https://www.elitaft.com
 * @since      1.0.0
 * @package    Sermon_Media
 * @subpackage Sermon_Media/public
 * @author     Eli Taft <eli@elitaft.com>
 */

namespace simple_sermon_media_podcasting;

require_once plugin_dir_path( __FILE__ ) . '../class-sermon-media-mp3.php';
require_once plugin_dir_path( __FILE__ ) . '../class-sermon-media-public.php';

$smet0_podcast_title                = esc_attr( get_option( 'smet0_podcast_title' ) );
$smet0_podcast_desc                 = esc_attr( get_option( 'smet0_podcast_desc' ) );
$smet0_podcast_posts_per_page       = esc_attr( get_option( 'smet0_podcast_posts_per_page' ) );
$smet0_podcast_image                = esc_url_raw( get_option( 'smet0_podcast_image' ) );
$smet0_podcast_category             = esc_attr( get_option( 'smet0_podcast_category' ) );
$smet0_podcast_subcategory          = esc_attr( get_option( 'smet0_podcast_subcategory' ) );
$smet0_podcast_explicit             = esc_attr( get_option( 'smet0_podcast_explicit' ) );
$smet0_podcast_owner                = esc_attr( get_option( 'smet0_podcast_owner' ) );
$smet0_podcast_email                = esc_attr( get_option( 'smet0_podcast_email' ) );
$smet0_podcast_bible_passage_prefix = esc_attr( get_option( 'smet0_podcast_bible_passage_prefix' ) );

/**
 *
 * Don't even try to print the RSS feed if these settings haven't been filled out
 *
 * @since 1.0.0
 */
if ( ( $smet0_podcast_posts_per_page == '' ) or
	 ( $smet0_podcast_image == '' ) or
	 ( $smet0_podcast_category == '' ) or
	 ( $smet0_podcast_explicit == '' ) or
	 ( $smet0_podcast_owner == '' ) or
	 ( $smet0_podcast_email == '' ) ) {
	print(
		'Your podcast has not been set up because you have not yet entered the required settings. '
		. ' In the WordPress Admin, under "Sermons," click "Options"'
	);
	exit();
}


// Query the Podcast Custom Post Type and fetch the latest 100 posts
$smet0_args = array(
	'post_type'      => 'sermon_media_post',
	'posts_per_page' => (int) $smet0_podcast_posts_per_page,
);
$smet0_loop = new \WP_Query( $smet0_args );

$dateformatstring = _x( 'D, d M Y H:i:s O', 'Date formating for RSS feeds.' );

// Output the XML header
header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . esc_attr( get_option( 'blog_charset' ) ), true );
echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?' . '>';
?>

<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
	<atom:link href="<?php echo Sermon_Media_Public::get_current_url(); ?>" rel="self" type="application/rss+xml" />
	<title><?php echo $smet0_podcast_title; ?></title>
	<pubDate><?php echo date( $dateformatstring ); ?></pubDate>
	<lastBuildDate><?php echo date( $dateformatstring ); ?></lastBuildDate>
	<link><?php echo esc_url_raw( get_bloginfo( 'url' ) ); ?></link>
	<language><?php echo esc_attr( get_bloginfo( 'language' ) ); ?></language>
	<copyright><?php echo date( 'Y' ); ?> <?php echo esc_attr( get_bloginfo( 'name' ) ); ?></copyright>
	<itunes:summary><?php echo $smet0_podcast_desc; ?></itunes:summary>
	<image>
	  <url>
		<?php echo $smet0_podcast_image; ?>
	  </url>
	  <title><?php echo $smet0_podcast_title; ?></title>
	  <link><?php echo esc_url_raw( get_bloginfo( 'url' ) ); ?></link>
	</image>
	<itunes:author><?php echo esc_attr( get_bloginfo( 'name' ) ); ?></itunes:author>
	<itunes:category text="<?php echo $smet0_podcast_category; ?>">
	  <?php
		if ( $smet0_podcast_subcategory != '' ) {
			echo '<itunes:category text="' . $smet0_podcast_subcategory . '"/>';}
		?>
	</itunes:category>
		<itunes:image href="<?php echo $smet0_podcast_image; ?>" />
	<itunes:explicit><?php echo ( $smet0_podcast_explicit == '1' ? 'yes' : 'no' ); ?></itunes:explicit>
	<itunes:owner>
	  <itunes:name><?php echo $smet0_podcast_owner; ?></itunes:name>
	  <itunes:email><?php echo $smet0_podcast_email; ?></itunes:email>
	</itunes:owner>
	<description><?php echo $smet0_podcast_desc; ?></description>

	<?php

	/**
	 * Start the loop for podcast posts
	 *
	 * @since 1.0.0
	 */
	while ( $smet0_loop->have_posts() ) :
		$smet0_loop->the_post();
		?>
		<?php
		$smet0_title    = esc_attr( get_the_title() );
		$smet0_pub_date = date( $dateformatstring, esc_attr( get_the_time( 'U' ) ) );
		$smet0_gid      = esc_url_raw( get_permalink() );
		$smet0_author   = esc_attr( get_bloginfo( 'name' ) );
		$smet0_summary  = $smet0_podcast_bible_passage_prefix . ' ' . esc_attr( get_post_meta( get_the_ID(), 'sermon_media_bible_passage', true ) );

		$smet0_mp3_url        = esc_url_raw( get_post_meta( get_the_ID(), 'sermon_media_mp3_url', true ) );
		$smet0_mp3_url_parsed = parse_url( $smet0_mp3_url );
		$smet0_mp3_path       = ABSPATH . $smet0_mp3_url_parsed['path'];

		$smet0_mp3_file     = new SMET0_MP3File( $smet0_mp3_path );
		$smet0_mp3_duration = $smet0_mp3_file->getDurationEstimate();
		if ( $smet0_mp3_duration > 0 ) {
			$smet0_mp3_duration = gmdate( 'H:i:s', $smet0_mp3_duration );
		}

		$smet0_mp3_size = $smet0_mp3_file->get_filesize();

		if ( has_post_thumbnail( get_the_ID() ) ) {
			$smet0_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			$smet0_image = '<itunes:image href="' . esc_url_raw( $smet0_image[0] ) . '" />';
		} else {
			$smet0_image = '';
		}
		?>

	<item>
	  <title><?php echo $smet0_title; ?></title>
	  <pubDate><?php echo $smet0_pub_date; ?></pubDate>
	  <guid><?php echo $smet0_gid; ?></guid>
	  <itunes:author><?php echo $smet0_author; ?></itunes:author>
	  <itunes:summary><?php echo $smet0_summary; ?></itunes:summary>
	  <description><?php echo $smet0_summary; ?></description>
		<?php echo $smet0_image; ?>
	  <enclosure url="<?php echo $smet0_mp3_url; ?>" length="<?php echo $smet0_mp3_size; ?>" type="audio/mpeg" />
	  <itunes:duration><?php echo $smet0_mp3_duration; ?></itunes:duration>
	  <itunes:explicit><?php echo ( $smet0_podcast_explicit == '1' ? 'yes' : 'no' ); ?></itunes:explicit>
	</item>
	<?php endwhile; ?>

  </channel>

</rss>

