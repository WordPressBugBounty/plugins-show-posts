<?php

function atw_posts_help_link($ref, $label) {

    $t_dir = atw_posts_plugins_url('/help/' . $ref, '');
    $icon = atw_posts_plugins_url('/help/help.png','');
    $pp_help =  '<a href="' . $t_dir . '" target="_blank" title="' . $label . '">'
		. '<img class="entry-cat-img" src="' . $icon . '" style="position:relative; top:4px; padding-left:4px;" title="Click for help" alt="Click for help" /></a>';
    echo $pp_help ;
}


function atw_posts_save_msg($msg) {
    echo '<div id="message" class="updated fade" style="width:70%;"><p><strong>' . $msg .
	    '</strong></p></div>';
}

function atw_posts_error_msg($msg) {
    echo '<div id="message" class="updated fade" style="background:#F88;" style="width:70%;"><p><strong>' . $msg .
	    '</strong></p></div>';
}

function atw_media_lib_button($fillin = '') {
?>
&nbsp;&larr;&nbsp;<a style='text-decoration:none;' href="javascript:atw_media_lib('<?php echo $fillin;?>');" ><img src="<?php echo atw_posts_plugins_url('/images/media-button.png'); ?>" title="Select image from Media Library. Click 'Insert into Post' to paste url here." alt="media" /></a>
<?php
}

// =======================================>>> Save/Restore <<<=================================
function atw_posts_download_link($desc, $filebase, $ext, $time) {
	$nonce = wp_create_nonce('show_posts_download');

	$downloader = plugins_url() . '/show-posts/includes/downloader.php';
	$download_img_path = plugins_url() . '/show-posts/images/download.png';
	$filename = "{$filebase}-{$time}.{$ext}";
	$href = $downloader . "?_wpnonce={$nonce}&_ext={$ext}&_file={$filename}";
?>
	<a style="margin-left:9em;text-decoration: none;" href="<?php echo esc_url($href); ?>">
	<span class="download-link"><img src="<?php echo esc_url($download_img_path); ?>" />
	<?php esc_html_e('Download', 'weaver-xtreme' /*adm*/); echo '</span></a> - ';
    echo $desc; echo ' &nbsp;';
	echo 'Save as:'; echo ' ' . $filename . "<br /><br />\n";
}

function atw_posts_restore_filter() {
	if (!(isset($_POST['uploadit']) && $_POST['uploadit'] == 'yes')) return;

    if (!atw_posts_allow_file_read()) {
        atw_posts_save_msg( 'File Read Access problem. File not restored.' );
        return;
    }

    // upload theme from users computer
	// they've supplied and uploaded a file

	// echo '<pre>'; print_r($_FILES); echo '</pre>';

	$ok = true;     // no errors so far

	if (isset($_FILES['post_uploaded']['name']))
		$filename = $_FILES['post_uploaded']['name'];
	else
		$filename = "";

	if (isset($_FILES['post_uploaded']['tmp_name'])) {
		$openname = $_FILES['post_uploaded']['tmp_name'];
	} else {
		$openname = "";
	}

	//Check the file extension
	$check_file = strtolower($filename);
	$pat = '.';				// PHP version strict checking bug...
	$end = explode($pat, $check_file);
	$ext_check = end($end);


	if ($filename == "") {
		$errors[] = 'You didn\'t select a file to upload.' . "<br />";
		$ok = false;
	}

	if (!$ok) {
		echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">' .
		'ERROR' . '</em></strong></p><p>';
		foreach($errors as $error){
			echo $error.'<br />';
		}
		echo '</p></div>';
	} else {    // OK - read file and save to My Saved Theme
		// $handle has file handle to temp file.//
		$contents = file_get_contents($openname);

		if ( ! atw_posts_set_to_serialized_values($contents) ) {
				echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">' .
'Sorry, there was a problem uploading your file.
The file you picked was not a valid Weaver Show Posts settings file.' .
'</em></strong></p></div>';
		} else {
			atw_posts_save_msg( 'Weaver Show Posts set to uploaded Filter.' );
		}
	}
}

function atw_posts_set_to_serialized_values($contents) {
    if (!atw_posts_allow_file_read()) {
        return false;
    }

	$restore = unserialize($contents);

	if (!isset($restore['cur_filter']))
		return false;

	$current_filter = $restore['cur_filter'];

	if (!isset($restore[$current_filter]))
		return false;

	atw_posts_setopt('current_filter', $current_filter);

	global $atw_posts_opts_cache;

    unset($atw_posts_opts_cache['filters'][$current_filter]);

    $atw_posts_opts_cache['filters'][$current_filter]= $restore[$current_filter];
    atw_posts_wpupdate_option('atw_posts_settings',$atw_posts_opts_cache);

	return true;
}

/*
    ================= nonce helpers =====================
*/
function atw_posts_submitted($submit_name) {
    // do a nonce check for each form submit button
    // pairs 1:1 with aspen_nonce_field
    $nonce_act = $submit_name.'_act';
    $nonce_name = $submit_name.'_nonce';

    if (isset($_POST[$submit_name])) {
	if (isset($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name],$nonce_act)) {
	    return true;
	} else {
	    die("WARNING: invalid form submit detected ($submit_name). Probably caused by session time-out, or, rarely, a failed security check. Please contact WeaverTheme.com if you continue to receive this message.");
	}
    } else {
	return false;
    }
}

function atw_posts_nonce_field($submit_name,$echo = true) {
    // pairs 1:1 with sumbitted
    // will be one for each form submit button

    return wp_nonce_field($submit_name.'_act',$submit_name.'_nonce',$echo);
}

/*
    ================= form helpers =====================
*/

function atw_posts_get_POST( $id ) {
    return isset( $_POST[$id]) ? stripslashes( $_POST[$id] ) : '';
}

// general values - atw_posts_getopt

function atw_posts_form_checkbox($id, $desc, $br = '<br />') {
?>
    <div style = "display:inline;padding-left:2.5em;text-indent:-1.7em;"><label><input type="checkbox" name="<?php echo $id ?>" id="<?php echo $id; ?>"
        <?php checked(atw_posts_getopt($id) ); ?> >&nbsp;
<?php   echo $desc . '</label></div>' . $br . "\n";
}

// filter values - atw_posts_get_filter_opts

function atw_posts_filter_checkbox($id, $desc, $br = '<br />') {
?>
    <div style = "display:inline;padding-left:2.5em;text-indent:-1.7em;"><label><input type="checkbox" name="<?php echo $id; ?>" id="<?php echo $id; ?>"
        <?php checked(atw_posts_get_filter_opt($id) ); ?> >&nbsp;
<?php   echo $desc . '</label></div>' . $br . "\n";
}

function atw_posts_filter_textarea($id, $desc, $br = '<br />') {
?>
    <div style="margin-top:5px;display:inline-block;padding-left:4em;text-indent:-1.7em;"><label>
    <textarea style="margin-bottom:-8px;" cols=32 rows=1 maxlength=64 name="<?php echo $id; ?>"><?php echo sanitize_text_field( atw_posts_get_filter_opt($id) ); ?></textarea>
    &nbsp;
<?php   echo $desc . '</label></div>' . $br . "\n";
}

function atw_posts_filter_val($id, $desc, $br = '<br />') {
?>
    <div style = "margin-top:5px;display:inline-block;padding-left:2.5em;text-indent:-1.7em;"><label>
    <input class="regular-text" type="text" style="width:50px;height:22px;" name="<?php echo $id; ?>" value="<?php echo sanitize_text_field( atw_posts_get_filter_opt($id) ); ?>" />
    &nbsp;
<?php   echo $desc . '</label></div>' . $br . "\n";
}

?>
