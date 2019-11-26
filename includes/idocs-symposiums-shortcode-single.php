<?php

/**
 * iDocs Symposiums Single Symposium Shortcode Class.
 *
 * A class that encapsulates the iDocs Symposiums Shortcode which renders a
 * single Symposium.
 *
 * @since 0.1
 *
 * @package iDocs_Symposium
 */
class iDocs_Symposium_Shortcode_Single {

	/**
	 * Plugin (calling) object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;

	/**
	 * Shortcode name.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $shortcode_name The name of the shortcode tag.
	 */
	public $shortcode_name = 'idocs_symposium';



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

		// register shortcode
		add_action( 'init', [ $this, 'shortcode_register' ] );

		// Shortcake compat
		add_action( 'register_shortcode_ui', [ $this, 'shortcake' ] );
		//add_action( 'enqueue_shortcode_ui', [ $this, 'shortcake_scripts' ] );

	}



	/**
	 * Register our shortcode.
	 *
	 * @since 0.1
	 */
	public function shortcode_register() {

		// Register shortcodes.
		add_shortcode( $this->shortcode_name, [ $this, 'shortcode_render' ] );

	}



	// #########################################################################



	/**
	 * Add the symposium to a page via a shortcode.
	 *
	 * @since 0.1
	 *
	 * @param array $attr The saved shortcode attributes.
	 * @param str $content The enclosed content of the shortcode.
	 * @return str $symposium The HTML markup for the shortcode.
	 */
	public function shortcode_render( $attr, $content = null ) {

		// Init return.
		$symposium = '';

		// Init defaults.
		$defaults = array(
			'id' => '',
			'show_title' => '1',
			'show_meta' => '1',
		);

		// Parse attributes.
		$shortcode_atts = shortcode_atts( $defaults, $attr, 'idocs_symposium' );

		// Bail id there's no ID.
		if ( empty( $shortcode_atts['id'] ) ) {
			return $symposium;
		}

		// Maybe hide title.
		$show_title = true;
		if ( '1' != $shortcode_atts['show_title'] ) {
			$show_title = false;
		}

		// Maybe hide metadata.
		$show_meta = true;
		if ( '1' != $shortcode_atts['show_meta'] ) {
			$show_meta = false;
		}

		// Get posts of the specified type.
		$args = array(
			'post_type' => $this->plugin->cpt->post_type_name,
			'post_status' => 'publish',
			'no_found_rows' => true,
			'posts_per_page' => 1,
			'p' => $shortcode_atts['id'],
		);

		// Do query.
		$query = new WP_Query( $args );

		// Grab rendered posts.
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$symposium = idocs_get_the_symposium( $show_title, $show_meta );
			endwhile;
			wp_reset_postdata();
		}

		// --<
		return $symposium;

	}



	/**
	 * Add compatibility with Shortcake UI.
	 *
	 * Be aware that in Shortcode UI < 0.7.2, values chosen via selects do not
	 * "stick". At present, you will need the master branch of Shortcode UI from
	 * GitHub, which has solved this problem.
	 *
	 * @see https://github.com/wp-shortcake/shortcake/issues/747
	 *
	 * Furthermore, multi-selects do not function at all.
	 *
	 * @see https://github.com/wp-shortcake/shortcake/issues/757
	 *
	 * @since 0.1
	 */
	public function shortcake() {

		// Let's be extra-safe and bail if not present.
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) return;

		// Add styles for TinyMCE editor.
		//add_filter( 'mce_css', array( $this, 'shortcake_styles' ) );

		// Register this shortcode.
		shortcode_ui_register_for_shortcode(

			// Shortcode name.
			$this->shortcode_name,

			// Settings.
			array(

				// Window title.
				'label' => __( 'Symposium', 'idocs-symposiums' ),

				// Icon.
				'listItemImage' => 'dashicons-groups',

				// Window elements.
				'attrs' => array(

					// Symposiums list.
					array(
						'label' => __( 'Select Symposium', 'idocs-symposiums' ),
						'attr'  => 'id',
						'type'  => 'select',
						'options' => $this->shortcake_select(),
						'description' => __( 'Please select a Symposium.', 'idocs-symposiums' ),
					),

					// show meta
					array(
						'label' => __( 'Show Title', 'idocs-symposiums' ),
						'description' => __( 'Please select if you want to show the Symposium title.', 'idocs-symposiums' ),
						'attr' => 'show_title',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'idocs-symposiums' ) ),
							array( 'value' => '0', 'label' => __( 'No', 'idocs-symposiums' ) ),
						),
						'value' => '1',
					),

					// Show metadata.
					array(
						'label' => __( 'Show Metadata', 'idocs-symposiums' ),
						'description' => __( 'Please select if you want to display additional information for each Symposium.', 'idocs-symposiums' ),
						'attr' => 'show_meta',
						'type' => 'radio',
						'options' => array(
							array( 'value' => '1', 'label' => __( 'Yes', 'idocs-symposiums' ) ),
							array( 'value' => '0', 'label' => __( 'No', 'idocs-symposiums' ) ),
						),
						'value' => '1',
					),

				),

			)

		);

	}



	/**
	 * Get options array for the select.
	 *
	 * @since 0.1
	 *
	 * @return array $options The properly formatted array for the select.
	 */
	public function shortcake_select() {

		// Init return.
		$options = array(
			array( 'value' => '', 'label' => __( 'None', 'idocs-symposiums' ) ),
		);

		// Get all symposiums.
		$args = array(
			'post_type' => $this->plugin->cpt->post_type_name,
			'post_status' => 'publish',
			'no_found_rows' => true,
			'posts_per_page' => -1,
		);

		// Do query.
		$symposiums = new WP_Query( $args );

		// Populate options.
		if ( $symposiums->have_posts() ) {
			// Must be a foreach...
			// @see https://core.trac.wordpress.org/ticket/18408
			foreach( $symposiums->get_posts() AS $symposium ) {
				$options[] = array(
					'value' => $symposium->ID,
					'label' => get_the_title( $symposium->ID ),
				);
			}
		}

		// --<
		return $options;

	}



	/**
	 * Enqueue Javascript for custom functionality in Shortcake.
	 *
	 * @since 0.1
	 */
	public function shortcake_scripts() {

		wp_enqueue_script(
			'idocs-symposiums-shortcode-ui',
			IDOCS_SYMPOSIUMS_URL . 'assets/js/idocs-symposiums.js',
			array( 'shortcode-ui' ),
			IDOCS_SYMPOSIUMS_VERSION
		);

	}



	/**
	 * Add stylesheet to TinyMCE when Shortcake is active.
	 *
	 * @since 0.1
	 *
	 * @param str $mce_css The existing list of stylesheets that TinyMCE will load.
	 * @return str $mce_css The modified list of stylesheets that TinyMCE will load.
	 */
	public function shortcake_styles( $mce_css ) {

		// Add our styles to TinyMCE.
		$mce_css .= ', ' . IDOCS_SYMPOSIUMS_URL . 'assets/css/idocs-symposiums.css';

		// --<
		return $mce_css;

	}



} // Class ends.



