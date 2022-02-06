<?php
   /**
    * Post rendering content according to caller of get_template_part
    *
    * @package Understrap
    */
   
   // Exit if accessed directly.
   defined( 'ABSPATH' ) || exit;
   ?>
<?php $articleUrl = get_field( "url" ); 
      $magazine = get_field( "magazine_name" );
      $pubDate = get_the_date();
      $tags = get_tags();

?>






<div class="col-sm-12 col-md-6">
   <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
      <header class="entry-header">
         <!--FEATURED IMAGE -->
         <div class="featured-image">
         
         <?php if($articleUrl) {
            echo '<a href="'.$articleUrl.'" target="_blank" title="'.get_the_title().'">'.get_the_post_thumbnail( $post->ID, 'large' ).'</a>';
            } else {
               echo get_the_post_thumbnail( $post->ID, 'large' );
               }
               ?>
        </div>
<!--END FEATURED IMAGE -->


        <?php if($articleUrl) { 
        echo '<a href="'.$articleUrl.'" target="_blank" title="'.get_the_title().'">'."I'm a little teapot short and stout".'</a>';
        } else {
            echo "Here is my handle and here is my spout.";
        }
        ?>
 <!-- MAGAZINE AND DATE --> 
 <?php if($magazine) {
    echo '<p><a href="'.get_field("url").'" target="_blank" >'.$magazine.'</a></p>';
 } else {
    echo  '<p>Yes, we have no bananas.</p>';
 }
?>

 <!-- END MAGAZINE AND DATE -->






         <?php
            the_title(
            	sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_field("url") ) ),
            	'</a></h2>'
            );
            ?>
         <?php if ( 'post' === get_post_type() ) : ?>
         <div class="entry-meta">
            <?php understrap_posted_on(); ?>
         </div>
         <!-- .entry-meta -->
         <?php endif; ?>
      </header>
      <!-- .entry-header -->
      <div class="entry-content">
         <?php
            the_excerpt();
            understrap_link_pages();

            ?>
      </div>
      <!-- .entry-content -->
      <footer class="entry-footer">
         
      <?php 
      // Refer to this page: https://developer.wordpress.org/reference/functions/the_tags/
      the_tags( 'Tagged with: ',' , ' ); 
      ?>

            </footer>
      <!-- .entry-footer -->
   </article>
   <!-- #post-## -->
</div>