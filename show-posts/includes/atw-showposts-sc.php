<?php
// ====================================== >>> atw_show_posts_shortcode <<< ======================================

function atw_show_posts_shortcode($args = '')
{
    /* implement [weaver_show_posts]  */

    /* DOC NOTES:
    CSS styling: The group of posts will be wrapped with a <div> with a class called
    .wvr-show-posts. You can add an additional class to that by providing a 'class=classname' option
    (without the leading '.' used in the actual CSS definition). You can also provide inline styling
    by providing a 'style=value' option where value is whatever styling you need, each terminated
    with a semi-colon (;).

    The optional header is in a <div> called .wvr_show_posts_header. You can add an additional class
    name with 'header_class=classname'. You can provide inline styling with 'header_style=value'.

    .wvr-show-posts .hentry {margin-top: 0px; margin-right: 0px; margin-bottom: 40px; margin-left: 0px;}
    .widget-area .wvr-show-posts .hentry {margin-top: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;}
    */

    $opts = array(
        /* formatting options */
        'cols' => '1',   // display posts in 1 to 3 columns
        'excerpt_length' => '',    // excerpt length
        'hide_bottom_info' => '',    // hide bottom info line
        'hide_featured_image' => '',    // hide featured image - FI is displayed by default
        'hide_title' => '',    // hide the title?
        'hide_top_info' => '',    // hide the top info line
        'show' => '',    // show: title | excerpt | full | titlelist | title_featured
        'show_avatar' => false, // show the author avatar
        'more_msg' => '',    // replacement for Continue Reading excerpt message
        'use_paging' => false, // Use paging when displaying multiple posts
        'no_top_clear' => false  // prevent emitting clear:both
    );


    $slider = '';
    $filter = '';
    if (isset($args['slider'])) {
        $slider = $args['slider'];
        unset($args['slider']);

        if (!function_exists('atw_slider_installed')) {
            return '<strong>ERROR with [show_posts slider="' . $slider . '"]: Weaver Slider Plugin not installed.</strong>';
        }

        if ($slider == '' || atw_posts_get_slider_opt('name', $slider) == '') {
            return '<strong>ERROR with [show_posts slider="' . $slider . '"]: You must specify a valid slider name.</strong>';
        }

        $filter = atw_posts_get_slider_opt('selected_slider_filter', $slider);
        if ($filter == '') {
            $filter = 'default';
        }

        if (atw_posts_get_filter_opt('slug', $filter) != $filter) {
            return '<strong>ERROR with [show_posts slider="' . $slider . '"]: Filter (' . $filter . ') is not a defined filter.</strong>';
        }
        $params = atw_posts_get_filter_params($filter);
        if ($params != '') {        // they specified a $filter via slider, so wipe out everything else
            unset($args);
            $args = shortcode_parse_atts($params);
            $args['use_paging'] = false;         // use_paging breaks sliders
        } else {
            $filter = '';
        }
    } elseif (isset($args['filter'])) {
        $filter = $args['filter'];
        $params = atw_posts_get_filter_params($filter);
        if ($params != '') {        // they specified a $filter arg, so use it and wipe out anything else...
            unset($args);
            $args = shortcode_parse_atts($params);
        } else {
            $filter = '';
        }
    }


    $qargs = atw_posts_get_qargs($args, $opts);

    extract(shortcode_atts($opts, $args));  // setup local vars

    if ($show == 'titlelist' && $slider) {
        $show = 'title';
    }                    // cheap fix...

    // set transient opts for these options

    atw_trans_set('showposts', true);    // global to see if we are in this function

    atw_trans_set('show', $show);        // this will always be set - but '' (blank) implies 'full' for built-in, but not Weaver/Aspen settings for them

    if ($hide_title != '') {
        atw_trans_set('hide_title', true);
    }
    if ($hide_top_info != '') {
        atw_trans_set('hide_top_info', true);
    }
    if ($hide_bottom_info != '') {
        atw_trans_set('hide_bottom_info', true);
    }
    if ($hide_featured_image != '') {
        atw_trans_set('hide_featured_image', true);
    }
    if (isset($args['show_avatar'])) {     // need this weirdness for Aspen/Weaver compatibility (not set means use global setting)
        if ($show_avatar) {
            atw_trans_set('show_avatar', true);
        } else {
            atw_trans_set('show_avatar', 'no');
        }
    }
    if ($more_msg != '') {
        atw_trans_set('more_msg', $more_msg);
    }


    $ourposts = new WP_Query(apply_filters('atw_show_posts_wp_query', $qargs, $args));

    /* now start the content */
    $class = '';
    if ($filter != '') {
        $class = ' atw-show-posts-filter-' . $filter;
    }

    $content = '';
    $tail = '';

    if ($slider == '') {
        $content = '';
        if (!$no_top_clear) {
            $content = '<div style="clear:both;"></div>';
        }
        $content .= '<div class="atw-show-posts' . $class . '">';
        $tail = "</div><!-- show_posts -->\n";
    }

    ob_start();

    if ($slider && function_exists('atw_slider_installed') && atw_posts_get_slider_opt('content_type', $slider) == 'images') {
        atw_slider_do_gallery($ourposts, $slider);
        // reset stuff
        wp_reset_query();
        wp_reset_postdata();
        atw_trans_clear_all();
        $content .= ob_get_clean();    // get the output

        return $content;
    }

    $slide_li_begin = '';
    $slide_li_end = '';

    if ($slider) {
        $style = '';

        $slide_li_begin = '<div class="atwk-slide"><div class="slide-content slide-post"' . $style . '>' . "\n";
        $slide_li_end = "\n</div></div><!-- slide-content slide-post -->\n";
    }

    // add excerpt filter here
    if ($excerpt_length != '') {
        $GLOBALS['atw_show_posts_excerpt_length'] = $excerpt_length;
        add_filter('excerpt_length', 'atw_posts_excerpt_length_filter', 20);   // user our excerpt filter early to override others
    }


    if ($show == 'titlelist') {
        echo '<ul>';
    }

    $posts_out = 0;
    $col = 0;
    if (!$ourposts->have_posts()) {
        echo apply_filters('wvr_show_posts_no_posts', esc_html__('No posts found.', 'atw_showposts'));
    }

    if (WEAVER_SHOWPOSTS_TEMPLATE && atw_posts_get_filter_opt('post_template', $filter)) {
        require_once(dirname(__FILE__) . '/atw-posts-template.php');
    } // NOW - load the template code

    while ($ourposts->have_posts()) {
        $ourposts->the_post();
        $posts_out++;

        echo $slide_li_begin;

        // aspen_per_post_style();
        if ($show == 'titlelist') {
            ?>
            <li><a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr(__('Permalink to %s', 'show-posts')),
                    the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title(); ?></a></li>
            <?php
        } else {
            switch ($cols) {
                case 2:
                    $col++;
                    $style = '';
                    if (($col % 2) == 1) {    // force stuff to be even
                        $style = ' style="clear:left;"';
                    }
                    echo('<div class="atw-content-2-col atw-cf"' . $style . '>' . "\n");
                    atw_show_content($slider, $filter);
                    echo("</div> <!-- atw-content-2-col -->\n");

                    break;
                case 3:
                    $col++;
                    $style = '';
                    if (($col % 3) == 1) {    // force stuff to be even
                        $style = ' style="clear:left;"';
                    }
                    echo('<div class="atw-content-3-col atw-cf"' . $style . '>' . "\n");
                    atw_show_content($slider, $filter);
                    echo("</div> <!-- atw-content-3-col -->\n");

                    break;
                case 1:
                default:
                    atw_show_content($slider, $filter);
                    break;
            }    // end switch $cols
        }

        echo $slide_li_end;

    } // end loop
    if ($show == 'titlelist') {
        echo "</ul>\n";
    }

    // unhook excerpt filter here
    if ($excerpt_length != '') {
        unset($GLOBALS['atw_show_posts_excerpt_length']);
        remove_filter('excerpt_length', 'atw_posts_excerpt_length_filter', 20);   // user our excerpt filter early to override others
    }

    if ($use_paging) {
        if (!$no_top_clear) {
            echo '<div style="clear:both;"></div>';
        }

        ?>
        <div id="atw-show-posts-navigation" class="atw-post-nav">
            <?php
            $big = 999999;
            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, $qargs['paged']),
                'total' => $ourposts->max_num_pages,
            ));
            ?>
        </div>
        <?php
    }
    $content .= ob_get_clean();    // get the output

    // get posts

    $content .= $tail;


    // reset stuff
    wp_reset_query();
    wp_reset_postdata();
    atw_trans_clear_all();

    return $content;
}

// =================================== >>> atw_posts_excerpt_length_filter <<< =========================

function atw_posts_excerpt_length_filter($length)
{
    $val = $GLOBALS['atw_show_posts_excerpt_length'];
    if ($val > 0 || $val === '0') {
        return $val;
    }
    if ($length != 0) {
        return $length;
    } else {
        return 40;
    }
}

// ====================================== >>> atw_show_content <<< ======================================

function atw_show_content($slider, $filter = '')
{

    $cur_post_id = get_the_ID();

    /* We have to do our own sticky processing because WP is_sticky() will not work because we are in our own WP_Query,
     * and will thus never be on the home page which is one of the tests in the core is_sticky()
     */

    $sticky = is_sticky($cur_post_id);

    do_action('atw_show_sliders_post_pager', $slider);

    $saved_the_content_filter_key = atw_save_the_content_filters();

    if ((!atw_posts_getopt('ignore_aspen_weaver') && (atw_posts_is_wvrx() || atw_posts_is_wii()))
        || (atw_posts_getopt('use_native_theme_templates') && atw_posts_theme_has_templates())
    ) {

        if ($sticky) {
            echo '<div class="sticky">';
        }

        if (atw_posts_is_wvrx()) {
            get_template_part('templates/content', get_post_format());
        } elseif (function_exists('twentysixteen_setup')) {            // custom support for twentysixteen
            get_template_part('template-parts/content', get_post_format());
        } else {
            get_template_part('content', get_post_format());
        }

        if ($sticky) {
            echo '</div>';
        }
        echo "<div style='clear:both;'></div>\n";
        atw_restore_the_content_filters($saved_the_content_filter_key);

        return;
    }

    if (WEAVER_SHOWPOSTS_TEMPLATE) {
        $template = atw_posts_get_filter_opt('post_template', $filter);

        if ($template != '') {
            atw_posts_do_template($slider, $template);
            atw_restore_the_content_filters($saved_the_content_filter_key);

            return;
        }
    }


    $add_class = 'atw-post';
    if ($sticky) {
        $add_class .= ' sticky';
    }
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class($add_class); ?>>
        <header class="atw-entry-header">
            <?php
            if (!atw_trans_get('hide_title')) {        // ========== TITLE
                ?>
                <hgroup class="atw-entry-hdr"><h2 class="atw-entry-title">
                        <a href="<?php the_permalink(); ?>"
                           title="<?php printf(esc_attr(__('Permalink to %s', 'show-posts')),
                               the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title(); ?></a>
                    </h2></hgroup>

                <?php
            }

            if (!atw_trans_get('hide_top_info')) {    // ============ TOP META
                ?>
                <div class="atw-entry-meta">
                    <div class="atw-entry-meta-icons">
                        <?php

                        printf(__('<span class="entry-date"><a href="%1$s" title="%2$s" rel="bookmark"><time datetime="%3$s" pubdate>%4$s</time></a></span> <span class="by-author"><span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'show-posts'),
                            esc_url(get_permalink()),
                            esc_attr(get_the_time()),
                            esc_attr(get_the_date('c')),
                            esc_html(get_the_date()),
                            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                            esc_attr(sprintf(__('View all posts by %s', 'show-posts'), get_the_author())),
                            esc_html(get_the_author())
                        );

                        if (atw_trans_get('show_avatar') != '' && atw_trans_get('show_avatar') != 'no') {
                            echo '&nbsp;&nbsp;' . get_avatar(get_the_author_meta('user_email'), 22, null, 'avatar');
                        }
                        ?>
                    </div><!-- .atw-entry-meta-icons -->
                </div><!-- .atw-entry-meta -->
                <?php
            }
            ?>
        </header><!-- .atw-entry-header -->
        <?php
        if (atw_trans_get('show') == 'title') {
            echo '</article><!-- #post-' . get_the_ID() . '-->';
            atw_restore_the_content_filters($saved_the_content_filter_key);

            return;
        }

        if (atw_trans_get('show') == 'title_featured') {

            if (get_post_thumbnail_id()) {
                //$image = wp_get_attachment_image_src( get_post_thumbnail_id( ), 'thumbnail' );        // (url, width, height)
                //$href = $image[0];
                $href = get_permalink();
                ?>
                <p class='atw-featured-image'><a
                            href="<?php echo $href; ?>"><?php esc_url(the_post_thumbnail('thumbnail')); ?></a></p>
                <?php
            }
            echo '</article><!-- #post-' . get_the_ID() . '-->';
            atw_restore_the_content_filters($saved_the_content_filter_key);

            return;
        }

        if (atw_trans_get('show') == 'excerpt') { // =================== EXCERPT
            ?>
            <div class="atw-entry-summary atw-cf">
                <?php
                atw_show_post_content($slider);
                ?>
            </div><!-- .atw-entry-summary -->
            <?php
        } else {                // ================== FULL CONTENT
            ?>
            <div class="atw-entry-content atw-cf">
                <?php
                atw_show_post_content($slider);
                ?>
            </div><!-- .atw-entry-content -->
            <?php
        }

        if (!atw_trans_get('hide_bottom_info')) {    // ================= BOTTOM META
            ?>

            <footer class="atw-entry-utility">
                <div class="atw-entry-meta-icons">
                    <?php
                    $categories_list = get_the_category_list(esc_html__(', ', 'show-posts'));
                    if ($categories_list) { ?>
                        <span class="cat-links">
<?php
echo $categories_list;
?>
		    </span>
                        <?php
                    } // End if categories
                    $tags_list = get_the_tag_list('', esc_html__(', ', 'show-posts'));
                    if ($tags_list) {
                        ?>
                        <span class="tag-links">
<?php
echo $tags_list;
?>
			</span>
                        <?php
                    } // End if $tags_list
                    if (comments_open()) {
                        ?>
                        <span class="comments-link">
<?php
wp_kses_post(comments_popup_link(__('Leave a reply', 'show-posts'),
    __('<b>1</b> Reply', 'show-posts'),
    __('<b>%</b> Replies', 'show-posts'),
    'leave-reply'));
?>

		    </span>
                        <div style="clear:both;"></div>
                        <?php
                    } // End if comments_open()
                    ?>
                </div><!-- .entry-meta-icons -->
            </footer><!-- .atw-entry-utility -->
            <?php
        }
        edit_post_link(esc_html__('Edit', 'show-posts'), '<span class="atw-edit-link">', '</span>');
        ?>
    </article><!-- #post-<?php the_ID(); ?> -->

    <?php
    atw_restore_the_content_filters($saved_the_content_filter_key);
}

function atw_save_the_content_filters()
{
    global $wp_filter, $wp_current_filter;

    $tag = 'the_content';
    if (empty($wp_filter) || empty($wp_current_filter) || !in_array($tag, $wp_current_filter)) {
        return false;
    }

    return key($wp_filter[$tag]);
}

function atw_restore_the_content_filters($key = false)
{
    global $wp_filter;
    $tag = 'the_content';
    if ($key !== false) {
        reset($wp_filter[$tag]);
        while (key($wp_filter[$tag]) !== $key) {
            if (next($wp_filter[$tag]) === false) {
                break;
            }
        }
    }
}

// ====================================== >>> atw_show_post_content <<< ======================================

function atw_show_post_content($slider)
{
    // display a post - show thumbnail, link to full size image
    if (!atw_trans_get('hide_featured_image') && get_post_thumbnail_id()) {
        //$image = wp_get_attachment_image_src( get_post_thumbnail_id( ), 'full' );        // (url, width, height)
        //$href = $image[0];
        $href = get_permalink();
        ?>
        <span class='atw-featured-image'><a
                    href="<?php echo $href; ?>"><?php the_post_thumbnail('thumbnail'); ?></a></span>
        <?php
    }

    $content = '';

    if ($slider && function_exists('atw_slider_set_pager_image')) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');        // (url, width, height)
        $href = $image[0];
        if (!$href) {
            $content = get_the_content($more);
        }

    }

    $more = atw_trans_get('more_msg');
    if ($more == '') {
        $more = 'Continue Reading...';
    }      // we always want to show continue reading even if theme sets it to ''

    if (atw_trans_get('show') == 'excerpt') {
        the_excerpt($more);
    } elseif ($content != '') {
        echo $content;
    } else {
        // atw_show_post_the_content( $more );
        atw_show_post_the_content($more);

    }
}

// ====================================== >>> atw_show_post_content <<< ======================================
function atw_show_post_the_content($more)
{
    // use this to support nested the_content filters - slight modification of the WP the_content()

    $content = get_the_content($more, false);

    $content = do_shortcode($content); // try applying shortcodes before the_content filter

    $content = apply_filters('the_content', $content);
    //$content = str_replace( ']]>', ']]&gt;', $content );
    echo $content;
}
