<?php
/*
Plugin Name: 	UnityDog
Description: 	Unitydog enabled embedding of unity3d webplayer files in your blog.
Version: 		1.0.4
Author: 		Garry Newman
Author URI: 	http://www.garry.tv
License: 		MIT 
*/

//
// Unitydog Setup
//
function unitydog_init() 
{
	wp_register_script( 'unityobject2', plugins_url( 'unityobject2.js', __FILE__ ) );
	wp_register_script( 'unitydog', plugins_url( 'unitydog.js', __FILE__ ) );
	wp_register_style( 'unitydog', plugins_url( 'unitydog.css', __FILE__ ) );
}    
add_action( 'init', 'unitydog_init' );


//
// Don't forget to include the style sheets!
//
function unitydog_enqueue_style()
{
   wp_enqueue_script( 'unityobject2' );
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'unitydog' );
   wp_enqueue_style( 'unitydog' );
   
	$params = array
	(
		'fullscreen'	=> plugins_url( 'ctrl_fullscreen.png', 	__FILE__ ),
		'restore' 		=> plugins_url( 'ctrl_restore.png', 	__FILE__ ),
	);
	
	wp_localize_script( 'unitydog', 'unitydogsettings', $params );
}
add_action('wp_enqueue_scripts', 'unitydog_enqueue_style');

//
// Add .unity3d to the allowed upload file list
//
function unitydog_upload_mimes( $existing_mimes=array() ) 
{
	$existing_mimes['unity3d'] = 'application/vnd.unity'; 
	return $existing_mimes; 
}
add_action( 'upload_mimes', 'unitydog_upload_mimes' );

//
// Add a media section for unity3d files
//
function unitydog_post_mime_types( $post_mime_types ) 
{
    $post_mime_types['application/vnd.unity'] = array(__('Unity'), __('Manage Unity'), _n_noop('Unity <span class="count">(%s)</span>', 'Unity <span class="count">(%s)</span>'));
    return $post_mime_types;
}
add_filter( 'post_mime_types', 'unitydog_post_mime_types' );

//
// Add [unity] tags when inserting it
//
function unitydog_media_send_to_editor( $html, $id, $attachment ) 
{
	$attachment = get_post( $id );
	if ( $attachment->post_mime_type != 'application/vnd.unity' ) return $html;
	
	return "[unity src=\"".$id."\"]"; 
}
add_action( 'media_send_to_editor', 'unitydog_media_send_to_editor', 50, 3 );


//
// The unity shortcode
//

function unitydog_unity( $atts ) 
{
	global $g_UnityPlayerCount;
	
	if ( !$atts['src'] ) return "(missing unity src)";
	
	$width 	= "\"".$atts['w']."\"";
	$height	= "\"".$atts['h']."\"";
	
	$url = wp_get_attachment_url( $atts['src'] );
	if ( !$url ) return "(missing unity attachment)";
	
	return '<unitydog width="'.$width.'" height="'.$height.'" src="'.$url.'"></unitydog>';
}
add_shortcode( 'unity', 'unitydog_unity' );

//
// A lot of webservers can't serve unity3d files by default.
// So rename it from .unity to .zip
//
function unitydog_add_attachment( $pid )
{
    $post = get_post( $pid );
    $file = get_attached_file( $pid );
    $path = pathinfo($file);
	
	// If it's not a unity file - get outa here stalker
	if ( $path['extension'] != "unity3d" ) return;
	
    $newfile = $file . ".zip"; // add a zip
	
    rename( $file, $newfile );    
    update_attached_file( $pid, $newfile );
}
add_action( 'add_attachment', 'unitydog_add_attachment' );
