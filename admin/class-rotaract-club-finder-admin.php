<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      2.0.0
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/admin
 * @author     Ressort IT-Entwicklung - Rotaract Deutschland <it-entwicklung@rotaract.de>
 */
class Rotaract_Club_Finder_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @param    string $rotaract_club_finder The name of this plugin.
	 * @param    string $version     The version of this plugin.
	 * @since    2.0.0
	 */
	public function __construct( string $rotaract_club_finder, string $version ) {
		$this->rotaract_club_finder = $rotaract_club_finder;
		$this->version              = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {
		// Nothing happens here.
	}

	/**
	 * Register the JavaScript for the admin area.
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
	 * ### REMOVE ###
	 * HTML notice that elasticsearch configuration is missing.
	 *
	 * @since    2.0.0
	 */
	public function elastic_missing_notice() {
		include $this->get_partial( 'notice-elastic-missing.php' );
	}

	/**
	 * HTML notice that meilisearch configuration is missing.
	 *
	 * @since    2.0.0
	 */
	public function meili_missing_notice() {
		include $this->get_partial( 'notice-meili-missing.php' );
	}

	/**
	 * HTML notice that OpenCage configuration is missing.
	 *
	 * @since    2.0.0
	 */
	public function opencage_missing_notice() {
		include $this->get_partial( 'notice-opencage-missing.php' );
	}

}
