<?php

/**
 * iDocs Symposiums ACF Class.
 *
 * A class that encapsulates all ACF functionality for iDocs Symposiums.
 *
 * @package iDocs_Symposiums
 */
class iDocs_Symposiums_ACF {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Bail if ACF isn't found.
		if ( ! function_exists( 'acf' ) ) {
			return;
		}

		// Store reference to plugin.
		$this->plugin = $plugin;

		// Init when this plugin is loaded.
		add_action( 'idocs_symposiums_loaded', [ $this, 'register_hooks' ] );

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Add field groups.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );

		// Add fields.
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}



	/**
	 * Add ACF Field Groups.
	 *
	 * @since 0.1
	 */
	public function field_groups_add() {

		// Attach the field group to our CPT.
		$field_group_location = [[[
			'param' => 'post_type',
			'operator' => '==',
			'value' => $this->plugin->cpt->post_type_name,
		]]];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			//'the_content',
			//'excerpt',
			'discussion',
			'comments',
			//'revisions',
			'author',
			'format',
			'page_attributes',
			//'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define field group.
		$field_group = [
			'key' => 'group_idocs_sym_data',
			'title' => __( 'Symposium Information', 'idocs-symposiums' ),
			'fields' => [],
			'location' => $field_group_location,
			'hide_on_screen' => $field_group_hide_elements,
		];

		// Now add the group.
		acf_add_local_field_group( $field_group );

	}



	/**
	 * Add ACF Fields.
	 *
	 * @since 0.1
	 */
	public function fields_add() {

		// Define a "Year" field.
		$this->field_year_add();

		// Define a "Link" field.
		$this->field_link_add();

		// Define a "Speaker List" field.
		$this->field_link_speakers_add();

		// Define a "Category" field.
		$this->field_category_add();

		// Define a "Programme Upload" field.
		$this->field_upload_programme_add();

		// Define a "Call for Participation Upload" field.
		$this->field_upload_participation_add();

	}



	/**
	 * Add "Year" Field.
	 *
	 * @since 0.1
	 */
	public function field_year_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_sym_year',
			'label' => __( 'Year', 'idocs-symposiums' ),
			'name' => 'year',
			'type' => 'date_picker',
			'instructions' => '',
			'display_format' => 'Y',
			'return_format' => 'Y',
			'first_day' => 1,
			'parent' => 'group_idocs_sym_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Link" Field.
	 *
	 * @since 0.1
	 */
	public function field_link_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_sym_link',
			'label' => __( 'Link to Symposium Microsite', 'idocs-symposiums' ),
			'name' => 'link',
			'type' => 'url',
			'instructions' => '',
			'default_value' => '',
			'placeholder' => '',
			'parent' => 'group_idocs_sym_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Speaker List" Field.
	 *
	 * @since 0.1
	 */
	public function field_link_speakers_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_sym_link_speakers',
			'label' => __( 'Link to Speaker List', 'idocs-symposiums' ),
			'name' => 'link_speakers',
			'type' => 'url',
			'instructions' => '',
			'default_value' => '',
			'placeholder' => '',
			'parent' => 'group_idocs_sym_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Category" Field.
	 *
	 * @since 0.1
	 */
	public function field_category_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_sym_category',
			'label' => __( 'Related Posts Category', 'idocs-symposiums' ),
			'name' => 'category',
			'type' => 'taxonomy',
			'field_type' => 'select',
			'allow_null' => 0,
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'object',
			'multiple' => 0,
			'instructions' => '',
			'default_value' => '',
			'placeholder' => '',
			'parent' => 'group_idocs_sym_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Programme Document" Field.
	 *
	 * @since 0.1
	 */
	public function field_upload_programme_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_sym_upload_programme',
			'label' => __( 'Programme Document', 'idocs-symposiums' ),
			'name' => 'upload_programme',
			'type' => 'file',
			'return_format' => 'array',
			'library' => 'all',
			'min_size' => '',
			'max_size' => '',
			'parent' => 'group_idocs_sym_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



	/**
	 * Add "Call for Participation" Field.
	 *
	 * @since 0.1
	 */
	public function field_upload_participation_add() {

		// Define field.
		$field = [
			'key' => 'field_idocs_sym_upload_participation',
			'label' => __( 'Call for Participation Document', 'idocs-symposiums' ),
			'name' => 'upload_participation',
			'type' => 'file',
			'return_format' => 'array',
			'library' => 'all',
			'min_size' => '',
			'max_size' => '',
			'parent' => 'group_idocs_sym_data',
		];

		// Now add field.
		acf_add_local_field( $field );

	}



} // class iDocs_Symposiums_Metaboxes ends



