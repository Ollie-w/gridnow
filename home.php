<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php the_title( '<h3>', '</h3>' ); ?>
<?php if(has_post_thumbnail()) { the_post_thumbnail(); }
 ?>
 </div>

<?php endwhile; else : ?>
	<p><?php _e( 'Sorry, no posts matched your criteria.', 'gridnow' ); ?></p>
<?php endif; ?>

	</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
