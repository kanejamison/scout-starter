<?php
/**
 * Homepage template.
 *
 * @package Scout_Starter
 */

get_header();

$hero_image  = get_theme_mod( 'scout_hero_image', '' );
$hero_class  = $hero_image ? 'hero hero--has-image' : 'hero';
$hero_style  = $hero_image ? sprintf( 'style="background-image: url(%s)"', esc_url( $hero_image ) ) : '';
$tagline     = get_theme_mod( 'scout_hero_tagline', __( 'Adventure starts here.', 'scout-starter' ) );
$cta_text    = get_theme_mod( 'scout_cta_text', __( 'Join Us', 'scout-starter' ) );
$cta_url     = get_theme_mod( 'scout_cta_url', '#' );
$subtitle    = scout_starter_unit_subtitle();
?>

<section class="<?php echo esc_attr( $hero_class ); ?>" <?php echo $hero_style; ?>>
	<div class="container">
		<span class="hero__unit-type"><?php echo esc_html( scout_starter_unit_type_label() ); ?></span>
		<h1 class="hero__title"><?php echo esc_html( scout_starter_unit_name() ); ?></h1>

		<?php if ( $subtitle ) : ?>
			<p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php elseif ( $tagline ) : ?>
			<p class="hero__subtitle"><?php echo esc_html( $tagline ); ?></p>
		<?php endif; ?>

		<?php if ( $cta_text && $cta_url ) : ?>
			<a href="<?php echo esc_url( $cta_url ); ?>" class="hero__cta">
				<?php echo esc_html( $cta_text ); ?>
			</a>
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
// Recent posts section (shows latest 3 posts if blog posts exist).
$recent_posts = new WP_Query( array(
	'posts_per_page'      => 3,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
) );

if ( $recent_posts->have_posts() ) :
	?>
	<section class="section section--alt">
		<div class="container">
			<h2 class="section__title"><?php esc_html_e( 'Latest News', 'scout-starter' ); ?></h2>
			<div class="card-grid">
				<?php
				while ( $recent_posts->have_posts() ) :
					$recent_posts->the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endif;

get_footer();
