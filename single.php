<?php
/**
 * Single post template.
 *
 * @package Scout_Starter
 */

get_header();
?>

<div class="section">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );

			// Comments (if open).
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		endwhile;
		?>
	</div>
</div>

<?php
get_footer();
