<?php
/**
 * This file registers a custom block pattern category and a block pattern for the MyriamGurba theme.
 *
 * @package MyriamGurba
 */

if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'myriamgurba',
		array( 'label' => __( 'Myriam Gurba', 'understrap-child' ) )
	);
}

// Define the block pattern content.
$block_pattern_contents = '
<!-- wp:cover {"overlayColor":"yellow","isUserOverlayColor":true,"isDark":false,"className":"blurb-box m-0 "} -->
<div class="wp-block-cover is-light blurb-box m-0"><span aria-hidden="true" class="wp-block-cover__background has-yellow-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>“Myriam Gurba is the most fearless writer in America. And most generous and kind to those who have no champion, while setting fire to the towers of the villainous. Long may she reign."</p>
<!-- /wp:paragraph --><cite>Luis Alberto Urrea, Pulitzer Prize finalist and author of Good Night, Irene</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>“Honest and darkly funny, the book is riddled with moments that will have you nodding, cringing, and crying right along with the author.”</p>
<!-- /wp:paragraph --><cite>Harper\'s Bazaar</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>"Like most truly great books, Mean made me laugh, cry and think. Myriam Gurba\'s a scorchingly good writer."</p>
<!-- /wp:paragraph --><cite>Cheryl Strayd, NYTimes</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>"What makes this so good? There are many answers, beginning with Gurba\'s radical and necessary empathy."</p>
<!-- /wp:paragraph --><cite>David Ulin, Alta Online</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>"A scalding memoir that comes with a full accounting of the costs of survival, of being haunted by those you could not save and learning to live with their ghosts."</p>
<!-- /wp:paragraph --><cite>Parul Sehgal, NYTimes</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>"Stop everything and read this brave and tender book."</p>
<!-- /wp:paragraph --><cite>Carmen Giménez Smith, Oprah Magazine</cite></blockquote>
<!-- /wp:quote --></div></div>
<!-- /wp:cover -->';










if ( function_exists( 'register_block_pattern' ) ) {
	register_block_pattern(
		'myriamgurba/category-block-pattern',
		array(
			'title'         => __( 'Blurbs Block Pattern', 'understrap-child' ),
			'description'   => _x( 'A custom block pattern displaying press blurbs', 'Block pattern description', 'understrap-child' ),
			'content'       => $block_pattern_contents,
			'categories'    => array( 'myriamgurba' ),
			'viewportWidth' => 1000,
			// 'image'         => get_template_directory_uri() . '/assets/images/category-block-pattern.jpg',
		)
	);
}
