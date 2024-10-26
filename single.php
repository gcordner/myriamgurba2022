<?php
/**
 * Template Name: Full Width Page
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package MyriamGurba
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );

// if ( is_front_page() ) {
// get_template_part( 'global-templates/hero' );
// }
?>

<div class="wrapper p-0  mt-4" id="full-width-page-wrapper">
<!--mt-4 to comoensate for being covered by fixed-top navbar -->
	<div class="container-fluid p-0 has-yellow-background-color archive" id="content">
<!--ZEPHYR! -->
		<div class="row">

			<div class="col-md-12 content-area" id="primary">

				<main class="site-main" id="main" role="main">

				<?php
				if ( have_posts() ) {
					?>
					<header class="page-header">

					</header><!-- .page-header -->
					<div class="container overflow-hidden">
	<div class="row gx-5 gy-3">
					<?php
					// Start the loop.
					while ( have_posts() ) {
						the_post();

						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						if ( 'book' === get_post_type() ) {
							get_template_part( 'loop-templates/content-book', get_post_format() );

						} else {
							get_template_part( 'loop-templates/content', 'single' );
						}
					}
				} else {
					get_template_part( 'loop-templates/content', 'none' );
				}
				?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row end -->

	</div><!-- #content -->

</div><!-- #full-width-page-wrapper -->

<?php
get_footer();
