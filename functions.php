<?php
/**
 * Baidu Pure Theme functions and definitions
 */

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.1.0' );
}

if ( ! function_exists( 'baidu_pure_setup' ) ) :
	function baidu_pure_setup() {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		register_nav_menus( array( 'primary' => esc_html__( 'Primary Menu', 'baidu-pure' ) ) );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
        
        // Register Sidebar
        register_sidebar( array(
            'name'          => esc_html__( 'Sidebar', 'baidu-pure' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'baidu-pure' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );
	}
endif;
add_action( 'after_setup_theme', 'baidu_pure_setup' );

function baidu_pure_scripts() {
	wp_enqueue_style( 'baidu-pure-style', get_stylesheet_uri(), array(), _S_VERSION );
    
    // Enqueue main.js
    wp_enqueue_script( 'baidu-pure-script', get_template_directory_uri() . '/js/main.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    // No jQuery or external scripts for 100/100 performance
}
add_action( 'wp_enqueue_scripts', 'baidu_pure_scripts' );

/**
 * ============================================================================
 * 1. THEME OPTIONS PAGE (Baidu Settings)
 * ============================================================================
 */
function baidu_pure_add_admin_menu() {
	add_menu_page( '百度 SEO 设置', '百度 SEO', 'manage_options', 'baidu_pure_options', 'baidu_pure_options_page', 'dashicons-chart-area', 60 );
}
add_action( 'admin_menu', 'baidu_pure_add_admin_menu' );

function baidu_pure_settings_init() {
	register_setting( 'baiduPlugin', 'baidu_api_token' );
	register_setting( 'baiduPlugin', 'baidu_site_url' );
	register_setting( 'baiduPlugin', 'auto_link_rules' ); // Format: keyword|url (one per line)
}
add_action( 'admin_init', 'baidu_pure_settings_init' );

function baidu_pure_options_page() {
	?>
	<div class="wrap">
        <h1>百度 SEO 设置 (Baidu SEO)</h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'baiduPlugin' );
			do_settings_sections( 'baiduPlugin' );
			?>
			<table class="form-table">
                <tr>
                    <th scope="row">站点域名 (Site URL)</th>
                    <td>
                        <input type="text" name="baidu_site_url" value="<?php echo esc_attr( get_option( 'baidu_site_url', home_url() ) ); ?>" class="regular-text" />
                        <p class="description">请填写您在百度站长平台注册的完整域名 (例如：https://www.sqjnqi.com)。</p>
                    </td>
                </tr>
				<tr>
					<th scope="row">百度准入密钥 (API Token)</th>
					<td>
                        <input type="password" name="baidu_api_token" value="<?php echo esc_attr( get_option( 'baidu_api_token' ) ); ?>" class="regular-text" />
                        <p class="description">请从 <a href="https://ziyuan.baidu.com/" target="_blank">百度搜索资源平台</a> 获取您的 API 推送密钥。</p>
                    </td>
				</tr>
                <tr>
                    <th scope="row">自动内链规则</th>
                    <td>
                        <textarea name="auto_link_rules" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( get_option( 'auto_link_rules' ) ); ?></textarea>
                        <p class="description">格式：<code>关键词|目标链接</code> (每行一条规则)。<br>例如：<code>WordPress|https://cn.wordpress.org</code><br>文章内容中出现的关键词将自动添加链接。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">每日推送测试</th>
                    <td>
                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=baidu_pure_options&baidu_run_daily_push=1' ), 'baidu_manual_push' ); ?>" class="button button-secondary">立即执行每日推送</a>
                        <p class="description">手动触发一次“普通收录”推送（随机抓取 20 篇已发布文章）。结果请查看 <code>debug.log</code>。</p>
                    </td>
                </tr>
			</table>
			<?php submit_button('保存设置'); ?>
		</form>
	</div>
	<?php
}

/**
 * ============================================================================
 * 2. BAIDU ACTIVE PUSH (主动推送)
 * ============================================================================
 */
function baidu_pure_active_push( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    
    // Check if transitioning to publish or updating published post
    $post_status = get_post_status( $post_id );
    if ( 'publish' !== $post_status ) return;

    $api_token = get_option( 'baidu_api_token' );
    $site_url  = get_option( 'baidu_site_url' );
    
    if ( ! $api_token || ! $site_url ) return;

    $api_url = "http://data.zz.baidu.com/urls?site=$site_url&token=$api_token";
    $permalink = get_permalink( $post_id );

    $response = wp_remote_post( $api_url, array(
        'headers' => array( 'Content-Type' => 'text/plain' ),
        'body'    => $permalink
    ) );

    // Log for debugging
    if ( is_wp_error( $response ) ) {
        error_log( 'Baidu Push Network Error: ' . $response->get_error_message() );
        return;
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( 200 !== $response_code ) {
        error_log( "Baidu Push API Error ($response_code): " . ($data['message'] ?? $body) );
    } else {
        error_log( "Baidu Push Success! Remain: " . ($data['remain'] ?? 'unknown') );
    }
}
add_action( 'publish_post', 'baidu_pure_active_push' );

/**
 * ============================================================================
 * 3. BAIDU SEO OPTIMIZATIONS
 * ============================================================================
 */

// Title Separator: Baidu prefers "_"
function baidu_pure_title_separator( $sep ) {
    return '_';
}
add_filter( 'document_title_separator', 'baidu_pure_title_separator' );

// Auto-Internal Linking Filter
function baidu_pure_auto_link_content( $content ) {
    $rules_raw = get_option( 'auto_link_rules' );
    if ( ! $rules_raw ) return $content;

    $lines = explode( "\n", $rules_raw );
    foreach ( $lines as $line ) {
        $parts = explode( '|', trim( $line ) );
        if ( count( $parts ) == 2 ) {
            $keyword = trim( $parts[0] );
            $url     = trim( $parts[1] );
            if ( $keyword && $url ) {
                // Replace only first occurrence, avoid replacing inside tags
                $pattern = '/(?!(?:[^<]+>|[^>]+<\/a>))\b(' . preg_quote( $keyword, '/' ) . ')\b/iu';
                $content = preg_replace( $pattern, '<a href="' . esc_url( $url ) . '" title="$1">$1</a>', $content, 1 );
            }
        }
    }
    return $content;
}
add_filter( 'the_content', 'baidu_pure_auto_link_content' );

// SEO Meta Tags & JSON-LD
function baidu_pure_seo_meta() {
    global $post;
    
    // Meta Description
    $description = '';
    if ( is_single() || is_page() ) {
        $description = get_the_excerpt();
        if ( ! $description ) {
             $description = wp_trim_words( $post->post_content, 50 );
        }
    } else {
        $description = get_bloginfo( 'description' );
    }
    // Clean description: remove newlines, tabs, and tags
    $description = strip_tags( $description );
    $description = str_replace( array( "\r", "\n", "\t" ), '', $description );
    $description = esc_attr( $description );
    
    if ( $description ) {
        echo '<meta name="description" content="' . $description . '">' . "\n";
    }

    // Keywords (New)
    if ( is_single() ) {
        $tags = get_the_tags();
        if ( $tags ) {
            $keywords = array();
            foreach( $tags as $tag ) {
                $keywords[] = $tag->name;
            }
            echo '<meta name="keywords" content="' . esc_attr( implode( ',', $keywords ) ) . '">' . "\n";
        }
    }

    // Applicable Device (Mobile Friendly)
    echo '<meta name="applicable-device" content="pc,mobile">' . "\n";

    // Canonical
    echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />' . "\n";

    // JSON-LD (Schema.org)
    if ( is_single() ) {
        ?>
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Article",
          "headline": "<?php the_title(); ?>",
          "datePublished": "<?php echo get_the_date( 'c' ); ?>",
          "dateModified": "<?php echo get_the_modified_date( 'c' ); ?>",
          "author": {
            "@type": "Person",
            "name": "<?php the_author(); ?>"
          }
        }
        </script>
        <?php
    }
}
add_action( 'wp_head', 'baidu_pure_seo_meta', 5 );

/**
 * ============================================================================
 * 4. MODULES
 * ============================================================================
 */
require get_template_directory() . '/inc/sitemap.php';
require get_template_directory() . '/inc/spider.php';
require get_template_directory() . '/inc/image-seo.php';
require get_template_directory() . '/inc/daily-push.php';

/**
 * Get first image from post content (Fixed manually)
 */
function baidu_pure_get_first_image() {
    global $post;
    $first_img = '';
    if ( !empty($post->post_content) ) {
        // Use a broader regex to catch src attributes
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        if(isset($matches[1][0])) {
            $first_img = $matches[1][0];
        }
    }
    
    if(empty($first_img)){
        return false;
    }
    return $first_img;
}

function baidu_pure_localize_comment_form($defaults) {
    $defaults['title_reply'] = '发表评论';
    $defaults['title_reply_to'] = '回复给 %s';
    $defaults['cancel_reply_link'] = '取消回复';
    $defaults['label_submit'] = '提交评论';
    return $defaults;
}
add_filter('comment_form_defaults', 'baidu_pure_localize_comment_form');
