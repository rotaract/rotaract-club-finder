/**
 * Custom JS intended to be included in the in public view.
 *
 * @link       https://github.com/rotaract/rotaract-club-finder
 * @since      2.0.0
 *
 * @package    Rotaract_Club_Finder
 * @subpackage Rotaract_Club_Finder/public/js
 */

/**
 * Initialize Google Maps widget.
 */
function initMap( searchedLocation = {}, markers = {} ) {
	// Set default search parameter.
	let center = {lat: 51.186867, lng: 10.0575056}; // Somewhere in Eschwege.
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
	let map = new google.maps.Map(
		document.getElementById( 'map' ),
		{
			center: center,
			zoom: zoom
		}
	)

	const infoWindow  = new google.maps.InfoWindow()
	const markerCount = Object.keys( markers ).length
	for (let i = 0; i < markerCount; i++) {
		const club = markers[i];
		let text   = '<b>RAC ' + club['name'] + '</b><br>Distrikt ' + club['district']
		if (club['homepage_url']) {
			text += '<br><br><a href="' + club['homepage_url'] + '" target="_blank">zur Clubseite</a>';
		}
		const marker = new google.maps.Marker(
			{
				position: {lat: parseFloat( club['_geo']['lat'] ), lng: parseFloat( club['_geo']['lng'] )},
				icon: scriptData.icon,
				map: map,
				title: 'RAC ' + club['name'],
				text: text
			}
		)
		google.maps.event.addListener(
			marker,
			'click',
			function() {
				infoWindow.setContent( this.text );
				infoWindow.open( map, this );
			}
		);
	}
}

function handleResults( data ) {
	console.log('I proudly run with Meilisearch!');
	console.log(data);
	const clubs          = data.data.clubs;
	const meili      = data.data.meilidata;
	const searchLocation = data.data.geodata;

	const clubCount = Object.keys( meili ).length;
	let text        = '';
	if (clubCount > 0) {
		text = '<h3>Sucherergebnisse <small style="font-weight: normal;">(' + clubCount + ')</small></h3>';
	}
	for (let i = 0; i < clubCount; i++) {
		let club = meili[i];	
		text    += '<div class="club-finder-list-line">' +
					'<div class="club-finder-list-name">' +
					'<b>RAC ' + club['name'] + '</b><br>' +
					'<span class="district">Distrikt ' + club['district'] + '</span>' +
					'</div>';
		if (club['homepage_url']) {
			text += '<div class="club-finder-list-link">' +
					'<a href="' + club['homepage_url'] + '" target="_blank">zur Clubseite</a>' +
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

	const call = jQuery.post(
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

jQuery.getScript( scriptData.gmapsjs );

const search = document.getElementById( 'rotaract-club-search' );
search.addEventListener( 'submit', searchClubs );
