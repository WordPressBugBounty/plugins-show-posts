<?php
/*
 *  Weaver X Widgets and shortcodes - widgets
 */

class WeaverSP_Widget_Text extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'WeaverSS_Widget_Slider',
		 'description' => esc_html__('Show Posts in a widget','show-posts' /*adm*/));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('wvr_showposts', esc_html__('Weaver Show Posts','show-posts' /*adm*/), $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		// Get menu

		$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		$post_list = $instance['post_list'];
		if (!$post_list) $post_list = 'default';

		$add_class = $instance['add_class'];
		if (!$add_class) $add_class ='';


		echo $args['before_widget'];

		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		$before = $add_class ? "<div class='{$add_class}'>" : '';
		$after = $add_class ? "</div>" : '';

		require_once(dirname( __FILE__ ) . '/atw-showposts-sc.php');

		echo $before . atw_show_posts_shortcode( array('filter' => $post_list) ) . $after;

		echo $args['after_widget'];
}

function update( $new_instance, $old_instance ) {
	$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
	$instance['post_list'] = $new_instance['post_list'];
	$instance['add_class'] = strip_tags( stripslashes($new_instance['add_class']) );
	return $instance;
}

function form( $instance ) {
	$title = isset( $instance['title'] ) ? $instance['title'] : '';
	$post_list = isset( $instance['post_list'] ) ? $instance['post_list'] : 'default';
	$add_class = isset( $instance['add_class'] ) ? $instance['add_class'] : '';


	// Get posts

	$posts = atw_posts_getopt('filters');



	// If no menus exists, direct the user to go and create some.
	if ( empty($posts)  ) {
		echo '<p>' . 'No Post Filters have been created yet. Create some on the <em>Weaver Posts (& Slider Options) -> Filters</em> admin menu.' .'</p>';
		return;
	}
?>
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo('Title (optional):' /*a*/ ) ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_textarea($title); ?>" />
	</p>

	<p>
	<label for="<?php echo $this->get_field_id('post_list'); ?>"><?php echo('Select a Post'); ?></label><br />
	<select id="<?php echo $this->get_field_id('post_list'); ?>" name="<?php echo $this->get_field_name('post_list'); ?>">
<?php
		foreach ( $posts as $post) {
			$selected = $post_list == $post['slug'] ? ' selected="selected"' : '';
			echo '<option'. $selected .' value="' . $post['slug'] .'">'. $post['name'] .'</option>';
		}
?>
	</select>
	</p>

	<p>
	<label for="<?php echo $this->get_field_id('add_class'); ?>"><?php echo('Additional Classes to Wrap Posts (optional):'  ) ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('add_class'); ?>" name="<?php echo $this->get_field_name('add_class'); ?>" value="<?php echo esc_textarea($add_class); ?>" />
	</p>
<?php
   }
}


add_action("widgets_init", "wvrx_sp_load_widgets");


function wvrx_sp_load_widgets() {
	register_widget("WeaverSP_Widget_Text");
}
