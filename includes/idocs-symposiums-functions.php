<?php

/**
 * iDocs Symposiums Theme functions.
 *
 * Global scope functions that are available to the theme can be found here.
 *
 * @package iDocs_Symposiums
 */



/**
 * Show a symposium link.
 *
 * @since 0.1
 */
function idocs_the_symposium_link() {
	echo idocs_get_the_symposium_link();
}



/**
 * Build a symposium link.
 *
 * @since 0.1
 *
 * @return $symposium The built symposium link.
 */
function idocs_get_the_symposium_link() {

	// Init return.
	$link = '';

	// Build link.
	$url = get_field( 'field_idocs_sym_link' );
	if ( ! empty( $url ) ) {
		$link = '<a href="' . $url . '">' . esc_html__( 'Visit the microsite for more programme details', 'idocs-symposiums' ) . '</a>';
	}

	// --<
	return $link;

}



