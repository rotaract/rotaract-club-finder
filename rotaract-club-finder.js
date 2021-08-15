function initMap(searchedLocation = {}, markers = {}) {

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
	map = new google.maps.Map(
		document.getElementById( 'map' ),
		{
			center: center,
			zoom: zoom
		}
	);

	var icon        = '/wp-content/plugins/rotaract-club-finder/images/rac-marker.svg'
	var infoWindow  = new google.maps.InfoWindow();
	var markerCount = Object.keys( markers ).length;
	for (var i = 0; i < markerCount; i++) {
		var club   = markers[i]['_source'];
		var marker = new google.maps.Marker(
			{
				position: {lat: parseFloat( club['location']['lat'] ), lng: parseFloat( club['location']['lon'] )},
				icon: icon,
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

var script  = document.createElement( 'script' );
script.type = 'text/javascript';
script.src  = 'https://maps.googleapis.com/maps/api/js?key=' + script_data.clubApiKeyGoogle + '&callback=initMap';
document.getElementById( 'map' ).append( script );
var search = document.getElementById( 'rotaract-club-search' );
search.addEventListener(
	'submit',
	function(e) {
		e.preventDefault();
		var searchField = document.getElementById( 'rotaract-search' );

		var apiUrl = 'https://api.opencagedata.com/geocode/v1/json?q=' +
		searchField.value + '+germany&key=' + script_data.clubApiKeyOpenCage + '&pretty=1';
		var xhttp  = new XMLHttpRequest();
		xhttp.open( "GET", apiUrl, false );
		xhttp.send();
		var result = JSON.parse( xhttp.responseText )['results'];
		var range  = document.getElementById( 'club-finder-range' ).value;

		var lat                = result[0]['geometry']['lat'];
		var lng                = result[0]['geometry']['lng'];
		var elasticHandlerCall = new XMLHttpRequest();
		elasticHandlerCall.open( 'POST', '/wp-content/plugins/rotaract-club-finder/rotaract-elastic-caller.php?lat=' + lat + '&lng=' + lng + '&range=' + range, false );
		elasticHandlerCall.send();
		var rs = JSON.parse( elasticHandlerCall.responseText );
		console.log( rs );
		var clubs            = rs['hits']['hits'];
		var searchedLocation = result[0]['geometry'];

		var clubCount = Object.keys( clubs ).length;
		var text      = '';
		if (clubCount > 0) {
			text = '<h3>Sucherergebnisse</h3>';
		}
		for (var i = 0; i < clubCount; i++) {
			var club = clubs[i]['_source'];
			text    += '<div class="club-finder-list-line"><div class="club-finder-list-name"><b>RAC ' + club['name'] + '</b><br><span class="district">' + club['district_name'] + '</span></div><div class="club-finder-list-link"><a href="' + club['homepage_url'] + '" target="_blank">zur Clubseite</a></div></div>';
		}

		document.getElementById( 'club-finder-list' ).innerHTML = text;
		initMap( searchedLocation,clubs );
	}
);
