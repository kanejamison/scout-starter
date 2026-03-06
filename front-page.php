<?php
/**
 * Homepage template.
 *
 * @package Scout_Starter
 */

get_header();
?>

<section class="hero">
	<div class="container">
		<h1 class="hero__title"><?php bloginfo( 'name' ); ?></h1>

		<?php $tagline = get_bloginfo( 'description' ); ?>
		<?php if ( $tagline ) : ?>
			<p class="hero__subtitle"><?php echo esc_html( $tagline ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
// If a static front page has content, render it.
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$content = get_the_content();
		if ( $content ) :
			?>
			<section class="section">
				<div class="container">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			</section>
			<?php
		endif;
	endwhile;
endif;
?>

<?php
// Recent posts section — only shown when enabled in Customizer.
$news_enabled = get_theme_mod( 'scout_news_enabled', false );
$news_heading  = get_theme_mod( 'scout_news_heading', __( 'Latest News', 'scout-starter' ) );

$recent_posts = $news_enabled ? new WP_Query( array(
	'posts_per_page'      => 3,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
) ) : false;

if ( $news_enabled && $recent_posts && $recent_posts->have_posts() ) :
	?>
	<section class="section section--alt">
		<div class="container">
			<h2 class="section__title"><?php echo esc_html( $news_heading ); ?></h2>
			<div class="card-grid">
				<?php
				while ( $recent_posts->have_posts() ) :
					$recent_posts->the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
endif;

get_footer();
