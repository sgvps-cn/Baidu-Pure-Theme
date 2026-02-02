<?php
get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<div class="entry-meta">
						<span class="posted-on"><?php echo esc_html( get_the_date() ); ?></span>
						<span class="byline"> <?php the_author(); ?></span>
					</div>
				</header>

				<div class="entry-content">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'baidu-pure' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>
			
                <footer class="entry-footer" style="padding: 0 1.5rem 1.5rem 1.5rem; margin-top: -1rem; display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: flex-start;">
                    <?php
                    $tags_list = get_the_tag_list( '', '');
                    if ( $tags_list ) {
                        echo '<span style="font-weight:bold; color:var(--color-secondary); font-size:0.9rem; margin-right:0.2rem; align-self:center;">标签：</span>';
                        echo '<div class="tags-links">';
                        echo $tags_list;
                        echo '</div>';
                    }
                    ?>
                </footer><!-- .entry-footer -->
</article>

			<?php
			// Previous/next post navigation.
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">上一篇</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">下一篇</span> <span class="nav-title">%title</span>',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			comments_template();

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
