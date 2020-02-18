<?php

/**
 * iDocs Symposiums Custom Post Type Class
 *
 * A class that encapsulates a Custom Post Type for iDocs Symposiums.
 *
 * @package iDocs_Symposiums
 */
class iDocs_Symposiums_CPT {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * Custom Post Type name.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The name of the Custom Post Type.
	 */
	public $post_type_name = 'symposium';

	/**
	 * Taxonomy name.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $taxonomy_name The name of the Custom Taxonomy.
	 */
	public $taxonomy_name = 'symposiumcat';



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

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

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Amend the UI on our post type edit screen.
		//add_action( 'do_meta_boxes', [ $this, 'post_type_ui_filter' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Create taxonomy.
		add_action( 'init', [ $this, 'taxonomy_create' ] );

		// Fix hierarchical taxonomy metabox display.
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );

		// Add a filter to the wp-admin listing table.
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );

		// Add feature image size.
		//add_action( 'after_setup_theme', [ $this, 'feature_image_create' ] );

	}



	/**
	 * Remove Uncode theme elements.
	 *
	 * Not used.
	 *
	 * @since 0.1
	 */
	public function post_type_ui_filter() {

		// Remove theme metaboxes
		remove_meta_box( 'uncode_gallery_div', $this->post_type_name, 'normal' );
		remove_meta_box( '_uncode_page_options', $this->post_type_name, 'normal' );

	}



	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}



	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}



	// #########################################################################



	/**
	 * Create our Custom Post Type.
	 *
	 * @since 0.1
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;

		// Bail if already done.
		if ( $registered ) return;

		// Set up the post type called "Symposium".
		register_post_type( $this->post_type_name, [

			// Labels.
			'labels' => [
				'name'               => __( 'Symposiums', 'idocs-symposiums' ),
				'singular_name'      => __( 'Symposium', 'idocs-symposiums' ),
				'add_new'            => __( 'Add New', 'idocs-symposiums' ),
				'add_new_item'       => __( 'Add New Symposium', 'idocs-symposiums' ),
				'edit_item'          => __( 'Edit Symposium', 'idocs-symposiums' ),
				'new_item'           => __( 'New Symposium', 'idocs-symposiums' ),
				'all_items'          => __( 'All Symposiums', 'idocs-symposiums' ),
				'view_item'          => __( 'View Symposium', 'idocs-symposiums' ),
				'search_items'       => __( 'Search Symposiums', 'idocs-symposiums' ),
				'not_found'          => __( 'No matching Symposium found', 'idocs-symposiums' ),
				'not_found_in_trash' => __( 'No Symposiums found in Trash', 'idocs-symposiums' ),
				'menu_name'          => __( 'Symposiums', 'idocs-symposiums' ),
			],

			// Defaults.
			'menu_icon'   => 'dashicons-groups',
			'description' => __( 'A symposium post type', 'idocs-symposiums' ),
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'has_archive' => true,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 25,
			'map_meta_cap' => true,

			// Rewrite.
			'rewrite' => [
				'slug' => 'symposiums',
				'with_front' => false
			],

			// Supports.
			'supports' => [
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
			],

		] );

		//flush_rewrite_rules();

		// Flag done.
		$registered = true;

	}



	/**
	 * Override messages for a custom post type.
	 *
	 * @since 0.1
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our custom post type.
		$messages[$this->post_type_name] = [

			// Unused - messages start at index 1.
			0 => '',

			// Item updated.
			1 => sprintf(
				__( 'Symposium updated. <a href="%s">View Symposium</a>', 'idocs-symposiums' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2 => __( 'Custom field updated.', 'idocs-symposiums' ),
			3 => __( 'Custom field deleted.', 'idocs-symposiums' ),
			4 => __( 'Symposium updated.', 'idocs-symposiums' ),

			// Item restored to a revision.
			5 => isset( $_GET['revision'] ) ?

					// Revision text.
					sprintf(
						// Translators: %s: date and time of the revision.
						__( 'Symposium restored to revision from %s', 'idocs-symposiums' ),
						wp_post_revision_title( (int) $_GET['revision'], false )
					) :

					// No revision.
					false,

			// Item published.
			6 => sprintf(
				__( 'Symposium published. <a href="%s">View Symposium</a>', 'idocs-symposiums' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7 => __( 'Symposium saved.', 'idocs-symposiums' ),

			// Item submitted.
			8 => sprintf(
				__( 'Symposium submitted. <a target="_blank" href="%s">Preview Symposium</a>', 'idocs-symposiums' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9 => sprintf(
				__( 'Symposium scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Symposium</a>', 'idocs-symposiums' ),
				// Translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ),
				strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				__( 'Symposium draft updated. <a target="_blank" href="%s">Preview Symposium</a>', 'idocs-symposiums' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			)

		];

		// --<
		return $messages;

	}



	/**
	 * Override the "Add title" label.
	 *
	 * @since 0.1
	 *
	 * @param str $title The existing title - usually "Add title".
	 * @return str $title The modified title.
	 */
	public function post_type_title( $input ) {

		// Bail if not our post type.
		if ( $this->post_type_name !== get_post_type() ) {
			return $input;
		}

		// Overwrite with our string.
		$input = __( 'Add the title of the Symposium', 'idocs-symposiums' );

		// --<
		return $input;

	}



	/**
	 * Create our Custom Taxonomy.
	 *
	 * @since 0.1
	 */
	public function taxonomy_create() {

		// Only call this once.
		static $registered;

		// Bail if already done.
		if ( $registered ) return;

		// Register a taxonomy for this CPT.
		register_taxonomy(

			// Taxonomy name.
			$this->taxonomy_name,

			// Post type.
			$this->post_type_name,

			// Arguments.
			[

				// Same as "category".
				'hierarchical' => true,

				// Labels.
				'labels' => [
					'name'              => _x( 'Symposium Types', 'taxonomy general name', 'idocs-symposiums' ),
					'singular_name'     => _x( 'Symposium Type', 'taxonomy singular name', 'idocs-symposiums' ),
					'search_items'      => __( 'Search Symposium Types', 'idocs-symposiums' ),
					'all_items'         => __( 'All Symposium Types', 'idocs-symposiums' ),
					'parent_item'       => __( 'Parent Symposium Type', 'idocs-symposiums' ),
					'parent_item_colon' => __( 'Parent Symposium Type:', 'idocs-symposiums' ),
					'edit_item'         => __( 'Edit Symposium Type', 'idocs-symposiums' ),
					'update_item'       => __( 'Update Symposium Type', 'idocs-symposiums' ),
					'add_new_item'      => __( 'Add New Symposium Type', 'idocs-symposiums' ),
					'new_item_name'     => __( 'New Symposium Type Name', 'idocs-symposiums' ),
					'menu_name'         => __( 'Symposium Types', 'idocs-symposiums' ),
				],

				// Rewrite rules.
				'rewrite' => [
					'slug' => 'symposium-types'
				],

				// Show column in wp-admin.
				'show_admin_column' => true,
				'show_ui' => true,

			]

		);

		//flush_rewrite_rules();

		// Flag done.
		$registered = true;

	}



	/**
	 * Fix the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 0.1
	 *
	 * @param array $args The existing arguments.
	 * @param int $post_id The WordPress post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) AND $args['taxonomy'] == $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}



	/**
	 * Create our Feature Image size.
	 *
	 * @since 0.1
	 */
	public function feature_image_create() {

		// Define a small, square custom image size, cropped to fit.
		add_image_size(
			'idocs-symposium',
			apply_filters( 'idocs_symposium_image_width', 384 ),
			apply_filters( 'idocs_symposium_image_height', 384 ),
			true // Crop.
		);

	}



	/**
	 * Add a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 0.1
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type,
		if ( $typenow != $this->post_type_name ) return;

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Show a dropdown.
		wp_dropdown_categories( [
			'show_option_all' => sprintf( __( 'Show All %s', 'idocs-symposiums' ), $taxonomy->label ),
			'taxonomy' => $this->taxonomy_name,
			'name' => $this->taxonomy_name,
			'orderby' => 'name',
			'selected' => isset( $_GET[$this->taxonomy_name] ) ? $_GET[$this->taxonomy_name] : '',
			'show_count' => true,
			'hide_empty' => true,
			'value_field' => 'slug',
			'hierarchical' => 1,
		] );

	}



} // class iDocs_Symposiums_CPT ends.



