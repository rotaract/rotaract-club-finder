<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      3.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/admin/partials
 */

?>
<div class="error notice">
	<p>
		<strong><?php esc_html_e( 'Rotaract Club Finder', 'rotaract-club-finder' ); ?>:</strong>
		<?php esc_html_e( 'Please set Meilisearch Host in your WordPress configuration!', 'rotaract-club-finder' ); ?>
	</p>
</div>
