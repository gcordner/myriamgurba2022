<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>
<!--This is the hero -->

<?php if ( is_front_page() ) : ?>
	<section class="hero-home dark-overlay mb-5" style="border:2px solid red;"><img class="bg-image" src="file:///home/geoffcordner/Dropbox/websites/directory-2-0/bootstrap-5/html/img/photo/photo-1467987506553-8f3916508521.jpg" alt="">
      <div class="container py-7">
        <div class="overlay-content text-center text-white">
          <h1 class="display-3 text-serif fw-bold text-shadow mb-0">Escape the city today</h1>
          
        </div>
      </div>
    </section>
    <?php endif; ?>

<!-- end hero -->
<?php

$container = get_theme_mod( 'understrap_container_type' );

?>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'loop-templates/content', 'page' );

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				}
				?>

			</main><!-- #main -->

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
