<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      1.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 * @author     Ressort IT-Entwicklung - Rotaract Deutschland <it-entwicklung@rotaract.de>
 */
class Rotaract_Club_Finder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rotaract_Club_Finder_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected Rotaract_Club_Finder_Loader $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected string $version;

	/**
	 * The Elasticsearch caller.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Rotaract_Elastic_Caller $elastic_caller    The object that handles search calls to the Elasticsearch instance.
	 */
	protected Rotaract_Elastic_Caller $elastic_caller;

	/**
	 * The OpenCage caller.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Rotaract_OpenCage_Caller $opencage_caller    The object that handles search calls to the OpenCage API.
	 */
	protected Rotaract_OpenCage_Caller $opencage_caller;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ROTARACT_CLUB_FINDER_VERSION' ) ) {
			$this->version = ROTARACT_CLUB_FINDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'rotaract-club-finder';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rotaract_Club_Finder_Loader. Orchestrates the hooks of the plugin.
	 * - Rotaract_Club_Finder_I18n. Defines internationalization functionality.
	 * - Rotaract_Club_Finder_Admin. Defines all hooks for the admin area.
	 * - Rotaract_Club_Finder_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-rotaract-club-finder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-rotaract-club-finder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-rotaract-club-finder-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-rotaract-club-finder-public.php';

		/**
		 * Logic for receiving the event data from elastic API.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/callers/class-rotaract-elastic-caller.php';

		/**
		 * Logic for receiving the event data from OpenCage API.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/callers/class-rotaract-opencage-caller.php';

		$this->loader = new Rotaract_Club_Finder_Loader();

		$this->elastic_caller = new Rotaract_Elastic_Caller();
		$this->opencage_caller = new Rotaract_OpenCage_Caller();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rotaract_Club_Finder_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rotaract_Club_Finder_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rotaract_Club_Finder_Admin( $this->get_plugin_name(), $this->get_version(), $this->elastic_caller );

		if ( ! $this->elastic_caller->isset_elastic_host() ) {
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'elastic_missing_notice' );
		}
		if ( ! $this->opencage_caller->isset_opencage_api_key() ) {
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'opencage_missing_notice' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rotaract_Club_Finder_Public( $this->get_plugin_name(), $this->get_version(), $this->elastic_caller, $this->opencage_caller );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_shortcode( 'rotaract-club-finder', $plugin_public, 'club_finder_shortcode' );
		$this->loader->add_action( 'wp_ajax_find_clubs_in_range', $plugin_public, 'find_clubs_in_range' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rotaract_Club_Finder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader(): Rotaract_Club_Finder_Loader {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version(): string {
		return $this->version;
	}

}
