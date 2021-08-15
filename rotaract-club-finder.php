<?php
/**
 * Plugin Name: Rotaract Club Finder
 * Plugin URI: https:...rotaract.de/rotaract-club-finder
 * Description: Plugin for Google store locator integration with advanced search.
 * Version: 1.0
 * Author: Ressort IT-Entwicklung
 * Author: URI: https://rotaract.de
 *
 */


function myEnqueueScripts() {

    wp_register_style('rotaract-club-finder', plugins_url( 'rotaract-club-finder.css', __FILE__));
    wp_enqueue_style('rotaract-club-finder');

    wp_enqueue_script('rotaract-club-finder', plugins_url( 'rotaract-club-finder.js', __FILE__));

    $scriptData = array(
        'clubApiKeyGoogle' => get_option('clubApiKeyGoogle'),
        'clubApiKeyOpenCage' => get_option('clubApiKeyOpenCage')
    );

    wp_localize_script('rotaract-club-finder', 'scriptData', $scriptData);
}

add_shortcode('RotaractClubFinder', 'initClubFinder');

function initClubFinder() {

	$html =  '<b>Suche</b>';

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

// create custom plugin settings menu
add_action('admin_menu', 'storeLocatorSettings');

function storeLocatorSettings() {

	//create new top-level menu
	//add_menu_page('Rotaract Store Locator Settings', 'Rotaract Store Locator', 'administrator', __FILE__, 'settingsPage' , plugins_url('/images/icons/club-finder-icon.jpg', __FILE__) );
	add_menu_page('Rotaract Club Finder Settings', 'Rotaract Club Finder', 'administrator', __FILE__, 'settingsPage' , 'dashicons-location-alt' );

	//call register settings function
	add_action( 'admin_init', 'registerSettingsPage' );
}


function registerSettingsPage() {
	//register our settings
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
					<td><input type="text" name="clubApiKeyGoogle" value="<?php echo esc_attr( get_option('clubApiKeyGoogle') ); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">OpenCage API-Key</th>
					<td><input type="text" name="clubApiKeyOpenCage" value="<?php echo esc_attr( get_option('clubApiKeyOpenCage') ); ?>" /></td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>
	</div>
<?php } ?>
