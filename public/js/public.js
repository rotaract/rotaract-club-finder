function initMap( searchedLocation = {}, markers = {} ) {

	let center = {lat: 52.510494, lng: 13.396764};
	let zoom   = 5;
	if (Object.entries( searchedLocation ).length !== 0 && searchedLocation.constructor === Object) {
		center    = searchedLocation;
		var range = document.getElementById( 'club-finder-range' ).value;
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
	for (var i = 0; i < markerCount; i++) {
		var club   = markers[i]['_source'];
		var marker = new google.maps.Marker(
			{
				position: {lat: parseFloat( club['location']['lat'] ), lng: parseFloat( club['location']['lon'] )},
				icon: scriptData.icon,
				map: map,
				title: 'RAC ' + club['name'],
				text: '<b>RAC ' + club['name'] + '</b><br>' + club['district_name'] + '<br><br><a href="' + club['homepage_url'] + '" target="_blank">zur Clubseite</a>'
			}
		);
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
	const clubs          = data.data.clubs;
	const searchLocation = data.data.geodata;

	const clubCount = Object.keys( clubs ).length;
	let text        = '';
	if (clubCount > 0) {
		text = '<h3>Sucherergebnisse <small style="font-weight: normal;">(' + clubCount + ')</small></h3>';
	}
	for (let i = 0; i < clubCount; i++) {
		let club = clubs[i]['_source'];
		text    += '<div class="club-finder-list-line"><div class="club-finder-list-name"><b>RAC ' + club['name'] + '</b><br><span class="district">' + club['district_name'] + '</span></div><div class="club-finder-list-link"><a href="' + club['homepage_url'] + '" target="_blank">zur Clubseite</a></div></div>';
	}

	document.getElementById( 'club-finder-list' ).innerHTML = text;
	initMap( searchLocation, clubs );
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
