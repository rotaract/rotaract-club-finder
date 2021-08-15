<?php

/**
* Receive appointments from elestic that match the search_param.
*
* @param String $api_path absolute API path.
* @param String $search_param API attributes in JSON format.
*
* @return array of appointments
*/
function elastic_request( string $api_path, string $search_param ): array {

	$url    = trailingslashit( 'hosting.rotaract.de:9200' ) . $api_path;
	$header = array(
		'Content-Type' => 'application/json',
	);

	$res      = wp_remote_post(
		$url,
		array(
			'headers' => $header,
			'body'    => $search_param,
		)
	);
	$res_body = wp_remote_retrieve_body( $res );

	$result = json_decode( $res_body )->hits->hits;
	return $result ?: array();
}

function get_clubs( string $range, string $lat, string $lng ) {

	$path         = 'clubs/_search';
	$search_param = array(
		'_source' => array(
			'location',
			'name',
			'district_name',
			'homepage_url',
		),
		'query' => array(
			'filter' => array(
				'geo_distance' => array(
					'distance' => $range . 'km',
					'location' => array(
						'lat' => $lat,
						'lng' => $lng,
					),
				),
				'terms' => array(
					'status' => array(
						'active',
						'founding',
						'preparing',
					)
				),
			),
		),
	);

	$res = elastic_request( $path, wp_json_encode( $search_param ) );
	return $res;

}

echo get_clubs( $_REQUEST['range'] ?: '20', $_REQUEST['lat'], $_REQUEST['lng'] );
