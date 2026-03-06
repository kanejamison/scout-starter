<?php
/**
 * Page template.
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
			get_template_part( 'template-parts/content', 'page' );
		endwhile;
		?>
	</div>
</div>

<?php
get_footer();
