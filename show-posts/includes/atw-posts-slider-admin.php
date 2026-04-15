<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// ========================================= >>> atw_posts_select_filter <<< ===============================
function atw_posts_slider_admin() {
    // admin for style options...

    if (function_exists('atw_slider_installed')) {
        atw_slider_do_slider_admin();
        return;
    }
?>
   <h2 style="color:blue;">Weaver Show Sliders Plugin</h2>

<p>
    Our "The Weaver <em>Show Sliders</em> plugin" was recommended by previous versions of Weaver Show Posts, but it
    is no longer a modern slider tool. We are not longer supporting it, and it is not recommended any longer.
</p>
<?php
}
