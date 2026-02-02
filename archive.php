<?php
get_header();
?>

	<main id="primary" class="site-main">
		<header class="topic-header">
			<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
			?>
		</header>


		<?php
		if ( have_posts() ) :

			

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php 
                    $auto_img = baidu_pure_get_first_image();
                    if ( has_post_thumbnail() || $auto_img ) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php 
                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( 'medium_large' );
                                } else {
                                    echo '<img src="' . esc_url($auto_img) . '" alt="' . the_title_attribute('echo=0') . '" />';
                                }
                                ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <header class="entry-header">
                            <?php
                            if ( is_singular() ) :
                                the_title( '<h1 class="entry-title">', '</h1>' );
                            else :
                                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                            endif;

                            if ( 'post' === get_post_type() ) :
                                ?>
                                <div class="entry-meta">
                                    <?php
                                    echo '<span class="posted-on">' . esc_html( get_the_date() ) . '</span>';
                                    ?>
                                </div><!-- .entry-meta -->
                            <?php endif; ?>
                        </header><!-- .entry-header -->

                        <div class="entry-content">
                            <?php
                            if ( is_singular() ) {
                                the_content();
                            } else {
                                the_excerpt();
                            }
                            ?>
                        </div><!-- .entry-content -->
                    </div>
				</article><!-- #post-<?php the_ID(); ?> -->
				<?php

			endwhile;

			the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __( 'Previous', 'baidu-pure' ), 'next_text' => __( 'Next', 'baidu-pure' ) ) );

		else :

			?>
			<section class="no-results not-found">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'baidu-pure' ); ?></h1>
				</header>
			</section>
			<?php

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
