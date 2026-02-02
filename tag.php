<?php
/**
 * The template for displaying Tag pages with a specialized Topic layout.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Baidu_Pure
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header topic-header">
				<h1 class="page-title"><?php single_tag_title(); ?></h1>
				<?php
				$description = tag_description();
				if ( $description ) :
					?>
					<div class="archive-description"><?php echo $description; ?></div>
				<?php endif; ?>
			</header><!-- .page-header -->

            <?php
            // Section 1: Editor's Choice (Top 3 Commented posts in this tag)
            $tag_id = get_queried_object_id();
            $hot_query = new WP_Query( array(
                'tag_id'         => $tag_id,
                'posts_per_page' => 3,
                'orderby'        => 'comment_count',
                'order'          => 'DESC',
                'post_status'    => 'publish',
            ) );

            if ( $hot_query->have_posts() ) :
                ?>
                <section class="topic-hot-posts">
                    <h2 class="section-title"><?php esc_html_e( '精选推荐', 'baidu-pure' ); ?></h2>
                    <div class="hot-posts-grid">
                        <?php
                        while ( $hot_query->have_posts() ) :
                            $hot_query->the_post();
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class( 'hot-post-card' ); ?>>
                                <header class="entry-header">
                                    <?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
                                </header>
                                <div class="entry-summary">
                                    <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </section>
                <?php
            endif;
            ?>

            <section class="topic-latest-updates">
                <h2 class="section-title"><?php esc_html_e( '最新动态', 'baidu-pure' ); ?></h2>
                <?php
                /* Start the Loop */
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                            <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

                            <div class="entry-meta">
                                <?php echo '<span class="posted-on">' . esc_html( get_the_date() ) . '</span>'; ?>
                            </div>
                        </header>

                        <div class="entry-content">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                    <?php
                endwhile;

                the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => '上一页', 'next_text' => '下一页' ) );
                ?>
            </section>

		<?php else : ?>

			<section class="no-results not-found">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( '未找到相关内容', 'baidu-pure' ); ?></h1>
				</header>
			</section>

		<?php endif; ?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
