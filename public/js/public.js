/**
 * Custom JS intended to be included in the in public view.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      3.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/public/js
 */

/**
 * Initialize Leaflet JS map.
 */
const clubFinderMap    = L.map( 'club-finder-map' );
const clubFinderLayers = L.layerGroup().addTo( clubFinderMap );
L.tileLayer(
	'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
	{
		maxZoom: 19,
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	}
).addTo( clubFinderMap );

function initMap( searchedLocation = {}, markers = {} ) {
	// Set default search parameter.
	let center = { lat: 51.186867, lng: 10.0575056 }; // Center of Germany.
	let zoom   = 5;

	if (Object.entries( searchedLocation ).length !== 0 && searchedLocation.constructor === Object) {
		center      = searchedLocation;
		const range = document.getElementById( 'club-finder-range' ).value  // In kilometer.
		switch (range) {
			case '5':
				zoom = 11.5;
				break;
			case '10':
				zoom = 11;
				break;
			case '20':
				zoom = 10;
				break;
			case '50':
				zoom = 8;
				break;
			default:
				zoom = 5;
				break;
		}
	}
	clubFinderMap.setView( Object.values( center ), zoom );

	const icon = L.icon({
		iconUrl: scriptData.icon,
		iconSize: [ 60, 60 ]
	});

	clubFinderLayers.clearLayers();
	Object.values( markers ).forEach(club => {
		let text   = `<p><b>RAC ${club['name']}</b><br>Distrikt ${club['district']?.substring( 1 )}</p>`;
		if (club['homepage_url']) {
			text += `<p><a href="${club['homepage_url']}" target="_blank">zur Clubseite</a></p>`;
		}
		L.marker(
			[ parseFloat( club['_geo']['lat'] ), parseFloat( club['_geo']['lng'] )],
			{ icon }
		).bindPopup( text ).addTo( clubFinderLayers );
	});
}
initMap();

function handleResults( data ) {
	const meili          = data.data.meilidata;
	const searchLocation = data.data.geodata;

	const clubCount = Object.keys( meili ).length;
	let text        = '';
	if (clubCount > 0) {
		text = `<h3>Sucherergebnisse <small style="font-weight: normal;">(${clubCount})</small></h3>`;
	}
	for (let i = 0; i < clubCount; i++) {
		let club = meili[i];
		text += '<div class="club-finder-list-line">' +
				'<div class="club-finder-list-name">' +
				`<b>RAC ${club['name']}</b><br>` +
				`<span class="district">Distrikt ${club['district']?.substring( 1 )}</span>` +
				'</div>';
		if (club['homepage_url']) {
			text += '<div class="club-finder-list-link">' +
					`<a href="${club['homepage_url']}" target="_blank">zur Clubseite</a>` +
					'</div>';
		}
		text += '</div>';
	}

	document.getElementById( 'club-finder-list' ).innerHTML = text;
	initMap( searchLocation, meili );
}

function searchClubs( event ) {
	event.preventDefault();
	const searchLocation = document.getElementById( 'rotaract-search' ).value;
	const range          = document.getElementById( 'club-finder-range' ).value;

	jQuery.post(
		scriptData.ajaxUrl,
		{
			_ajax_nonce: scriptData.nonce,
			action: 'find_clubs_in_range',
			location: searchLocation,
			range: range
		},
		handleResults,
		'json'
	);
}

const search = document.getElementById( 'rotaract-club-search' );
search.addEventListener( 'submit', searchClubs );
