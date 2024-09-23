<?php
/*
Plugin Name: Weaver Show Posts
Plugin URI: http://WeaverTheme.com
Description: Weaver Show Posts - Show  posts or custom posts within your Theme's pages or posts using a shortcode and a form-based interface.
Author: wpweaver
Author URI: http://weavertheme.com/about/
Version: 1.8.1

License: GPL

Weaver Show Posts
Copyright (C) 2014-2023, Bruce E. Wampler - weaver@weavertheme.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/* CORE FUNCTIONS
*/

const WEAVER_SHOWPOSTS_VERSION = '1.8.1';
const WEAVER_SHOWPOSTS_MINIFY = '.min';        // '' for dev, '.min' for production
const WEAVER_SHOWPOSTS_TEMPLATE = false;      // future feature

// ===============================>>> REGISTER ACTIONS <<<===============================

add_action( 'plugins_loaded', 'atw_posts_plugins_loaded');

    function atw_posts_plugins_loaded() {

        function atw_showposts_installed() {
        return true;
    }

    add_action( 'media_buttons', 'atw_posts_add_form_buttons', 20 );
    add_action('admin_menu', 'atw_posts_admin_menu');
    add_action('wp_enqueue_scripts', 'atw_posts_enqueue_scripts' );
    add_action('template_redirect', 'atw_posts_emit_css' );
    add_action('init', 'atw_posts_setup_shortcodes');  // allow shortcodes to load after theme has loaded so we know which version to use
}

// ===============================>>> DEFINE ACTIONS <<<===============================


/** --------------------------------------------------------------------------------------------
* Add the Weaver Slider button to the post editor
*/

function atw_posts_add_form_buttons(){
    $page = is_admin() ? get_current_screen() : null;

    if(  isset($page) && $page-> id!= 'atw_slider_post'  ) {
        echo '<a href="#TB_inline?width=400&height=300&inlineId=select-show-posts-dialog" class="thickbox button" id="add_atw_posts_posts" title="' . esc_html__("Add [show_posts]", 'show-posts') . '"><span class="dashicons dashicons-admin-post"></span> ' . __("Add [show_posts]", 'show-posts') . '</a>';
        add_action( 'admin_footer', 'atw_posts_select_posts_form' );
    }

    if ( function_exists( 'atw_slider_installed') && isset($page) && $page->id != 'atw_slider_post' ) {
        echo '<a href="#TB_inline?width=400&height=300&inlineId=select-show-sliders-dialog" class="thickbox button" id="add_atw_slider_slidrs" title="' . esc_html__("Add [show_slider]", 'atw-slider') . '"><span class="dashicons dashicons-images-alt"></span></span> ' . __("Add [show_slider]", 'show-posts') . '</a>';
        add_action( 'admin_footer', 'atw_posts_select_slider_form' );
    }
}

/**
* Displays the Insert a [show_posts] Selector
*/
function atw_posts_select_posts_form() {
    atw_posts_select_scripts_and_styles();
?>
    <div id="select-show-posts-dialog" style="display:none">
        <h3><?php esc_html_e('Insert [show_posts]', 'show-posts'); ?></h3>
        <p><?php esc_html_e('Add a [show_posts filter=specify-filter-name] into this page/post', 'show-posts'); ?></p>
<?php
    $filters = atw_posts_getopt('filters');

    echo '<label for="atw-slider-post-select">Select a filter: </label><select id="atw-slider-post-select" >';
    foreach ($filters as $filter => $val) {     // display dropdown of available filters
            echo '<option value="'. $filter . '">' . $val['name'] .  ' (' . $filter . ')</option>';
    }
    echo '</select>';
?>
    <br/><br/>

        <a href="#" id="select-atw-show-posts" class="button button-primary button-large" onClick="atwSelectShowPosts(); return false;">Add</a>
        <a href="#" id="cancel-insert-show-posts" class="button  button-large" onClick="atwCancelSelectShowPosts(); return false;">Cancel</a>

    </div>
<?php
}

/**
* Displays the Insert [show_slider] Selector
*/
function atw_posts_select_slider_form() {
    atw_posts_select_scripts_and_styles();
?>
    <div id="select-show-sliders-dialog" style="display:none">
        <h3><?php esc_html_e('Insert [show_slider]', 'show-posts'); ?></h3>
        <p><?php esc_html_e('Add a [show_slider name=specify-slider-name] into this page/post', 'show-posts'); ?></p>
<?php
    $sliders = atw_posts_getopt('sliders');

    echo '<label for="atw-slider-slider-select">Select a Slider: </label><select id="atw-slider-slider-select" >';
    foreach ($sliders as $slider => $val) {     // display dropdown of available sliders
        echo '<option value="'. $slider . '">' . $val['name'] .  ' (' . $slider . ')</option>';
    }
    echo '</select>';
?>
    <br/><br/>

        <a href="#" id="select-atw-show-posts" class="button button-primary button-large" onClick="atwSelectSliders(); return false;">Add</a>
        <a href="#" id="cancel-insert-show-posts" class="button  button-large" onClick="atwCancelSelectSliders(); return false;">Cancel</a>

    </div>
<?php
}


/*
* Enqueue scripts styles for select box in editor - can't be done when plugin is
* loaded - needs to be done by the add-button code
*/
function atw_posts_select_scripts_and_styles() {
    wp_enqueue_script( 'atw-posts-editor-buttons', plugins_url( 'js/atw-posts-editor-buttons.js', __FILE__ ), array( 'jquery' ), 1.0, true );

    //wp_enqueue_style( 'atw-slider-selector-style', plugins_url( 'css/atw-slider-selector-style.css', __FILE__ ));
}
// ---------------------------------------------------------------------

function atw_posts_admin() {
    require_once(dirname( __FILE__ ) . '/includes/atw-posts-admin-top.php'); // NOW - load the admin stuff
    atw_posts_admin_page();
}

function atw_posts_admin_menu() {

    //$page = add_submenu_page('edit.php?post_type=atw_posts_post',
	$show_slider = false;
	if ( function_exists('atw_slider_installed') ) {			// for simple case where show_posts gets installed first
		$show_slider = true;
	} else {
		if (!function_exists('is_plugin_active'))
			include_once (ABSPATH . 'wp-admin/includes/plugin.php');	// need this for is_plugin_active
		$show_slider = is_plugin_active('show-sliders/atw-show-sliders.php');
	}

    $menu = $show_slider ? 'Weaver Posts &amp; Slider Options' : 'Weaver Show Posts Options';
    $full = $show_slider ? 'Weaver Show Posts and Show Sliders' : 'Weaver Show Posts by WeaverTheme.com';

    $page = add_menu_page(
	  'Weaver Show Posts', $menu, 'switch_themes',
      'atw_showposts_page', 'atw_posts_admin','dashicons-admin-post',63);

	/* using registered $page handle to hook stylesheet loading for this admin page */

    add_action('admin_print_styles-'.$page, 'atw_posts_admin_scripts');
}


function atw_posts_admin_scripts() {
    /* called only on the admin page, enqueue our special style sheet here (for tabbed pages) */
    wp_enqueue_style('atw_sw_Stylesheet', atw_posts_plugins_url('/atw-admin-style', WEAVER_SHOWPOSTS_MINIFY . '.css'), array(), WEAVER_SHOWPOSTS_VERSION);

    wp_enqueue_script('atw_Yetii', atw_posts_plugins_url('/js/yetii/yetii',WEAVER_SHOWPOSTS_MINIFY.'.js'), array(),WEAVER_SHOWPOSTS_VERSION);
    wp_enqueue_script('atw_Admin', atw_posts_plugins_url('/js/atw-posts-admin',WEAVER_SHOWPOSTS_MINIFY.'.js'), array(), WEAVER_SHOWPOSTS_VERSION);


}

function atw_posts_plugins_url($file,$ext='') {
    return plugins_url($file,__FILE__) . $ext;
}

// ############


function atw_posts_enqueue_scripts() {	// enqueue runtime scripts

    if (function_exists('atw_posts_header')) atw_posts_header();

    // add plugin CSS here, too.

    wp_register_style('atw-posts-style-sheet',atw_posts_plugins_url('atw-posts-style', WEAVER_SHOWPOSTS_MINIFY.'.css'),null,WEAVER_SHOWPOSTS_VERSION,'all');
    wp_enqueue_style('atw-posts-style-sheet');

    /* if ( atw_posts_getopt( 'custom_css' ) != '' ) {
        wp_register_style( 'atw-posts-custom', '/?atwpostscss=1' );
        wp_enqueue_style( 'atw-posts-custom' );
    } */
}

// ############ stuff for custom CSS

/**
 * Add Query Var Stylesheet trigger
 *
 * Adds a query var to our stylesheet, so it can trigger our psuedo-stylesheet
 */
function atw_posts_add_trigger( $vars ) {
	$vars[] = 'atwpostscss';
	return $vars;
}

//add_filter( 'query_vars','atw_posts_add_trigger' );


/**
 * If trigger (query var) is tripped, load our pseudo-stylesheet
 */
function atw_posts_emit_css() {
	if ( intval( get_query_var( 'atwpostscss' ) ) == 1 ) {
			header( 'Content-type: text/css' );
            $css = '/* Weaver Show Posts Custom CSS */';
			$css .= atw_posts_getopt( 'custom_css' );
			$esc_css = esc_html( $css );
			$content = str_replace( '&gt;', '>', $esc_css ); // put these back
            $content = str_replace( '&lt;', '<', $esc_css ); // put these back
			echo $content;
			exit;
	}
}

add_action('wp_head', 'atw_posts_wp_head', 20);
function atw_posts_wp_head() {
	$css = atw_posts_getopt( 'custom_css' );
	if ( ! $css )
		return;
?>
<style type="text/css">
<?php
    $css = "/* Weaver Show Posts Custom CSS */\n" . $css;
	$esc_css = esc_html( $css );
	$content = str_replace( '&gt;', '>', $esc_css ); // put these back
    $content = str_replace( '&lt;', '<', $esc_css ); // put these back
	echo $content;
?>
</style>
<?php
}


// ############


function atw_posts_setup_shortcodes() {
    remove_shortcode('show_posts');                         // alias
    add_shortcode('show_posts','atw_show_posts_sc');

    if ( function_exists('atw_posts_getopt') && atw_posts_getopt( 'textWidgetShortcodes' ) ) {
        add_filter('widget_text', 'atw_post_text_widget_shortcode' );
    }
}


function atw_post_text_widget_shortcode( $text ) {
    return do_shortcode( $text );
}

function atw_show_posts_sc($args = '') {
    require_once(dirname( __FILE__ ) . '/includes/atw-showposts-sc.php');
	$schema_head = '';
	$schema_tail = '';

	if ( function_exists( 'weaverx_schema') && !isset( $args['slider'] ) ) {		// make a blog wrapper if using Weaver Xtreme, not in slider
		$schema_head = weaverx_schema('show_posts_begin');
		$schema_tail = weaverx_schema('show_posts_end');
	}
    return $schema_head . atw_show_posts_shortcode($args) . $schema_tail;
}


// ############

require_once(dirname( __FILE__ ) . '/includes/atw-runtime-lib.php'); // NOW - load the basic library
require_once(dirname( __FILE__ ) . '/includes/posts-widgets.php'); // NOW - load the basic library

