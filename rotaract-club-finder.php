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

add_shortcode( 'RotaractClubFinder', 'initClubFinder' );

function initClubFinder() {

	$html = '<b>Suche</b>';

	$html     .= '<form id="rotaract-club-search">';
	$html     .= '<input type="text" id="rotaract-search" name="search">';
	$html     .= '<select id="club-finder-range">';
	$html     .= '<option value="5">5km</option>';
	$html     .= '<option value="10">10km</option>';
	$html     .= '<option value="20">20km</option>';
	$html     .= '<option value="50">50km</option>';
	$html     .= '</select>';
		$html .= '<button type="submit"><i class="fas fa-search" title="Suchen"></i></button>';
	$html     .= '</form>';

	$html .= '<div id="map"></div>';
	$html .= '<div id="club-finder-list"></div>';

	myEnqueueScripts();
	return $html;

}

// create custom plugin settings menu
add_action( 'admin_menu', 'storeLocatorSettings' );

function storeLocatorSettings() {

	// create new top-level menu
	// add_menu_page('Rotaract Store Locator Settings', 'Rotaract Store Locator', 'administrator', __FILE__, 'settingsPage' , plugins_url('/images/icons/club-finder-icon.jpg', __FILE__) );
	add_menu_page( 'Rotaract Club Finder Settings', 'Rotaract Club Finder', 'administrator', __FILE__, 'settingsPage', 'dashicons-location-alt' );

	// call register settings function
	add_action( 'admin_init', 'registerSettingsPage' );
}


function registerSettingsPage() {
	// register our settings
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
