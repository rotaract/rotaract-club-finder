<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      1.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/includes
 * @author     Ressort IT-Entwicklung - Rotaract Deutschland <it-entwicklung@rotaract.de>
 */
class Rotaract_Club_Finder_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'rotaract-club-finder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
