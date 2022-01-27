<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
<div class = "container-fluid g-0">

<header class="entry-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
         <!--subheading -->
    <h3 class="book-subheading"><?php the_field('featured_text'); ?></h3>
    <!-- end subheading -->


		<!-- .entry-meta -->

	</header>
<!-- FOR INFO ON ORDERING COLUMNS, GO HERE:
https://getbootstrap.com/docs/5.0/utilities/flex/#order -->


<div class="row d-flex flex-wrap-reverse">
<div class="order-sm-2 order-m-1 order-lg-1 col-sm-12 col-md-6 col-lg-7 entry-content">
    <h3 class="mobi-show">About <?php the_title(); ?></h3>
<?php
		the_content();
		understrap_link_pages();
		?>



</div>

<!-- IMAGE AND BOOK META -->
<div class="order-sm-1 order-md-2 order-lg-2 col-sm-12 col-md-6 col-lg-5">
<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
<div class="book-meta">
    <h3 class="price">Price: <?php the_field('price'); ?></h3>
    <hr class="wp-block-separator"/>
    <p><strong>Publisher: </strong> <?php the_field('publisher'); ?></p>
    <p><strong>Available In</strong> <?php the_field('available_in'); ?></p>
    <p><strong>ISBN:</strong> <?php the_field('isbn'); ?></p>

    <p><strong>Published:</strong> <?php the_field('publication_year'); ?></p>

    <?php if( get_field('button_1_button_1_text') ){ ?>
    <a href="<?php the_field('button_1_button_1_url') ?>" target="_blank"><button class="button-orange" role="button"><?php the_field('button_1_button_1_text') ?></button></a>
    <?php }; ?>
    <br>

    <?php if( get_field('button_2_button_2_text') ){ ?>
    <a href="<?php the_field('button_2_button_2_url') ?>" target="_blank"><button class="button-green" role="button"><?php the_field('button_2_button_2_text') ?></button></a>
    <?php }; ?>
    <hr class="wp-block-separator mobi-show"/>
    



</div>

</div>

</div>
</div>





	


   
    




	

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->