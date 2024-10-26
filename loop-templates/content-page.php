<?php
/**
 * Partial template for content in page.php
 *
 * @package MyriamGurba
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<!-- <header class="entry-header">

		<?php the_title( '<h1 class="entry-title page-header">', '</h1>' ); ?>

	</header><!end-- entry-header --end>

	<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
-->
	<div class="entry-content">

		<?php
		the_content();
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_edit_post_link(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
