<?php
/**
 * The template for displaying archive pages
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper p-0" id="full-width-page-wrapper">

	<div class="container-full has-yellow-background-color mt-4 archive" id="content" tabindex="-1">

		<div class="row max-width">

			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main mt-6" id="main">

				<?php
				if ( have_posts() ) {
					?>
					<header class="page-header">
						<?php
						post_type_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
						?>
					</header><!-- .page-header -->
                    <div class="container">
    <div class="row">
					<?php
					// Start the loop.
					while ( have_posts() ) {
						the_post();

						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'loop-templates/content-writing', get_post_format() );
					}
				} else {
					get_template_part( 'loop-templates/content', 'none' );
				}
				?>
</div>
            </div>
			</main><!-- #main -->

			<?php
			// Display the pagination component.
			understrap_pagination();
			// Do the right sidebar check.
			get_template_part( 'global-templates/right-sidebar-check' );
			?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #archive-wrapper -->

<?php
get_footer();
