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

      if($articleUrl) {
         $url = $articleUrl;
      }
      else {
         $url = get_permalink();
      }

?>






<div class="col-sm-12 col-md-6 col-lg-4 mb-5">
   <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
      <header class="entry-header">
         <!--FEATURED IMAGE -->
         <!-- MAGAZINE AND DATE --> 
         <div class="magazine">

 <?php if($magazine) {
    echo '<p><a href="'.get_field("url").'" target="_blank" >'.$magazine.'</a> <span class="date"> l '.$pubDate.'</span></p>';
 } else {
    echo  '<p><span class="date">'.$pubDate.'</span></p>';
 }
?>
</div>
 <!-- END MAGAZINE AND DATE -->
         <div class="featured-image">
         
         <?php {
            echo '<a href="'.$url.'" target="_blank" title="'.get_the_title().'">'.get_the_post_thumbnail( $post->ID, 'large' ).'</a>';
                           }
               ?>
        </div>
<!--END FEATURED IMAGE -->



 
 <?php if($articleUrl) { 
the_title(
    sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_field("url") ) ),
    '</a></h2>'
);        } else {
    the_title(
        sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
        '</a></h2>'
    );        }
        ?>


      </header>
      <!-- .entry-header -->
      <div class="entry-content">
         <?php
         /* to remove READ MORE link from excerpt:
         https://stackoverflow.com/questions/42202169/wordpress-remove-read-more-from-excerpt
            
         or do it using css:
         https://wpmarks.com/how-to-remove-the-read-more-button-in-wordpress/

         */
            the_excerpt();
            understrap_link_pages();

            ?>
      </div>
      <!-- .entry-content -->
      <footer class="entry-footer">
          <div class="tags">
         
      <?php 
      // Refer to this page: https://developer.wordpress.org/reference/functions/the_tags/
    //   the_tags( 'Tagged with: ',' , ' ); 
      the_tags( '',' ' );
      ?>
</div>
            </footer>
      <!-- .entry-footer -->
   </article>
   <!-- #post-## -->
</div>