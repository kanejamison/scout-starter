<?php
/**
 * Template part: post card (used in grids/archives).
 *
 * @package Scout_Starter
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="card__image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'scout-card' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="card__body">
		<h3 class="card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<p class="card__meta">
			<?php scout_starter_posted_on(); ?>
		</p>

		<div class="card__excerpt">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
