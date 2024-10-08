<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://github.com/rotaract/rotaract-club-finder
 * @since 2.0.0
 * @package Rotaract_Club_Finder
 * @category Core
 *
 * @wordpress-plugin
 * Plugin Name:       Rotaract Club Finder
 * Plugin URI:        https://github.com/rotaract/rotaract-club-finder
 * Description:       Plugin for Google store locator integration with advanced search.
 * Version:           5.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Ressort IT-Entwicklung - Rotaract Deutschland
 * Author URI:        https://rotaract.de/rotaract-deutschland/
 * License:           EUPL-1.2
 * License URI:       https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 * Text Domain:       rotaract-club-finder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ROTARACT_CLUB_FINDER_VERSION', '5.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rotaract-club-finder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_rotaract_club_finder() {

	$plugin = new Rotaract_Club_Finder();
	$plugin->run();
}
run_rotaract_club_finder();
