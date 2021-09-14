<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/public/partials
 */

?>
<h2>Suche</h2>
<form id="rotaract-club-search">
	<input type="text" id="rotaract-search" name="search">
	<select id="club-finder-range">
		<option value="5">5km</option>
		<option value="10">10km</option>
		<option value="20" selected="selected">20km</option>
		<option value="50">50km</option>
	</select>
	<button type="submit"><i class="fas fa-search" title="Suchen"></i></button>
</form>
<div id="map"></div>
<div id="club-finder-list"></div>
