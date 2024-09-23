<?php
/*

This code is Copyright 2011-2016 by Bruce E. Wampler, all rights reserved.
This code is licensed under the terms of the accompanying license file: license.txt.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

require_once(dirname( __FILE__ ) . '/atw-admin-lib.php'); // NOW - load the admin stuff

function atw_posts_admin_page() {
    atw_posts_submits();
    if ( function_exists('atw_slider_installed')) {
        $name = 'Show Posts (V ' . WEAVER_SHOWPOSTS_VERSION . ') &amp; Show Sliders Plugins (V ' . WEAVER_SLIDER_PI_VERSION . ')';
    } else {
        $name = 'Show Posts Plugin (V ' . WEAVER_SHOWPOSTS_VERSION . ')';
    }
?>

<div class="atw-wrap">
    <h2>Weaver <?php echo $name;?> - Settings <?php atw_donate_button(); ?></h2>
    <hr />

<div id="tabwrap_plus" style="padding-left:5px;">
    <div id="tab-container-plus" class='yetii'>
	<ul id="tab-container-plus-nav" class='yetii'>

    <li><a href="#tab-showposts" title="Show Posts Shortcode"><?php echo(atw_posts_t_('Show Posts' /*a*/ )); ?></a></li>

    <li ><a href="#tab-filters" title="Filters"><?php echo(atw_posts_t_('Filters' /*a*/ )); ?></a></li>

    <li><a href="#mt-tab-slider"  title="Slider"><?php echo(atw_posts_t_('Sliders' /*a*/ )); ?></a></li>

<?php   if (function_exists('atw_slider_installed')) { ?>
    <li><a href="#mt-tab-gallery"  title="[gallery]/Lightbox"><?php echo(atw_posts_t_('[gallery]/Lightbox' /*a*/ )); ?></a></li>
<?php } ?>

    <li ><a href="#tab-css" title="Style"><?php echo(atw_posts_t_('Custom CSS' /*a*/ )); ?></a></li>

    <li ><a href="#tab-help" title="Style"><?php echo(atw_posts_t_('Quick Start Help' /*a*/ )); ?></a></li>

	</ul>
        <hr />

<?php   /* IMPORTANT - in spite of the id's, these MUST be in the correct order - the same as the above list... */
?>

        <!-- ******* -->

        <div id="tab-showposts" class="tab_plus" > <!-- Show Posts -->
<?php
            require_once(dirname( __FILE__ ) . '/atw-posts-showposts-admin.php'); // NOW - load the admin stuff
            atw_posts_showposts_admin();
?>
        </div>

        <!-- ******* -->

        <div id="tab-filters" class="tab_plus" > <!-- Filter -->
<?php
            require_once(dirname( __FILE__ ) . '/atw-posts-filters-admin.php'); // NOW - load the admin stuff
            atw_posts_filters_admin();
?>
        </div>

        <!-- ******* -->

        <div id="mt-tab-slider" class="tab_plus" > <!-- Slider -->
<?php
            require_once(dirname( __FILE__ ) . '/atw-posts-slider-admin.php'); // NOW - load the admin stuff
            atw_posts_slider_admin();

?>
        </div>


 <!-- ******* -->

 <?php   if ( function_exists('atw_slider_installed') && function_exists('atw_slider_gallery_admin') ) { ?>

        <div id="mt-tab-gallery" class="tab_plus" > <!-- [gallery] -->
<?php
        atw_slider_gallery_admin();
?>
        </div>
<?php } ?>

        <!-- ******* -->

        <div id="tab-css" class="tab_plus" > <!-- Custom CSS -->

<?php
            require_once(dirname( __FILE__ ) . '/atw-posts-style-admin.php'); // NOW - load the admin stuff
            atw_posts_style_admin();
?>
        </div>

        <!-- ******* -->

        <div id="tab-help" class="tab_plus" > <!-- Help -->

<?php
            require_once(dirname( __FILE__ ) . '/atw-posts-help-admin.php'); // NOW - load the admin stuff
            atw_posts_help_admin();
?>
        </div>
    </div>

</div> <!-- #tabwrap_plus -->

</div>

<script type="text/javascript">
	var tabber2 = new Yetii({
	id: 'tab-container-plus',
	tabclass: 'tab_plus',
	persist: true
	});
</script>


<?php
} // end atw_posts_admin

// ========================================= FORM DISPLAY ===============================


function atw_posts_t_($s) {
    return $s;
}


function atw_posts_submits() {
    // process settings for plugin parts

    if ( function_exists('atw_slider_installed')) {
        atw_slider_load_admin();
        atw_slider_submits();
    }

    $actions = array('atw_posts_new_filter', 'atw_posts_delete_filter', 'atw_posts_duplicate_filter',
                     'atw_posts_add_post_type', 'atw_posts_add_category_name', 'atw_posts_add_tag', 'atw_posts_add_author',
                     'atw_posts_add_date', 'atw_posts_add_group', 'atw_posts_hide_category_name',

                     'atw_posts_save_filter_opts', 'atw_posts_save_style_opts', 'atw_posts_save_showposts_opts',
					 'atw_posts_restore_filter'
        );

    // need to respond to onchange="this.form.submit()" for 'selected_slider'
    if (atw_posts_get_POST( 'selected_filter')) {
        $new_filter =  atw_posts_get_POST( 'selected_filter');
        $cur_filter =  atw_posts_getopt('current_filter');

        if ($cur_filter != $new_filter) {
            atw_posts_set_to_filter( );
            return;
        }
    }


    foreach ( $actions as $functionName ) {
        if ( isset( $_POST[$functionName] ) ) {
            if ( atw_posts_submitted( $functionName ) && function_exists( $functionName ) ) {
                if ($functionName())
                    break;
            }
        }
    }
}

// ======================== options handlers ==========================
function atw_posts_add_group() {
    $val = sanitize_text_field( atw_posts_get_POST( 'group_selection' ) );
    atw_posts_add_qvalue( 'atw_slider_group', $val );

    $post_type = atw_posts_get_filter_opt('post_type');
    if ($post_type == '') {   // first time
        atw_posts_set_filter_opt( 'post_type', 'post_type=atw_slider_post');
    } else if ( strpos( $post_type,'atw_slider_post' ) === false ) {   // not there yet
        atw_posts_set_filter_opt( 'post_type', $post_type . ',atw_slider_post');    // add it on...
    }

    return true;
}

function atw_posts_add_date() {
    $val = sanitize_text_field( atw_posts_get_POST( 'date_selection' ) );
    atw_posts_set_filter_opt('date', 'date=' . $val);
    return true;
}


function atw_posts_add_author() {
    $val = sanitize_text_field( atw_posts_get_POST( 'author_selection' ) );
    atw_posts_add_qvalue( 'author', $val );
    return true;
}

function atw_posts_add_post_type() {
    $val = sanitize_text_field( atw_posts_get_POST( 'post_type_selection' ) );
    atw_posts_add_qvalue( 'post_type', $val );
    return true;
}

function atw_posts_add_category_name() {
    $val = sanitize_text_field( atw_posts_get_POST( 'category_name_selection' ) );
    atw_posts_add_qvalue( 'category_name', $val );
    return true;
}

function atw_posts_hide_category_name() {
    $val = sanitize_text_field( atw_posts_get_POST( 'category_name_selection' ) );
    atw_posts_add_qvalue( 'category_name', '-' . $val );
    return true;
}

function atw_posts_add_tag() {
    $val = sanitize_text_field( atw_posts_get_POST( 'tag_selection' ) );
    atw_posts_add_qvalue( 'tag', $val );
    return true;
}

function atw_posts_add_qvalue($qarg, $add) {
    if ($add == '')
        return;         // nothing to add
    $cur_arg = atw_posts_get_filter_opt($qarg);
    if ($cur_arg == '') {   // first time
        $val = $qarg . '=' . $add;
        atw_posts_set_filter_opt($qarg,$val);
    } else {
        if ( strpos( $cur_arg, $qarg . '=') !== 0 ) {
            atw_posts_error_msg('Invalid form for query string (' . $qarg . '): ' . $cur_arg . '. Please clear and start over.' );
        } elseif ( strpos( $cur_arg, $add ) !== false && $add != 'post') {
            atw_posts_error_msg('Do not add duplicate values to query string: ' . $add . '. Ignored.');
        } else {
            atw_posts_set_filter_opt($qarg, str_replace(' ', '', $cur_arg . ',' . $add) );
            //atw_posts_save_msg('Filter value field set. Click "Save Filter Options" to save new field value.');
        }
    }
}


function atw_posts_set_to_filter() {
    $selected = sanitize_title_with_dashes( atw_posts_get_POST( 'selected_filter' ) );

    // Validate
    $filters = atw_posts_getopt('filters');
    $found = false;
    foreach ($filters as $filter => $val) {     //  available filters
		if (!isset($filter) || $filter == '') {

		}

        if ($filter == $selected) {
            $found = true;
            break;
        }
    }
    if ( !$found ) {
        atw_posts_error_msg("Filter not found. Try again.");
        return true;
    }

    atw_posts_setopt('current_filter',$selected);
    //$name = atw_posts_get_filter_opt('name');

    //atw_posts_save_msg('Filter field set: ' . $name . '. Click "Save Filter Options" to save settings for this filter.');
    return true;
}

function atw_posts_delete_filter() {

    $selected = sanitize_title_with_dashes( atw_posts_get_POST( 'selected_filter' ) );

    // Validate
    $filters = atw_posts_getopt('filters');
    $found = false;
    foreach ($filters as $filter => $val) {     // display dropdown of available filters
        if ($filter == $selected) {
            $found = true;
            break;
        }
    }
    if ( !$found ) {
        atw_posts_error_msg("Filter not found. Try again.");
        return true;
    }

    atw_posts_delete_filter_opts($selected);
    return true;
}


function atw_posts_duplicate_filter() {
	$name = sanitize_text_field( atw_posts_get_POST ( 'filter_name' ) );
    $slug = sanitize_title_with_dashes($name);
    if ( $name == '' ) {
        atw_posts_error_msg('Please provide a name for the new filter.');
        return true;
    }


	$current_filter = atw_posts_getopt('current_filter');
	global $atw_posts_opts_cache;
	$cur_opts = $atw_posts_opts_cache['filters'][$current_filter];

    atw_posts_setopt('current_filter', $slug);
	$atw_posts_opts_cache['filters'][$slug] = $cur_opts;

    atw_posts_set_filter_opt( 'name', $name);
    atw_posts_set_filter_opt( 'slug', $slug);

	$new_opts = $cur_opts = $atw_posts_opts_cache['filters'][$slug];

    atw_posts_save_msg('Filter Duplicated: "' . $name . '" (Slug: <em>' . $slug . '</em>)');
    return true;
}



function atw_posts_new_filter() {
    $name = sanitize_text_field( atw_posts_get_POST ( 'filter_name' ) );
    $slug = sanitize_title_with_dashes($name);
    if ( $name == '' ) {
        atw_posts_error_msg('Please provide a name for the new filter.');
        return true;
    }
    atw_posts_setopt('current_filter', $slug);
    atw_posts_set_filter_opt( 'name', $name);
    atw_posts_set_filter_opt( 'slug', $slug);
    atw_posts_save_msg('New Filter Created: "' . $name . '" (Slug: <em>' . $slug . '</em>)');
    return true;
}


// ========================================= >>> atw_posts_save_filter_opts <<< ===============================
// *******

function atw_posts_save_filter_opts($show_message = true) {

    //echo '<pre>';print_r($_POST); echo '</pre>';

    // **** text fields and selects

    $text_opts = array (
        'post_type', 'category_name', 'tag', 'author', 'taxonomy', 'date', 'atw_slider_group', 'wp_query_args', 'cols',
        'show', 'orderby', 'order', 'posts_per_page', 'offset', 'more_msg', 'post_slug', 'post_ids', 'excerpt_length',
        'post_template'
    );

    foreach ($text_opts as $opt) {
        $val = sanitize_text_field( atw_posts_get_POST( $opt ) );
        atw_posts_set_filter_opt( $opt, $val );
    }

    if ( atw_posts_get_POST( 'post_template' ) != '' ) {  // template specified
        $val = wp_check_invalid_utf8( trim(atw_posts_get_POST( 'post_template' )) );

        if ( current_user_can('unfiltered_html') ) {
            atw_posts_set_filter_opt('post_template',  $val);
        }
    }

    // **** check boxes
    $check_opts = array (
        'hide_title','hide_top_info','hide_bottom_info','show_avatar', 'use_paging', 'no_top_clear',
        'show_sticky_posts', 'hide_featured_image',
    );

    foreach ($check_opts as $opt) {
        if ( atw_posts_get_POST( $opt ) != '' ) {
            atw_posts_set_filter_opt($opt, true );
        } else {
            atw_posts_set_filter_opt($opt, false );
        }
    }

    // **** "global" check boxes
    $global_check_opts = array (
        'ignore_aspen_weaver', 'use_native_theme_templates'
    );

    foreach ($global_check_opts as $opt) {
        if ( atw_posts_get_POST( $opt ) != '' ) {
            atw_posts_setopt($opt, true );
        } else {
            atw_posts_setopt($opt, false );
        }
    }

    atw_posts_save_all_options();    // and save them to db
    if ( $show_message )
        atw_posts_save_msg('Filter Options saved');
}


function atw_posts_save_style_opts() {
    $css = wp_check_invalid_utf8( trim(atw_posts_get_POST( 'atw_custom_css' )) );
    atw_posts_setopt( 'custom_css', $css );

    atw_posts_save_all_options();    // and save them to db
    atw_posts_save_msg( 'Custom CSS Rules saved' );
}


function atw_posts_save_showposts_opts() {

    if ( atw_posts_get_POST( 'textWidgetShortcodes' ) != '' ) {
            atw_posts_setopt( 'textWidgetShortcodes', true );
        } else {
            atw_posts_setopt( 'textWidgetShortcodes', false );
        }

    atw_posts_save_all_options();    // and save them to db
    atw_posts_save_msg('Show Posts Options saved');
}

// update atw_posts_get_filter_params in atw-runtime-lib...

// #############

function atw_donate_button() {

	if ( true ) {
		$img = WP_CONTENT_URL . '/plugins/show-posts/images/donate-button.png';
	?>
<div style="float:right;padding-right:30px;display:inline-block;"><div style="font-size:14px;font-weight:bold;display:inline-block;vertical-align: top;"><?php wp_kses_post(__('Like <em>Weaver Show Posts</em>? Please', 'weaver-xtreme' /*adm*/)); ?></div>&nbsp;&nbsp;<a href='//weavertheme.com/donate' target='_blank' alt='Please Donate' ><img src="<?php echo $img; ?>" alt="donate" style="max-height:28px;"/></a>
</div>

<?php }
}



?>
