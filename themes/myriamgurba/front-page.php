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
	<section class="hero-home dark-overlay">
		<div class="row">
			<div class="d-none d-sm-block col-sm-6 col-md-4 col-lg-3"><img src="/wp-content/uploads/2022/01/myriam-gurba-02.jpg" alt="Black and white portrait of Myriam Gurba" class="img-fluid"></div>
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3"><img src="/wp-content/uploads/2022/01/myriam-gurba-01.jpg" alt="Black and white portrait of Myriam Gurba" class="img-fluid"></div>
			<div class="d-none d-md-block d-lg-block d-xl-block col-md-4 col-lg-3"><img src="/wp-content/uploads/2022/01/myriam-gurba-03.jpg" alt="Black and white portrait of Myriam Gurba" class="img-fluid"></div>
			<div class="d-none d-md-none d-lg-block d-xl-block col-lg-3"><img src="/wp-content/uploads/2022/01/myriam-gurba-02.jpg" alt="Black and white portrait of Myriam Gurba" class="img-fluid"></div>
			</div>
	
	<div class="container py-7">
        <div class="overlay-content text-center text-white">
			<!--
          <h1 class="display-3 text-serif fw-bold text-shadow mb-0">Escape the city tomorrow</h1>
-->
          
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
