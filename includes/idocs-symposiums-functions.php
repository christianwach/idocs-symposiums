<?php

/**
 * iDocs Symposiums Theme functions.
 *
 * Global scope functions that are available to the theme can be found here.
 *
 * @package iDocs_Symposiums
 */



/**
 * Show a Symposium.
 *
 * @since 0.1
 *
 * @param bool $show_title Prepends the title when true, otherwise hides title.
 * @param bool $show_meta Appends the metadata when true, otherwise hides metadata.
 */
function idocs_the_symposium( $show_title = true, $show_meta = true ) {
	echo idocs_get_the_symposium( $show_title, $show_meta );
}



/**
 * Build a Symposium.
 *
 * @since 0.1
 *
 * @param bool $show_title Prepends the title when true, otherwise hides title.
 * @param bool $show_meta Appends the metadata when true, otherwise hides metadata.
 * @return str $markup The built Symposium.
 */
function idocs_get_the_symposium( $show_title = true, $show_meta = true ) {

	// Init return.
	$markup = '';

	// Maybe add title.
	if ( $show_title === true ) {
		$markup .= '<h2 class="idocs-symposium-title">' . apply_filters( 'the_title', get_the_title(), get_the_ID() ) . '</h2>';
	}

	// Add content.
	$markup .= apply_filters( 'the_content', get_the_content() );

	// Maybe add metadata.
	if ( $show_meta === true ) {
		$markup .= idocs_get_the_symposium_meta();
	}

	// --<
	return $markup;

}


/**
 * Show Symposium metadata.
 *
 * @since 0.1
 */
function idocs_the_symposium_meta() {
	echo idocs_get_the_symposium_meta();
}



/**
 * Build Symposium metadata.
 *
 * @since 0.1
 *
 * @return str $markup The built Symposium metadata.
 */
function idocs_get_the_symposium_meta() {

	// Init return.
	$markup = '';

	// Init list.
	$list = array();

	// Maybe add link.
	$link = idocs_get_the_symposium_link();
	if ( ! empty( $link ) ) {
		$list[] = $link;
	}

	// Maybe add "Speaker List" link.
	$speakers = idocs_get_the_symposium_link_speakers();
	if ( ! empty( $speakers ) ) {
		$list[] = $speakers;
	}

	// Maybe add "Download Programme" element.
	$programme = idocs_get_the_symposium_file_programme();
	if ( ! empty( $programme ) ) {
		$list[] = $programme;
	}

	// Maybe add "Download Call for Participation" element.
	$participation = idocs_get_the_symposium_file_participation();
	if ( ! empty( $participation ) ) {
		$list[] = $participation;
	}

	// Maybe add "Related Posts" element.
	$taxonomy = idocs_get_the_symposium_taxonomy();
	if ( ! empty( $taxonomy ) ) {
		$list[] = $taxonomy;
	}

	// Bail if none found.
	if ( empty( $list ) ) {
		return $markup;
	}

	// Build rendered list.
	$markup = '<ul><li>' . implode( '</li><li>', $list ) . '</li></ul>';

	// --<
	return $markup;

}



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
 * @return str $link The built symposium link.
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



/**
 * Show a link to the Symposium Speaker List.
 *
 * @since 0.1
 */
function idocs_the_symposium_link_speakers() {
	echo idocs_get_the_symposium_link_speakers();
}



/**
 * Build a link to the Symposium Speaker List.
 *
 * @since 0.1
 *
 * @return str $link The built link to the Symposium Speaker List.
 */
function idocs_get_the_symposium_link_speakers() {

	// Init return.
	$link = '';

	// Build link.
	$url = get_field( 'field_idocs_sym_link_speakers' );
	if ( ! empty( $url ) ) {
		$link = '<a href="' . $url . '">' . esc_html__( 'Visit the microsite for the full list of speakers', 'idocs-symposiums' ) . '</a>';
	}

	// --<
	return $link;

}



/**
 * Show a "Download Programme" element.
 *
 * @since 0.1
 */
function idocs_the_symposium_file_programme() {
	echo idocs_get_the_symposium_file_programme();
}



/**
 * Build a "Download Programme" element.
 *
 * @since 0.1
 *
 * @return str $markup The built "Download Programme" element.
 */
function idocs_get_the_symposium_file_programme() {

	// Init return.
	$markup = '';

	// Build element.
	$file = get_field( 'upload_programme' );

	// Bail if empty.
	if ( empty( $file ) ) {
		return $markup;
	}

	// Get markup.
	$markup = idocs_get_the_symposium_file_markup( $file );

	// --<
	return $markup;

}



/**
 * Show a "Download Call for Participation" element.
 *
 * @since 0.1
 */
function idocs_the_symposium_file_participation() {
	echo idocs_get_the_symposium_file_participation();
}



/**
 * Build a "Download Call for Participation" element.
 *
 * @since 0.1
 *
 * @return str $markup The built "Download Call for Participation" element.
 */
function idocs_get_the_symposium_file_participation() {

	// Init return.
	$markup = '';

	// Build element.
	$file = get_field( 'upload_participation' );

	// Bail if empty.
	if ( empty( $file ) ) {
		return $markup;
	}

	// Get markup.
	$markup = idocs_get_the_symposium_file_markup( $file );

	// --<
	return $markup;

}



/**
 * Build a "File Download" element.
 *
 * @since 0.1
 *
 * @return str $markup The built "File Download" element.
 */
function idocs_get_the_symposium_file_markup( $file ) {

	// Init return.
	$markup = '';

	// Extract variables.
	$url = $file['url'];
	$title = $file['title'];
	$caption = $file['caption'];
	$icon = $file['icon'];

	$text = '<img src="' . esc_attr( $icon ) . '" style="width: 1em;" alt="' . esc_attr__( 'File icon', 'idocs-symposiums' ) . '" /> <span>' . esc_html( $title ) . '</span>';

	// Wrap in anchor tag.
	$markup = '<a href="' . esc_attr( $url ) . '" title="' . esc_attr( $title ) . '">' . $text . '</a>';

	// --<
	return $markup;

}



/**
 * Show a "Download Call for Participation" element.
 *
 * @since 0.1
 */
function idocs_the_symposium_taxonomy() {
	echo idocs_get_the_symposium_taxonomy();
}



/**
 * Build a "Related Posts" element.
 *
 * @since 0.1
 *
 * @return str $markup The built "Related Posts" element.
 */
function idocs_get_the_symposium_taxonomy() {

	// Init return.
	$markup = '';

	// Build element.
	$category = get_field( 'category' );

	// Bail if empty.
	if ( empty( $category ) ) {
		return $markup;
	}

	// Get the URL of this category.
	$category_link = get_category_link( $category->term_id );

	// Build markup.
	$markup = '<a href="' . esc_url( $category_link ) . '" title="' . esc_attr__( 'See Related Posts', 'idocs-symposiums' ) . '">' .
		esc_html__( 'See Related Posts', 'idocs-symposiums' ) .
	'</a>';

    // --<
	return $markup;

}



