<?php /*
--------------------------------------------------------------------------------
Plugin Name: i-Docs Symposiums
Plugin URI: https://i-docs.org
Description: Provides Symposiums for the i-Docs website.
Author: Christian Wach
Version: 0.1
Author URI: https://haystack.co.uk
Text Domain: idocs-symposiums
Domain Path: /languages
--------------------------------------------------------------------------------
*/



// Set our version here.
define( 'IDOCS_SYMPOSIUMS_VERSION', '0.1' );

// Store reference to this file.
if ( ! defined( 'IDOCS_SYMPOSIUMS_FILE' ) ) {
	define( 'IDOCS_SYMPOSIUMS_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'IDOCS_SYMPOSIUMS_URL' ) ) {
	define( 'IDOCS_SYMPOSIUMS_URL', plugin_dir_url( IDOCS_SYMPOSIUMS_FILE ) );
}
// Store PATH to this plugin's directory.
if ( ! defined( 'IDOCS_SYMPOSIUMS_PATH' ) ) {
	define( 'IDOCS_SYMPOSIUMS_PATH', plugin_dir_path( IDOCS_SYMPOSIUMS_FILE ) );
}



/**
 * i-Docs Symposiums Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 0.1
 */
class iDocs_Symposiums {

	/**
	 * Custom Post Type object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The Custom Post Type object.
	 */
	public $cpt;

	/**
	 * Advanced Custom Fields object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The Advanced Custom Fields object.
	 */
	public $acf;



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Initialise on "plugins_loaded".
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}



	/**
	 * Do stuff on plugin init.
	 *
	 * @since 0.1
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) AND $done === true ) {
			return;
		}

		// Load translation.
		$this->translation();

		// Include files.
		$this->include_files();

		// Set up objects and references.
		$this->setup_objects();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 0.1
		 */
		do_action( 'idocs_symposiums_loaded' );

		// We're done.
		$done = true;

	}



	/**
	 * Enable translation.
	 *
	 * @since 0.1
	 */
	public function translation() {

		// Load translations.
		load_plugin_textdomain(
			'idocs-symposiums', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( IDOCS_SYMPOSIUMS_FILE ) ) . '/languages/' // Relative path to files.
		);

	}



	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Include functions.
		include IDOCS_SYMPOSIUMS_PATH . 'includes/idocs-symposiums-functions.php';

		// Include CPT class.
		include IDOCS_SYMPOSIUMS_PATH . 'includes/idocs-symposiums-cpt.php';

		// Include ACF class.
		include IDOCS_SYMPOSIUMS_PATH . 'includes/idocs-symposiums-acf.php';

	}



	/**
	 * Set up this plugin's objects.
	 *
	 * @since 0.1
	 */
	public function setup_objects() {

		// Init CPT object.
		$this->cpt = new iDocs_Symposiums_CPT( $this );

		// Init ACF object.
		$this->acf = new iDocs_Symposiums_ACF( $this );

	}



	/**
	 * Perform plugin activation tasks.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Maybe init.
		$this->initialise();

		// Pass through.
		$this->cpt->activate();

	}



	/**
	 * Perform plugin deactivation tasks.
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Maybe init.
		$this->initialise();

		// Pass through.
		$this->cpt->deactivate();

	}



}



/**
 * Utility to get a reference to this plugin.
 *
 * @since 0.1
 *
 * @return iDocs_Symposiums $idocs_symposiums The plugin reference.
 */
function idocs_symposiums() {

	// Store instance in static variable.
	static $idocs_symposiums = false;

	// Maybe return instance.
	if ( false === $idocs_symposiums ) {
		$idocs_symposiums = new iDocs_Symposiums();
	}

	// --<
	return $idocs_symposiums;

}



// Initialise plugin now.
idocs_symposiums();

// Activation.
register_activation_hook( __FILE__, [ idocs_symposiums(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ idocs_symposiums(), 'deactivate' ] );

// Uninstall uses the 'uninstall.php' method.
// See: http://codex.wordpress.org/Function_Reference/register_uninstall_hook



