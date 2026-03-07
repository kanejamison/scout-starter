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

	$btn1_label = get_post_meta( get_the_ID(), '_scout_hero_btn1_label', true );
	$btn1_url   = get_post_meta( get_the_ID(), '_scout_hero_btn1_url',   true );
	$btn1_bg    = get_post_meta( get_the_ID(), '_scout_hero_btn1_bg',    true ) ?: 'var(--color-accent)';
	$btn1_text  = get_post_meta( get_the_ID(), '_scout_hero_btn1_text',  true ) ?: '#ffffff';
	$btn2_label = get_post_meta( get_the_ID(), '_scout_hero_btn2_label', true );
	$btn2_url   = get_post_meta( get_the_ID(), '_scout_hero_btn2_url',   true );
	$btn2_bg    = get_post_meta( get_the_ID(), '_scout_hero_btn2_bg',    true ) ?: 'transparent';
	$btn2_text  = get_post_meta( get_the_ID(), '_scout_hero_btn2_text',  true ) ?: '#ffffff';

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
				<?php
				$has_buttons = ( $btn1_label && $btn1_url ) || ( $btn2_label && $btn2_url );
				if ( $has_buttons ) {
					?>
					<div class="hero__actions">
						<?php if ( $btn1_label && $btn1_url ) {
							$btn1_class = 'btn' . ( 'transparent' === $btn1_bg ? ' btn--outline' : '' );
							printf(
								'<a href="%s" class="%s" style="background-color:%s;color:%s;">%s</a>',
								esc_url( $btn1_url ),
								esc_attr( $btn1_class ),
								esc_attr( $btn1_bg ),
								esc_attr( $btn1_text ),
								esc_html( $btn1_label )
							);
						} ?>
						<?php if ( $btn2_label && $btn2_url ) {
							$btn2_class = 'btn' . ( 'transparent' === $btn2_bg ? ' btn--outline' : '' );
							printf(
								'<a href="%s" class="%s" style="background-color:%s;color:%s;">%s</a>',
								esc_url( $btn2_url ),
								esc_attr( $btn2_class ),
								esc_attr( $btn2_bg ),
								esc_attr( $btn2_text ),
								esc_html( $btn2_label )
							);
						} ?>
					</div>
					<?php
				}
				?>
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
