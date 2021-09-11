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
 * @since 1.0.0
 * @package Rotaract_Club_Finder
 * @category Core
 *
 * @wordpress-plugin
 * Plugin Name:       Rotaract Club Finder
 * Plugin URI:        https://github.com/rotaract/rotaract-club-finder
 * Description:       Plugin for Google store locator integration with advanced search.
 * Version:           1.3.7
 * Author:            Ressort IT-Entwicklung - Rotaract Deutschland
 * Author URI:        https://rotaract.de/ueber-rotaract/rdk/
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
define( 'ROTARACT_CLUB_FINDER_VERSION', '1.3.7' );

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
 * @since    1.0.0
 */
function run_rotaract_club_finder() {

	$plugin = new Rotaract_Appointments();
	$plugin->run();

}
run_rotaract_club_finder();


<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name: Rotaract Club Finder
 * Plugin URI: https:github.com/rotaract/rotaract-club-finder
 * Description: Plugin for Google store locator integration with advanced search.
 * Version: 1.0.0
 * Author: Rotaract Germany
 * Author URI: https://rotaract.de/ueber-rotaract/rdk/
 *
 * @link https://github.com/rotaract/rotaract-club-finder
 * @since 1.0.0
 * @package Rotaract_Club_Finder
 * @category Core
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

function myEnqueueScripts() {

	wp_register_style( 'rotaract-club-finder', plugins_url( 'rotaract-club-finder.css', __FILE__ ) );
	wp_enqueue_style( 'rotaract-club-finder' );

	wp_enqueue_script( 'rotaract-club-finder', plugins_url( 'rotaract-club-finder.js', __FILE__ ) );

	$script_data = array(
		'clubApiKeyGoogle'   => get_option( 'clubApiKeyGoogle' ),
		'clubApiKeyOpenCage' => get_option( 'clubApiKeyOpenCage' ),
	);

	wp_localize_script( 'rotaract-club-finder', 'script_data', $script_data );
}

function initClubFinder() {

	$html = '<b>Suche</b>';

	$html .= '<form id="rotaract-club-search">';
	$html .= '<input type="text" id="rotaract-search" name="search">';
	$html .= '<select id="club-finder-range">';
	$html .= '<option value="5">5km</option>';
	$html .= '<option value="10">10km</option>';
	$html .= '<option value="20" selected="selected">20km</option>';
	$html .= '<option value="50">50km</option>';
	$html .= '</select>';
	$html .= '<button type="submit"><i class="fas fa-search" title="Suchen"></i></button>';
	$html .= '</form>';

	$html .= '<div id="map"></div>';
	$html .= '<div id="club-finder-list"></div>';

	myEnqueueScripts();
	return $html;

}
add_shortcode( 'RotaractClubFinder', 'initClubFinder' );

/**
 * Creates custom plugin settings menu.
 */
function storeLocatorSettings() {

	// Create new top-level menu.
	// add_menu_page('Rotaract Store Locator Settings', 'Rotaract Store Locator', 'administrator', __FILE__, 'settingsPage' , plugins_url('/images/icons/club-finder-icon.jpg', __FILE__) );
	add_menu_page( 'Rotaract Club Finder Settings', 'Rotaract Club Finder', 'administrator', __FILE__, 'settingsPage', 'dashicons-location-alt' );

	// Call register settings function.
	add_action( 'admin_init', 'registerSettingsPage' );
}
add_action( 'admin_menu', 'storeLocatorSettings' );

function registerSettingsPage() {
	// Register our settings.
	register_setting( 'rotaract-club-finder-settings-group', 'clubApiKeyGoogle' );
	register_setting( 'rotaract-club-finder-settings-group', 'clubApiKeyOpenCage' );
}

function settingsPage() {
	?>
	<div class="wrap">
		<h1>Rotaract Store Locator</h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'rotaract-club-finder-settings-group' ); ?>
			<?php do_settings_sections( 'rotaract-club-finder-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Google API-Key</th>
					<td><input type="text" name="clubApiKeyGoogle" value="<?php echo esc_attr( get_option( 'clubApiKeyGoogle' ) ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">OpenCage API-Key</th>
					<td><input type="text" name="clubApiKeyOpenCage" value="<?php echo esc_attr( get_option( 'clubApiKeyOpenCage' ) ); ?>" /></td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>
	</div>
<?php } ?>
