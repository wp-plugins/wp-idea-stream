<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function wp_idea_stream_teeny_button_filter( $buttons = array() ) {
	
	$add_buttons = array( 'image' );
	$remove_buttons = array(
		'wp_more',
		'spellchecker',
		'wp_adv',
	);

	if ( 1 != wp_idea_stream_editor_settings( 'link' ) ) {
		$remove_buttons = array_merge( $remove_buttons, array(
			'link',
			'unlink',
		) );
	}

	// Remove unused buttons
	$buttons = array_diff( $buttons, $remove_buttons );

	// Eventually add the image button
	if ( 1 == wp_idea_stream_editor_settings( 'image' ) ) {
		$buttons = array_diff( $buttons, array( 'fullscreen' ) );
		array_push( $buttons, 'image', 'fullscreen' );
	}
	
	return $buttons;
}

function wp_idea_stream_the_title_filter( $title ) {
	return wp_kses( stripslashes( $title ), array() );
}
add_filter( 'wpis_editor_the_title', 'wp_idea_stream_the_title_filter', 1, 1 );

function wp_idea_stream_the_content_filter( $content ) {
	$allowedhtml = wp_idea_stream_allowed_html_tags();
	
	return wp_kses( stripslashes( $content ), $allowedhtml );
}
add_filter( 'wpis_editor_the_content', 'wp_idea_stream_the_content_filter', 1, 1 );
