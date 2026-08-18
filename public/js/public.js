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

function districtLabel( club ) {
	return scriptData.i18n.district + ' ' + ( club['district'] ? club['district'].substring( 1 ) : '' );
}

function buildClubPopup( club ) {
	const content     = document.createElement( 'div' );
	const title       = document.createElement( 'b' );
	title.textContent = scriptData.i18n.clubPrefix + ' ' + club['name'];
	content.appendChild( title );
	content.appendChild( document.createElement( 'br' ) );
	content.appendChild( document.createTextNode( districtLabel( club ) ) );
	if (club['homepage_url']) {
		content.appendChild( document.createElement( 'br' ) );
		const link       = document.createElement( 'a' );
		link.href        = club['homepage_url'];
		link.target      = '_blank';
		link.textContent = scriptData.i18n.clubPage;
		content.appendChild( link );
	}
	return content;
}

function initMap( searchedLocation = {}, markers = {} ) {
	// Set default search parameter.
	let center = { lat: 51.186867, lng: 10.0575056 }; // Center of Germany.
	let zoom   = 5;

	if (Object.entries( searchedLocation ).length !== 0 && searchedLocation.constructor === Object) {
		center      = searchedLocation;
		const range = document.getElementById( 'club-finder-range' ).value;  // In kilometer.
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

	const icon = L.icon(
		{
			iconUrl: scriptData.icon,
			iconSize: [ 60, 60 ]
		}
	);

	clubFinderLayers.clearLayers();
	Object.values( markers ).forEach(
		function ( club ) {
			L.marker(
				[ parseFloat( club['_geo']['lat'] ), parseFloat( club['_geo']['lng'] ) ],
				{ icon }
			).bindPopup( buildClubPopup( club ) ).addTo( clubFinderLayers );
		}
	);
}
initMap();

function handleResults( data ) {
	const meili          = data.data.meilidata;
	const searchLocation = data.data.geodata;

	const list      = document.getElementById( 'club-finder-list' );
	const clubCount = Object.keys( meili ).length;

	list.replaceChildren();

	if (clubCount > 0) {
		const heading          = document.createElement( 'h3' );
		const count            = document.createElement( 'small' );
		count.style.fontWeight = 'normal';
		count.textContent      = '(' + clubCount + ')';
		heading.textContent    = scriptData.i18n.searchResults + ' ';
		heading.appendChild( count );
		list.appendChild( heading );
	}

	for (let i = 0; i < clubCount; i++) {
		const club    = meili[i];
		const row     = document.createElement( 'div' );
		row.className = 'club-finder-list-line';

		const nameDiv     = document.createElement( 'div' );
		nameDiv.className = 'club-finder-list-name';
		const bold        = document.createElement( 'b' );
		bold.textContent  = scriptData.i18n.clubPrefix + ' ' + club['name'];
		nameDiv.appendChild( bold );
		nameDiv.appendChild( document.createElement( 'br' ) );
		const district       = document.createElement( 'span' );
		district.className   = 'district';
		district.textContent = districtLabel( club );
		nameDiv.appendChild( district );
		row.appendChild( nameDiv );

		if (club['homepage_url']) {
			const linkDiv     = document.createElement( 'div' );
			linkDiv.className = 'club-finder-list-link';
			const link        = document.createElement( 'a' );
			link.href         = club['homepage_url'];
			link.target       = '_blank';
			link.textContent  = scriptData.i18n.clubPage;
			linkDiv.appendChild( link );
			row.appendChild( linkDiv );
		}

		list.appendChild( row );
	}

	initMap( searchLocation, meili );
}

function showError( message ) {
	const list        = document.getElementById( 'club-finder-list' );
	const error       = document.createElement( 'p' );
	error.textContent = message;
	list.replaceChildren( error );
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
		'json'
	).done(
		function ( data ) {
			if (data.success) {
				handleResults( data );
			} else {
				const msg = data.data && data.data.message === 'Location not found.'
					? scriptData.i18n.locationUnknown
					: scriptData.i18n.searchError;
				showError( msg );
			}
		}
	).fail(
		function () {
			showError( scriptData.i18n.searchError );
		}
	);
}

const search = document.getElementById( 'rotaract-club-search' );
search.addEventListener( 'submit', searchClubs );
