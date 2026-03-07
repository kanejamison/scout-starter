<?php
/**
 * Page template.
 *
 * Handles both standard pages and pages with the hero section enabled.
 * Hero settings are set per-page via the "Hero Section" meta box in the editor.
 *
 * @package Scout_Starter
 */

get_header();

while ( have_posts() ) {
	the_post();

	$hero_enabled  = (bool) get_post_meta( get_the_ID(), '_scout_hero_enabled', true );
	$show_excerpt  = get_post_meta( get_the_ID(), '_scout_hero_show_excerpt', true );
	$show_excerpt  = ( '' === $show_excerpt ) ? true : (bool) $show_excerpt;
	$thumbnail_url = get_the_post_thumbnail_url( null, 'full' );
	$excerpt       = get_the_excerpt();

	if ( $hero_enabled ) {
		$hero_classes = 'hero' . ( $thumbnail_url ? ' hero--has-image' : '' );
		$hero_style   = $thumbnail_url ? ' style="background-image: url(' . esc_url( $thumbnail_url ) . ');"' : '';
		?>
		<section class="<?php echo esc_attr( $hero_classes ); ?>"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<div class="container">
				<h1 class="hero__title"><?php the_title(); ?></h1>
				<?php if ( $show_excerpt && $excerpt ) { ?>
					<p class="hero__subtitle"><?php echo esc_html( $excerpt ); ?></p>
				<?php } ?>
			</div>
		</section>
		<?php
	}
	?>

	<div class="section">
		<div class="container">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( ! $hero_enabled ) { ?>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>
				<?php } ?>

				<div class="entry-content">
					<?php
					the_content();

					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'scout-starter' ),
						'after'  => '</div>',
					) );
					?>
				</div>
			</article>
		</div>
	</div>

	<?php
}

// Recent posts section — front page only, when enabled in Customizer.
if ( is_front_page() ) {
	$news_enabled = get_theme_mod( 'scout_news_enabled', false );
	$news_heading = get_theme_mod( 'scout_news_heading', __( 'Latest News', 'scout-starter' ) );

	$recent_posts = $news_enabled ? new WP_Query( array(
		'posts_per_page'      => 3,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	) ) : false;

	if ( $news_enabled && $recent_posts && $recent_posts->have_posts() ) {
		?>
		<section class="section section--alt">
			<div class="container">
				<h2 class="section__title"><?php echo esc_html( $news_heading ); ?></h2>
				<div class="card-grid">
					<?php
					while ( $recent_posts->have_posts() ) {
						$recent_posts->the_post();
						get_template_part( 'template-parts/content', 'card' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	}
}

get_footer();
