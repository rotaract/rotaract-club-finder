<?php

$location = '{"lat" : ' . $_REQUEST['lat'] . ', "lon" : ' . $_REQUEST['lng'] . '}';
if ( ! empty( $_REQUEST['range'] ) ) {
	$range = $_REQUEST['range'];
} else {
	$range = '20';
}

$searchParam = '{
				"_source": ["location", "name", "district_name", "homepage_url"],
                "query" : {
                "bool" : {
                        "must" : {
                                "match_all" : {}
                        },
                       "filter" : [{
                                "geo_distance" : {
                                        "distance" : "' . $range . 'km",
                                        "location" : ' . $location . '
                                }
                        }, 
                        {"terms": { "status": ["active", "founding", "preparing"]}}

                        ]
                }
        }

        }';
$header      = array(
	'content-type: application/json',
);
$url         = 'curl -X GET "hosting.rotaract.de:9200/clubs/_search" -H \'Content-Type: application/json\' -d \'' . $searchParam . '\'';
$url         = 'hosting.rotaract.de:9200/clubs/_search';
$curl        = curl_init();
curl_setopt( $curl, CURLOPT_URL, $url );
curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $curl, CURLOPT_POSTFIELDS, $searchParam );
$res = curl_exec( $curl );
curl_close( $curl );
echo $res;




