<?php
// will down load current settings based on db setting
// __ added - 12/11/14
    if ( !current_user_can( 'manage_options' ) ) {
        exit;
    }

	$wp_root = dirname(__FILE__) .'/../../../../';
	if(file_exists($wp_root . 'wp-load.php')) {
		require_once($wp_root . "wp-load.php");
	} else if(file_exists($wp_root . 'wp-config.php')) {
		require_once($wp_root . "wp-config.php");
	} else {
		exit;
	}


	@error_reporting(0);

	$nonce = '';
	$show_fn = '';
	$ext = '';

	if (isset($_GET['_wpnonce']))
		$nonce = $_GET['_wpnonce'];

	if (isset($_GET['_file']))
		$show_fn = $_GET['_file'];

	if (isset($_GET['_ext']))
		$ext = $_GET['_ext'];

	if ( !$nonce || !$show_fn || !$ext ) {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die(esc_html__('Sorry - invalid download','show-posts' /*adm*/));
	}

	if (! wp_verify_nonce($nonce, 'show_posts_download')) {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die(esc_html__('Sorry - download must be initiated from admin panel.','show-posts' /*adm*/) . ':' . esc_html($nonce));
	}

	if (headers_sent()) {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die(esc_html__('Headers Sent: The headers have been sent by another plugin - there may be a plugin conflict.','show-posts' /*adm*/));
	}

    //@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
	//wp_die("Ready to download: {$show_fn} - ext: {$ext}");

	$show_opts = get_option('atw_posts_settings' ,array());

	if ( $ext == 'filter' ) {
		$save = array();
		$cur_filter = $show_opts['current_filter'];
		$save['cur_filter'] = $cur_filter;
		$save[$cur_filter] = $show_opts['filters'][$cur_filter];
	} elseif ($ext == 'slider' ) {
		$save = array();
		$cur_slider = $show_opts['current_slider'];
		$save['cur_slider'] = $cur_slider;
		$save[$cur_slider] = $show_opts['sliders'][$cur_slider];
	} else {
		@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		wp_die("Error - trying to save invalid type of settings: " . esc_html( $ext ) . '.');
	}

	$save_settings = serialize($save);

	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='. esc_html($show_fn) );
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . strlen($save_settings));
	echo $save_settings;
	exit;
