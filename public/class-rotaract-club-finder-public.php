<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/public
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      2.0.0
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/public
 * @author     Ressort IT-Entwicklung - Rotaract Deutschland <it-entwicklung@rotaract.de>
 */
class Rotaract_Club_Finder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $rotaract_club_finder    The ID of this plugin.
	 */
	private string $rotaract_club_finder;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private string $version;

	/**
	 * The Elasticsearch caller.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      Rotaract_Elastic_Caller $elastic_caller    The object that handles search calls to the Elasticsearch instance.
	 */
	private Rotaract_Elastic_Caller $elastic_caller;

	/**
	 * The Elasticsearch caller.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      Rotaract_OpenCage_Caller $opencage_caller    The object that handles search calls to the Elasticsearch instance.
	 */
	private Rotaract_OpenCage_Caller $opencage_caller;

	/**
	 * The Google Maps API key.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $google_api_key    The secret key for Google Maps API calls.
	 */
	private string $google_api_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param    string                   $rotaract_club_finder    The name of the plugin.
	 * @param    string                   $version        The version of this plugin.
	 * @param    Rotaract_Elastic_Caller  $elastic_caller Elasticsearch call handler.
	 * @param    Rotaract_OpenCage_Caller $opencage_caller Elasticsearch call handler.
	 * @since    2.0.0
	 */
	public function __construct( string $rotaract_club_finder, string $version, Rotaract_Elastic_Caller $elastic_caller, Rotaract_OpenCage_Caller $opencage_caller ) {
		$this->rotaract_club_finder = $rotaract_club_finder;
		$this->version              = $version;
		$this->elastic_caller       = $elastic_caller;
		$this->opencage_caller      = $opencage_caller;

		if ( defined( 'GOOGLE_API_KEY' ) ) {
			$this->google_api_key = GOOGLE_API_KEY;
		}
	}

	/**
	 * Check if Google Maps API key is set.
	 *
	 * @return boolean
	 */
	public function isset_google_api_key(): bool {
		return isset( $this->google_api_key );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->rotaract_club_finder, plugins_url( 'css/public.css', __FILE__ ), array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {
		// Nothing happens here.
	}

	/**
	 * Returns the full include path for a partial.
	 *
	 * @param string $filename Name of the file to be included.
	 *
	 * @return string Path for include statement.
	 */
	private function get_partial( string $filename ): string {
		return plugin_dir_path( __FILE__ ) . 'partials/' . $filename;
	}

	/**
	 * Enqueues all style and script files and init calendar.
	 */
	public function club_finder_shortcode(): void {
		if ( ! $this->isset_google_api_key() ) {
			return;
		}

		wp_enqueue_script( $this->rotaract_club_finder . '-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $this->google_api_key . '&callback=initMap', array(), 'weekly', true );
		wp_enqueue_script( $this->rotaract_club_finder, plugins_url( 'js/public.js', __FILE__ ), array( $this->rotaract_club_finder . '-google-maps' ), $this->version, true );
		wp_localize_script(
			$this->rotaract_club_finder,
			'scriptData',
			array(
				'icon'    => plugins_url( 'images/rac-marker.svg' ),
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( $this->rotaract_club_finder ),
			)
		);

		include $this->get_partial( 'shortcode.php' );
	}

	/**
	 * AJAX handler using JSON
	 */
	public function find_clubs_in_range(): void {
		check_ajax_referer( $this->rotaract_club_finder );
		if ( ! isset( $_POST['location'], $_POST['range'] ) ) {
			return;
		}
		$location = sanitize_text_field( wp_unslash( $_POST['location'] ) );
		$range    = sanitize_text_field( wp_unslash( $_POST['range'] ) );

		$geodata = $this->opencage_caller->opencage_request( $location );
		$clubs   = $this->elastic_caller->get_clubs( $range, $geodata['lat'], $geodata['lng'] );

		wp_send_json_success( $clubs );
	}

}
